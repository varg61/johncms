<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_NEWS') or die('Error: restricted access');

if (Vars::$USER_RIGHTS >= 7) {
    echo '<div class="phdr"><a href="' . $url . '"><b>' . __('site_news') . '</b></a> | ' . __('clear') . '</div>';
    if (isset($_POST['submit'])) {
        $cl = isset($_POST['cl']) ? intval($_POST['cl']) : '';
        switch ($cl) {
            case '1':
                // Чистим новости, старше 1 недели
                mysql_query("DELETE FROM `cms_news` WHERE `time`<='" . (time() - 604800) . "'");
                mysql_query("OPTIMIZE TABLE `cms_news`");
                echo '<p>' . __('clear_week_confirmation') . '</p><p><a href="' . $url . '">' . __('to_news') . '</a></p>';
                break;

            case '2':
                // Проводим полную очистку
                mysql_query("TRUNCATE TABLE `cms_news`");
                echo '<p>' . __('clear_all_confirmation') . '</p><p><a href="' . $url . '">' . __('to_news') . '</a></p>';
                break;
            default :
                // Чистим сообщения, старше 1 месяца
                mysql_query("DELETE FROM `cms_news` WHERE `time`<='" . (time() - 2592000) . "'");
                mysql_query("OPTIMIZE TABLE `cms_news`");
                echo '<p>' . __('clear_month_confirmation') . '</p><p><a href="' . $url . '">' . __('to_news') . '</a></p>';
        }
    } else {
        echo '<div class="menu"><form id="clean" method="post" action="' . $url . '?act=clean">' .
            '<p><h3>' . __('clear_param') . '</h3>' .
            '<input type="radio" name="cl" value="0" checked="checked" />' . __('clear_month') . '<br />' .
            '<input type="radio" name="cl" value="1" />' . __('clear_week') . '<br />' .
            '<input type="radio" name="cl" value="2" />' . __('clear_all') . '</p>' .
            '<p><input type="submit" name="submit" value="' . __('clear') . '" /></p>' .
            '</form></div>' .
            '<div class="phdr"><a href="' . $url . '">' . __('cancel') . '</a></div>';
    }
} else {
    $tpl->back = $url;
    $tpl->message = __('access_forbidden');
    $tpl->contents = $tpl->includeTpl('message', 1);
}