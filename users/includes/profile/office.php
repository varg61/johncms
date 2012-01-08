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

$textl = $lng_profile['my_office'];
require_once('../includes/head.php');

/*
-----------------------------------------------------------------
Проверяем права доступа
-----------------------------------------------------------------
*/
if ($user['user_id'] != Vars::$USER_ID) {
    echo Functions::displayError(Vars::$LNG['access_forbidden']);
    require_once('../includes/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Личный кабинет пользователя
-----------------------------------------------------------------
*/
//TODO: Добавить счетчик Гостевой
$total_photo = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = " . Vars::$USER_ID), 0);
echo '<div class="phdr"><b>' . $lng_profile['my_office'] . '</b></div>' .
     '<div class="list2"><p>' .
     '<div>' . Functions::getImage('contacts.png') . '&#160;<a href="profile.php">' . $lng_profile['my_profile'] . '</a></div>' .
     '<div>' . Functions::getImage('rating.png') . '&#160;<a href="profile.php?act=stat">' . Vars::$LNG['statistics'] . '</a></div>' .
     '</p><p>' .
     '<div>' . Functions::getImage('album_4.png') . '&#160;<a href="album.php?act=list">' . Vars::$LNG['photo_album'] . '</a>&#160;(' . $total_photo . ')</div>' .
     '<div>' . Functions::getImage('comments.png') . '&#160;<a href="profile.php?act=guestbook">' . Vars::$LNG['guestbook'] . '</a>&#160;(' . $user['comm_count'] . ')</div>' .
     (Vars::$USER_RIGHTS >= 1 ? '</p><p><div>' . Functions::getImage('blocked.png') . '&#160;<a href="../guestbook/index.php?act=ga&amp;do=set">' . Vars::$LNG['admin_club'] . '</a> (<span class="red">()</span>)</div>' : '') .
     '</p></div><div class="menu"><p>' .
     '<h3>' . Functions::getImage('mail-inbox.png') . '&#160;' . $lng_profile['my_mail'] . '</h3><ul>' .
     '<li><a href="">' . Vars::$LNG['mail_new'] . '</a>&#160;(x)</li>' .
     '<li><a href="pm.php">' . $lng_profile['all_mail'] . '</a></li>' .
     (!isset(Vars::$USER_BAN['1']) && !isset(Vars::$USER_BAN['3']) ? '<p><form action="pm.php?act=write" method="post"><input type="submit" value=" ' . Vars::$LNG['write'] . ' " /></form></p>' : '') .
     '</ul><h3>' . Functions::getImage('contacts.png') . '&#160;' . Vars::$LNG['contacts'] . '</h3><ul>' .
     '<li><a href="cont.php">' . Vars::$LNG['contacts'] . '</a>&#160;()</li>' .
     '<li><a href="ignor.php">' . Vars::$LNG['blocking'] . '</a>&#160;()</li>' .
     '</ul></p></div>' .
     '<div class="bmenu"><p><h3>' . Functions::getImage('settings.png') . '&#160;' . $lng_profile['my_settings'] . '</h3><ul>' .
     '<li><a href="profile.php?act=settings">' . Vars::$LNG['system_settings'] . '</a></li>' .
     '<li><a href="profile.php?act=edit">' . $lng_profile['profile_edit'] . '</a></li>' .
     '<li><a href="profile.php?act=password">' . Vars::$LNG['change_password'] . '</a></li>' .
     (Vars::$USER_RIGHTS >= 1 ? '<li><span class="red"><a href="../' . Vars::$SYSTEM_SET['admp'] . '/index.php"><b>' . Vars::$LNG['admin_panel'] . '</b></a></span></li>' : '') .
     '</ul></p></div>';