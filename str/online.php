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
session_name("SESID");
session_start();
$headmod = 'online';
$textl = 'Онлайн';
require_once ("../incfiles/core.php");
require_once ("../incfiles/head.php");
echo '<div class="phdr"><b>Кто в онлайне?</b></div>';
$onltime = $realtime - 300;
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate` > '$onltime'"), 0);
$req = mysql_query("SELECT * FROM `users` WHERE `lastdate` > '" . intval($onltime) . "' ORDER BY `name` LIMIT " . $start . "," . $kmess);
while ($res = mysql_fetch_array($req))
{
    echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
    echo $res['datereg'] > $realtime - 86400 ? '<img src="../images/add.gif" alt=""/>&nbsp;' : '';
    echo '<img src="../theme/' . $skin . '/images/' . ($res['sex'] == 'm' ? 'm' : 'f') . '.gif" alt=""  width="16" height="16"/>&nbsp;';
    echo ($user_id && $user_id != $res['id'] ? '<a href="anketa.php?user=' . $res['id'] . '"><b>' . $res['name'] . '</b></a>' : '<b>' . $res['name'] . '</b>');
    switch ($res['rights'])
    {
        case 7:
            echo ' (Adm)';
            break;
        case 6:
            echo ' (Smd)';
            break;
        case 5:
        case 4:
        case 3:
        case 2:
            echo ' (Mod)';
            break;
        case 1:
            echo ' (Kil)';
            break;
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
    $prh = mysql_result(mysql_query("SELECT COUNT(*) FROM `count` WHERE `time` > '" . $res['sestime'] . "' AND `name` = '" . $res['name'] . "'"), 0);
    echo ' (' . $prh . ' - ' . $sitevr . ') ';
    if ($user_id)
    {
        //echo "Где: ";
        $wh = mysql_query("SELECT * FROM `count` WHERE `name` = '" . $res['name'] . "' ORDER BY `time` DESC LIMIT 1");
        $wh1 = mysql_fetch_array($wh);
        $wher = $wh1['where'];
        $wher1 = explode(",", $wher);
        $where = $wher1[0];
        switch ($where)
        {
            case 'forumfiles':
                echo '<a href="../forum/index.php?act=files">Файлы форума</a>';
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
                echo '<a href="privat.php">Приват</a>';
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
                echo '<a href="../forum/index.php">Форум</a>';
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
    }
    if ($dostmod == 1)
    {
        echo '<div class="sub"><u>UserAgent</u>: ' . $res['browser'] . '<br /><u>IP Address</u>: ' . long2ip($res['ip']) . '</div>';
    }

    echo '</div>';
    ++$i;
}
echo '<div class="phdr">Всего он-лайн: ' . $total . '</div>';
if ($total > 10)
{
    echo '<p>' . pagenav('online.php?', $start, $total, $kmess) . '</p>';
    echo '<p><form action="online.php" method="get"><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
}

require_once ("../incfiles/end.php");

?>