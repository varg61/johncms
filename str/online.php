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
$headmod = 'online';
$textl = 'Онлайн';
require_once('../incfiles/core.php');
require_once('../incfiles/head.php');

// Показываем список Online
echo '<div class="phdr"><b>Кто на сайте</b></div>';
if ($rights > 0)
    echo '<div class="topmenu">' . ($act == 'guest' ? '<a href="online.php">Авторизованные</a> | Гости' : 'Авторизованные | <a href="online.php?act=guest">Гости</a>') . '</div>';
$onltime = $realtime - 300;
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . ($act == 'guest' ? 'cms_guests' : 'users') . "` WHERE `lastdate` > '$onltime'"), 0);
if ($total) {
    $req = mysql_query("SELECT * FROM `" . ($act == 'guest' ? 'cms_guests' : 'users') . "` WHERE `lastdate` > '$onltime' ORDER BY " . ($act == 'guest' ? "`movings` DESC" : "`name` ASC") . " LIMIT $start,$kmess");
    while ($res = mysql_fetch_assoc($req)) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        if ($user_id) {
            // Вычисляем местоположение
            $where = explode(",", $res['place']);
            switch ($where[0]) {
                case 'forumfiles':
                    $place = '<a href="../forum/index.php?act=files">Файлы форума</a>';
                    break;

                case 'forumwho':
                    $place = '<a href="../forum/index.php?act=who">Смотрит кто в форуме?</a>';
                    break;

                case 'anketa':
                    $place = '<a href="anketa.php">Анкета</a>';
                    break;

                case 'settings':
                    $place = '<a href="usset.php">Настройки</a>';
                    break;

                case 'users':
                    $place = '<a href="users.php">Список юзеров</a>';
                    break;

                case 'online':
                    $place = 'Тут, в списке';
                    break;

                case 'privat':
                case 'pradd':
                    $place = '<a href="../index.php?act=cab">Приват</a>';
                    break;

                case 'birth':
                    $place = '<a href="brd.php">Список именинников</a>';
                    break;

                case 'read':
                    $place = '<a href="../read.php">Читает FAQ</a>';
                    break;

                case 'load':
                    $place = '<a href="../download/index.php">Загрузки</a>';
                    break;

                case 'gallery':
                    $place = '<a href="../gallery/index.php">Галерея</a>';
                    break;

                case 'forum':
                case 'forums':
                    $place = '<a href="../forum/index.php">' . $lng['forum'] . '</a>&#160;/&#160;<a href="../forum/index.php?act=who">&gt;&gt;</a>';
                    break;

                case 'chat':
                    $place = '<a href="../chat/index.php">' . $lng['chat'] . '</a>';
                    break;

                case 'guest':
                    $place = '<a href="guest.php">Гостевая</a>';
                    break;

                case 'lib':
                    $place = '<a href="../library/index.php">Библиотека</a>';
                    break;

                case 'mainpage':
                default:
                    $place = '<a href="../index.php">Главная</a>';
                    break;
            }
        }
        $arg = array(
            'stshide' => 1,
            'header' => (' (' . $res['movings'] . ' - ' . timecount($realtime - $res['sestime']) . ')<br /><img src="../images/info.png" width="16" height="16" align="middle" />&#160;' . $place)
        );
        echo show_user($res, $arg);
        echo '</div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>Никого нет</p></div>';
}
echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
if ($total > 10) {
    echo '<p>' . pagenav('online.php?' . ($act == 'guest' ? 'act=guest&amp;' : ''), $start, $total, $kmess) . '</p>';
    echo '<p><form action="online.php" method="get"><input type="text" name="page" size="2"/>' . ($act == 'guest' ? '<input type="hidden" value="guest" name="act" />' : '') .
        '<input type="submit" value="К странице &gt;&gt;"/></form></p>';
}

require_once('../incfiles/end.php');

?>
