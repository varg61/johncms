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

require_once('../includes/head.php');

/*
-----------------------------------------------------------------
Удалить картинку
-----------------------------------------------------------------
*/
if ($img && $user['user_id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 6) {
    $req = mysql_query("SELECT * FROM `cms_album_files` WHERE `id` = '$img' AND `user_id` = '" . $user['user_id'] . "' LIMIT 1");
    if (mysql_num_rows($req)) {
        $res = mysql_fetch_assoc($req);
        $album = $res['album_id'];
        echo '<div class="phdr"><a href="album.php?act=show&amp;al=' . $album . '&amp;user=' . $user['user_id'] . '"><b>' . Vars::$LNG['photo_album'] . '</b></a> | ' . $lng_profile['image_delete'] . '</div>';
        //TODO: Сделать проверку, чтоб администрация не могла удалять фотки старших по должности
        if (isset($_POST['submit'])) {
            // Удаляем файлы картинок
            @unlink('../files/users/album/' . $user['user_id'] . '/' . $res['img_name']);
            @unlink('../files/users/album/' . $user['user_id'] . '/' . $res['tmb_name']);
            // Удаляем записи из таблиц
            mysql_query("DELETE FROM `cms_album_files` WHERE `id` = '$img'");
            mysql_query("DELETE FROM `cms_album_votes` WHERE `file_id` = '$img'");
            mysql_query("OPTIMIZE TABLE `cms_album_votes`");
            mysql_query("DELETE FROM `cms_album_comments` WHERE `sub_id` = '$img'");
            mysql_query("OPTIMIZE TABLE `cms_album_comments`");
            header('Location: album.php?act=show&al=' . $album . '&user=' . $user['user_id']);
        } else {
            echo '<div class="rmenu"><form action="album.php?act=image_delete&amp;img=' . $img . '&amp;user=' . $user['user_id'] . '" method="post">' .
                '<p>' . $lng_profile['image_delete_warning'] . '</p>' .
                '<p><input type="submit" name="submit" value="' . Vars::$LNG['delete'] . '"/></p>' .
                '</form></div>' .
                '<div class="phdr"><a href="album.php?act=show&amp;al=' . $album . 'user=' . $user['user_id'] . '">' . Vars::$LNG['cancel'] . '</a></div>';
        }
    } else {
        echo Functions::displayError(Vars::$LNG['error_wrong_data']);
    }
}
?>