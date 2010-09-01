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
Удалить картинку
-----------------------------------------------------------------
*/
if ($img && $user['id'] == $user_id || $rights >= 6) {
    echo '<div class="phdr"><a href="index.php?id=' . $user['id'] . '"><b>' . $lng['photo_album'] . '</b></a> | ' . $lng_profile['image_delete'] . '</div>';
    $req = mysql_query("SELECT * FROM `cms_album_files` WHERE `id` = '$img' AND `user_id` = '" . $user['id'] . "' LIMIT 1");
    if (mysql_num_rows($req)) {
        $res = mysql_fetch_assoc($req);
        $album = $res['album_id'];
        // Сделать проверку, чтоб администрация не могла удалять фотки старших по должности
        if (isset($_POST['submit'])) {
            @unlink('../../files/users/album/' . $user['id'] . '/' . $res['img_name']);
            @unlink('../../files/users/album/' . $user['id'] . '/' . $res['tmb_name']);
            mysql_query("DELETE FROM `cms_album_files` WHERE `id` = '$img' AND `user_id` = '" . $user['id'] . "' LIMIT 1");
            header('Location: index.php?act=album&al=' . $album . '&id=' . $user['id']);
        } else {
            echo '<div class="rmenu"><form action="index.php?act=image_delete&amp;img=' . $img . '&amp;id=' . $user['id'] . '" method="post">' .
                '<p>' . $lng_profile['image_delete_warning'] . '</p>' .
                '<p><input type="submit" name="submit" value="' . $lng['delete'] . '"/></p>' .
                '</form></div>' .
                '<div class="phdr"><a href="index.php?act=album&amp;al=' . $album . 'id=' . $user['id'] . '">' . $lng['cancel'] . '</a></div>';
        }
    } else {
        echo display_error($lng['error_wrong_data']);
    }
}
?>