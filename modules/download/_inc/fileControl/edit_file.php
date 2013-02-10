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
Редактирование файла
-----------------------------------------------------------------
*/
$req_down = DB::PDO()->query("SELECT * FROM `cms_download_files` WHERE `id` = '" . VARS::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = $req_down->fetch();
if (!$req_down->rowCount() || !is_file($res_down['dir'] . '/' . $res_down['name']) || (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
    echo Functions::displayError('<a href="' . $url . '">' . __('download_title') . '</a>');
    exit;
}
if (isset($_POST['submit'])) {
    $name = isset($_POST['text']) ? trim($_POST['text']) : NULL;
    $name_link = isset($_POST['name_link']) ? Functions::checkout(mb_substr($_POST['name_link'], 0, 200)) : NULL;
    if ($name_link && $name) {
        $STH = DB::PDO()->prepare('
            UPDATE `cms_download_files` SET
            `rus_name` = ?,
            `text`     = ?
            WHERE `id` = ?
        ');

        $STH->execute(array(
            $name,
            $name_link,
            Vars::$ID
        ));
        $STH = NULL;

        header('Location: ' . $url . '?act=view&id=' . Vars::$ID);
    } else
        echo functions::displayError(__('error_empty_fields'), '<a href="' . $url . '?act=edit_file&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
} else {
    $file_name = Functions::checkout($res_down['rus_name']);
    echo '<div class="phdr"><b>' . $file_name . '</b></div>' .
        '<div class="list1"><form action="' . $url . '?act=edit_file&amp;id=' . Vars::$ID . '" method="post">' .
        __('name_file') . '(мах. 200):<br /><input type="text" name="text" value="' . $file_name . '"/><br />' .
        __('link_file') . ' (мах. 200):<br /><input type="text" name="name_link" value="' . $res_down['text'] . '"/><br />' .
        '<input type="submit" name="submit" value="' . __('sent') . '"/></form></div>' .
        '<div class="phdr"><a href="' . $url . '?act=view&amp;id=' . Vars::$ID . '">' . __('back') . '</a></div>';
}