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
Новые файлы
-----------------------------------------------------------------
*/
$textl = __('new_files');
$sql_down = '';
if (Vars::$ID) {
    $cat = DB::PDO()->query("SELECT * FROM `cms_download_category` WHERE `id` = '" . Vars::$ID . "' LIMIT 1");
    $res_down_cat = $cat->fetch();
    if (!$cat->rowCount() || !is_dir($res_down_cat['dir'])) {
        echo Functions::displayError(__('not_found_dir'), '<a href="' . $url . '">' . __('download_title') . '</a>');
        exit;
    }
    $title_pages = Validate::checkout(mb_substr($res_down_cat['rus_name'], 0, 30));
    $textl = __('new_files') . ': ' . (mb_strlen($res_down_cat['rus_name']) > 30 ? $title_pages . '...' : $title_pages);
    $sql_down = ' AND `dir` LIKE \'' . ($res_down_cat['dir']) . '%\' ';
}
echo '<div class="phdr"><b>' . $textl . '</b></div>';
$total = DB::PDO()->query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2'  AND `time` > $sql_down")->fetchColumn();
/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
if ($total > Vars::$USER_SET['page_size'])
    echo '<div class="topmenu">' . Functions::displayPagination($url . '?id=' . Vars::$ID . '&amp;act=new_files&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
/*
-----------------------------------------------------------------
Выводим список
-----------------------------------------------------------------
*/
if ($total) {
    $i = 0;
    $req_down = DB::PDO()->query("SELECT * FROM `cms_download_files` WHERE `type` = '2'  AND `time` > $old $sql_down ORDER BY `time` DESC " . Vars::db_pagination());
    while ($res_down = $req_down->fetch()) {
        echo (($i++ % 2) ? '<div class="list2">' : '<div class="list1">') . Download::displayFile($res_down) . '</div>';
    }
} else {
    echo '<div class="rmenu"><p>' . __('list_empty') . '</p></div>';
}
echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination($url . '?id=' . Vars::$ID . '&amp;act=new_files&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="' . $url . '" method="get">' .
        '<input type="hidden" name="id" value="' . Vars::$ID . '"/>' .
        '<input type="hidden" value="new_files" name="act" />' .
        '<input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
}
echo '<p><a href="' . $url . '?id=' . Vars::$ID . '">' . __('download_title') . '</a></p>';