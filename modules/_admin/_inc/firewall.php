<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_ADMIN') or die('Error: restricted access');
$url = Router::getUri(3);
$backurl = Router::getUri(2);

$tpl = Template::getInstance();
$firewall = new Firewall();

$mod = Vars::$MOD == 'white' ? 'white' : 'black';
$color = Vars::$MOD == 'white' ? 'green' : 'red';

switch (Vars::$ACT) {
    case 'add':
        // Нижний блок с подсказками
        echo'<div class="phdr"><a href="' . $url . (Vars::$MOD == 'black' ? '' : '?mod=white') . '">' . __('back') . '</a></div>' .
            '<div class="topmenu"><p>' .
            (Vars::$MOD == 'black'
                ? '<strong>' . mb_strtoupper(__('black_list')) . ':</strong> ' . __('black_list_help')
                : '<strong>' . mb_strtoupper(__('white_list')) . ':</strong> ' . __('white_list_help')
            ) .
            '</p>' . (isset($_POST['submit']) ? '' : '<p>' . __('add_ip_help') . '</p>') .
            '</div>' .
            '<p><a href="' . $backurl . '">' . __('admin_panel') . '</a></p>';
        break;

    case 'clear':
        /*
        -----------------------------------------------------------------
        Очищаем все адреса выбранного списка
        -----------------------------------------------------------------
        */

        break;

    case 'del':
        /*
        -----------------------------------------------------------------
        Удаляем выбранные адреса IP
        -----------------------------------------------------------------
        */

        break;

    default:
        /*
        -----------------------------------------------------------------
        Главное меню модуля
        -----------------------------------------------------------------
        */
        $menu = array(
            ($mod != 'white' ? '<strong>' . __('black_list') . '</strong>' : '<a href="' . $url . '?act=firewall">' . __('black_list') . '</a>'),
            ($mod == 'white' ? '<strong>' . __('white_list') . '</strong>' : '<a href="' . $url . '?act=firewall&amp;mod=white">' . __('white_list') . '</a>')
        );
        echo'<div class="phdr"><a href="' . $backurl . '"><b>' . __('admin_panel') . '</b></a> | ' . __('ip_accesslist') . '</div>' .
            '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';

        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ip_bwlist` WHERE `mode` = '" . $mod . "'"), 0);
        Vars::fixPage($total);

        if ($total > Vars::$USER_SET['page_size']) {
            echo'<div class="topmenu">' . Functions::displayPagination($url . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        }

        // Выводим список IP
        echo'<form action="' . $url . '?act=add&amp;mod=' . $mod . '" method="post">' .
            '<div class="' . ($mod == 'white' ? 'gmenu' : 'rmenu') . '"><input type="submit" name="delete" value="' . __('add') . '"/></div></form>';
        if ($total) {
            echo '<form action="' . $url . '?act=del&amp;mod=' . $mod . '" method="post">';
            $req = mysql_query("SELECT `cms_ip_bwlist`.*, `users`.`nickname`
                FROM `cms_ip_bwlist` LEFT JOIN `users` ON `cms_ip_bwlist`.`user_id` = `users`.`id`
                WHERE `cms_ip_bwlist`.`mode` = '" . $mod . "'
                ORDER BY `cms_ip_bwlist`.`timestamp` DESC
                " . Vars::db_pagination()
            );
            for ($i = 0; ($res = mysql_fetch_assoc($req)) !== FALSE; ++$i) {
                echo($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                    '<input type="checkbox" name="del[]" value="' . $res['ip'] . '"/>&#160;' .
                    '<strong>IP: <span class="' . $color . '">' . long2ip($res['ip']) . ($res['ip'] != $res['ip_upto'] ? ' - ' . long2ip($res['ip_upto']) : '') . '</span></strong>' .
                    (empty($res['description']) ? '' : '<div class="sub">' . Validate::checkout($res['description'], 1) . '</div>') .
                    '<div class="sub"><span class="gray">' .
                    __('date') . ':&#160;' . Functions::displayDate($res['timestamp']) .
                    '<br />' . __('who_added') . ':&#160;' . $res['nickname'] .
                    '</span></div></div>';
            }
            echo '<div class="rmenu"><input type="submit" name="delete" value="' . __('delete') . ' "/></div></form>';
        } else {
            echo'<div class="menu"><p>' . __('list_empty') . '</p></div>';
        }

        // Нижний блок с подсказками
        echo'<div class="phdr">' . __('total') . ': ' . $total . '</div>' .
            '<div class="topmenu"><small><p>' .
            ($mod == 'white'
                ? '<strong>' . mb_strtoupper(__('white_list')) . ':</strong> ' . __('white_list_help')
                : '<strong>' . mb_strtoupper(__('black_list')) . ':</strong> ' . __('black_list_help')
            ) . '</p></small></div>';

        // Постраничная навигация
        if ($total > Vars::$USER_SET['page_size']) {
            echo'<div class="topmenu">' . Functions::displayPagination($url . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                '<p><form action="' . $url . '" method="post">' .
                '<input type="text" name="page" size="2"/>' .
                '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
                '</form></p>';
        }

        // Ссылки внизу
        echo'<p>' . ($total ? '<a href="' . $url . '?act=clear&amp;mod=' . $mod . '">' . __('clear_list') . '</a><br />' : '') .
            '<a href="' . $backurl . '">' . __('admin_panel') . '</a></p>';
}

//$tpl->contents = $tpl->includeTpl('firewall');