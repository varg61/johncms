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

defined('_IN_JOHNCMS') or die('Error: restricted access');

$textl = 'Кто в форуме?';
$headmod = $id ? 'forum,' . $id : 'forumwho';
require_once ("../incfiles/head.php");
$onltime = $realtime - 300;

if (!$user_id)
{
    header('Location: index.php');
    exit;
}

$do = isset($_GET['do']) ? $_GET['do'] : '';

if ($id)
{
    ////////////////////////////////////////////////////////////
    // Показываем общий список тех, кто в вбранной теме       //
    ////////////////////////////////////////////////////////////
    $req = mysql_query("SELECT `text` FROM `forum` WHERE `id` = '$id' AND `type` = 't' LIMIT 1");
    if (mysql_num_rows($req))
    {
        $res = mysql_fetch_assoc($req);
        echo '<div class="phdr"><b>Кто в теме</b> &quot;' . $res['text'] . '&quot;</div>';
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate` > $onltime AND `place` = 'forum,$id'"), 0);
        if ($total)
        {
            $req = mysql_query("SELECT * FROM `users` WHERE `lastdate` > $onltime AND `place` = 'forum,$id' ORDER BY `name` LIMIT $start, $kmess");
            while ($res = mysql_fetch_assoc($req))
            {
                echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                echo '<img src="../theme/' . $skin . '/images/' . ($res['sex'] == 'm' ? 'm' : 'f') . ($res['datereg'] > $realtime - 86400 ? '_new.gif" width="20"' : '.gif" width="16"') . ' height="16"/>&nbsp;';
                echo ($user_id && $user_id != $res['id'] ? '<a href="../str/anketa.php?user=' . $res['id'] . '"><b>' . $res['name'] . '</b></a>' : '<b>' . $res['name'] . '</b>');
                // Метка должности
                $user_rights = array(1 => 'Kil', 3 => 'Mod', 6 => 'Smd', 7 => 'Adm', 8 => 'SV');
                echo $user_rights[$res['rights']];
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
                echo '</div>';
                ++$i;
            }
        }
    } else
    {
        header('Location: index.php');
    }
    echo '<div class="phdr">В теме ' . $total . ' человек</div>';
    echo '<p><a href="index.php?id=' . $id . '">В тему</a></p>';
} else
{
    ////////////////////////////////////////////////////////////
    // Показываем общий список тех, кто в форуме              //
    ////////////////////////////////////////////////////////////
    echo '<div class="phdr"><b>Кто в форуме</b></div>';
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate` > $onltime AND `place` LIKE 'forum%'"), 0);
    if ($total)
    {
        $req = mysql_query("SELECT * FROM `users` WHERE `lastdate` > $onltime AND `place` LIKE 'forum%' ORDER BY `name` LIMIT $start, $kmess");
        while ($res = mysql_fetch_assoc($req))
        {
            echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
            echo '<img src="../theme/' . $skin . '/images/' . ($res['sex'] == 'm' ? 'm' : 'f') . ($res['datereg'] > $realtime - 86400 ? '_new.gif" width="20"' : '.gif" width="16"') . ' height="16"/>&nbsp;';
            echo ($user_id && $user_id != $res['id'] ? '<a href="../str/anketa.php?user=' . $res['id'] . '"><b>' . $res['name'] . '</b></a>' : '<b>' . $res['name'] . '</b> ');
            // Метка должности
            if ($res['rights'])
            {
                $user_rights = array(1 => 'Kil', 3 => 'Mod', 6 => 'Smd', 7 => 'Adm', 8 => 'SV');
                echo ' (' . $user_rights[$res['rights']] . ')';
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
            // Вычисляем местоположение
            $place = '';
            switch ($res['place'])
            {
                case 'forum':
                    $place = '<a href="index.php">на главной форума</a>';
                    break;
                case 'forumwho':
                    $place = 'тут, в списке';
                    break;
                case 'forumfiles':
                    $place = '<a href="index.php?act=files">смотрит файлы форума</a>';
                    break;
                case 'forumnew':
                    $place = '<a href="index.php?act=new">смотрит непрочитанное</a>';
                    break;
                default:
                    $where = explode(",", $res['place']);
                    if ($where[0] == 'forum' && intval($where[1]))
                    {
                        $req_t = mysql_query("SELECT `type`, `refid`, `text` FROM `forum` WHERE `id` = '$where[1]' LIMIT 1");
                        if (mysql_num_rows($req_t))
                        {
                            $res_t = mysql_fetch_assoc($req_t);
                            $theme = mb_substr($res_t['text'], 0, 40);
                            $link = '<a href="index.php?id=' . $where[1] . '">' . $theme . '</a>';
                            switch ($res_t['type'])
                            {
                                case 'f':
                                    $place = 'в категории &quot;' . $link . '&quot;';
                                    break;
                                case 'r':
                                    $place = 'в разделе &quot;' . $link . '&quot;';
                                    break;
                                case 't':
                                    $place = (isset($where[2]) ? 'пишет в тему &quot;' : 'в теме &quot;') . $link . '&quot;';
                                    break;
                                case 'm':
                                    $req_m = mysql_query("SELECT `text` FROM `forum` WHERE `id` = '" . $res_t['refid'] . "' AND `type` = 't' LIMIT 1");
                                    if (mysql_num_rows($req_m))
                                    {
                                        $res_m = mysql_fetch_assoc($req_m);
                                        $theme = mb_substr($res_m['text'], 0, 40);
                                        $place = (isset($where[2]) ? 'отвечает в теме' : 'в теме') . ' &quot;<a href="index.php?id=' . $res_t['refid'] . '">' . $theme . '</a>&quot;';
                                    }
                                    break;
                            }
                        }
                    }
            }
            echo '<div class="sub"><u>Находится</u>: ' . $place . '</div>';
            echo '</div>';
            ++$i;
        }
    } else
    {
        echo '<div class="menu"><p>В форуме никого нет</p></div>';
    }
    echo '<div class="phdr">Всего: ' . $total . '</div>';
    echo '<p><a href="index.php?act=who' . ($do == 'guest' ? '">Показать авторизованных' : '&amp;do=guest">Показать гостей') . '</a><br /><a href="index.php">В форум</a></p>';
}

require_once ("../incfiles/end.php");

?>