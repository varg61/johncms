<?php

/**
* @package     JohnCMS
* @link        http://johncms.com
* @copyright   Copyright (C) 2008-2011 JohnCMS Community
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      http://johncms.com/about
*/

defined('_IN_JOHNCMS') or die('Error: restricted access');
$textl = $lng_profile['my_office'];
require('../incfiles/head.php');

/*
-----------------------------------------------------------------
Проверяем права доступа
-----------------------------------------------------------------
*/
if ($user['id'] != $user_id) {
    echo functions::display_error($lng['access_forbidden']);
    require('../incfiles/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Личный кабинет пользователя
-----------------------------------------------------------------
*/
$total_photo = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '$user_id'"), 0);
echo '<div class="phdr"><b>' . $lng_profile['my_office'] . '</b></div>' .
     '<div class="list2"><p>' .
     '<div>' . functions::get_image('contacts.png') . '&#160;<a href="profile.php">' . $lng_profile['my_profile'] . '</a></div>' .
     '<div>' . functions::get_image('rating.png') . '&#160;<a href="profile.php?act=stat">' . $lng['statistics'] . '</a></div>' .
     '</p><p>' .
     '<div>' . functions::get_image('album_4.png') . '&#160;<a href="album.php?act=list">' . $lng['photo_album'] . '</a>&#160;(' . $total_photo . ')</div>' .
     '<div>' . functions::get_image('comments.png') . '&#160;<a href="profile.php?act=guestbook">' . $lng['guestbook'] . '</a>&#160;(' . $user['comm_count'] . ')</div>' .
     ($rights >= 1 ? '</p><p><div>' . functions::get_image('blocked.png') . '&#160;<a href="../guestbook/index.php?act=ga&amp;do=set">' . $lng['admin_club'] . '</a> (<span class="red">' . counters::guestbook(2) . '</span>)</div>' : '') .
     '</p></div><div class="menu"><p>' .
     '<h3>' . functions::get_image('mail-inbox.png') . '&#160;' . $lng_profile['my_mail'] . '</h3><ul>' .
     '<li><a href="">' . $lng['mail_new'] . '</a>&#160;(x)</li>' .
     '<li><a href="pm.php">' . $lng_profile['all_mail'] . '</a></li>' .
     (!isset($ban['1']) && !isset($ban['3']) ? '<p><form action="pm.php?act=write" method="post"><input type="submit" value=" ' . $lng['write'] . ' " /></form></p>' : '') .
     '</ul><h3>' . functions::get_image('contacts.png') . '&#160;' . $lng['contacts'] . '</h3><ul>' .
     '<li><a href="cont.php">' . $lng['contacts'] . '</a>&#160;()</li>' .
     '<li><a href="ignor.php">' . $lng['blocking'] . '</a>&#160;()</li>' .
     '</ul></p></div>' .
     '<div class="bmenu"><p><h3>' . functions::get_image('settings.png') . '&#160;' . $lng_profile['my_settings'] . '</h3><ul>' .
     '<li><a href="profile.php?act=settings">' . $lng['system_settings'] . '</a></li>' .
     '<li><a href="profile.php?act=edit">' . $lng_profile['profile_edit'] . '</a></li>' .
     '<li><a href="profile.php?act=password">' . $lng['change_password'] . '</a></li>' .
     ($rights >= 1 ? '<li><span class="red"><a href="../' . $set['admp'] . '/index.php"><b>' . $lng['admin_panel'] . '</b></a></span></li>' : '') .
     '</ul></p></div>';