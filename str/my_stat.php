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
$headmod = 'mystat';
require('../incfiles/core.php');
$lng_stat = load_lng('stat');
$textl = $lng['my_stat'];
require('../incfiles/head.php');
if (!$user_id) {
    echo display_error($lng['access_guest_forbidden']);
    require('../incfiles/end.php');
    exit;
}

$user = $id ? $id : $user_id;
$req_u = mysql_query("SELECT * FROM `users` WHERE `id` = '$user' LIMIT 1");
if (mysql_num_rows($req_u)) {
    $res_u = mysql_fetch_assoc($req_u);
    // Заголовок модуля
    echo '<div class="phdr"><b>' . ($id ? $lng['statistics_user'] : $lng['statistics_my']) . '</b>';
    switch ($act) {
        case 'forum':
            echo ' | ' . $lng['forum'];
            break;

        case 'guest':
            echo ' | ' . $lng['guestbook'];
            break;
    }
    echo '</div>';
    /*
    -----------------------------------------------------------------
    Главное Меню модуля
    -----------------------------------------------------------------
    */
    $menu = array ();
    $menu[] = !$act ? $lng['statistics'] : '<a href="my_stat.php?id=' . $id . '">' . $lng['statistics'] . '</a>';
    if ($res_u['postforum'])
        $menu[] = $act == 'forum' ? $lng['forum'] : '<a href="my_stat.php?act=forum&amp;id=' . $id . '">' . $lng['forum'] . '</a>';
    if ($res_u['postguest'])
        $menu[] = $act == 'guest' ? $lng['guestbook'] : '<a href="my_stat.php?act=guest&amp;id=' . $id . '">' . $lng['guestbook'] . '</a>';
    echo '<div class="topmenu">' . display_menu($menu) . '</div>';
    /*
    -----------------------------------------------------------------
    Данные пользователя
    -----------------------------------------------------------------
    */
    echo '<div class="user"><p>';
    $arg = array (
        'lastvisit' => 1,
        'iphist' => 1,
        'header' => '<b>ID:' . $res_u['id'] . '</b>'
    );
    echo display_user($res_u, $arg);
    echo '</p></div>';
    switch ($act) {
        case 'go':
            /*
            -----------------------------------------------------------------
            Переход к своему последнему посту на Форуме
            -----------------------------------------------------------------
            */
            $doid = isset($_GET['doid']) ? abs(intval($_GET['doid'])) : '';
            switch ($do) {
                case 'f':
                    // Переход на нужную страницу Форума
                    $set_forum = array ();
                    $set_forum = unserialize($datauser['set_forum']);
                    if (empty($set_forum))
                        $set_forum['upfp'] = 0;
                    $req = mysql_query("SELECT * FROM `forum` WHERE `id` = '$doid' AND `type` = 'm' LIMIT 1");
                    if (mysql_num_rows($req)) {
                        $res = mysql_fetch_assoc($req);
                        $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '" . $doid . "'"), 0) / $kmess);
                        header('Location: ../forum/index.php?id=' . $res['refid'] . '&page=' . $page);
                    } else {
                        header('Location: ../forum/index.php');
                    }
                    break;
                    default :
                    header('Location: ../index.php');
            }
            break;

        case 'forum':
            /*
            -----------------------------------------------------------------
            Статистика активности на Форуме
            -----------------------------------------------------------------
            */
            $req = mysql_query("SELECT `refid`, MAX(time) FROM `forum` WHERE `user_id` = '$user' AND `type` = 'm'" . ($rights >= 7 ? '' : " AND `close` != '1'") . " GROUP BY `refid` ORDER BY `time` DESC LIMIT 10");
            if (mysql_num_rows($req)) {
                while ($res = mysql_fetch_assoc($req)) {
                    $arrid = $res['MAX(time)'];
                    $arr[$arrid] = $res['refid'];
                }
                krsort($arr);
                foreach ($arr as $key => $val) {
                    $req_t = mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $val . "' AND `type` = 't' LIMIT 1");
                    $res_t = mysql_fetch_assoc($req_t);
                    $req_m = mysql_query("SELECT * FROM `forum` WHERE `refid` = '" . $val . "' AND `user_id` = '$user' AND`type` = 'm' ORDER BY `id` DESC LIMIT 1");
                    $res_m = mysql_fetch_assoc($req_m);
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo '<span class="gray">(' . date("d.m.Y / H:i", $res_m['time'] + $set_user['sdvig'] * 3600) . ')</span>';
                    echo ' <a href="my_stat.php?act=go&amp;do=f&amp;doid=' . $res_m['id'] . '">' . $res_t['text'] . '</a>';
                    $text = mb_substr($res_m['text'], 0, 500);
                    $text = preg_replace('#\[c\](.*?)\[/c\]#si', '', $text);
                    $text = checkout($text, 1, 1);
                    echo '<div class="sub">' . $text . '</div>';
                    echo '</div>';
                    ++$i;
                }
            } else {
                echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
            }
            echo '<div class="phdr"><a href="../forum/index.php">' . $lng['forum'] . '</a></div>';
            break;

        case 'guest':
            /*
            -----------------------------------------------------------------
            Статистика активности в Гостевой
            -----------------------------------------------------------------
            */
            $req = mysql_query("SELECT * FROM `guest` WHERE `user_id` = '$user' AND `adm` = '0' ORDER BY `id` DESC LIMIT 10");
            if (mysql_num_rows($req)) {
                while ($res = mysql_fetch_array($req)) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo ' <span class="gray">(' . date("d.m.Y / H:i", $res['time'] + $set_user['sdvig'] * 3600) . ')</span>';
                    $text = checkout($res['text'], 1, 1);
                    echo '<div class="sub">' . $text . '</div>';
                    echo '</div>';
                    ++$i;
                }
            } else {
                echo '<div class="menu"><p>' . $lng_stat['guest_empty'] . '</p></div>';
            }
            echo '<div class="phdr"><a href="guest.php">' . $lng['guestbook'] . '</a></div>';
            break;

        default:
            /*
            -----------------------------------------------------------------
            Общая статистика активности
            -----------------------------------------------------------------
            */
            echo '<div class="menu"><p>' .
                '<h3><img src="../images/rate.gif" width="16" height="16" class="left" />&#160;' . $lng_stat['site_activity'] . '</h3><ul>' .
                '<li><a href="my_stat.php?act=forum">' . $lng['forum'] . '</a>: <b>' . $res_u['postforum'] . '</b></li>' .
                '<li><a href="my_stat.php?act=guest">' . $lng['guestbook'] . '</a>: <b>' . $res_u['postguest'] . '</b></li>' .
                '<li>' . $lng['comments'] . ': <b>' . $res_u['komm'] . '</b></li>' .
                '<li>' . $lng['chat'] . ': <b>' . $res_u['postchat'] . '</b></li>' .
                '<li>' . $lng['quiz'] . ': <b>' . $res_u['otvetov'] . '</b></li>' .
                '<li>' . $lng_stat['game_balance'] . ': <b>' . $res_u['balans'] . '</b></li>' .
                '</ul></p>' .
                '<p><h3><img src="../images/star.gif" width="16" height="16" class="left" />&#160;' . $lng_stat['achievements'] . '</h3>';
            /*
            -----------------------------------------------------------------
            Вывод таблицы со списком достижений
            Код таблицы Vert1go + AlkatraZ
            -----------------------------------------------------------------
            Параметры $num и $query можно редактировать под себя.
            $num - определяет пороги достижений по статистике
            $query - определяет, какую именно статистику будем показывать
            -----------------------------------------------------------------
            */
            $num = array (
                100,
                500,
                1000,
                5000
            );
            $query = array (
                'postforum' => $lng['forum'],
                'postguest' => $lng['guestbook'],
                'komm' => $lng['comments'],
                'postchat' => $lng['chat'],
                'otvetov' => $lng['quiz']
            );
            echo '<table><tr>';
            foreach ($num as $val) {
                echo '<td width="26"><small>' . $val . '</small></td>';
            }
            echo '<td></td></tr>';
            foreach ($query as $key => $val) {
                echo '<tr>';
                foreach ($num as $achieve) {
                    echo '<td align="center"><img src="../images/' . ($res_u[$key] >= $achieve ? 'green' : 'red') . '.gif" alt=""/></td>';
                }
                echo '<td><small><b>' . $val . '</b></small></td></tr>';
            }
            echo '</table>';
            echo '</p></div>';
            /*
            -----------------------------------------------------------------
            Если были нарушения, то показываем их
            -----------------------------------------------------------------
            */
            if ($total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '$user'"), 0))
                echo '<div class="rmenu">' . $lng['infringements'] . ': <a href="anketa.php?act=ban&amp;id=' . $user . '">' . $total . '</a></div>';
            echo '<div class="phdr"><a href="users_top.php">' . $lng_stat['top_10'] . '</a></div>';
    }
} else {
    echo display_error($lng['error_user_not_exist']);
}
echo '<p><a href="my_cabinet.php">' . $lng['personal'] . '</a></p>';
require('../incfiles/end.php');
?>