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
$headmod = 'mainpage';
// Внимание! Если файл находится в корневой папке, нужно указать $rootpath = '';
$rootpath = '';

require('incfiles/core.php');
require('incfiles/head.php');
if (isset($_SESSION['ref']))
    unset($_SESSION['ref']);
if (isset($_GET['err']))
    $act = 404;
switch ($act) {
    case '404':
        /*
        -----------------------------------------------------------------
        Сообщение об ошибке 404
        -----------------------------------------------------------------
        */
        echo display_error($lng['error_404']);
        break;

    case 'users':
        /*
        -----------------------------------------------------------------
        Актив сайта
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . $lng['community'] . '</b></div>' .
            '<div class="gmenu"><form action="users/users_search.php" method="post">' .
            '<p><h3><img src="images/search.png" width="16" height="16" class="left" />&#160;' . $lng['search'] . '</h3>' .
            '<input type="text" name="search" value="' . checkout($search) . '" />' .
            '<input type="submit" value="' . $lng['search'] . '" name="submit" /><br />' .
            '<small>' . $lng['search_nick_help'] . '</small></p></form></div>' .
            '<div class="menu"><p><h3><img src="images/users.png" width="16" height="16" class="left" />&#160;' . $lng['users'] . '</h3><ul>' .
            '<li><a href="users/users.php">' . $lng['common_list'] . '</a> (' . stat_countusers() . ')</li>';
        $mon = date("m", $realtime);
        if (substr($mon, 0, 1) == 0) {
            $mon = str_replace("0", "", $mon);
        }
        $day = date("d", $realtime);
        if (substr($day, 0, 1) == 0) {
            $day = str_replace("0", "", $day);
        }
        $brth = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `dayb` = '$day' AND `monthb` = '$mon' AND `preg` = '1'"), 0);
        if ($brth)
            echo '<li><a href="users/brd.php">' . $lng['birthday_men'] . '</a> (' . $brth . ')</li>';
        echo '<li><a href="users/moders.php">' . $lng['administration'] . '</a></li>' .
            '<li><a href="users/users_top.php">' . $lng['users_top'] . '</a></li>' .
            '</ul></p></div>' .
            '<div class="phdr"><a href="index.php">' . $lng['back'] . '</a></div>';
        break;

    case 'info':
        /*
        -----------------------------------------------------------------
        Информационный блок
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . $lng['information'] . '</b></div>';
        echo '<div class="menu"><a href="pages/faq.php?act=smileys">' . $lng['smileys'] . '</a></div>';
        echo '<div class="menu"><a href="users/avatar.php">' . $lng['avatars'] . '</a></div>';
        echo '<div class="menu"><a href="pages/faq.php">' . $lng['help'] . ' (FAQ)</a></div>';
        echo '<div class="phdr"><a href="index.php">' . $lng['back'] . '</a></div>';
        break;

    case 'digest':
        /*
        -----------------------------------------------------------------
        Дайджест
        -----------------------------------------------------------------
        */
        if (!$user_id) {
            echo display_error($lng['access_guest_forbidden']);
            require_once('incfiles/end.php');
            exit;
        }
        echo '<div class="phdr"><b>' . $lng['digest'] . '</b></div>';
        echo '<div class="gmenu"><p>' . $lng['hi'] . ', <b>' . $login . '</b><br/>' . $lng['welcome_to'] . ' ' . $copyright . '!<br /><a href="index.php">' . $lng['enter_on_site'] . '</a></p></div>';
        // Поздравление с днем рождения
        if ($datauser['dayb'] == $day && $datauser['monthb'] == $mon) {
            echo '<div class="rmenu"><p>' . $lng['happy_birthday'] . '</p></div>';
        }
        // Дайджест Администратора
        if ($rights >= 1) {
            $newusers_total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `datereg` > '" . ($realtime - 86400) . "' AND `preg` = '1'"), 0);
            $reg_total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `preg` = 0"), 0);
            $ban_total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time`>'" . $realtime . "'"), 0);
            echo '<div class="menu"><p><h3>' . $lng['administrative_events'] . '</h3><ul>';
            if ($newusers_total > 0)
                echo '<li><a href="users/users.php">' . $lng['users_new'] . '</a> (' . $newusers_total . ')</li>';
            if ($reg_total > 0)
                echo '<li><a href="' . $admp . '/index.php?act=usr_reg">' . $lng['users_on_reg'] . '</a> (' . $reg_total . ')</li>';
            if ($ban_total > 0)
                echo '<li><a href="' . $admp . '/index.php?act=usr_ban">' . $lng['users_on_ban'] . '</a> (' . $ban_total . ')</li>';
            $total_libmod = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = 0"), 0);
            if ($total_libmod > 0)
                echo '<li><a href="library/index.php?act=moder">' . $lng['library_on_moderation'] . '</a> (' . $total_libmod . ')</li>';
            $total_admin = stat_guestbook(2);
            if ($total_admin > 0)
                echo '<li><a href="guestbook/index.php?act=ga&amp;do=set">' . $lng['admin_club'] . '</a> (' . $total_admin . ')</li>';
            if (!$newusers_total && !$reg_total && !$ban_total && !$total_libmod && !$total_admin)
                echo '<li>' . $lng['events_no_new'] . '</li>';
            echo '</ul></p></div>';
        }
        // Дайджест юзеров
        echo '<div class="menu"><p><h3>' . $lng['site_new'] . '</h3><ul>';
        $total_news = mysql_result(mysql_query("SELECT COUNT(*) FROM `news` WHERE `time` > " . ($realtime - 86400)), 0);
        if ($total_news > 0)
            echo '<li><a href="news/index.php">' . $lng['news'] . '</a> (' . $total_news . ')</li>';
        $total_forum = forum_new();
        if ($total_forum > 0)
            echo '<li><a href="forum/index.php?act=new">' . $lng['forum'] . '</a> (' . $total_forum . ')</li>';
        $total_guest = stat_guestbook(1);
        if ($total_guest > 0)
            echo '<li><a href="guestbook/index.php?act=ga">' . $lng['guestbook'] . '</a> (' . $total_guest . ')</li>';
        $total_gal = stat_gallery(1);
        if ($total_gal > 0)
            echo '<li><a href="gallery/index.php?act=new">' . $lng['gallery'] . '</a> (' . $total_gal . ')</li>';
        if ($set_karma['on']) {
            $total_karma = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `karma_user` = '$user_id' AND `time` > " . ($realtime - 86400)), 0);
            if ($total_karma > 0)
                echo '<li><a href="users/karma.php?act=new">' . $lng['new_responses'] . '</a> (' . $total_karma . ')</li>';
        }
        $old = $realtime - (3 * 24 * 3600);
        $total_lib = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = 1 AND `time` > " . $old), 0);
        if ($total_lib > 0)
            echo '<li><a href="library/index.php?act=new">' . $lng['library'] . '</a> (' . $total_lib . ')</li>';
        // Если нового нет, выводим сообщение
        if (!$total_news && !$total_forum && !$total_guest && !$total_gal && !$total_lib && !$total_karma)
            echo '<li>' . $lng['events_no_new'] . '</li>';
        // Дата последнего посещения
        $last = isset($_GET['last']) ? intval($_GET['last']) : $datauser['lastdate'];
        echo '</ul></p></div><div class="phdr">' . $lng['last_visit'] . ': ' . date("d.m.Y (H:i)", $last) . '</div>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Главное меню сайта
        -----------------------------------------------------------------
        */
        if (isset($_SESSION['ref']))
            unset($_SESSION['ref']);
        include 'pages/mainmenu.php';
}

require('incfiles/end.php');
?>