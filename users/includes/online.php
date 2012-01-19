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

$textl = Vars::$LNG['online'];
require_once(SYSPATH . 'head.php');

$link = '';
$sql_total = '';
$sql_list = '';

$menu[] = !Vars::$MOD ? '<b>' . Vars::$LNG['users'] . '</b>' : '<a href="index.php?act=online">' . Vars::$LNG['users'] . '</a>';
$menu[] = Vars::$MOD == 'history' ? '<b>' . Vars::$LNG['history'] . '</b>' : '<a href="index.php?act=online&amp;mod=history">' . Vars::$LNG['history'] . '</a> ';
if (Vars::$USER_RIGHTS) {
    $menu[] = Vars::$MOD == 'guest' ? '<b>' . Vars::$LNG['guests'] . '</b>' : '<a href="index.php?act=online&amp;mod=guest">' . Vars::$LNG['guests'] . '</a>';
    $menu[] = Vars::$MOD == 'ip' ? '<b>' . Vars::$LNG['ip_activity'] . '</b>' : '<a href="index.php?act=online&amp;mod=ip">' . Vars::$LNG['ip_activity'] . '</a>';
}

echo'<div class="phdr"><b>' . Vars::$LNG['who_on_site'] . '</b></div>' .
    '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';

switch (Vars::$MOD) {
    case 'du':
        /*
        -----------------------------------------------------------------
        Скачиваем список пользователей
        -----------------------------------------------------------------
        */
        if (!Vars::$USER_ID) {
            require_once(SYSPATH . 'end.php');
            exit;
        }
        $out = 'Users Online ' . date("d.m.Y / H:i", time() + (Vars::$SYSTEM_SET['timeshift'] + Vars::$USER_SET['timeshift']) * 3600);
        $out .= "\r\n===============================\r\n";
        $req = mysql_query("SELECT `cms_sessions`.`user_id` AS `id`, `cms_sessions`.`session_timestamp` AS `last_visit`, `cms_sessions`.`ip`, `cms_sessions`.`ip_via_proxy`, `cms_sessions`.`user_agent`, `cms_sessions`.`place`, `cms_sessions`.`views`, `cms_sessions`.`movings`, `cms_sessions`.`start_time`, `users`.`nickname`, `users`.`sex`, `users`.`rights`
            FROM `cms_sessions` LEFT JOIN `users` ON `cms_sessions`.`user_id` = `users`.`id`
            WHERE `cms_sessions`.`user_id` > 0 AND `cms_sessions`.`session_timestamp` > " . (time() - 300) . "
            ORDER BY `users`.`nickname` ASC");
        while ($res = mysql_fetch_assoc($req)) {
            $out .= $res['nickname'] . ' [' . $res['sex'] . '] (' . $res['views'] . '/' . $res['movings'] . ' - ' . Functions::timeCount(time() - $res['start_time']) . ')';
            if (Vars::$USER_RIGHTS || $res['id'] == Vars::$USER_ID) {
                $out .= "\r\n" . '  IP: ' . long2ip($res['ip']);
                if ($res['ip_via_proxy']) {
                    $out .= ' / ' . long2ip($res['ip_via_proxy']);
                }
                $out .= "\r\n" . '  UA: ' . $res['user_agent'];
            }
            $out .= "\r\n\r\n";
        }
        Functions::downloadFile($out, 'online_users.txt');
        exit;
        break;

    case 'dh':
        /*
        -----------------------------------------------------------------
        Скачиваем историю Онлайн пользователей
        -----------------------------------------------------------------
        */
        if (!Vars::$USER_ID) {
            require_once(SYSPATH . 'end.php');
            exit;
        }
        $out = 'Users Online history ' . date("d.m.Y / H:i", time() + (Vars::$SYSTEM_SET['timeshift'] + Vars::$USER_SET['timeshift']) * 3600);
        $out .= "\r\n=======================================\r\n";
        $req = mysql_query("SELECT * FROM `users` WHERE `last_visit` > " . (time() - 172800) . " AND `last_visit` < " . (time() - 310) . " ORDER BY `last_visit` DESC");
        while ($res = mysql_fetch_assoc($req)) {
            $out .= $res['nickname'] . ' [' . $res['sex'] . '] (' . Functions::displayDate($res['last_visit']) . ')';
            if (Vars::$USER_RIGHTS || $res['id'] == Vars::$USER_ID) {
                $out .= "\r\n" . '  IP: ' . long2ip($res['ip']);
                if ($res['ip_via_proxy']) {
                    $out .= ' / ' . long2ip($res['ip_via_proxy']);
                }
                $out .= "\r\n" . '  UA: ' . $res['user_agent'];
            }
            $out .= "\r\n\r\n";
        }
        Functions::downloadFile($out, 'online_history.txt');
        exit;
        break;

    case 'dg':
        /*
        -----------------------------------------------------------------
        Скачиваем список гостей
        -----------------------------------------------------------------
        */
        if (!Vars::$USER_RIGHTS) {
            require_once(SYSPATH . 'end.php');
            exit;
        }
        $out = 'Guests Online ' . date("d.m.Y / H:i", time() + (Vars::$SYSTEM_SET['timeshift'] + Vars::$USER_SET['timeshift']) * 3600);
        $out .= "\r\n================================\r\n";
        $req = mysql_query("SELECT `user_id` AS `id`, `session_timestamp` AS `last_visit`, `ip`, `ip_via_proxy`, `user_agent`, `place`, `views`, `movings`, `start_time`
            FROM `cms_sessions`
            WHERE `user_id` = 0 AND `session_timestamp` > " . (time() - 300) . "
            ORDER BY `session_timestamp` DESC");
        while ($res = mysql_fetch_assoc($req)) {
            $out .= 'GUEST (' . $res['views'] . '/' . $res['movings'] . ' - ' . Functions::timeCount(time() - $res['start_time']) . ')';
            $out .= "\r\n" . '  IP: ' . long2ip($res['ip']);
            if ($res['ip_via_proxy']) {
                $out .= ' / ' . long2ip($res['ip_via_proxy']);
            }
            $out .= "\r\n" . '  UA: ' . $res['user_agent'];
            $out .= "\r\n\r\n";
        }
        Functions::downloadFile($out, 'online_guests.txt');
        exit;
        break;

    case 'di';
        /*
        -----------------------------------------------------------------
        Скачиваем список IP
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS < 6) {
            require_once(SYSPATH . 'end.php');
            exit;
        }
        arsort(Vars::$IP_REQUESTS_LIST);
        $out = 'IP Requests ' . date("d.m.Y / H:i", time() + (Vars::$SYSTEM_SET['timeshift'] + Vars::$USER_SET['timeshift']) * 3600);
        $out .= "\r\n==============================\r\n";
        foreach (Vars::$IP_REQUESTS_LIST as $key => $val) {
            $out .= long2ip($key) . '  [' . $val . "]\r\n";
        }
        Functions::downloadFile($out, 'online_ip.txt');
        exit;
        break;

    case 'ip':
        /*
        -----------------------------------------------------------------
        Показываем список активных IP, со счетчиком обращений
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS < 6) {
            require_once(SYSPATH . 'end.php');
            exit;
        }
        $total = count(Vars::$IP_REQUESTS_LIST);
        $end = Vars::$START + Vars::$USER_SET['page_size'];
        if ($end > $total) $end = $total;
        arsort(Vars::$IP_REQUESTS_LIST);
        $i = 0;
        $ip_list = array();
        foreach (Vars::$IP_REQUESTS_LIST as $key => $val) {
            $ip_list[$i] = array($key => $val);
            ++$i;
        }
        if ($total) {
            if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=online&amp;mod=ip&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            for ($i = Vars::$START; $i < $end; $i++) {
                $out = each($ip_list[$i]);
                $ip = long2ip($out[0]);
                echo($out[0] == Vars::$IP ? '<div class="gmenu">' : ($i % 2 ? '<div class="list2">' : '<div class="list1">')) .
                    '<div style="float:left">' . Functions::getImage('host.gif') . '</div>' .
                    '<div style="float:left;margin-left:6px;font-size:x-small"><a href="' . Vars::$HOME_URL . '/' . Vars::$SYSTEM_SET['admp'] . '/ip_whois.php?ip=' . $ip . '">[w]</a></div>' .
                    '<div style="float:left;margin-left:6px"><b><a href="' . Vars::$HOME_URL . '/' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=search_ip&amp;ip=' . $ip . '">' . $ip . '</a></b></div>' .
                    '<div style="margin-left:120px"><span class="red"><b>' . $out[1] . '</b></span></div>' .
                    '</div>';
            }
            echo '</table>';
            echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
            if ($total > Vars::$USER_SET['page_size']) {
                echo'<div class="topmenu">' . Functions::displayPagination('index.php?act=online&amp;mod=ip&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                    '<p><form action="index.php?act=online&amp;mod=ip" method="post">' .
                    '<input type="text" name="page" size="2"/>' .
                    '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/></form></p>';
            }
            echo'<p><a href="index.php?act=online&amp;mod=di">' . Vars::$LNG['download_list'] . '</a></p>';
        } else {
            echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
        }
        require_once(SYSPATH . 'end.php');
        exit;
        break;

    case 'guest':
        /*
        -----------------------------------------------------------------
        Заппросы для списка гостей Онлайн
        -----------------------------------------------------------------
        */
        $sql_total = "SELECT COUNT(*) FROM `cms_sessions` WHERE `user_id` = 0 AND `session_timestamp` > " . (time() - 300);
        $sql_list = "SELECT `user_id` AS `id`, `session_timestamp` AS `last_visit`, `ip`, `ip_via_proxy`, `user_agent`, `place`, `views`, `movings`, `start_time`
            FROM `cms_sessions`
            WHERE `user_id` = 0 AND `session_timestamp` > " . (time() - 300) . "
            ORDER BY `session_timestamp` ASC" . Vars::db_pagination();
        $link = 'dg';
        break;

    case 'history':
        /*
        -----------------------------------------------------------------
        Заппросы для истории посетилелей за последние 2 суток
        -----------------------------------------------------------------
        */
        $sql_total = "SELECT COUNT(*) FROM `users` WHERE `last_visit` > " . (time() - 172800 . " AND `last_visit` < " . (time() - 310));
        $sql_list = "SELECT * FROM `users` WHERE `last_visit` > " . (time() - 172800) . " AND `last_visit` < " . (time() - 310) . " ORDER BY `last_visit` DESC" . Vars::db_pagination();
        $link = 'dh';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Заппросы для списка посетителей Онлайн
        -----------------------------------------------------------------
        */
        $sql_total = "SELECT COUNT(*) FROM `cms_sessions` WHERE `user_id` > 0 AND `session_timestamp` > " . (time() - 300);
        $sql_list = "SELECT `cms_sessions`.`user_id` AS `id`, `cms_sessions`.`session_timestamp` AS `last_visit`, `cms_sessions`.`ip`, `cms_sessions`.`ip_via_proxy`, `cms_sessions`.`user_agent`, `cms_sessions`.`place`, `cms_sessions`.`views`, `cms_sessions`.`movings`, `cms_sessions`.`start_time`, `users`.`nickname`, `users`.`sex`, `users`.`rights`
            FROM `cms_sessions` LEFT JOIN `users` ON `cms_sessions`.`user_id` = `users`.`id`
            WHERE `cms_sessions`.`user_id` > 0 AND `cms_sessions`.`session_timestamp` > " . (time() - 300) . "
            ORDER BY `users`.`nickname` ASC" . Vars::db_pagination();
        $link = 'du';
}

/*
-----------------------------------------------------------------
Показываем списки Онлайн
-----------------------------------------------------------------
*/
$total = mysql_result(mysql_query($sql_total), 0);
if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=online&amp;' . (Vars::$MOD ? 'mod=' . Vars::$MOD . '&amp;' : ''), Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
if ($total) {
    $req = mysql_query($sql_list);
    $i = 0;
    while ($res = mysql_fetch_assoc($req)) {
        if ($res['id'] == Vars::$USER_ID) echo '<div class="gmenu">';
        else echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        $arg['header'] = ' <span class="gray">(';
        if (Vars::$MOD == 'history') {
            $arg['header'] .= Functions::displayDate($res['last_visit']) . ')</span>';
        } else {
            $arg['header'] .= $res['views'] . '/' . $res['movings'] . ' - ' . Functions::timeCount(time() - $res['start_time']);
            $arg['header'] .= ')</span><br />' . Functions::getImage('info.png', '', 'align="middle"') . '&#160;' . Functions::displayPlace($res['id'], $res['place']);
        }
        echo Functions::displayUser($res, $arg);
        echo '</div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
}
echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=online&amp;' . (Vars::$MOD ? 'mod=' . Vars::$MOD . '&amp;' : ''), Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="index.php?act=online' . (Vars::$MOD ? '&amp;mod=' . Vars::$MOD : '') . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/>' .
        '</form></p>';
}

if (Vars::$USER_ID && $total) {
    echo'<p><a href="index.php?act=online&amp;mod=' . $link . '">' . Vars::$LNG['download_list'] . '</a></p>';
}