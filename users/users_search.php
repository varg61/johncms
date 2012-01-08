<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

$headmod = 'usersearch';
require_once('../includes/core.php');
$textl = Vars::$LNG['search_user'];
require_once('../includes/head.php');

/*
-----------------------------------------------------------------
Принимаем данные, выводим форму поиска
-----------------------------------------------------------------
*/
$search_post = isset($_POST['search']) ? trim($_POST['search']) : false;
$search_get = isset($_GET['search']) ? rawurldecode(trim($_GET['search'])) : '';
$search = $search_post ? $search_post : $search_get;
echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['community'] . '</b></a> | ' . Vars::$LNG['search_user'] . '</div>' .
    '<form action="users_search.php" method="post">' .
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
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_user` WHERE `nickname` LIKE '" . mysql_real_escape_string($search_db) . "'"), 0);
    Vars::fixPage($total);
    echo '<div class="phdr"><b>' . Vars::$LNG['search_results'] . '</b></div>';
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<div class="topmenu">' . Functions::displayPagination('users_search.php?search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    }
    if ($total) {
        $req = mysql_query("SELECT * FROM `cms_user` WHERE `nickname` LIKE '" . mysql_real_escape_string($search_db) . "' ORDER BY `nickname` ASC LIMIT " . Vars::db_pagination());
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
        echo'<div class="topmenu">' . Functions::displayPagination('users_search.php?search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
            '<p><form action="users_search.php?search=' . urlencode($search) . '" method="post">' .
            '<input type="text" name="page" size="2"/>' .
            '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/>' .
            '</form></p>';
    }
} else {
    if (!empty(Login::$error)) echo Functions::displayError(Login::$error);
    echo '<div class="phdr"><small>' . Vars::$LNG['search_nick_help'] . '</small></div>';
}
echo '<p>' . ($search && empty(Login::$error) ? '<a href="search.php">' . Vars::$LNG['search_new'] . '</a><br />' : '') .
    '<a href="index.php">' . Vars::$LNG['back'] . '</a></p>';

require_once('../includes/end.php');