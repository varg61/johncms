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

//TODO: Доработать

$headmod = 'online';
$textl = Vars::$LNG['online'];
//$lng_online = Vars::load_lng('online');
require_once(SYSPATH . 'head.php');

/*
-----------------------------------------------------------------
Показываем список Online
-----------------------------------------------------------------
*/
$menu[] = !Vars::$MOD ? '<b>' . Vars::$LNG['users'] . '</b>' : '<a href="index.php?act=online">' . Vars::$LNG['users'] . '</a>';
$menu[] = Vars::$MOD == 'history' ? '<b>' . Vars::$LNG['history'] . '</b>' : '<a href="index.php?act=online&amp;mod=history">' . Vars::$LNG['history'] . '</a> ';
if (Vars::$USER_RIGHTS) {
    $menu[] = Vars::$MOD == 'guest' ? '<b>' . Vars::$LNG['guests'] . '</b>' : '<a href="index.php?act=online&amp;mod=guest">' . Vars::$LNG['guests'] . '</a>';
    $menu[] = Vars::$MOD == 'ip' ? '<b>' . Vars::$LNG['ip_activity'] . '</b>' : '<a href="index.php?act=online&amp;mod=ip">' . Vars::$LNG['ip_activity'] . '</a>';
}

echo'<div class="phdr"><b>' . Vars::$LNG['who_on_site'] . '</b></div>' .
    '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';

switch (Vars::$MOD) {
    case 'download_ip';
        // Скачиваем список IP
        if (Vars::$USER_RIGHTS < 6) {
            echo 'dgsdg';
            require_once(SYSPATH . 'end.php');
            exit;
        }
        $file = CACHEPATH . 'ip_requests_list.txt';
        arsort(Vars::$IP_REQUESTS_LIST);
        $shift = (Vars::$SYSTEM_SET['timeshift'] + Vars::$USER_SET['timeshift']) * 3600;
        $out = 'IP Requests ' . date("d.m.Y / H:i", time() + $shift);
        $out .= "\r\n------------------------------\r\n";
        foreach (Vars::$IP_REQUESTS_LIST as $key => $val) {
            $out .= long2ip($key) . '  [' . $val . "]\r\n";
        }
        file_put_contents($file, $out);
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
        }
        exit;
        break;

    case 'ip':
        if (Vars::$USER_RIGHTS < 6) {
            require_once(SYSPATH . 'end.php');
            exit;
        }
        // Список активных IP, со счетчиком обращений
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
            echo'<p><a href="index.php?act=online&amp;mod=download_ip">' . Vars::$LNG['download_list'] . '</a></p>';
        } else {
            echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
        }
        require_once(SYSPATH . 'end.php');
        exit;
        break;

    case 'guest':
        // Список гостей Онлайн
        $sql_total = "SELECT COUNT(*) FROM `cms_sessions` WHERE `lastdate` > " . (time() - 300);
        $sql_list = "SELECT * FROM `cms_sessions` WHERE `lastdate` > " . (time() - 300) . " ORDER BY `movings` DESC LIMIT " . Vars::db_pagination();
        break;

    case 'history':
        // История посетилелей за последние 2 суток
        $sql_total = "SELECT COUNT(*) FROM `users` WHERE `lastdate` > " . (time() - 172800 . " AND `lastdate` < " . (time() - 310));
        $sql_list = "SELECT * FROM `users` WHERE `lastdate` > " . (time() - 172800) . " AND `lastdate` < " . (time() - 310) . " ORDER BY `sestime` DESC LIMIT " . Vars::db_pagination();
        break;

    default:
        // Список посетителей Онлайн
        $sql_total = "SELECT COUNT(*) FROM `cms_sessions` WHERE `user_id` > 0 AND `session_timestamp` > " . (time() - 300);
        $sql_list = "SELECT `cms_sessions`.`user_id`, `users`.*
            FROM `cms_sessions` LEFT JOIN `users` ON `cms_sessions`.`user_id` = `users`.`id`
            WHERE `cms_sessions`.`user_id` > 0 AND `cms_sessions`.`session_timestamp` > " . (time() - 300) . "
            ORDER BY `users`.`nickname` ASC" . Vars::db_pagination();
}

$total = mysql_result(mysql_query($sql_total), 0);
if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=online&amp;' . (Vars::$MOD ? 'mod=' . Vars::$MOD . '&amp;' : ''), Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
if ($total) {
    $req = mysql_query($sql_list);
    $i = 0;
    while ($res = mysql_fetch_assoc($req)) {
        if ($res['id'] == Vars::$USER_ID) echo '<div class="gmenu">';
        else echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        $arg['stshide'] = 1;
        $arg['header'] = ' <span class="gray">(';
        if (Vars::$MOD == 'history') {
            $arg['header'] .= Functions::displayDate($res['sestime']);
        } else {
            $arg['header'] .= $res['movings'] . ' - ' . Functions::timeCount(time() - $res['sestime']);
        }
        $arg['header'] .= ')</span><br />' . Functions::getImage('info.png', '', 'align="middle"') . '&#160;' . Functions::displayPlace($res['id'], $res['place']);
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