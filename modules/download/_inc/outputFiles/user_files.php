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
/*
-----------------------------------------------------------------
Файлы юзера
-----------------------------------------------------------------
*/
$textl = lng('user_files');
if (($user = Vars::getUser()) === FALSE || (!Vars::$USER && !Vars::$USER_ID)) {
	echo Functions::displayError(lng('user_does_not_exist'));
 	exit;
}
if(!Vars::$USER) Vars::$USER = Vars::$USER_ID;
echo '<div class="phdr"><a href="/profile?user=' . Vars::$USER . '">' . lng('profile') . '</a></div>' .
'<div class="user"><p>' . functions::displayUser($user, array('iphide' => 0)) . '</p></div>' .
'<div class="phdr"><b>' . lng('user_files') . '</b></div>';
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2'  AND `user_id` = " . Vars::$USER), 0);
/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
if ($total > Vars::$USER_SET['page_size'])
	echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?user=' . Vars::$USER . '&amp;act=user_files&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
/*
-----------------------------------------------------------------
Список файлов
-----------------------------------------------------------------
*/
$i = 0;
if ($total) {
    $req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `type` = '2'  AND `user_id` = " . Vars::$USER . " ORDER BY `time` DESC " . Vars::db_pagination());
    while ($res_down = mysql_fetch_assoc($req_down)) {
        echo (($i++ % 2) ? '<div class="list2">' : '<div class="list1">') . Download::displayFile($res_down) . '</div>';
     }
} else {
    echo '<div class="rmenu"><p>' . lng('list_empty') . '</p></div>';
}
echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
if ($total > Vars::$USER_SET['page_size']) {
	echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?user=' . Vars::$USER . '&amp;act=user_files&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
 	'<p><form action="' . Vars::$URI . '" method="get">' .
  	'<input type="hidden" name="USER" value="' . Vars::$USER . '"/>' .
   	'<input type="hidden" value="user_files" name="act" />' .
    '<input type="text" name="page" size="2"/><input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
}
echo '<p><a href="' . Vars::$URI . '">' . lng('download_title') . '</a></p>';