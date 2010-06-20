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

defined('_IN_JOHNADM') or die('Error: restricted access');

require_once('../incfiles/ban.php');
switch ($mod) {
    case 'amnesty':
        if ($rights < 9) {
            echo display_error('Амнистия доступна только для Супервизоров');
        } else {
            echo '<div class="phdr"><b>Амнистия</b></div>';
            if (isset($_POST['submit'])) {
                $term = isset($_POST['term']) && $_POST['term'] == 1 ? 1 : 0;
                if ($term) {
                    // Очищаем таблицу Банов
                    mysql_query("TRUNCATE TABLE `cms_ban_users`");
                    echo '<div class="gmenu"><p>Таблица Банов очищена.<br />Удалена вся история нарушений</p></div>';
                } else {
                    // Разбаниваем активные Баны
                    $req = mysql_query("SELECT * FROM `cms_ban_users` WHERE `ban_time` > '" . $realtime . "'");
                    while ($res = mysql_fetch_array($req)) {
                        $ban_left = $res['ban_time'] - $realtime;
                        if ($ban_left < 2592000) {
                            mysql_query("UPDATE `cms_ban_users` SET `ban_time`='$realtime', `ban_raz`='--Амнистия--' WHERE `id` = '" . $res['id'] . "'");
                        }
                    }
                    echo '<div class="gmenu"><p>Разбанены все пользователи, которые имели активные Баны (кроме банов &quot;До отмены&quot;)</p></div>';
                }
            } else {
                echo '<form action="index.php?act=usr_ban&amp;mod=amnesty" method="post"><div class="menu"><p>';
                echo '<input type="radio" name="term" value="0" checked="checked" />&#160;Разбанить всех<br />';
                echo '<input type="radio" name="term" value="1" />&#160;Очистить базу';
                echo '</p><p><input type="submit" name="submit" value="Амнистия" />';
                echo '</p></div></form>';
                echo '<div class="phdr"><small>&quot;Разбанить всех&quot; - прекращает действие всех активных Банов<br />';
                echo '&quot;Очистить базу&quot; - прекращает действие всех банов и очищает всю историю нарушений</small></div>';
            }
            echo '<p><a href="index.php?act=usr_ban">Бан панель</a><br /><a href="index.php">' . $lng['admin_panel'] . '</a></p>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        БАН-панель, список нарушителей
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>' . $lng['admin_panel'] . '</b></a> | ' . $lng['ban_panel'] . '</div>';
        echo '<div class="topmenu"><span class="gray">' . $lng['sorting'] . ':</span> ';
        if (isset($_GET['count']))
            echo '<a href="index.php?act=usr_ban">' . $lng['term'] . '</a> | ' . $lng['infringements'] . '</div>';
        else
            echo $lng['term'] . ' | <a href="index.php?act=usr_ban&amp;count">' . $lng['infringements'] . '</a></div>';
        $sort = isset($_GET['count']) ? 'bancount' : 'bantime';
        $req = mysql_query("SELECT `user_id` FROM `cms_ban_users` GROUP BY `user_id`");
        $total = mysql_num_rows($req);
        $req = mysql_query("SELECT COUNT(`cms_ban_users`.`user_id`) AS `bancount`, MAX(`cms_ban_users`.`ban_time`) AS `bantime`, `users`.*
        FROM `cms_ban_users` LEFT JOIN `users` ON `cms_ban_users`.`user_id` = `users`.`id`
        GROUP BY `user_id`
        ORDER BY `$sort` DESC
        LIMIT $start, $kmess");
        if (mysql_num_rows($req)) {
            while ($res = mysql_fetch_array($req)) {
                echo '<div class="' . ($res['bantime'] > $realtime ? 'r' : '') . 'menu">';
                echo show_user($res);
                //TODO: Переделать на более удобный показ бана
                //echo show_user($res, 0, 2, ' [' . $res['bancount'] . ']&#160;<a href="../str/users_ban.php?id=' . $res['id'] . '">&gt;&gt;</a>');
                echo '</div>';
            }
        } else {
            echo '<div class="menu"><p>' . $lng['empty_list'] . '</p></div>';
        }
        echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
        if ($total > $kmess) {
            echo '<p>' . pagenav('index.php?act=usr_ban&amp;', $start, $total, $kmess) . '</p>';
            echo '<p><form action="index.php?act=usr_ban" method="post"><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
        }
        echo '<p>' . ($rights == 9 && $total ? '<a href="index.php?act=usr_ban&amp;mod=amnesty">Амнистия</a><br />' : '') . '<a href="index.php">' . $lng['admin_panel'] . '</a></p>';
}

require_once('../incfiles/end.php');

?>