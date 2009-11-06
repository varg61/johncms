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
            $req = mysql_query("SELECT `id`, `name`, `rights`, `sex`, `datereg` FROM `users` WHERE `lastdate` > $onltime AND `place` = 'forum,$id' ORDER BY `name` LIMIT $start, $kmess");
            while ($res = mysql_fetch_assoc($req))
            {
                echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                echo '<img src="../theme/' . $skin . '/images/' . ($res['sex'] == 'm' ? 'm' : 'f') . ($res['datereg'] > $realtime - 86400 ? '_new.gif" width="20"' : '.gif" width="16"') . ' height="16"/>&nbsp;';
                echo ($user_id && $user_id != $res['id'] ? '<a href="../str/anketa.php?user=' . $res['id'] . '"><b>' . $res['name'] . '</b></a>' : '<b>' . $res['name'] . '</b>');
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
        $req = mysql_query("SELECT `id`, `name`, `rights`, `sex`, `datereg`, `place` FROM `users` WHERE `lastdate` > $onltime AND `place` LIKE 'forum%' ORDER BY `name` LIMIT $start, $kmess");
        while ($res = mysql_fetch_assoc($req))
        {
            echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
            echo '<img src="../theme/' . $skin . '/images/' . ($res['sex'] == 'm' ? 'm' : 'f') . ($res['datereg'] > $realtime - 86400 ? '_new.gif" width="20"' : '.gif" width="16"') . ' height="16"/>&nbsp;';
            echo ($user_id && $user_id != $res['id'] ? '<a href="../str/anketa.php?user=' . $res['id'] . '"><b>' . $res['name'] . '</b></a>' : '<b>' . $res['name'] . '</b>');
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
            echo '<br />';

            // Вычисляем местоположение
            $place = explode(",", $res['place']);
            if ($res['place'] == 'forum')
                echo '<a href="index.php">На главной форума</a>';
            elseif ($res['place'] == 'forumwho')
                echo 'Тут, в списке';
            elseif ($res['place'] == 'forumfiles')
                echo '<a href="index.php?act=files">Файлы форума</a>';
            elseif ($place[0] == 'forum' && intval($place[1]))
            {
                $req_t = mysql_query("SELECT `type`, `refid`, `text` FROM `forum` WHERE `id` = '$place[1]' LIMIT 1");
                if (mysql_num_rows($req_t))
                {
                    $res_t = mysql_fetch_assoc($req_t);
                    if ($res_t['type'] == 'f')
                        echo 'В категории: <a href="index.php?id=' . $place[1] . '">' . $res_t['text'] . '</a>';
                    if ($res_t['type'] == 'r')
                        echo 'В разделе: <a href="index.php?id=' . $place[1] . '">' . $res_t['text'] . '</a>';
                    if ($res_t['type'] == 't')
                        echo 'В теме: <a href="index.php?id=' . $place[1] . '">' . $res_t['text'] . '</a>';
                    if ($res_t['type'] == 'm')
                    {
                        $req_m = mysql_query("SELECT `text` FROM `forum` WHERE `id` = '" . $res_t['refid'] . "' AND `type` = 't' LIMIT 1");
                        if (mysql_num_rows($req_m))
                        {
                            $res_m = mysql_fetch_assoc($req_m);
                            echo 'В теме: <a href="index.php?id=' . $res_t['refid'] . '">' . $res_m['text'] . '</a>';
                        }
                    }
                }
            }
            echo '</div>';
            ++$i;
        }
    } else
    {
        echo '<div class="menu"><p>В форуме никого нет</p></div>';
    }
    echo '<div class="phdr">Всего: ' . $total . '</div>';
    echo '<p><a href="index.php">В форум</a></p>';
}

require_once ("../incfiles/end.php");

?>