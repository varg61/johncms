<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
$url = Router::getUri(2);

/*
-----------------------------------------------------------------
Редактирование категорий
-----------------------------------------------------------------
*/
if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
    $req = DB::PDO()->query("SELECT * FROM `cms_download_category` WHERE `id` = " . VARS::$ID);
    $res = $req->fetch();
    if (!$req->rowCount() || !is_dir($res['dir'])) {
        echo Functions::displayError(__('not_found_dir'), '<a href="' . $url . '">' . __('download_title') . '</a>');
        exit;
    }

    /*
    -----------------------------------------------------------------
    Сдвиг категорий
    -----------------------------------------------------------------
    */
    if (isset($_GET['up']) || isset($_GET['down'])) {
        if (isset($_GET['up'])) {
            $order = 'DESC';
            $val = '<';
        } else {
            $order = 'ASC';
            $val = '>';
        }
        $req_two = DB::PDO()->query("SELECT * FROM `cms_download_category` WHERE `refid` = '" . $res['refid'] . "' AND `sort` $val '" . $res['sort'] . "' ORDER BY `sort` $order LIMIT 1");
        if ($req_two->rowCount()) {
            $res_two = $req_two->fetch();
            DB::PDO()->exec("UPDATE `cms_download_category` SET `sort` = '" . $res_two['sort'] . "' WHERE `id` = '" . VARS::$ID . "' LIMIT 1");
            DB::PDO()->exec("UPDATE `cms_download_category` SET `sort` = '" . $res['sort'] . "' WHERE `id` = '" . $res_two['id'] . "' LIMIT 1");
        }
        header('location: ' . $url . '?id=' . $res['refid']);
        exit;
    }

    /*
    -----------------------------------------------------------------
    Изменяем данные
    -----------------------------------------------------------------
    */
    if (isset($_POST['submit'])) {
        $rus_name = isset($_POST['rus_name']) ? trim($_POST['rus_name']) : '';
        if (empty($rus_name))
            $error[] = __('error_empty_fields');
        $error_format = FALSE;
        if (Vars::$USER_RIGHTS == 9 && isset($_POST['user_down'])) {
            $format = isset($_POST['format']) ? trim($_POST['format']) : FALSE;
            $format_array = explode(', ', $format);
            foreach ($format_array as $value) {
                if (!in_array($value, $defaultExt))
                    $error_format .= 1;
            }
            $user_down = 1;
            $format_files = Functions::checkout($format);
        } else {
            $user_down = 0;
            $format_files = '';
        }
        if ($error_format)
            $error[] = __('extensions_ok') . ': ' . implode(', ', $defaultExt);
        if ($error) {
            echo functions::displayError($error, '<a href="' . $url . '?act=edit_cat&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
            exit;
        }

        $desc = isset($_POST['desc']) ? trim($_POST['desc']) : '';

        $STH = DB::PDO()->prepare('
            UPDATE `cms_download_category` SET
            `field`    = ?,
            `text`     = ?,
            `desc`     = ?,
            `rus_name` = ?
            WHERE `id` = ?
        ');

        $STH->execute(array(
            $user_down,
            $format_files,
            $desc,
            $rus_name,
            VARS::$ID
        ));
        $STH = NULL;

        header('location: ' . $url . '?id=' . VARS::$ID);
    } else {
        $name = Functions::checkout($res['rus_name']);
        echo '<div class="phdr"><b>' . __('download_edit_cat') . ':</b> ' . $name . '</div>' .
            '<div class="menu"><form action="' . $url . '?act=edit_cat&amp;id=' . VARS::$ID . '" method="post">' .
            __('dir_name_view') . ':<br/><input type="text" name="rus_name" value="' . $name . '"/><br/>' .
            __('dir_desc') . ' (max. 500):<br/><textarea name="desc" rows="4">' . Functions::checkout($res['desc']) . '</textarea><br/>';
        if (Vars::$USER_RIGHTS == 9) {
            echo '<div class="sub"><input type="checkbox" name="user_down" value="1"' . ($res['field'] ? ' checked="checked"' : '') . '/> ' . __('user_download') . '<br/>' .
                __('extensions') . ':<br/><input type="text" name="format" value="' . $res['text'] . '"/></div>' .
                '<div class="sub">' . __('extensions_ok') . ':<br /> ' . implode(', ', $defaultExt) . '</div>';
        }
        echo ' <input type="submit" name="submit" value="' . __('sent') . '"/><br/></form></div>';
    }
    echo '<div class="phdr">' .
        '<a href="' . $url . '?id=' . VARS::$ID . '">' . __('back') . '</a> | ' .
        '<a href="' . $url . '">' . __('download_title') . '</a></div>';
} else {
    header('Location: ' . Vars::$HOME_URL . '404');
}