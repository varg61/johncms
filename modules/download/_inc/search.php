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
Поиск файлов
-----------------------------------------------------------------
*/
$search_post = isset($_POST['search']) ? trim($_POST['search']) : false;
$search_get = isset($_GET['search']) ? rawurldecode(trim($_GET['search'])) : '';
$search = $search_post ? $search_post : $search_get;
/*
-----------------------------------------------------------------
Форма для поиска
-----------------------------------------------------------------
*/
echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('download_title') . '</b></a> | ' . lng('search') . '</div>' .
'<form action="' . Vars::$URI . '?act=search" method="post"><div class="gmenu"><p>' .
lng('name_file') . ':<br /><input type="text" name="search" value="' . Validate::checkout($search) . '" /><br />' .
'<input name="id" type="checkbox" value="1" ' . (Vars::$ID ? 'checked="checked"' : '') . '/> ' . lng('search_for_desc') . '<br />' .
'<input type="submit" value="Поиск" name="submit" /><br />' .
'</p></div></form>';
/*
-----------------------------------------------------------------
Проверяем на коректность ввода
-----------------------------------------------------------------
*/
$error = false;
if (!empty($search) && mb_strlen($search) < 2 || mb_strlen($search) > 64)
    $error = lng('search_error');
/*
-----------------------------------------------------------------
Выводим результаты поиска
-----------------------------------------------------------------
*/
if ($search && !$error) {
	/*
	-----------------------------------------------------------------
	Подготавливаем данные для запроса
	-----------------------------------------------------------------
	*/
	$search = preg_replace("/[^\w\x7F-\xFF\s]/", " ", $search);
    $search_db = strtr($search, array ('_' => '\\_', '%' => '\\%', '*' => '%'));
    $search_db = '%' . $search_db . '%';
    $sql = (Vars::$ID ? '`about`' : '`rus_name`') . ' LIKE \'' . mysql_real_escape_string($search_db) . '\'';
	/*
	-----------------------------------------------------------------
	Результаты поиска
	-----------------------------------------------------------------
	*/
	echo '<div class="phdr"><b>' . lng('search_result') . '</b></div>';
	$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2'  AND $sql"), 0);
	if ($total > Vars::$USER_SET['page_size']) {
		$check_search = Validate::checkout(rawurlencode($search));
		echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=search&amp;search=' . $check_search . '&amp;id=' . Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    }
    if ($total) {
        $req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `type` = '2'  AND $sql ORDER BY `rus_name` ASC " . Vars::db_pagination());
        $i = 0;
        while ($res_down = mysql_fetch_assoc($req_down)) {
            echo (($i++ % 2) ? '<div class="list2">' : '<div class="list1">') . Download::displayFile($res_down) . '</div>';
        }
    } else {
        echo '<div class="rmenu"><p>' . lng('search_list_empty') . '</p></div>';
    }
    echo '<div class="phdr">' . lng('total') . ':  ' . $total . '</div>';
	/*
	-----------------------------------------------------------------
	Навигация
	-----------------------------------------------------------------
	*/
	if ($total > Vars::$USER_SET['page_size']) {
		echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=search&amp;search=' . $check_search . '&amp;id=' . Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
 	 	'<p><form action="' . Vars::$URI . '" method="get">' .
  		'<input type="hidden" value="' . $check_search . '" name="search" />' .
  		'<input type="hidden" value="search" name="act" />' .
  		'<input type="hidden" value="' . Vars::$ID . '" name="id" />' .
    	'<input type="text" name="page" size="2"/><input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
	}
	echo '<p><a href="' . Vars::$URI . '?act=search">' . lng('search_new') . '</a></p>';
} else {
	/*
	-----------------------------------------------------------------
	FAQ по поиску и вывод ошибки
	-----------------------------------------------------------------
	*/
    if ($error) echo Functions::displayError($error);
	 echo '<div class="phdr"><small>' . lng('search_faq') . '</small></div>';
}
echo '<p><a href="' . Vars::$URI . '">' . lng('download_title') . '</a></p>';