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

if ($rights != 9)
    die('Error: restricted access');

switch ($mod) {
    case 'add':
        /*
        -----------------------------------------------------------------
        Добавляем нового оператора в базу,
        обрабатываем имеющиеся адреса, попадающие в диапазон оператора
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php?act=sys_ipop"><b>Операторы</b></a> | Добавить</div>';
        if (isset($_POST['submit'])) {
            $error = array ();
            $name = isset($_POST['name']) ? check($_POST['name']) : '';
            $start_ip = isset($_POST['startip']) ? trim($_POST['startip']) : '';
            $end_ip = isset($_POST['startip']) ? trim($_POST['endip']) : '';
            // Проверка правильности заполнения формы
            if (empty($name) || empty($start_ip) || empty($end_ip))
                $error[] = 'Нужно заполнить все поля формы';
            if (mb_strlen($name) < 2)
                $error[] = 'Длина названия минимум 2 символа';
            if (!ip_valid($start_ip))
                $error[] = 'Начальный адрес IP введен неверно';
            if (!ip_valid($end_ip))
                $error[] = 'Конечный адрес IP введен неверно';
            if (!$error) {
                // Проверка на конфликт диапазона адресов
                $start_ip = ip2long($start_ip);
                $end_ip = ip2long($end_ip);
                $req = mysql_query("SELECT * FROM `cms_operators` WHERE ('$start_ip' BETWEEN `ip_min` AND `ip_max`) OR ('$end_ip' BETWEEN `ip_min` AND `ip_max`) OR (`ip_min` >= '$start_ip' AND `ip_max` <= '$end_ip')");
                $total = mysql_num_rows($req);
                if ($total > 0) {
                    echo '<div class="rmenu"><p>Введенный Вами диапазон, конфликтует с находящимися в базе IP адресами</p></div>';
                    while ($res = mysql_fetch_array($req)) {
                        $ip_total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_users_iphistory` WHERE `user_ip` BETWEEN " . $res['ip_min'] . " AND " . $res['ip_max']), 0);
                        $ip_min = long2ip($res['ip_min']);
                        $ip_max = long2ip($res['ip_max']);
                        $link = 'index.php?act=usr_search_ip&amp;search=' . rawurlencode($ip_min . '-' . $ip_max);
                        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                        echo '<b>' . checkout($res['name']) . '</b> [&nbsp;<a href="' . $link . '">' . $ip_total . '</a>&nbsp;]' .
                            '<div class="sub">' .
                            '<b>' . $ip_min . '</b> - <b>' . $ip_max . '</b><br />' .
                            '<a href="index.php?act=sys_ipop&amp;mod=edit&amp;id=' . $res['id'] . '">Изменить</a> | <a href="index.php?act=sys_ipop&amp;mod=del&amp;id=' . $res['id'] . '">Удалить</a>' .
                            '</div></div>';
                        ++$i;
                    }
                    echo '<div class="phdr">Всего: ' . $total . '</div>';
                    echo '<p><a href="index.php?act=sys_ipop">Адреса операторов</a><br /><a href="index.php">Админ панель</a></p>';
                    require_once('../incfiles/end.php');
                    exit;
                }
            }
            if (!$error) {
                // Конвертируем имеющиеся IP адреса
                //TODO: Написать конвертер адресов
                // Если нет ошибок, то добавляем оператора в базу
                mysql_query("INSERT INTO `cms_operators` SET `name` = '$name', `ip_min` = '$start_ip', `ip_max` = '$end_ip'");
            } else {
                // Если были ошибки, выводим сообщение
                echo display_error($error, '<a href="index.php?act=sys_ipop&amp;mod=add">Назад</a>');
            }
        } else {
            echo '<div class="gmenu"><form action="index.php?act=sys_ipop&amp;mod=add" method="post"><p>';
            echo '<h3>Название</h3>&nbsp;<input type="text" name="name" /><br />&nbsp;<small>Макс. 50 символов</small>';
            echo '</p><p><h3>Диапазон IP</h3>&nbsp;<small>Начальный адрес</small><br />&nbsp;<input type="text" name="startip" />';
            echo '<br />&nbsp;<small>Конечный адрес</small><br />&nbsp;<input type="text" name="endip" />';
            echo '</p><p>&nbsp;<input type="submit" name="submit" value="Добавить" />';
            echo '</p></form></div>';
        }
        echo '<div class="phdr"><a href="index.php?act=sys_ipop">Отмена</a></div>';
        break;

    case 'del':
        /*
        -----------------------------------------------------------------
        Удаляем оператора из базы,
        очищаем имеющиеся ссылки на данного оператора
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php?act=sys_ipop"><b>Операторы</b></a> | Удалить</div>';
        $error = false;
        if (!$id)
            $error = 'Неверные данные';
        else {
            $req = mysql_query("SELECT * FROM `cms_operators` WHERE `id` = '$id' LIMIT 1");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                $ip_total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_users_iphistory` WHERE `user_ip` BETWEEN " . $res['ip_min'] . " AND " . $res['ip_max']), 0);
                $ip_min = long2ip($res['ip_min']);
                $ip_max = long2ip($res['ip_max']);
                if (isset($_POST['submit'])) {
                    // Удаляем связанные записи из истории адресов
                    //TODO: Написать удаление связанных адресов
                    // Удаляем оператора из базы
                    mysql_query("DELETE FROM `cms_operators` WHERE `id` = '$id' LIMIT 1");
                    header('Location: index.php?act=sys_ipop');
                }
                else {
                    // Подтверждение удаления оператора
                    $link = 'index.php?act=usr_search_ip&amp;search=' . rawurlencode($ip_min . '-' . $ip_max);
                    echo '<div class="list2">';
                    echo '<b>' . checkout($res['name']) . '</b> [&nbsp;<a href="' . $link . '">' . $ip_total . '</a>&nbsp;]' .
                        '<div class="sub">' .
                        '<b>' . $ip_min . '</b> - <b>' . $ip_max . '</b><br />' .
                        '</div></div>';
                    echo '<div class="rmenu"><p>ВНИМАНИЕ!<br />Вы действительно хотите удалить данного оператора?';
                    if ($ip_total)
                        echo '<br />Вместе с ним удалятся все связанные записи в истории IP (' . $ip_total . ' адресов)';
                    echo '</p><form action="index.php?act=sys_ipop&amp;mod=del&amp;id=' . $id . '" method="post">' .
                        '<p><input type="submit" name="submit" value="Удалить" /></p>' .
                        '</form></div>';
                }
            } else {
                $error = 'Неверные данные';
            }
        }
        if ($error)
            echo display_error($error);
        echo '<div class="phdr"><a href="index.php?act=sys_ipop">Отмена</a></div>';
        break;

    case 'edit':
        /*
        -----------------------------------------------------------------
        Редактируем данные оператора,
        если менялся диапазон адресов, то обрабатываем связанные записи
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php?act=sys_ipop"><b>Операторы</b></a> | Редактировать</div>';
        break;

    case 'search':
        /*
        -----------------------------------------------------------------
        Поиск оператора по IP адресу, попадающему в его диапазон
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php?act=sys_ipop"><b>Операторы</b></a> | Поиск</div>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Выводим список операторов
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>Админ панель</b></a> | Операторы</div>';
        echo '<div class="topmenu"><a href="index.php?act=sys_ipop&amp;mod=add">Добавить</a> | <a href="index.php?act=sys_ipop&amp;mod=search">Поиск по IP</a></div>';
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_operators`"), 0);
        if ($total) {
            $req = mysql_query("SELECT * FROM `cms_operators` ORDER BY `name` ASC LIMIT $start,$kmess");
            while ($res = mysql_fetch_assoc($req)) {
                $ip_total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_users_iphistory` WHERE `user_ip` BETWEEN " . $res['ip_min'] . " AND " . $res['ip_max']), 0);
                $ip_min = long2ip($res['ip_min']);
                $ip_max = long2ip($res['ip_max']);
                $link = 'index.php?act=usr_search_ip&amp;search=' . rawurlencode($ip_min . '-' . $ip_max);
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                echo '<b>' . checkout($res['name']) . '</b> [&nbsp;<a href="' . $link . '">' . $ip_total . '</a>&nbsp;]' .
                    '<div class="sub">' .
                    '<b>' . $ip_min . '</b> - <b>' . $ip_max . '</b><br />' .
                    '<a href="index.php?act=sys_ipop&amp;mod=edit&amp;id=' . $res['id'] . '">Изменить</a> | <a href="index.php?act=sys_ipop&amp;mod=del&amp;id=' . $res['id'] . '">Удалить</a>' .
                    '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>Записей нет</p></div>';
        }
        echo '<div class="phdr">Всего: ' . $total . '</div>';
}

echo '<p><a href="index.php">Админ панель</a></p>';

?>