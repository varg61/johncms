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

require_once('../includes/core.php');

/*
-----------------------------------------------------------------
Выводим список пользователей
-----------------------------------------------------------------
*/
$menu = array(
    (Vars::$MOD != 'adm' ? Vars::$LNG['users'] : '<a href="userlist.php">' . Vars::$LNG['users'] . '</a>'),
    (Vars::$MOD == 'adm' ? Vars::$LNG['administration'] : '<a href="userlist.php?act=adm">' . Vars::$LNG['administration'] . '</a>')
);
echo'<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['community'] . '</b></a> | ' . Vars::$LNG['users_list'] . '</div>' .
    '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';

$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `" . (Vars::$MOD == 'adm' ? 'rights' : 'level') . "` > 0"), 0);
Vars::fixPage($total);

if ($total > Vars::$USER_SET['page_size']) {
    echo'<div class="topmenu">' . Functions::displayPagination('userlist.php?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
}

if ($total) {
    $req = mysql_query("SELECT * FROM `users` WHERE " . (Vars::$MOD == 'adm' ? '`rights` > 0 ORDER BY `rights`' : '`level` > 0 ORDER BY `id`') . " DESC" . Vars::db_pagination());
    for ($i = 0; $res = mysql_fetch_assoc($req); $i++) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        echo Functions::displayUser($res) . '</div>';
    }
} else {
    echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
}

echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo'<div class="topmenu">' . Functions::displayPagination('userlist.php?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="userlist.php" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/>' .
        '</form></p>';
}
echo '<p><a href="search.php">' . Vars::$LNG['search_user'] . '</a><br />' .
    '<a href="index.php">' . Vars::$LNG['back'] . '</a></p>';