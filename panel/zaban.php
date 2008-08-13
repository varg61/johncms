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

require_once ("../incfiles/core.php");
require_once ("../incfiles/head.php");

if ($dostkmod == 1)
{
    require_once ('../incfiles/ban.php');
    $id = isset($_GET['id']) ? intval($_GET['id']) : '';
    $do = isset($_GET['do']) ? $_GET['do'] : '';
    switch ($do)
    {
        case 'razban':
            ////////////////////////////////////////////////////////////
            // Снятие бана с сохранением истории нарушений            //
            ////////////////////////////////////////////////////////////
            if (!empty($id))
            {
                if (mysql_num_rows(mysql_query("SELECT * FROM `cms_ban_users` WHERE `id`='" . $id . "';")) != 1)
                {
                    echo '<p>Ошибка<br /><a href="main.php">В админку</a></p>';
                    require_once ("../incfiles/end.php");
                    exit;
                }
                if (isset($_GET['yes']))
                {
                    mysql_query("UPDATE `cms_ban_users` SET `ban_time`='" . $realtime . "', `ban_raz`='" . $login . "' WHERE `id`='" . $id . "';");
                    echo '<p>Действие Бана прекращено.<br /><a href="zaban.php">Бан-панель</a><br /><a href="main.php">В админку</a></p>';
                } else
                {
                    echo '<div class="phdr">Разбанить</div>';
                    echo '<p>1) Прекращается действие активного бана<br />2) Остается запись в истории нарушений.</p>';
                    echo '<p><b>Вы уверены?</b><br /><a href="zaban.php?do=razban&amp;id=' . $id . '&amp;yes=1">Разбанить</a><br /><a href="zaban.php?do=detail&amp;id=' . $id . '">Отмена</a></p>';
                }
            }
            break;

        case 'delban':
            ////////////////////////////////////////////////////////////
            // Снятие бана и удаление истории нарушений               //
            ////////////////////////////////////////////////////////////
            if (!empty($id))
            {
                if (mysql_num_rows(mysql_query("SELECT * FROM `cms_ban_users` WHERE `id`='" . $id . "';")) != 1)
                {
                    echo '<p>Ошибка<br /><a href="main.php">В админку</a></p>';
                    require_once ("../incfiles/end.php");
                    exit;
                }
                if (isset($_GET['yes']))
                {
                    mysql_query("DELETE FROM `cms_ban_users` WHERE `id`='" . $id . "' LIMIT 1;");
                    echo '<p>Бан удален.<br /><a href="zaban.php">Бан-панель</a><br /><a href="main.php">В админку</a></p>';
                } else
                {
                    echo '<div class="phdr">Удалить Бан</div>';
                    echo '<p>1) Активный бан удаляется<br />2) Удаляется текущая запись из истории нарушений.</p>';
                    echo '<p><b>Вы уверены?</b><br /><a href="zaban.php?do=delban&amp;id=' . $id . '&amp;yes=1">Удалить Бан</a><br /><a href="zaban.php?do=detail&amp;id=' . $id . '">Отмена</a></p>';
                }
            }
            break;

        case 'detail':
            ////////////////////////////////////////////////////////////
            // Детали отдельного бана                                 //
            ////////////////////////////////////////////////////////////
            $req = mysql_query("SELECT `cms_ban_users`.*, `users`.`name`, `users`.`name_lat`
			FROM `cms_ban_users` LEFT JOIN `users` ON `cms_ban_users`.`user_id` = `users`.`id`
			WHERE `cms_ban_users`.`id`='" . $id . "';");
            if (mysql_num_rows($req) != 0)
            {
                $res = mysql_fetch_array($req);
                echo '<div class="phdr">Бан детально</div>';
                echo '<div class="menu">Ник: <a href="../str/anketa.php?user=' . $res['user_id'] . '"><b>' . $res['name'] . '</b></a></div>';
                echo '<div class="menu">Тип бана: <b>' . $ban_term[$res['ban_type']] . '</b><br />';
                echo $ban_desc[$res['ban_type']] . '</div>';
                echo '<div class="menu">Забанил: ' . $res['ban_who'] . '</div>';
                echo '<div class="menu">Когда: ' . gmdate('d.m.Y, H:i:s', $res['ban_while']) . '</div>';
                echo '<div class="menu">Срок: ' . timecount($res['ban_time'] - $res['ban_while']) . '</div>';
                echo '<div class="menu">Причина: ' . $res['ban_reason'] . '</div>';
                $estimate = $res['ban_time'] - $realtime;
                echo '<div class="bmenu">Осталось: ' . timecount($estimate) . '</div><p>';
                echo '<a href="zaban.php?do=razban&amp;id=' . $id . '">Разбанить</a>';
                if ($dostadm == 1)
                    echo '<br /><a href="zaban.php?do=delban&amp;id=' . $id . '">Удалить бан</a>';
                echo '</p><p><a href="zaban.php">Назад</a><br /><a href="">В админку</a></p>';
            } else
            {
                echo 'Ошибка';
                require_once ("../incfiles/end.php");
                exit;
            }
            break;

        default:
            ////////////////////////////////////////////////////////////
            // Список забаненных                                      //
            ////////////////////////////////////////////////////////////
            echo '<div class="phdr">Кто в бане?</div>';
            $req = mysql_query("SELECT `cms_ban_users`.*, `users`.`name`
			FROM `cms_ban_users` LEFT JOIN `users` ON `cms_ban_users`.`user_id` = `users`.`id`
			WHERE `cms_ban_users`.`ban_time`>'" . $realtime . "';");
            $total = @mysql_num_rows($req);
            $page = (isset($_GET['page']) && ($_GET['page'] > 0)) ? intval($_GET['page']):
            1;
            $start = $page * $kmess - $kmess;
            if ($total < $start + $kmess)
            {
                $end = $total;
            } else
            {
                $end = $start + $kmess;
            }
            if ($total != 0)
            {
                // Выводим общий список забаненных
                while ($res = mysql_fetch_array($req))
                {
                    echo '<div class="menu"><a href="zaban.php?do=detail&amp;id=' . $res['id'] . '"><b>' . $res['name'] . '</b></a>&nbsp;';
                    echo $ban_term[$res['ban_type']];
                    echo '</div>';
                }
                echo '<div class="bmenu">Всего: ' . $total . '</div>';
                if ($total > $kmess)
                {
                    echo '<p>';
                    $pagenav = array('address' => 'ipban.php?', 'total' => $total, 'numpr' => $kmess, 'page' => $page);
                    pagenav($pagenav);
                    echo '</p>';
                }
            } else
            {
                echo '<p>Список пуст</p>';
            }
            echo '<p><a href="">Банить</a><br /><a href="">Амнистия</a></p>';
            echo '<p><a href="main.php">В админку</a></p>';
    }
} else
{
    header("Location: ../index.php?mod=404");
}

require_once ("../incfiles/end.php");

?>