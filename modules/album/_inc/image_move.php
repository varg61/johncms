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
Перемещение картинки в другой альбом
-----------------------------------------------------------------
*/
if ($img && $user['user_id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 6) {
    $req = mysql_query("SELECT * FROM `cms_album_files` WHERE `id` = '$img' AND `user_id` = '" . $user['user_id'] . "'");
    if (mysql_num_rows($req)) {
        $image = mysql_fetch_assoc($req);
        echo '<div class="phdr"><a href="album.php?act=show&amp;al=' . $image['album_id'] . '&amp;user=' . $user['user_id'] . '"><b>' . Vars::$LNG['photo_album'] . '</b></a> | ' . $lng['image_move'] . '</div>';
        if (isset($_POST['submit'])) {
            $req_a = mysql_query("SELECT * FROM `cms_album_cat` WHERE `id` = '$al' AND `user_id` = '" . $user['user_id'] . "'");
            if (mysql_num_rows($req_a)) {
                $res_a = mysql_fetch_assoc($req_a);
                mysql_query("UPDATE `cms_album_files` SET
                    `album_id` = '$al',
                    `access` = '" . $res_a['access'] . "'
                    WHERE `id` = '$img'
                ");
                echo '<div class="gmenu"><p>' . $lng['image_moved'] . '<br />' .
                     '<a href="album.php?act=show&amp;al=' . $al . '&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['continue'] . '</a></p></div>';
            } else {
                echo Functions::displayError(Vars::$LNG['error_wrong_data']);
            }
        } else {
            $req = mysql_query("SELECT * FROM `cms_album_cat` WHERE `user_id` = '" . $user['user_id'] . "' AND `id` != '" . $image['album_id'] . "' ORDER BY `sort` ASC");
            if (mysql_num_rows($req)) {
                echo '<form action="album.php?act=image_move&amp;img=' . $img . '&amp;user=' . $user['user_id'] . '" method="post">' .
                     '<div class="menu"><p><h3>' . $lng['album_select'] . '</h3>' .
                     '<select name="al">';
                while ($res = mysql_fetch_assoc($req)) {
                    echo '<option value="' . $res['id'] . '">' . Validate::filterString($res['name']) . '</option>';
                }
                echo '</select></p>' .
                     '<p><input type="submit" name="submit" value="' . Vars::$LNG['move'] . '"/></p>' .
                     '</div></form>' .
                     '<div class="phdr"><a href="album.php?act=show&amp;al=' . $image['album_id'] . '&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['cancel'] . '</a></div>';
            } else {
                echo Functions::displayError($lng['image_move_error'], '<a href="album.php?act=list&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['continue'] . '</a>');
            }
        }
    } else {
        echo Functions::displayError(Vars::$LNG['error_wrong_data']);
    }
}