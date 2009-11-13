<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                             Content Management System              //
// Официальный сайт сайт проекта:      http://johncms.com                     //
// Дополнительный сайт поддержки:      http://gazenwagen.com                  //
////////////////////////////////////////////////////////////////////////////////
// JohnCMS core team:                                                         //
// Евгений Рябинин aka john77          john77@gazenwagen.com                  //
// Олег Касьянов aka AlkatraZ          alkatraz@gazenwagen.com                //
//                                                                            //
// Информацию о версиях смотрите в прилагаемом файле version.txt              //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);

$headmod = 'online';
$textl = 'Онлайн';
require_once ("../incfiles/core.php");
require_once ("../incfiles/head.php");

echo '<div class="phdr"><b>Кто на сайте?</b></div>';
echo '<div class="bmenu">Список ' . ($act == 'guest' ? 'гостей' : 'авторизованных') . '</div>';
$onltime = $realtime - 300;
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . ($act == 'guest' ? 'cms_guests' : 'users') . "` WHERE `lastdate` > '$onltime'"), 0);
if ($total)
{
    $req = mysql_query("SELECT * FROM `" . ($act == 'guest' ? 'cms_guests' : 'users') . "` WHERE `lastdate` > '$onltime' ORDER BY " . ($act == 'guest' ? "`movings` DESC" : "`name` ASC") . " LIMIT $start,$kmess");
    while ($res = mysql_fetch_assoc($req))
    {
        echo ($i % 2) ? '<div class="list2">' : '<div class="list1">';
        if ($act == 'guest')
        {
            echo '<b>Гость</b>';
        } else
        {
            echo '<img src="../theme/' . $set_user['skin'] . '/images/' . ($res['sex'] == 'm' ? 'm' : 'f') . ($res['datereg'] > $realtime - 86400 ? '_new.gif" width="20"' : '.gif" width="16"') . ' height="16"/>&nbsp;';
            echo ($user_id && $user_id != $res['id'] ? '<a href="anketa.php?user=' . $res['id'] . '"><b>' . $res['name'] . '</b>&nbsp;</a>' : '<b>' . $res['name'] . '</b>');
            // Метка должности
            if ($res['rights'])
            {
                $user_rights = array(1 => 'Kil', 3 => 'Mod', 6 => 'Smd', 7 => 'Adm', 8 => 'SV');
                echo ' (' . $user_rights[$res['rights']] . ')';
            }
        }
        $svr = $realtime - $res['sestime'];
        if ($svr >= "3600")
        {
            $hvr = ceil($svr / 3600) - 1;
            if ($hvr < 10)
            {
                $hvr = "0$hvr";
            }
            $svr1 = $svr - $hvr * 3600;
            $mvr = ceil($svr1 / 60) - 1;
            if ($mvr < 10)
            {
                $mvr = "0$mvr";
            }
            $ivr = $svr1 - $mvr * 60;
            if ($ivr < 10)
            {
                $ivr = "0$ivr";
            }
            if ($ivr == "60")
            {
                $ivr = "59";
            }
            $sitevr = "$hvr:$mvr:$ivr";
        } else
        {
            if ($svr >= "60")
            {
                $mvr = ceil($svr / 60) - 1;
                if ($mvr < 10)
                {
                    $mvr = "0$mvr";
                }
                $ivr = $svr - $mvr * 60;
                if ($ivr < 10)
                {
                    $ivr = "0$ivr";
                }
                if ($ivr == "60")
                {
                    $ivr = "59";
                }
                $sitevr = "00:$mvr:$ivr";
            } else
            {
                $ivr = $svr;
                if ($ivr < 10)
                {
                    $ivr = "0$ivr";
                }
                $sitevr = "00:00:$ivr";
            }
        }
        echo ' (' . $res['movings'] . ' - ' . $sitevr . ') ';
        if ($user_id)
        {
            $where = explode(",", $res['place']);
            switch ($where[0])
            {
                case 'forumfiles':
                    echo '<a href="../forum/index.php?act=files">Файлы форума</a>';
                    break;
                case 'forumwho':
                    echo '<a href="../forum/index.php?act=who">Смотрит кто в форуме?</a>';
                    break;
                case 'anketa':
                    echo '<a href="anketa.php">Анкета</a>';
                    break;
                case 'settings':
                    echo '<a href="usset.php">Настройки</a>';
                    break;
                case 'users':
                    echo '<a href="users.php">Список юзеров</a>';
                    break;
                case 'online':
                    echo 'Тут, в списке';
                    break;
                case 'privat':
                case 'pradd':
                    echo '<a href="../index.php?mod=cab">Приват</a>';
                    break;
                case 'birth':
                    echo '<a href="brd.php">Список именинников</a>';
                    break;
                case 'read':
                    echo '<a href="../read.php">Читает FAQ</a>';
                    break;
                case 'load':
                    echo '<a href="../download/index.php">Загрузки</a>';
                    break;
                case 'gallery':
                    echo '<a href="../gallery/index.php">Галерея</a>';
                    break;
                case 'forum':
                case 'forums':
                    echo '<a href="../forum/index.php">Форум</a>&nbsp;/&nbsp;<a href="../forum/index.php?act=who">&gt;&gt;</a>';
                    break;
                case 'chat':
                    echo '<a href="../chat/index.php">Чат</a>';
                    break;
                case 'guest':
                    echo '<a href="guest.php">Гостевая</a>';
                    break;
                case 'lib':
                    echo '<a href="../library/index.php">Библиотека</a>';
                    break;
                case 'mainpage':
                default:
                    echo '<a href="../index.php">Главная</a>';
                    break;
            }
            if ($act == 'guest' || $dostmod)
            {
                echo '<div class="sub"><u>UserAgent</u>:&nbsp;' . $res['browser'];
                if ($dostmod)
                    echo '<br /><u>IP Address</u>:&nbsp;' . long2ip($res['ip']);
                echo '</div>';
            }
        }
        echo '</div>';
        ++$i;
    }
} else
{
    echo '<div class="menu"><p>Никого нет</p></div>';
}
echo '<div class="phdr">Всего: ' . $total . '</div>';
if ($total > 10)
{
    echo '<p>' . pagenav('online.php?' . ($act == 'guest' ? 'act=guest&amp;' : ''), $start, $total, $kmess) . '</p>';
    echo '<p><form action="online.php" method="get"><input type="text" name="page" size="2"/>' . ($act == 'guest' ? '<input type="hidden" value="guest" name="act" />' : '') . '<input type="submit" value="К странице &gt;&gt;"/></form></p>';
}
if ($user_id)
    echo '<p><a href="online.php' . ($act == 'guest' ? '">Показать авторизованных' : '?act=guest">Показать гостей') . '</a></p>';
require_once ("../incfiles/end.php");

?>