<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
$url = Router::getUrl(2);

/*
-----------------------------------------------------------------
Удалить картинку
-----------------------------------------------------------------
*/
if ($img && $user['id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 6) {
    $req = mysql_query("SELECT * FROM `cms_album_files` WHERE `id` = '$img' AND `user_id` = '" . $user['id'] . "' LIMIT 1");
    if (mysql_num_rows($req)) {
        $res = mysql_fetch_assoc($req);
        $album = $res['album_id'];
        echo '<div class="phdr"><a href="' . $url . '?act=show&amp;al=' . $album . '&amp;user=' . $user['id'] . '"><b>' . __('photo_album') . '</b></a> | ' . __('image_delete') . '</div>';
        //TODO: Сделать проверку, чтоб администрация не могла удалять фотки старших по должности
        if (isset($_POST['submit'])) {
            // Удаляем файлы картинок
            @unlink(ALBUMPATH . $user['id'] . DIRECTORY_SEPARATOR . $res['img_name']);
            @unlink(ALBUMPATH . $user['id'] . DIRECTORY_SEPARATOR . $res['tmb_name']);
            // Удаляем записи из таблиц
            mysql_query("DELETE FROM `cms_album_files` WHERE `id` = '$img'");
            mysql_query("DELETE FROM `cms_album_votes` WHERE `file_id` = '$img'");
            mysql_query("DELETE FROM `cms_album_comments` WHERE `sub_id` = '$img'");
            mysql_query("OPTIMIZE TABLE `cms_album_comments`, `cms_album_votes`");
            header('Location: ' . $url . '?act=show&al=' . $album . '&user=' . $user['id']);
        } else {
            echo'<div class="rmenu"><form action="' . $url . '?act=image_delete&amp;img=' . $img . '&amp;user=' . $user['id'] . '" method="post">' .
                '<p>' . __('image_delete_warning') . '</p>' .
                '<p><input type="submit" name="submit" value="' . __('delete') . '"/></p>' .
                '</form></div>' .
                '<div class="phdr"><a href="' . $url . '?act=show&amp;al=' . $album . 'user=' . $user['id'] . '">' . __('cancel') . '</a></div>';
        }
    } else {
        echo Functions::displayError(__('error_wrong_data'));
    }
}