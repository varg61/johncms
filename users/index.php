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

$headmod = 'users';
require_once('../includes/core.php');

/*
-----------------------------------------------------------------
Закрываем от неавторизованных юзеров
-----------------------------------------------------------------
*/
if (!Vars::$USER_ID && !Vars::$SYSTEM_SET['active']) {
    require_once('../includes/head.php');
    echo Functions::displayError(Vars::$LNG['access_guest_forbidden']);
    require_once('../includes/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$array = array(
    'admlist' => 'includes',
    'birth' => 'includes',
    'online' => 'includes',
    'top' => 'includes'
);
$path = !empty($array[Vars::$ACT]) ? $array[Vars::$ACT] . '/' : '';
if (array_key_exists(Vars::$ACT, $array) && file_exists($path . Vars::$ACT . '.php')) {
    require_once($path . Vars::$ACT . '.php');
} else {
    /*
    -----------------------------------------------------------------
    Актив сайта
    -----------------------------------------------------------------
    */
    $textl = Vars::$LNG['community'];
    require_once('../includes/head.php');
    //TODO: Доработать!
    //$brth = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `dayb` = '" . date('j', time()) . "' AND `monthb` = '" . date('n', time()) . "' AND `preg` = '1'"), 0);
    $count_adm = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_user` WHERE `rights` > 0"), 0);
    echo'<div class="phdr"><b>' . Vars::$LNG['community'] . '</b></div>' .
        '<div class="gmenu"><form action="users_search.php" method="post">' .
        '<p><h3>' . Vars::$LNG['search'] . '</h3>' .
        '<input type="text" name="search"/>' .
        '<input type="submit" value="' . Vars::$LNG['search'] . '" name="submit" /><br />' .
        '<small>' . Vars::$LNG['search_nick_help'] . '</small></p></form></div>' .
        '<div class="menu"><p>' .
        Functions::getImage('users.png') . '&#160;<a href="users_list.php">' . Vars::$LNG['users'] . '</a> (' . Counters::usersCount() . ')<br />' .
        ($brth ? '<br />' . Functions::getImage('award.png') . '&#160;<a href="index.php?act=birth">' . Vars::$LNG['birthday_men'] . '</a> (' . $brth . ')' : '') .
        '</p><p>' . Functions::getImage('album_4.png') . '&#160;<a href="album.php">' . Vars::$LNG['photo_albums'] . '</a> (' . Counters::albumCount() . ')</p>' .
        '<p>' . Functions::getImage('rating.png') . '&#160;<a href="index.php?act=top">' . Vars::$LNG['users_top'] . '</a></p>' .
        '</div>' .
        '<div class="phdr"><a href="index.php">' . Vars::$LNG['back'] . '</a></div>';
}

require_once('../includes/end.php');