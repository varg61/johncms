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

$lng_adm = Vars::loadLanguage('adm');

echo '<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . Vars::$LNG['admin_panel'] . '</b></a> | ' . Vars::$LNG['users_list'] . '</div>';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'asc';
if (empty(Vars::$MOD)) Vars::$MOD = 'id';

/*
-----------------------------------------------------------------
Меню выбора режимов сортировки
-----------------------------------------------------------------
*/
$menu = array(
    (Vars::$MOD != 'nick' && Vars::$MOD != 'ip' ? 'ID' : '<a href="user_list.php">ID</a>'),
    (Vars::$MOD == 'nick' ? Vars::$LNG['nick'] : '<a href="user_list.php?mod=nick">' . Vars::$LNG['nick'] . '</a>'),
    (Vars::$MOD == 'ip' ? 'IP' : '<a href="user_list.php?mod=ip">IP</a>')
);
echo'<div class="topmenu"><span class="gray">' . Vars::$LNG['sorting'] . ':</span>&#160;[' .
    ($sort == 'desc' ? '<a href="user_list.php?mod=' . Vars::$MOD . '&amp;sort=asc">DESC</a>' : '<a href="user_list.php?mod=' . Vars::$MOD . '&amp;sort=desc">ASC</a>') . ']&#160;&#160;&#160;&#160;' .
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
if (array_key_exists(Vars::$MOD . $sort, $arr)) $order = $arr[Vars::$MOD . $sort];
else $order = $arr['idasc'];

/*
-----------------------------------------------------------------
Выводим список пользователей
-----------------------------------------------------------------
*/
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `level` > 0"), 0);
$req = mysql_query("SELECT * FROM `users` WHERE `level` > 0 ORDER BY $order" . Vars::db_pagination());
$i = 0;
while (($res = mysql_fetch_assoc($req)) !== false) {
    $link = '';
    if (Vars::$USER_RIGHTS >= 7)
        $link .= '<a href="../users/profile.php?act=edit&amp;user=' . $res['id'] . '">' . Vars::$LNG['edit'] . '</a> | <a href="index.php?act=usr_del&amp;id=' . $res['id'] . '">' . Vars::$LNG['delete'] . '</a> | ';
    $link .= '<a href="../users/profile.php?act=ban&amp;mod=do&amp;user=' . $res['id'] . '">' . Vars::$LNG['ban_do'] . '</a>';
    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
    echo Functions::displayUser($res, array('header' => ('<b>ID:' . $res['id'] . '</b>'), 'sub' => $link));
    echo '</div>';
    ++$i;
}
echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=usr&amp;sort=' . $sort . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    echo '<p><form action="index.php?act=usr&amp;sort=' . $sort . '" method="post"><input type="text" name="page" size="2"/><input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/></form></p>';
}
echo'<p><a href="index.php?act=search_user">' . Vars::$LNG['search_user'] . '</a><br />' .
    '<a href="index.php">' . Vars::$LNG['admin_panel'] . '</a></p>';