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
    'image_upload'
);
if (in_array($act, $array) && file_exists($act . '.php')) {
    require_once($act . '.php');
} else {
    echo '<div class="phdr"><b>Личные альбомы</b></div>' .
        '<div class="list2"><p>' .
        '<img src="' . $home . '/images/users.png" width="16" height="16"/>&#160;<a href="">Новые фотографии</a> (0)<br />' .
        '<img src="' . $home . '/images/guestbook.gif" width="16" height="16"/>&#160;<a href="">Последние комментарии</a>' .
        '</p></div>' .
        '<div class="menu">' .
        '<p><h3><img src="' . $home . '/images/users.png" width="16" height="16" class="left" />&#160;Альбомы</h3><ul>' .
        '<li><a href="">Список альбомов</a> (0)</li>' .
        '</ul></p>' .
        '<p><h3><img src="' . $home . '/images/album-4.gif" width="16" height="16" class="left" />&#160;Фотографии</h3><ul>' .
        '<li><a href="">ТОП голосов</a></li>' .
        '<li><a href="">ТОП комментариев</a></li>' .
        '<li><a href="">ТОП просмотров</a></li>' .
        '<li><a href="">КГ/АМ</a></li>' .
        '</ul></p>' .
        '</div>' .
        '<div class="phdr"><a href="../index.php?act=users">' . $lng['users'] . '</a></div>';
}
require('../../incfiles/end.php');
?>