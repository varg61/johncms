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
Поиск файлов
-----------------------------------------------------------------
*/
$search_post = isset($_POST['search']) ? trim($_POST['search']) : FALSE;
$search_get = isset($_GET['search']) ? rawurldecode(trim($_GET['search'])) : '';
$search = $search_post ? $search_post : $search_get;

/*
-----------------------------------------------------------------
Форма для поиска
-----------------------------------------------------------------
*/
echo '<div class="phdr"><a href="' . $url . '"><b>' . __('download_title') . '</b></a> | ' . __('search') . '</div>' .
    '<form action="' . $url . '?act=search" method="post"><div class="gmenu"><p>' .
    __('name_file') . ':<br /><input type="text" name="search" value="' . Validate::checkout($search) . '" /><br />' .
    '<input name="id" type="checkbox" value="1" ' . (Vars::$ID ? 'checked="checked"' : '') . '/> ' . __('search_for_desc') . '<br />' .
    '<input type="submit" value="Поиск" name="submit" /><br />' .
    '</p></div></form>';
/*
-----------------------------------------------------------------
Проверяем на коректность ввода
-----------------------------------------------------------------
*/
$error = FALSE;
if (!empty($search) && mb_strlen($search) < 2 || mb_strlen($search) > 64)
    $error = __('search_error');
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
    $search_db = strtr($search, array('_' => '\\_', '%' => '\\%', '*' => '%'));
    $search_db = '%' . $search_db . '%';
    $search_db = DB::PDO()->quote($search_db);
    $sql = (Vars::$ID ? '`about`' : '`rus_name`') . ' LIKE ' . $search_db;
    /*
    -----------------------------------------------------------------
    Результаты поиска
    -----------------------------------------------------------------
    */
    echo '<div class="phdr"><b>' . __('search_result') . '</b></div>';
    $total = DB::PDO()->query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2'  AND $sql")->fetchColumn();
    if ($total > Vars::$USER_SET['page_size']) {
        $check_search = Validate::checkout(rawurlencode($search));
        echo '<div class="topmenu">' . Functions::displayPagination($url . '?act=search&amp;search=' . $check_search . '&amp;id=' . Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    }
    if ($total) {
        $req_down = DB::PDO()->query("SELECT * FROM `cms_download_files` WHERE `type` = '2'  AND $sql ORDER BY `rus_name` ASC " . Vars::db_pagination());
        $i = 0;
        while ($res_down = $req_down->fetch()) {
            echo (($i++ % 2) ? '<div class="list2">' : '<div class="list1">') . Download::displayFile($res_down) . '</div>';
        }
    } else {
        echo '<div class="rmenu"><p>' . __('search_list_empty') . '</p></div>';
    }
    echo '<div class="phdr">' . __('total') . ':  ' . $total . '</div>';
    /*
    -----------------------------------------------------------------
    Навигация
    -----------------------------------------------------------------
    */
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<div class="topmenu">' . Functions::displayPagination($url . '?act=search&amp;search=' . $check_search . '&amp;id=' . Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
            '<p><form action="' . $url . '" method="get">' .
            '<input type="hidden" value="' . $check_search . '" name="search" />' .
            '<input type="hidden" value="search" name="act" />' .
            '<input type="hidden" value="' . Vars::$ID . '" name="id" />' .
            '<input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
    }
    echo '<p><a href="' . $url . '?act=search">' . __('search_new') . '</a></p>';
} else {
    /*
    -----------------------------------------------------------------
    FAQ по поиску и вывод ошибки
    -----------------------------------------------------------------
    */
    if ($error) echo Functions::displayError($error);
    echo '<div class="phdr"><small>' . __('search_faq') . '</small></div>';
}
echo '<p><a href="' . $url . '">' . __('download_title') . '</a></p>';