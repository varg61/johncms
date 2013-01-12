<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
$url = Router::getUri(2);

/*
-----------------------------------------------------------------
Файлы на модерации
-----------------------------------------------------------------
*/
$textl = __('mod_files');
if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
    echo '<div class="phdr"><a href="' . Router::getUri(2) . '"><b>' . __('downloads') . '</b></a> | ' . $textl . '</div>';
    if (Vars::$ID) {
        mysql_query("UPDATE `cms_download_files` SET `type` = 2 WHERE `id` = '" . Vars::$ID . "' LIMIT 1");
        echo '<div class="gmenu">' . __('file_accepted_ok') . '</div>';
    } else if (isset($_POST['all_mod'])) {
        mysql_query("UPDATE `cms_download_files` SET `type` = 2 WHERE `type` = '3'");
        echo '<div class="gmenu">' . __('file_accepted_all_ok') . '</div>';
    }
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '3'"), 0);
    /*
    -----------------------------------------------------------------
    Навигация
    -----------------------------------------------------------------
    */
    if ($total > Vars::$USER_SET['page_size'])
        echo '<div class="topmenu">' . Functions::displayPagination($url . '?act=mod_files&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    $i = 0;
    if ($total) {
        $req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `type` = '3' ORDER BY `time` DESC " . Vars::db_pagination());
        while ($res_down = mysql_fetch_assoc($req_down)) {
            echo (($i++ % 2) ? '<div class="list2">' : '<div class="list1">') . Download::displayFile($res_down) .
                '<div class="sub"><a href="' . $url . '?act=mod_files&amp;id=' . $res_down['id'] . '">' . __('file_accepted') . '</a> | ' .
                '<span class="red"><a href="' . $url . '?act=delete_file&amp;id=' . $res_down['id'] . '">' . __('delete') . '</a></span></div></div>';
        }
        echo '<div class="rmenu"><form name="" action="' . $url . '?act=mod_files" method="post"><input type="submit" name="all_mod" value="' . __('file_accepted_all') . '"/></form></div>';
    } else {
        echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
    }
    echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
    /*
    -----------------------------------------------------------------
    Навигация
    -----------------------------------------------------------------
    */
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<div class="topmenu">' . Functions::displayPagination($url . '?act=mod_files&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
            '<p><form action="' . $url . '" method="get">' .
            '<input type="hidden" value="top_users" name="act" />' .
            '<input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
    }
    echo '<p><a href="' . $url . '">' . __('download_title') . '</a></p>';
} else {
    header('Location: ' . Vars::$HOME_URL . '404');
}