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
Новые файлы
-----------------------------------------------------------------
*/
$textl = lng('new_files');
$sql_down = '';
if (Vars::$ID) {
    $cat = mysql_query("SELECT * FROM `cms_download_category` WHERE `id` = '" . Vars::$ID . "' LIMIT 1");
    $res_down_cat = mysql_fetch_assoc($cat);
    if (mysql_num_rows($cat) == 0 || !is_dir($res_down_cat['dir'])) {
        echo Functions::displayError(lng('not_found_dir'), '<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
        exit;
    }
    $title_pages = Validate::filterString(mb_substr($res_down_cat['rus_name'], 0, 30));
    $textl = lng('new_files') . ': ' . (mb_strlen($res_down_cat['rus_name']) > 30 ? $title_pages . '...' : $title_pages);
    $sql_down = ' AND `dir` LIKE \'' . ($res_down_cat['dir']) . '%\' ';
}
echo '<div class="phdr"><b>' . $textl . '</b></div>';
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2'  AND `time` > $old $sql_down"), 0);
/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
if ($total > Vars::$USER_SET['page_size'])
	echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?id=' . Vars::$ID . '&amp;act=new_files&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
/*
-----------------------------------------------------------------
Выводим список
-----------------------------------------------------------------
*/
if ($total) {
    $i = 0;
    $req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `type` = '2'  AND `time` > $old $sql_down ORDER BY `time` DESC " . Vars::db_pagination());
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
	echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?id=' . Vars::$ID . '&amp;act=new_files&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
 	'<p><form action="' . Vars::$URI . '" method="get">' .
  	'<input type="hidden" name="id" value="' . Vars::$ID . '"/>' .
   	'<input type="hidden" value="new_files" name="act" />' .
    '<input type="text" name="page" size="2"/><input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
}
echo '<p><a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . lng('download_title') . '</a></p>';