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
$headmod = 'users';
require('../incfiles/core.php');

$array = array (
    'admlist' => 'modules',
    'birth' => 'modules',
    'online' => 'modules',
    'search' => 'modules',
    'top' => 'modules',
    'userlist' => 'modules'
);
$path = !empty($array[$act]) ? $array[$act] . '/' : '';
if (array_key_exists($act, $array) && file_exists($path . $act . '.php')) {
    require_once($path . $act . '.php');
} else {
    /*
    -----------------------------------------------------------------
    Актив сайта
    -----------------------------------------------------------------
    */
    $textl = $lng['community'];
    require('../incfiles/head.php');
    $mon = date("m", $realtime);
    if (substr($mon, 0, 1) == 0) {
        $mon = str_replace("0", "", $mon);
    }
    $day = date("d", $realtime);
    if (substr($day, 0, 1) == 0) {
        $day = str_replace("0", "", $day);
    }
    $brth = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `dayb` = '$day' AND `monthb` = '$mon' AND `preg` = '1'"), 0);
    $count_adm = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `rights` > 0"), 0);
    echo '<div class="phdr"><b>' . $lng['community'] . '</b></div>' .
        '<div class="gmenu"><form action="index.php?act=search" method="post">' .
        '<p><h3><img src="../images/search.png" width="16" height="16" class="left" />&#160;' . $lng['search'] . '</h3>' .
        '<input type="text" name="search"/>' .
        '<input type="submit" value="' . $lng['search'] . '" name="submit" /><br />' .
        '<small>' . $lng['search_nick_help'] . '</small></p></form></div>' .
        '<div class="menu"><p>' .
        '<img src="../images/contacts.png" width="16" height="16" />&#160;<a href="index.php?act=userlist">' . $lng['users'] . '</a> (' . stat_countusers() . ')<br />' .
        '<img src="../images/users.png" width="16" height="16" />&#160;<a href="index.php?act=admlist">' . $lng['administration'] . '</a> (' . $count_adm . ')' .
        ($brth ? '<br /><img src="../images/award.png" width="16" height="16" />&#160;<a href="index.php?act=birth">' . $lng['birthday_men'] . '</a> (' . $brth . ')' : '') .
        '</p><p><img src="../images/photo.gif" width="16" height="16" />&#160;<a href="album/index.php">' . $lng['photo_albums'] . '</a> (' . count_photo() . ')</p>' .
        '<p><img src="../images/rate.gif" width="16" height="16" />&#160;<a href="index.php?act=top">' . $lng['users_top'] . '</a></p>' .
        '</div>' .
        '<div class="phdr"><a href="index.php">' . $lng['back'] . '</a></div>';
}

require_once('../incfiles/end.php');
?>