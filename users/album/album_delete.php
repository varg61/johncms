<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Error: restricted access');

/*
-----------------------------------------------------------------
Удалить альбом
-----------------------------------------------------------------
*/
if ($al && $user['id'] == $user_id || $rights >= 6) {
    echo '<div class="phdr"><a href="index.php?id=' . $user['id'] . '"><b>' . $lng['photo_album'] . '</b></a> | ' . $lng['delete'] . '</div>';
    if (isset($_POST['submit'])) {
        // Удаляем файлы фотографий
        $req = mysql_query("SELECT * FROM `cms_album_files` WHERE `user_id` = '" . $user['id'] . "' AND `album_id` = '$al'");
        while ($res = mysql_fetch_assoc($req)) {
            @unlink('../../files/users/album/' . $user['id'] . '/' . $res['img_name']);
            @unlink('../../files/users/album/' . $user['id'] . '/' . $res['tmb_name']);
        }
        // Удаляем записи из таблицы файлов
        mysql_query("DELETE FROM `cms_album_files` WHERE `user_id` = '" . $user['id'] . "' AND `album_id` = '$al'");
        // Удаляем альбом
        mysql_query("DELETE FROM `cms_album_cat` WHERE `id` = '$al' AND `user_id` = '" . $user['id'] . "' LIMIT 1");
        echo '<div class="menu"><p>' . $lng_profile['album_deleted'] . '<br /><a href="index.php?act=catalogue&amp;id=' . $user['id'] . '">' . $lng['continue'] . '</a></p></div>';
    } else {
        echo '<div class="rmenu"><form action="index.php?act=album_delete&amp;al=' . $al . '&amp;id=' . $user['id'] . '" method="post">' .
            '<p>' . $lng_profile['album_delete_warning'] . '</p>' .
            '<p><input type="submit" name="submit" value="' . $lng['delete'] . '"/></p>' .
            '</form></div>' .
            '<div class="phdr"><a href="index.php?act=catalogue&amp;id=' . $user['id'] . '">' . $lng['cancel'] . '</a></div>';
    }
}
?>