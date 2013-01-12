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
$url = Router::getUri(2);

$arr_model = array('Nokia', 'Siemens', 'SE', 'Samsung', 'LG', 'Motorola', 'NEC', 'Philips', 'Sagem', 'Fly', 'Panasonic', 'Opera', 'komp');
if (!in_array($model, $arr_model)) {
    echo Functions::displayError(__('error_data'), '<a href="' . $url . '">' . __('statistics') . '</a>');
} else {


    $model1 = $model;
    $sql = '';
    if ($model == "Nokia") {
        $sql = "WHERE `browser` LIKE '%nokia%'";
    } elseif ($model == "Siemens") {
        $sql = "WHERE `browser` LIKE 'SIE%' OR `browser` LIKE '%benq%'";
    } elseif ($model == "SE") {
        $model1 = 'Sony Ericsson';
        $sql = "WHERE `browser` LIKE '%sony%' OR `browser` LIKE '%sonyeric%'";
    } elseif ($model == "Samsung") {
        $sql = "WHERE `browser` LIKE '%sec%' OR `browser` LIKE '%samsung%'";
    } elseif ($model == "LG") {
        $sql = "WHERE `browser` LIKE '%lg%'";
    } elseif ($model == "Motorola") {
        $sql = "WHERE `browser` LIKE '%mot%' OR `browser` LIKE '%motorol%'";
    } elseif ($model == "NEC") {
        $sql = "WHERE `browser` LIKE '%nec%'";
    } elseif ($model == "Philips") {
        $sql = "WHERE `browser` LIKE '%philips%'";
    } elseif ($model == "Sagem") {
        $sql = "WHERE `browser` LIKE '%sagem%'";
    } elseif ($model == "Fly") {
        $sql = "WHERE `browser` LIKE '%fly%'";
    } elseif ($model == "Panasonic") {
        $sql = "WHERE `browser` LIKE '%panasonic%'";
    } elseif ($model == "Opera") {
        $model1 = 'Opera Mini';
        $sql = "WHERE `browser` LIKE '%opera mini%'";
    } elseif ($model == "komp") {
        $model1 = __('computers');
        $sql = "WHERE `browser` LIKE '%windows%' OR `browser` LIKE '%linux%'";
    }

    echo '<div class="phdr">' . __('statistics_on') . ' ' . $model1 . '</div>';
    $count = mysql_result(mysql_query("SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` " . $sql . ";"), 0);
    if ($count > 0) {
        $req = mysql_query("SELECT * FROM `counter` " . $sql . " GROUP BY `ip`, `browser` ORDER BY `counter`.`date` DESC " . Vars::db_pagination());
        $i = 0;
        while ($arr = mysql_fetch_array($req)) {
            echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
            ++$i;
            $count_view = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `browser` = '" . $arr['browser'] . "' AND `ip` = '" . $arr['ip'] . "'"), 0);

            $time = date("H:i", $arr['date']);
            //TODO: Переделать ссылку
            echo '<b>' . $time . '</b> - ' . $arr['browser'] . '
        <div class="sub">Ip: <a href="' . Vars::$HOME_URL . '/admin?act=search_ip&amp;ip=' . $arr['ip'] . '">' . $arr['ip'] . '</a> <a href="' . Vars::$HOME_URL . 'admin/whois/' . $arr['ip'] . '" title="WhoIS IP">[?]</a> ';

            if ($arr['ip_via_proxy'])
                //TODO: Переделать ссылку
                echo '| <a href="' . Vars::$HOME_URL . '/admin?act=search_ip&amp;ip=' . $arr['ip_via_proxy'] . '">' . $arr['ip_via_proxy'] . '</a> <a href="' . Vars::$HOME_URL . '/admin/whois/' . $arr['ip_via_proxy'] . '" title = "WhoIS ip">[?]</a> ';

            echo '| ' . $arr['operator'] . ' | ' . $arr['country'] . ' | ' . __('movies') . ': ' . $count_view . '</div>';

            echo '</div>';
        }

        echo '<div class="phdr">' . __('total') . ': ' . $count . '</div>';
        if ($count > Vars::$USER_SET['page_size']) {
            echo '<div class="topmenu">';
            echo Functions::displayPagination($url . '?act=phone&amp;model=' . $model . '&amp;', Vars::$START, $count, Vars::$USER_SET['page_size']) . '</div>';
            echo '<p><form action="' . $url . '" method="get"><input type="hidden" name="act" value="phone"/><input type="hidden" name="model" value="' . $model . '"/><input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
        }

    } else {
        echo '<div class="rmenu">' . __('no_data') . '</div>';
    }
    $back_links = '<a href="?act=phones">' . __('back') . '</a><br/>';

}

?>