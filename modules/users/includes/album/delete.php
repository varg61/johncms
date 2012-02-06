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

/*
-----------------------------------------------------------------
Удалить альбом
-----------------------------------------------------------------
*/
if ($al && $user['user_id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 6) {
    $req_a = mysql_query("SELECT * FROM `cms_album_cat` WHERE `id` = '$al' AND `user_id` = '" . $user['user_id'] . "' LIMIT 1");
    if (mysql_num_rows($req_a)) {
        $res_a = mysql_fetch_assoc($req_a);
        echo '<div class="phdr"><a href="album.php?act=list&amp;user=' . $user['user_id'] . '"><b>' . Vars::$LNG['photo_album'] . '</b></a> | ' . Vars::$LNG['delete'] . '</div>';
        if (isset($_POST['submit'])) {
            $req = mysql_query("SELECT * FROM `cms_album_files` WHERE `album_id` = '" . $res_a['id'] . "'");
            while ($res = mysql_fetch_assoc($req)) {
                // Удаляем файлы фотографий
                @unlink('../files/users/album/' . $user['user_id'] . '/' . $res['img_name']);
                @unlink('../files/users/album/' . $user['user_id'] . '/' . $res['tmb_name']);
                // Удаляем записи из таблицы скачиваний
                mysql_query("DELETE FROM `cms_album_downloads` WHERE `file_id` = '" . $res['id'] . "'");
                // Удаляем записи из таблицы голосований
                mysql_query("DELETE FROM `cms_album_votes` WHERE `file_id` = '" . $res['id'] . "'");
                // Удаляем комментарии
                mysql_query("DELETE FROM `cms_album_comments` WHERE `sub_id` = '" . $res['id'] . "'");
            }
            // Удаляем записи из таблиц
            mysql_query("DELETE FROM `cms_album_files` WHERE `album_id` = '" . $res_a['id'] . "'");
            mysql_query("DELETE FROM `cms_album_cat` WHERE `id` = '" . $res_a['id'] . "'");
            mysql_query("OPTIMIZE TABLE `cms_album_cat`, `cms_album_downloads`, `cms_album_votes`, `cms_album_files`, `cms_album_comments`");
            echo '<div class="menu"><p>' . $lng_profile['album_deleted'] . '<br />' .
                '<a href="album.php?act=list&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['continue'] . '</a></p></div>';
        } else {
            echo '<div class="rmenu"><form action="album.php?act=delete&amp;al=' . $al . '&amp;user=' . $user['user_id'] . '" method="post">' .
                '<p>' . $lng_profile['album'] . ': <b>' . Validate::filterString($res_a['name']) . '</b></p>' .
                '<p>' . $lng_profile['album_delete_warning'] . '</p>' .
                '<p><input type="submit" name="submit" value="' . Vars::$LNG['delete'] . '"/></p>' .
                '</form></div>' .
                '<div class="phdr"><a href="album.php?act=list&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['cancel'] . '</a></div>';
        }
    } else {
        echo Functions::displayError(Vars::$LNG['error_wrong_data']);
    }
}