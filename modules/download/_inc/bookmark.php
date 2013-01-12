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
Закладки
-----------------------------------------------------------------
*/
$textl = __('download_bookmark');
if (!Vars::$USER_ID) {
    echo Functions::displayError(__('access_guest_forbidden'));
    exit;
}
echo '<div class="phdr"><b>' . $textl . '</b></div>';
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_bookmark` WHERE `user_id` = " . Vars::$USER_ID), 0);
/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
if ($total > Vars::$USER_SET['page_size'])
	echo '<div class="topmenu">' . Functions::displayPagination($url . '?act=bookmark&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
/*
-----------------------------------------------------------------
Список закладок
-----------------------------------------------------------------
*/
$i = 0;
if ($total) {
    $req_down = mysql_query("SELECT `cms_download_files`.*, `cms_download_bookmark`.`id` AS `bid`
    FROM `cms_download_files` LEFT JOIN `cms_download_bookmark` ON `cms_download_files`.`id` = `cms_download_bookmark`.`file_id`
    WHERE `cms_download_bookmark`.`user_id`=" . Vars::$USER_ID . " ORDER BY `cms_download_files`.`time` DESC " . Vars::db_pagination());
	while ($res_down = mysql_fetch_assoc($req_down)) {
        echo (($i++ % 2) ? '<div class="list2">' : '<div class="list1">') . Download::displayFile($res_down) . '</div>';
    }
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
	echo '<div class="topmenu">' . Functions::displayPagination($url . '?act=bookmark&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
 	'<p><form action="' . $url . '" method="get">' .
  	'<input type="hidden" value="bookmark" name="act" />' .
    '<input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
}
echo '<p><a href="' . $url . '">' . __('download_title') . '</a></p>';