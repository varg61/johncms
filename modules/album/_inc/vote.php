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
Голосуем за фотографию
-----------------------------------------------------------------
*/
if (!$img) {
    echo Functions::displayError(lng('error_wrong_data'));
    exit;
}
$check = mysql_query("SELECT * FROM `cms_album_votes` WHERE `user_id` = " . Vars::$USER_ID . " AND `file_id` = '$img' LIMIT 1");
if (mysql_num_rows($check)) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
$req = mysql_query("SELECT * FROM `cms_album_files` WHERE `id` = '$img' AND `user_id` != " . Vars::$USER_ID);
if (mysql_num_rows($req)) {
    $res = mysql_fetch_assoc($req);
    switch (Vars::$MOD) {
        case 'plus':
            /*
            -----------------------------------------------------------------
            Отдаем положительный голос
            -----------------------------------------------------------------
            */
            mysql_query("INSERT INTO `cms_album_votes` SET
                `user_id` = '" . Vars::$USER_ID . "',
                `file_id` = '$img',
                `vote` = '1'
            ");
            mysql_query("UPDATE `cms_album_files` SET `vote_plus` = '" . ($res['vote_plus'] + 1) . "' WHERE `id` = '$img'");
            break;

        case 'minus':
            /*
            -----------------------------------------------------------------
            Отдаем отрицательный голос
            -----------------------------------------------------------------
            */
            mysql_query("INSERT INTO `cms_album_votes` SET
                `user_id` = '" . Vars::$USER_ID . "',
                `file_id` = '$img',
                `vote` = '-1'
            ");
            mysql_query("UPDATE `cms_album_files` SET `vote_minus` = '" . ($res['vote_minus'] + 1) . "' WHERE `id` = '$img'");
            break;
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    echo Functions::displayError(lng('error_wrong_data'));
}