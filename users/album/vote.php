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
Голосуем за фотографию
-----------------------------------------------------------------
*/
if (!$img) {
    echo display_error($lng['error_wrong_data']);
    require('../../incfiles/end.php');
    exit;
}
$req = mysql_query("SELECT * FROM `cms_album_files` WHERE `id` = '$img' AND `user_id` != '$user_id' LIMIT 1");
if (mysql_num_rows($req)) {
    $res = mysql_fetch_assoc($req);
    switch ($mod) {
        case 'plus':
            /*
            -----------------------------------------------------------------
            Отдаем положительный голос
            -----------------------------------------------------------------
            */
            mysql_query("INSERT INTO `cms_album_votes` SET
                `user_id` = '$user_id',
                `file_id` = '$img',
                `vote` = '1'
            ");
            mysql_query("UPDATE `cms_album_files` SET `vote_plus` = '" . ($res['vote_plus'] + 1) . "' WHERE `id` = '$img' LIMIT 1");
            break;

        case 'minus':
            /*
            -----------------------------------------------------------------
            Отдаем отрицательный голос
            -----------------------------------------------------------------
            */
            mysql_query("INSERT INTO `cms_album_votes` SET
                `user_id` = '$user_id',
                `file_id` = '$img',
                `vote` = '-1'
            ");
            mysql_query("UPDATE `cms_album_files` SET `vote_minus` = '" . ($res['vote_minus'] + 1) . "' WHERE `id` = '$img' LIMIT 1");
            break;
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    echo display_error($lng['error_wrong_data']);
}
?>