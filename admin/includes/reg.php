<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNADM') or die('Error: restricted access');

//TODO: Доработать под новую таблицу юзеров

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 6) {
    header('Location: http://mobicms.net/404.php');
    exit;
}

echo '<div class="phdr"><a href="index.php"><b>' . $lng['admin_panel'] . '</b></a> | ' . $lng['reg_approve'] . '</div>';
switch (Vars::$MOD) {
    case 'approve':
        /*
        -----------------------------------------------------------------
        Подтверждаем регистрацию выбранного пользователя
        -----------------------------------------------------------------
        */
        if (!Vars::$ID) {
            echo Functions::displayError($lng['error_wrong_data']);
            require_once('../includes/end.php');
            exit;
        }
        //TODO: Доработать под новые таблицы!
        @mysql_query("UPDATE `users` SET `preg` = '1', `regadm` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "' WHERE `id` = " . Vars::$ID);
        echo '<div class="menu"><p>' . $lng['reg_approved'] . '<br /><a href="index.php?act=reg">' . $lng['continue'] . '</a></p></div>';
        break;

    case 'massapprove':
        /*
        -----------------------------------------------------------------
        Подтверждение всех регистраций
        -----------------------------------------------------------------
        */
        mysql_query("UPDATE `users` SET `preg` = '1', `regadm` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "' WHERE `preg` = '0'");
        echo '<div class="menu"><p>' . $lng['reg_approved'] . '<br /><a href="index.php?act=reg">' . $lng['continue'] . '</a></p></div>';
        break;

    case 'del':
        /*
        -----------------------------------------------------------------
        Удаляем выбранного пользователя
        -----------------------------------------------------------------
        */
        if (!Vars::$ID) {
            echo Functions::displayError($lng['error_wrong_data']);
            require_once('../includes/end.php');
            exit;
        }
        $req = mysql_query("SELECT `id` FROM `users` WHERE `id` = " . Vars::$ID . " AND `level` = '0'");
        if (mysql_num_rows($req)) {
            mysql_query("DELETE FROM `users` WHERE `id` = " . Vars::$ID);
            mysql_query("DELETE FROM `cms_users_iphistory` WHERE `user_id` = " . Vars::$ID . " LIMIT 1");
        }
        echo '<div class="menu"><p>' . $lng['user_deleted'] . '<br /><a href="index.php?act=reg">' . $lng['continue'] . '</a></p></div>';
        break;

    case 'massdel':
        /*
        -----------------------------------------------------------------
        Удаление всех регистраций
        -----------------------------------------------------------------
        */
        $req = mysql_query("SELECT `id` FROM `users` WHERE `preg` = '0'");
        while ($res = mysql_fetch_assoc($req)) {
            mysql_query("DELETE FROM `cms_users_iphistory` WHERE `user_id` = '" . $res['id'] . "'");
        }
        mysql_query("DELETE FROM `users` WHERE `preg` = '0'");
        mysql_query("OPTIMIZE TABLE `cms_users_iphistory` , `users`");
        echo '<div class="menu"><p>' . $lng['reg_deleted_all'] . '<br /><a href="index.php?act=reg">' . $lng['continue'] . '</a></p></div>';
        break;

    case 'delip':
        /*
        -----------------------------------------------------------------
        Удаляем все регистрации с заданным адресом IP
        -----------------------------------------------------------------
        */
        $ip = isset($_GET['ip']) ? intval($_GET['ip']) : false;
        if ($ip) {
            $req = mysql_query("SELECT `id` FROM `users` WHERE `preg` = '0' AND `ip` = '$ip'");
            while ($res = mysql_fetch_assoc($req)) {
                mysql_query("DELETE FROM `cms_users_iphistory` WHERE `user_id` = '" . $res['id'] . "'");
            }
            mysql_query("DELETE FROM `users` WHERE `preg` = '0' AND `ip` = '$ip'");
            mysql_query("OPTIMIZE TABLE `cms_users_iphistory` , `users`");
            echo'<div class="menu"><p>' . $lng['reg_del_ip_done'] . '<br />' .
                '<a href="index.php?act=reg">' . $lng['continue'] . '</a></p></div>';
        } else {
            echo Functions::displayError($lng['error_wrong_data']);
            require_once('../includes/end.php');
            exit;
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Выводим список пользователей, ожидающих подтверждения регистрации
        -----------------------------------------------------------------
        */
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `preg` = '0'"), 0);
        if ($total > Vars::$USER_SET['page_size']) echo'<div class="topmenu">' . Functions::displayPagination('index.php?act=reg&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        if ($total) {
            $req = mysql_query("SELECT * FROM `users` WHERE `preg` = '0' ORDER BY `id` DESC LIMIT " . Vars::db_pagination());
            $i = 0;
            while (($res = mysql_fetch_assoc($req)) !== false) {
                $link = array(
                    '<a href="index.php?act=reg&amp;mod=approve&amp;id=' . $res['id'] . '">' . $lng['approve'] . '</a>',
                    '<a href="index.php?act=reg&amp;mod=del&amp;id=' . $res['id'] . '">' . $lng['delete'] . '</a>',
                    '<a href="index.php?act=reg&amp;mod=delip&amp;ip=' . $res['ip'] . '">' . $lng['reg_del_ip'] . '</a>'
                );
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                echo Functions::displayUser($res, array(
                    'header' => '<b>ID:' . $res['id'] . '</b>',
                    'sub' => Functions::displayMenu($link)
                ));
                echo '</div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
        }
        echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo'<div class="topmenu">' . Functions::displayPagination('index.php?act=reg&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                '<p><form action="index.php?act=reg" method="post">' .
                '<input type="text" name="page" size="2"/>' .
                '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
                '</form></p>';
        }
        echo '<p>';
        if ($total)
            echo '<a href="index.php?act=reg&amp;mod=massapprove">' . $lng['reg_approve_all'] . '</a><br /><a href="index.php?act=reg&amp;mod=massdel">' . $lng['reg_del_all'] . '</a><br />';
        echo '<a href="index.php">' . $lng['admin_panel'] . '</a></p>';
}