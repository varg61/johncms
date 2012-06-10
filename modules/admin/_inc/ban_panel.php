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

//TODO: Доработать под новый бан!

switch (Vars::$MOD) {
    case 'amnesty':
        if (Vars::$USER_RIGHTS < 9) {
            echo Functions::displayError(lng('amnesty_access_error'));
        } else {
            echo '<div class="phdr"><a href="index.php?act=ban_panel"><b>' . lng('ban_panel') . '</b></a> | ' . lng('amnesty') . '</div>';
            if (isset($_POST['submit'])) {
                $term = isset($_POST['term']) && $_POST['term'] == 1 ? 1 : 0;
                if ($term) {
                    // Очищаем таблицу Банов
                    mysql_query("TRUNCATE TABLE `cms_ban_users`");
                    echo '<div class="gmenu"><p>' . lng('amnesty_clean_confirm') . '</p></div>';
                } else {
                    // Разбаниваем активные Баны
                    $req = mysql_query("SELECT * FROM `cms_ban_users` WHERE `ban_time` > '" . time() . "'");
                    while ($res = mysql_fetch_array($req)) {
                        $ban_left = $res['ban_time'] - time();
                        if ($ban_left < 2592000) {
                            $amnesty_msg = 'Amnesty';
                            mysql_query("UPDATE `cms_ban_users` SET `ban_time`='" . time() . "', `ban_raz`='--$amnesty_msg--' WHERE `id` = '" . $res['id'] . "'");
                        }
                    }
                    echo '<div class="gmenu"><p>' . lng('amnesty_delban_confirm') . '</p></div>';
                }
            } else {
                echo '<form action="index.php?act=ban_panel&amp;mod=amnesty" method="post"><div class="menu"><p>' .
                     '<input type="radio" name="term" value="0" checked="checked" />&#160;' . lng('amnesty_delban') . '<br />' .
                     '<input type="radio" name="term" value="1" />&#160;' . lng('amnesty_clean') .
                     '</p><p><input type="submit" name="submit" value="' . lng('amnesty') . '" />' .
                     '</p></div></form>' .
                     '<div class="phdr"><small>' . lng('amnesty_help') . '</small></div>';
            }
            echo '<p><a href="index.php?act=ban_panel">' . lng('ban_panel') . '</a><br /><a href="index.php">' . lng('admin_panel') . '</a></p>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        БАН-панель, список нарушителей
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>' . lng('admin_panel') . '</b></a> | ' . lng('ban_panel') . '</div>';
        echo '<div class="topmenu"><span class="gray">' . lng('sorting') . ':</span> ';
        if (isset($_GET['count']))
            echo '<a href="index.php?act=ban_panel">' . lng('term') . '</a> | ' . lng('infringements') . '</div>';
        else
            echo lng('term') . ' | <a href="index.php?act=ban_panel&amp;count">' . lng('infringements') . '</a></div>';
        $sort = isset($_GET['count']) ? 'bancount' : 'bantime';
        $req = mysql_query("SELECT `user_id` FROM `cms_ban_users` GROUP BY `user_id`");
        $total = mysql_num_rows($req);
        $req = mysql_query("SELECT COUNT(`cms_ban_users`.`user_id`) AS `bancount`, MAX(`cms_ban_users`.`ban_time`) AS `bantime`, `cms_ban_users`.`id` AS `ban_id`, `users`.*
            FROM `cms_ban_users` LEFT JOIN `users` ON `cms_ban_users`.`user_id` = `users`.`id`
            GROUP BY `user_id`
            ORDER BY `$sort` DESC
            LIMIT " . Vars::db_pagination()
        );
        if (mysql_num_rows($req)) {
            while ($res = mysql_fetch_array($req)) {
                echo '<div class="' . ($res['bantime'] > time() ? 'r' : '') . 'menu">';
                $arg = array(
                    'header' => '<br />' . Functions::getIcon('block.png') . '&#160;<small><a href="../users/profile.php?act=ban&amp;user=' . $res['id'] . '">' . lng('infringements_history') . '</a> [' . $res['bancount'] . ']</small>'
                );
                echo Functions::displayUser($res, $arg);
                echo '</div>';
            }
        } else {
            echo '<div class="menu"><p>' . lng('list_empty') . '</p></div>';
        }
        echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=ban_panel&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            echo '<p><form action="index.php?act=ban_panel" method="post"><input type="text" name="page" size="2"/><input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
        }
        echo '<p>' . (Vars::$USER_RIGHTS == 9 && $total ? '<a href="index.php?act=ban_panel&amp;mod=amnesty">' . lng('amnesty') . '</a><br />' : '') . '<a href="index.php">' . lng('admin_panel') . '</a></p>';
}