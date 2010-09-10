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
$headmod = 'album';
require('../../incfiles/head.php');

//TODO: Разобраться с правами доступа к функциям!!!
$max_album = 10;
$max_photo = 200;
$al = isset($_REQUEST['al']) ? abs(intval($_REQUEST['al'])) : NULL;
$img = isset($_REQUEST['img']) ? abs(intval($_REQUEST['img'])) : NULL;

/*
-----------------------------------------------------------------
Закрываем от неавторизованных юзеров
-----------------------------------------------------------------
*/
if(!$user_id){
    echo display_error($lng['access_guest_forbidden']);
    require('../../incfiles/end.php');
    exit;
}

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
Функция голосований за фотографии
-----------------------------------------------------------------
*/
function vote_photo($arg = null) {
    global $lng, $datauser, $user, $user_id, $ban;
    if ($arg) {
        $rating = $arg['vote_plus'] - $arg['vote_minus'];
        if ($rating > 0)
            $color = 'C0FFC0';
        elseif ($rating < 0)
            $color = 'F196A8';
        else
            $color = 'CCC';
        echo '<div class="gray">' . $lng['rating'] . ': <span style="color:#000;background-color:#' . $color . '">&#160;&#160;<big><b>' . $rating . '</b></big>&#160;&#160;</span> ' .
            '(' . $lng['vote_against'] . ': ' . $arg['vote_minus'] . ', ' . $lng['vote_for'] . ': ' . $arg['vote_plus'] . ')';
        if ($user_id != $arg['user_id'] && !$ban && $datauser['postforum'] > 10 && $datauser['total_on_site'] > 1200) {
            // Проверяем, имеет ли юзер право голоса
            $req = mysql_query("SELECT * FROM `cms_album_votes` WHERE `user_id` = '$user_id' AND `file_id` = '" . $arg['id'] . "' LIMIT 1");
            if(!mysql_num_rows($req))
                echo '<br />' . $lng['vote'] . ': <a href="index.php?act=vote&amp;mod=minus&amp;img=' . $arg['id'] . '">&lt;&lt; -1</a> | <a href="index.php?act=vote&amp;mod=plus&amp;img=' . $arg['id'] . '">+1 &gt;&gt;</a>';
        }
        echo '</div>';
    } else {
        return false;
    }
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
    'new',
    'top_trash',
    'top_votes',
    'users',
    'vote'
);
if (in_array($act, $array) && file_exists($act . '.php')) {
    require_once($act . '.php');
} else {
    $albumcount = mysql_result(mysql_query("SELECT COUNT(DISTINCT `user_id`) FROM `cms_album_files`"), 0);
    $newcount = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `time` > '" . ($realtime - 86400) . "' AND `access` > '1'"), 0);
    echo '<div class="phdr"><b>' . $lng['photo_albums'] . '</b></div>' .
        '<div class="gmenu"><p>' .
        '<img src="' . $home . '/images/users.png" width="16" height="16"/>&#160;<a href="index.php?act=new">' . $lng_profile['new_photo'] . '</a> (' . $newcount . ')<br />' .
        '<img src="' . $home . '/images/guestbook.gif" width="16" height="16"/>&#160;' . $lng_profile['new_comments'] . '' .
        '</p></div>' .
        '<div class="menu">' .
        '<p><h3><img src="' . $home . '/images/users.png" width="16" height="16" class="left" />&#160;' . $lng['albums'] . '</h3><ul>' .
        '<li><a href="index.php?act=users">' . $lng_profile['album_list'] . '</a> (' . $albumcount . ')</li>' .
        '</ul></p>' .
        '<p><h3><img src="' . $home . '/images/album-4.gif" width="16" height="16" class="left" />&#160;' . $lng_profile['photos'] . '</h3><ul>' .
        '<li><a href="index.php?act=top_votes">' . $lng_profile['top_votes'] . '</a></li>' .
        '<li>' . $lng_profile['top_views'] . '</li>' .
        '<li>' . $lng_profile['top_comments'] . '</li>' .
        '<li><a href="index.php?act=top_trash">' . $lng_profile['top_trash'] . '</a></li>' .
        '</ul></p>' .
        '</div>' .
        '<div class="phdr"><a href="../../index.php?act=users">' . $lng['users'] . '</a></div>';
}
require('../../incfiles/end.php');
?>