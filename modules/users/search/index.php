<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

/*
-----------------------------------------------------------------
Принимаем данные, выводим форму поиска
-----------------------------------------------------------------
*/
$search_post = isset($_POST['search']) ? trim($_POST['search']) : false;
$search_get = isset($_GET['search']) ? rawurldecode(trim($_GET['search'])) : '';
$search = $search_post ? $search_post : $search_get;
echo '<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . Vars::$LNG['community'] . '</b></a> | ' . Vars::$LNG['search_user'] . '</div>' .
    '<form action="' . Vars::$URI . '" method="post">' .
    '<div class="gmenu"><p>' .
    '<input type="text" name="search" value="' . Validate::filterString($search) . '" />' .
    '<input type="submit" value="' . Vars::$LNG['search'] . '" name="submit" />' .
    '</p></div></form>';

if ($search && Validate::nickname($search, 1) === true) {
    /*
    -----------------------------------------------------------------
    Выводим результаты поиска
    -----------------------------------------------------------------
    */
    $search_db = strtr($search, array(
        '_' => '\\_',
        '%' => '\\%'
    ));
    $search_db = '%' . $search_db . '%';
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `nickname` LIKE '" . mysql_real_escape_string($search_db) . "'"), 0);
    Vars::fixPage($total);
    echo '<div class="phdr"><b>' . Vars::$LNG['search_results'] . '</b></div>';
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    }
    if ($total) {
        $req = mysql_query("SELECT * FROM `users` WHERE `nickname` LIKE '" . mysql_real_escape_string($search_db) . "' ORDER BY `nickname` ASC" . Vars::db_pagination());
        $i = 0;
        for ($i = 0; ($res = mysql_fetch_assoc($req)) !== false; $i++) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            $res['nickname'] = preg_replace('|(' . preg_quote($search, '/') . ')|siu', '<span style="background-color: #FFFF33">$1</span>', $res['nickname']);
            echo Functions::displayUser($res);
            echo '</div>';
        }
    } else {
        echo '<div class="menu"><p>' . Vars::$LNG['search_results_empty'] . '</p></div>';
    }
    echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
    if ($total > Vars::$USER_SET['page_size']) {
        echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
            '<p><form action="' . Vars::$URI . '?search=' . urlencode($search) . '" method="post">' .
            '<input type="text" name="page" size="2"/>' .
            '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/>' .
            '</form></p>';
    }
} else {
    if (!empty(Validate::$error)) {
        echo Functions::displayError(Validate::$error);
    }
    echo '<div class="phdr"><small>' . Vars::$LNG['search_nick_help'] . '</small></div>';
}
echo '<p>' . ($search && empty(Validate::$error) ? '<a href="' . Vars::$URI . '">' . Vars::$LNG['search_new'] . '</a><br />' : '') .
    '<a href="' . Vars::$MODULE_URI . '">' . Vars::$LNG['back'] . '</a></p>';