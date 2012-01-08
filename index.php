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

$headmod = 'mainpage';
require_once('includes/core.php');
require_once('includes/head.php');

if (isset($_SESSION['ref']))
    unset($_SESSION['ref']);
if (isset($_GET['err']))
    Vars::$ACT = 404;

switch (Vars::$ACT) {
    case '404':
        /*
        -----------------------------------------------------------------
        Сообщение об ошибке 404
        -----------------------------------------------------------------
        */
        echo Functions::displayError(Vars::$LNG['error_404']);
        break;

    case 'digest':
        /*
        -----------------------------------------------------------------
        Дайджест
        -----------------------------------------------------------------
        */
        if (!Vars::$USER_ID) {
            echo Functions::displayError(Vars::$LNG['access_guest_forbidden']);
            require_once('includes/end.php');
            exit;
        }
        echo '<div class="phdr"><b>' . Vars::$LNG['digest'] . '</b></div>';
        echo '<div class="gmenu"><p>' . Vars::$LNG['hi'] . ', <b>' . Vars::$USER_DATA['nickname'] . '</b><br/>' . Vars::$LNG['welcome_to'] . ' ' . Vars::$SYSTEM_SET['copyright'] . '!<br /><a href="index.php">' . Vars::$LNG['enter_on_site'] . '</a></p></div>';
        // Поздравление с днем рождения
        //TODO: Доработать!
//        if ($datauser['dayb'] == date('j', time()) && $datauser['monthb'] == date('n', time())) {
//            echo '<div class="rmenu"><p>' . $lng['happy_birthday'] . '</p></div>';
//        }
        // Дайджест Администратора
        if (Vars::$USER_RIGHTS) {
            $new_users_total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_user` WHERE `join_date` > '" . (time() - 86400) . "' AND `preg` = '1'"), 0);
            $reg_total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_user` WHERE `level` = 0"), 0);
            $ban_total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time` > '" . time() . "'"), 0);
            echo '<div class="menu"><p><h3>' . Vars::$LNG['administrative_events'] . '</h3><ul>';
            if ($new_users_total > 0)
                echo '<li><a href="users/index.php?act=userlist">' . Vars::$LNG['users_new'] . '</a> (' . $new_users_total . ')</li>';
            if ($reg_total > 0 && Vars::$USER_RIGHTS >= 7)
                echo '<li><a href="' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=reg">' . Vars::$LNG['users_on_reg'] . '</a> (' . $reg_total . ')</li>';
            if ($ban_total > 0)
                echo '<li><a href="' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=ban_panel">' . Vars::$LNG['users_on_ban'] . '</a> (' . $ban_total . ')</li>';
            $total_libmod = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = 0"), 0);
            if ($total_libmod > 0 && Vars::$USER_RIGHTS >= 6)
                echo '<li><a href="library/index.php?act=moder">' . Vars::$LNG['library_on_moderation'] . '</a> (' . $total_libmod . ')</li>';
            $total_admin = Counters::guestbookCount(2);
            if ($total_admin > 0)
                echo '<li><a href="guestbook/index.php?act=ga&amp;do=set">' . Vars::$LNG['admin_club'] . '</a> (' . $total_admin . ')</li>';
            if (!$new_users_total && !$reg_total && !$ban_total && !$total_libmod && !$total_admin)
                echo '<li>' . Vars::$LNG['events_no_new'] . '</li>';
            echo '</ul></p></div>';
        }
        // Дайджест юзеров
        echo '<div class="menu"><p><h3>' . Vars::$LNG['site_new'] . '</h3><ul>';
        $total_news = mysql_result(mysql_query("SELECT COUNT(*) FROM `news` WHERE `time` > " . (time() - 86400)), 0);
        if ($total_news > 0)
            echo '<li><a href="news/index.php">' . Vars::$LNG['news'] . '</a> (' . $total_news . ')</li>';
        $total_forum = Counters::forumCountNew();
        if ($total_forum > 0)
            echo '<li><a href="forum/index.php?act=new">' . Vars::$LNG['forum'] . '</a> (' . $total_forum . ')</li>';
        $total_guest = Counters::guestbookCount(1);
        if ($total_guest > 0)
            echo '<li><a href="guestbook/index.php?act=ga">' . Vars::$LNG['guestbook'] . '</a> (' . $total_guest . ')</li>';
        $total_gal = Counters::galleryCount(1);
        if ($total_gal > 0)
            echo '<li><a href="gallery/index.php?act=new">' . Vars::$LNG['gallery'] . '</a> (' . $total_gal . ')</li>';
        if ($set_karma['on']) {
            $total_karma = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `karma_user` = '" . Vars::$USER_ID . "' AND `time` > " . (time() - 86400)), 0);
            if ($total_karma > 0)
                echo '<li><a href="users/profile.php?act=karma&amp;mod=new">' . Vars::$LNG['new_responses'] . '</a> (' . $total_karma . ')</li>';
        }
        $total_lib = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = 1 AND `time` > " . (time() - 259200)), 0);
        if ($total_lib > 0)
            echo '<li><a href="library/index.php?act=new">' . Vars::$LNG['library'] . '</a> (' . $total_lib . ')</li>';
        $total_album = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `time` > '" . (time() - 259200) . "' AND `access` > '1'"), 0);
        if ($total_album > 0) echo '<li><a href="users/album.php?act=top">' . Vars::$LNG['photo_albums'] . '</a> (' . $total_album . ')</li>';
        // Если нового нет, выводим сообщение
        if (!$total_news && !$total_forum && !$total_guest && !$total_gal && !$total_lib && !$total_karma)
            echo '<li>' . Vars::$LNG['events_no_new'] . '</li>';
        // Дата последнего посещения
        $last = isset($_GET['last']) ? intval($_GET['last']) : Vars::$USER_DATA['lastdate'];
        echo '</ul></p></div><div class="phdr">' . Vars::$LNG['last_visit'] . ': ' . date("d.m.Y (H:i)", $last) . '</div>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Главное меню сайта
        -----------------------------------------------------------------
        */
        if (isset($_SESSION['ref']))
            unset($_SESSION['ref']);
        include_once('pages/mainmenu.php');

        /*
        -----------------------------------------------------------------
        Карта сайта
        -----------------------------------------------------------------
        */
        if (isset(Vars::$SYSTEM_SET['sitemap'])) {
            $set_map = unserialize(Vars::$SYSTEM_SET['sitemap']);
            if (($set_map['forum'] || $set_map['lib']) && ($set_map['users'] || !Vars::$USER_ID) && ($set_map['browsers'] || !Vars::$IS_MOBILE)) {
                $map = new SiteMap();
                echo '<div class="sitemap">' . $map->mapGeneral() . '</div>';
            }
        }
}

require_once('includes/end.php');