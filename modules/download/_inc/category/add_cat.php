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
if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
    if (!Vars::$ID) $load_cat = $files_path;
    else {
        $req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . Vars::$ID . "' AND `type` = 1 LIMIT 1");
        $res_down = mysql_fetch_assoc($req_down);
        if (mysql_num_rows($req_down) == 0 || !is_dir($res_down['dir'] . '/' . $res_down['name'])) {
            echo Functions::displayError(lng('not_found_dir'), '<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
            exit;
        }
        $load_cat = $res_down['dir'] . '/' . $res_down['name'];
    }
    if (isset($_POST['submit'])) {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $rus_name = isset($_POST['rus_name']) ? trim($_POST['rus_name']) : '';
        $desc = isset($_POST['desc']) ? trim($_POST['desc']) : '';
        $user_down = isset($_POST['user_down']) ? 1 : 0;
        $format = $user_down && isset($_POST['format']) ? trim($_POST['format']) : FALSE;
        $error = array();
        if (empty($name))
            $error[] = lng('error_empty_fields');
        if (preg_match("/[^0-9a-zA-Z]+/", $name))
            $error[] = $error[] = lng('error_wrong_symbols');
        if (Vars::$USER_RIGHTS == 9 && $user_down) {
            foreach (explode(',', $format) as $value) {
                if (!in_array(trim($value), $defaultExt)) {
                    $error[] = lng('extensions_ok') . ': ' . implode(', ', $defaultExt);
                    break;
                }
            }
        }
        if ($error) {
            echo functions::displayError($error, '<a href="' . Vars::$URI . '?act=add_cat&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a>');
            exit;
        }
        if (empty($rus_name))
            $rus_name = $name;
        $dir = FALSE;
        if (!is_dir("$load_cat/$name"))
            $dir = mkdir("$load_cat/$name", 0777);
        if ($dir == TRUE) {
            chmod("$load_cat/$name", 0777);
            mysql_query("INSERT INTO `cms_download_files` SET
                `refid` = '" . Vars::$ID . "',
                `dir` = '$load_cat',
                `time` = '" . time() . "',
                `sort` = '" . time() . "',
                `name` = '" . mysql_real_escape_string($name) . "',
                `desc` = '" . mysql_real_escape_string($desc) . "',
                `type` = '1',
                `field` = '$user_down',
                `text` = '" . mysql_real_escape_string($format) . "',
                `rus_name` = '" . mysql_real_escape_string($rus_name) . "',
                `about` = ''
            ") or die(mysql_error());
            $cat_id = mysql_insert_id();
            echo '<div class="phdr"><b>' . lng('add_cat_title') . '</b></div>' .
                '<div class="list1">' . lng('add_cat_ok') . '</div>' .
                '<div class="list2"><a href="' . Vars::$URI . '?id=' . $cat_id . '">' . lng('continue') . '</a></div>';
        } else {
            echo functions::displayError(lng('add_cat_error'), '<a href="' . Vars::$URI . 'act=add_cat&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a>');
            exit;
        }
    } else {
        echo '<div class="phdr"><b>' . lng('add_cat_title') . '</b></div><div class="menu">' .
            '<form action="' . Vars::$URI . '?act=add_cat&amp;id=' . Vars::$ID . '" method="post">' .
            lng('dir_name') . ' [A-Za-z0-9]:<br/><input type="text" name="name"/><br/>' .
            lng('dir_name_view') . ':<br/><input type="text" name="rus_name"/><br/>' .
            lng('dir_desc') . ' (max. 500):<br/><textarea name="desc" cols="24" rows="4"></textarea><br/>';
        if (Vars::$USER_RIGHTS == 9) {
            echo '<div class="sub"><input type="checkbox" name="user_down" value="1" /> ' . lng('user_download') . '<br/>' .
                lng('extensions') . ':<br/><input type="text" name="format"/></div>' .
                '<div class="sub">' . lng('extensions_ok') . ':<br /> ' . implode(', ', $defaultExt) . '</div>';
        }
        echo ' <input type="submit" name="submit" value="' . lng('add_cat') . '"/><br/></form></div>';
    }
    echo '<div class="phdr">';
    if (Vars::$ID)
        echo '<a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . lng('back') . '</a> | ';
    echo '<a href="' . Vars::$URI . '">' . lng('download_title') . '</a></div>';
} else {
    header('Location: ' . Vars::$HOME_URL . '/404');
}