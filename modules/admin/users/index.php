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

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 1) {
    header('Location: http://johncms.com/404.php');
    exit;
}

echo '<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . lng('admin_panel') . '</b></a> | ' . lng('users_list') . '</div>';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'asc';
if (empty(Vars::$ACT)) Vars::$ACT = 'id';

/*
-----------------------------------------------------------------
Меню выбора режимов сортировки
-----------------------------------------------------------------
*/
$menu = array(
    (Vars::$ACT != 'nick' && Vars::$ACT != 'ip' ? 'ID' : '<a href="' . Vars::$URI . '">ID</a>'),
    (Vars::$ACT == 'nick' ? lng('nickname') : '<a href="' . Vars::$URI . '?act=nick">' . lng('nickname') . '</a>'),
    (Vars::$ACT == 'ip' ? 'IP' : '<a href="' . Vars::$URI . '?act=ip">IP</a>')
);
echo'<div class="topmenu"><span class="gray">' . lng('sorting') . ':</span>&#160;[' .
    ($sort == 'desc'
        ? '<a href="' . Vars::$URI . '?act=' . Vars::$ACT . '&amp;sort=asc">DESC</a>'
        : '<a href="' . Vars::$URI . '?act=' . Vars::$ACT . '&amp;sort=desc">ASC</a>'
    ) .
    ']&#160;&#160;&#160;&#160;' .
    Functions::displayMenu($menu) .
    '</div>';

/*
-----------------------------------------------------------------
Задаем режимы сортировки
-----------------------------------------------------------------
*/
$arr = array(
    'idasc' => '`id` ASC',
    'iddesc' => '`id` DESC',
    'nickasc' => '`nickname` ASC',
    'nickdesc' => '`nickname` DESC',
    'ipasc' => '`ip` ASC',
    'ipdesc' => '`ip` DESC'
);
if (array_key_exists(Vars::$ACT . $sort, $arr)) $order = $arr[Vars::$ACT . $sort];
else $order = $arr['idasc'];

/*
-----------------------------------------------------------------
Выводим список пользователей
-----------------------------------------------------------------
*/
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `level` > 0"), 0);
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=' . Vars::$ACT . '&amp;sort=' . $sort . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
}
$req = mysql_query("SELECT * FROM `users` WHERE `level` > 0 ORDER BY $order" . Vars::db_pagination());
for ($i = 0; $res = mysql_fetch_assoc($req); ++$i) {
    //TODO: Установить правильные ссылки на редактирование профилей
    $user_menu = array(
        (Vars::$USER_RIGHTS >= 7 ? '<a href="../users/profile.php?act=edit&amp;user=' . $res['id'] . '">' . lng('edit') . '</a>' : ''),
        (Vars::$USER_RIGHTS >= 7 ? '<a href="index.php?act=usr_del&amp;id=' . $res['id'] . '">' . lng('delete') . '</a>' : ''),
        '<a href="../users/profile.php?act=ban&amp;mod=do&amp;user=' . $res['id'] . '">' . lng('ban_do') . '</a>'
    );
    echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
        Functions::displayUser($res, array('header' => ('<b>ID:' . $res['id'] . '</b>'), 'sub' => Functions::displayMenu($user_menu))) .
        '</div>';
}
echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=' . Vars::$ACT . '&amp;sort=' . $sort . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="' . Vars::$URI . '?act=' . Vars::$ACT . '&amp;sort=' . $sort . '" method="post">' .
        '<input type="text" name="page" size="2"/><input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/>' .
        '</form></p>';
}
echo'<p><a href="' . Vars::$MODULE_URI . '/users/administration.php">' . lng('administration') . '</a><br />' .
    '<a href="' . Vars::$MODULE_URI . '">' . lng('admin_panel') . '</a></p>';