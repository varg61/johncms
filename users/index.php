<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require_once('../includes/core.php');
$textl = Vars::$LNG['community'];
require_once(SYSPATH . 'head.php');

/*
-----------------------------------------------------------------
Закрываем от неавторизованных юзеров
-----------------------------------------------------------------
*/
if (!Vars::$USER_ID && !Vars::$SYSTEM_SET['active']) {
    echo Functions::displayError(Vars::$LNG['access_guest_forbidden']);
    require_once(SYSPATH . 'end.php');
    exit;
}

/*
-----------------------------------------------------------------
Актив сайта
-----------------------------------------------------------------
*/
$count = new Counters();
$count_adm = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `rights` > 0"), 0);
echo'<div class="phdr"><b>' . Vars::$LNG['community'] . '</b></div>' .
    '<div class="gmenu"><form action="search.php" method="post">' .
    '<p><h3>' . Vars::$LNG['search'] . '</h3>' .
    '<input type="text" name="search"/>' .
    '<input type="submit" value="' . Vars::$LNG['search'] . '" name="submit" /><br />' .
    '<small>' . Vars::$LNG['search_nick_help'] . '</small></p></form></div>' .
    '<div class="menu"><p>' .
    Functions::getImage('users.png') . '&#160;<a href="userlist.php">' . Vars::$LNG['users'] . '</a> (' . $count->users . ')<br />' .
    //TODO: Доработать дни рожденья!
    '</p><p>' . Functions::getImage('album_4.png') . '&#160;<a href="album.php">' . Vars::$LNG['photo_albums'] . '</a> (' . $count->album . ')</p>' .
    '<p>' . Functions::getImage('rating.png') . '&#160;<a href="index.php?act=top">' . Vars::$LNG['users_top'] . '</a></p>' .
    '</div>' .
    '<div class="phdr"><a href="index.php">' . Vars::$LNG['back'] . '</a></div>';

require_once(SYSPATH . 'end.php');