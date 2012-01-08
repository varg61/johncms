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
require_once('../includes/head.php');

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

echo '<div class="phdr"><b>' . Vars::$LNG['who_on_site'] . '</b></div>' .
     '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';

switch (Vars::$MOD) {
    case 'ip':
        // Список активных IP, со счетчиком обращений
        $ip_array = array_count_values(Vars::$IP_REQUESTS_COUNT);
        $total = count($ip_array);
        $end = Vars::$START + Vars::$USER_SET['page_size'];
        if ($end > $total) $end = $total;
        arsort($ip_array);
        $i = 0;
        foreach ($ip_array as $key => $val) {
            $ip_list[$i] = array($key => $val);
            ++$i;
        }
        if ($total && Vars::$USER_RIGHTS) {
            if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=online&amp;mod=ip&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            for ($i = Vars::$START; $i < $end; $i++) {
                $out = each($ip_list[$i]);
                if ($out[0] == Vars::$IP) echo '<div class="gmenu">';
                else echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                echo '<a href="' . Vars::$SYSTEM_SET['homeurl'] . '/' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=search_ip&amp;ip=' . long2ip($out[0]) . '">' . long2ip($out[0]) .
                     '</a>&#160;-&#160;[' . $out[1] . ']';
                echo '</div>';
            }
            echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
            if ($total > Vars::$USER_SET['page_size']) {
                echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=online&amp;mod=ip&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                     '<p><form action="index.php?act=online&amp;mod=ip" method="post">' .
                     '<input type="text" name="page" size="2"/>' .
                     '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/></form></p>';
            }
        }
        require_once('../includes/end.php');
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
        $sql_total = "SELECT COUNT(*) FROM `users` WHERE `lastdate` > " . (time() - 300);
        $sql_list = "SELECT * FROM `users` WHERE `lastdate` > " . (time() - 300) . " ORDER BY `name` ASC LIMIT " . Vars::db_pagination();
}

$total = mysql_result(mysql_query($sql_total), 0);
if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=online&amp;' . (Vars::$MOD ? 'mod=' . Vars::$MOD . '&amp;' : ''), Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
if ($total) {
    $req = mysql_query($sql_list);
    $i = 0;
    while (($res = mysql_fetch_assoc($req)) !== false) {
        if ($res['id'] == Vars::$USER_ID) echo '<div class="gmenu">';
        else echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        $arg['stshide'] = 1;
        $arg['header'] = ' <span class="gray">(';
        if (Vars::$MOD == 'history') $arg['header'] .= Functions::displayDate($res['sestime']);
        else $arg['header'] .= $res['movings'] . ' - ' . Functions::timeCount(time() - $res['sestime']);
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