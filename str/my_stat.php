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

$headmod = 'mystat';
$textl = 'Личная статистика';
require_once ('../incfiles/core.php');
require_once ('../incfiles/head.php');

if (!$user_id)
{
    header('Location: ../index.php');
    exit;
}

$user = $id ? $id : $user_id;
$req_u = mysql_query("SELECT * FROM `users` WHERE `id` = '$user' LIMIT 1");
if (mysql_num_rows($req_u))
{
    $res_u = mysql_fetch_assoc($req_u);
    switch ($act)
    {
        case 'go':
            // Переход к последнему посту
            $do = isset($_GET['do']) ? trim($_GET['do']):
            '';
            $doid = isset($_GET['doid']) ? abs(intval($_GET['doid'])):
            '';
            switch ($do)
            {
                case 'f':
                    // Переход на нужную страницу Форума
                    $set_forum = array();
                    $set_forum = unserialize($datauser['set_forum']);
                    if (empty($set_forum))
                        $set_forum['upfp'] = 0;
                    $req = mysql_query("SELECT * FROM `forum` WHERE `id` = '$doid' AND `type` = 'm' LIMIT 1");
                    if (mysql_num_rows($req))
                    {
                        $res = mysql_fetch_assoc($req);
                        $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '" . $doid . "'"), 0) / $kmess);
                        header('Location: ../forum/index.php?id=' . $res['refid'] . '&page=' . $page);
                    } else
                    {
                        header('Location: ../forum/index.php');
                    }
                    break;
                default:
                    header('Location: ../index.php');
            }
            break;

        case 'forum':
            echo '<p>Форум | <a href="my_stat.php?act=guest">Гостевая</a> | <a href="my_stat.php?act=chat">Чат</a> | <a href="my_stat.php?act=kom">Комментарии</a></p>';
            echo '<div class="phdr"><b>Последняя активность на Форуме</b></div>';
            if ($id)
                echo '<div class="gmenu">Пользователь: ' . $res_u['name'] . '</div>';
            $req = mysql_query("SELECT `refid`, MAX(time) FROM `forum` WHERE `user_id` = '$user' AND `type` = 'm' GROUP BY `refid` ORDER BY `time` DESC LIMIT 10");
            while ($res = mysql_fetch_assoc($req))
            {
                $arrid = $res['MAX(time)'];
                $arr[$arrid] = $res['refid'];
            }
            krsort($arr);
            foreach ($arr as  $key => $val)
            {
                $req_t = mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $val . "' AND `type` = 't' LIMIT 1");
                $res_t = mysql_fetch_assoc($req_t);
                $req_m = mysql_query("SELECT * FROM `forum` WHERE `refid` = '" . $val . "' AND `user_id` = '$user' AND`type` = 'm' ORDER BY `id` DESC LIMIT 1");
                $res_m = mysql_fetch_assoc($req_m);
                echo ($i % 2) ? '<div class="list2">' : '<div class="list1">';
                echo '<a href="my_stat.php?act=go&amp;do=f&amp;doid=' . $res_m['id'] . '">' . $res_t['text'] . '</a>';
                echo ' <span class="gray">(' . date("d.m.Y / H:i", $res_m['time'] + $set_user['sdvig'] * 3600) . ')</span>';
                $text = mb_substr($res_m['text'], 0, 500);
                $text = checkout($text, 2, 1);
                echo '<div class="sub">' . $text . '</div>';
                echo '</div>';
                ++$i;
            }
            echo '<div class="phdr"><a href="../forum/index.php">В Форум</a></div>';
            break;

        case 'guest':
            echo '<p><a href="my_stat.php?act=forum">Форум</a> | Гостевая | <a href="my_stat.php?act=chat">Чат</a> | <a href="my_stat.php?act=kom">Комментарии</a></p>';
            echo '<div class="phdr"><b>Последняя активность в Гостевой</b></div>';
            echo display_error('Данный модуль еще не готов :-)');
            echo '<div class="phdr"><a href="my_stat.php">Статистика</a></div>';
            break;

        case 'chat':
            echo '<p><a href="my_stat.php?act=forum">Форум</a> | <a href="my_stat.php?act=guest">Гостевая</a> | Чат | <a href="my_stat.php?act=kom">Комментарии</a></p>';
            echo '<div class="phdr"><b>Последняя активность в Чате</b></div>';
            echo display_error('Данный модуль еще не готов :-)');
            echo '<div class="phdr"><a href="my_stat.php">Статистика</a></div>';
            break;

        case 'kom':
            echo '<p><a href="my_stat.php?act=forum">Форум</a> | <a href="my_stat.php?act=guest">Гостевая</a> | <a href="my_stat.php?act=chat">Чат</a> | Комментарии</p>';
            echo '<div class="phdr"><b>Последняя активность в комментариях</b></div>';
            echo display_error('Данный модуль еще не готов :-)');
            echo '<div class="phdr"><a href="my_stat.php">Статистика</a></div>';
            break;

        default:
            echo '<div class="phdr"><b>Статистика</b></div>';
            if ($id)
                echo '<div class="gmenu">Пользователь: ' . $res_u['name'] . '</div>';
            echo '<div class="menu"><p><h3><img src="../images/rate.gif" width="16" height="16" class="left" />&nbsp;' . ($id ? 'А' : 'Моя а') . 'ктивность</h3><ul>';
            echo '<li>Сообщений в Форуме: ' . $res_u['postforum'] . '</li>';
            //TODO: Дописать статистику по гостевой
            echo '<li>Сообщений в Гостевой: ' . $res_u['postguest'] . '</li>';
            echo '<li>Сообщений в Чате: ' . $res_u['postchat'] . '</li>';
            echo '<li>Ответов в Викторине: ' . $res_u['otvetov'] . '</li>';
            echo '<li>Игровой баланс: ' . $res_u['balans'] . '</li>';
            echo '<li>Комментариев: ' . $res_u['komm'] . '</li>';
            echo '</ul></p></div>';
            // Если были нарушения, то показываем их
            if ($total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '$user'"), 0))
                echo '<div class="rmenu">Нарушения: <a href="anketa.php?act=ban&amp;user=' . $user . '">' . $total . '</a></div>';
            echo '<div class="phdr"><a href="my_stat.php?act=forum' . ($id ? '&amp;id=' . $id : '') . '">Последние записи</a></div>';
    }
} else
{
    echo display_error('Такого пользователя нет');
}
echo '<p><a href="users_top.php">Топ 10 активности</a><br /><a href="../index.php?mod=cab">В кабинет</a></p>';

require_once ('../incfiles/end.php');

?>