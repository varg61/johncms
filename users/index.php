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

/*
-----------------------------------------------------------------
Закрываем от неавторизованных юзеров
-----------------------------------------------------------------
*/
if (!Vars::$USER_ID && !Vars::$SYSTEM_SET['active']) {
    echo Functions::displayError(Vars::$LNG['access_guest_forbidden']);
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
    '<h3>' . Functions::getImage('users.png') . '&#160;' . Vars::$LNG['community'] . ' <span class="green">(' . $count->users . ')</span></h3>' .
    '<ul>' .
    '<li><a href="userlist.php">' . Vars::$LNG['users'] . '</a></li>' .
    '<li><a href="userlist.php?act=adm">' . Vars::$LNG['administration'] . '</a></li>' .
    '</ul>' .
    '</p><p>' .
    //TODO: Доработать дни рожденья!
    '<h3>' . Functions::getImage('rating.png') . '&#160;' . Vars::$LNG['users_top'] . '</h3>' .
    '<ul>' .
    '<li><a href="top.php">' . Vars::$LNG['forum'] . '</a></li>' .
    '<li><a href="top.php">' . Vars::$LNG['comments'] . '</a></li>' .
    '<li><a href="top.php">' . Vars::$LNG['karma'] . '</a></li>' .
    '</ul>' .
    '</p></div>' .
    '<div class="phdr"><a href="index.php">' . Vars::$LNG['back'] . '</a></div>';