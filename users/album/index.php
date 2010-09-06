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

define('_IN_JOHNCMS', 1);
$rootpath = '../../';
require('../../incfiles/core.php');
$lng_profile = load_lng('profile');
require('../../incfiles/head.php');

//TODO: Разобраться с правами доступа к функциям!!!
$max_album = 10;
$max_photo = 200;
$al = isset($_REQUEST['al']) ? abs(intval($_REQUEST['al'])) : NULL;
$img = isset($_REQUEST['img']) ? abs(intval($_REQUEST['img'])) : NULL;

/*
-----------------------------------------------------------------
Получаем данные пользователя
-----------------------------------------------------------------
*/
$user = get_user($id);
if (!$user) {
    echo display_error($lng['user_does_not_exist']);
    require('../../incfiles/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$array = array (
    'album',
    'album_up',
    'album_down',
    'album_delete',
    'album_edit',
    'catalogue',
    'image_delete',
    'image_edit',
    'image_move',
    'image_upload',
    'users'
);
if (in_array($act, $array) && file_exists($act . '.php')) {
    require_once($act . '.php');
} else {
    $albumcount = mysql_result(mysql_query("SELECT COUNT(DISTINCT `album_id`) FROM `cms_album_files`"), 0);
    $newcount = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `time` > '" . ($realtime - 86400) . "'"), 0);
    echo '<div class="phdr"><b>' . $lng['photo_albums'] . '</b></div>' .
        '<div class="gmenu"><p>' .
        '<img src="' . $home . '/images/users.png" width="16" height="16"/>&#160;<a href="">' . $lng_profile['new_photo'] . '</a> (' . $newcount . ')<br />' .
        '<img src="' . $home . '/images/guestbook.gif" width="16" height="16"/>&#160;<a href="">' . $lng_profile['new_comments'] . '</a>' .
        '</p></div>' .
        '<div class="menu">' .
        '<p><h3><img src="' . $home . '/images/users.png" width="16" height="16" class="left" />&#160;' . $lng['albums'] . '</h3><ul>' .
        '<li><a href="index.php?act=users">' . $lng_profile['album_list'] . '</a> (' . $albumcount . ')</li>' .
        '</ul></p>' .
        '<p><h3><img src="' . $home . '/images/album-4.gif" width="16" height="16" class="left" />&#160;' . $lng_profile['photos'] . '</h3><ul>' .
        '<li><a href="">' . $lng_profile['top_votes'] . '</a></li>' .
        '<li><a href="">' . $lng_profile['top_views'] . '</a></li>' .
        '<li><a href="">' . $lng_profile['top_comments'] . '</a></li>' .
        '<li><a href="">' . $lng_profile['top_trash'] . '</a></li>' .
        '</ul></p>' .
        '</div>' .
        '<div class="phdr"><a href="../../index.php?act=users">' . $lng['users'] . '</a></div>';
}
require('../../incfiles/end.php');
?>