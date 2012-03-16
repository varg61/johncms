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
Закрываем от неавторизованных юзеров
-----------------------------------------------------------------
*/
if (!Vars::$USER_ID && !Vars::$SYSTEM_SET['active']) {
    echo Functions::displayError(lng('access_guest_forbidden'));
    exit;
}

/*
-----------------------------------------------------------------
Выводим список пользователей
-----------------------------------------------------------------
*/
$menu = array(
    (Vars::$ACT != 'adm' ? lng('users') : '<a href="' . Vars::$URI . '">' . lng('users') . '</a>'),
    (Vars::$ACT == 'adm' ? lng('administration') : '<a href="' . Vars::$URI . '?act=adm">' . lng('administration') . '</a>')
);

echo'<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . lng('community') . '</b></a> | ' . lng('users_list') . '</div>' .
    '<div class="topmenu"><p>' .
    '<form action="' . Vars::$MODULE_URI . '/search" method="post">' .
    '<input type="text" name="search"/> ' .
    '<input type="submit" value="' . lng('search') . '" name="submit"/>' .
    '</form>' .
    '</p></div>' .
    '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';

$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `" . (Vars::$ACT == 'adm' ? 'rights' : 'level') . "` > 0"), 0);
Vars::fixPage($total);

if ($total > Vars::$USER_SET['page_size']) {
    echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
}

if ($total) {
    $req = mysql_query("SELECT * FROM `users` WHERE " . (Vars::$ACT == 'adm' ? '`rights` > 0 ORDER BY `rights`' : '`level` > 0 ORDER BY `id`') . " DESC" . Vars::db_pagination());
    for ($i = 0; $res = mysql_fetch_assoc($req); $i++) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        $arg = array();
        if (Vars::$USER_RIGHTS) {
            $arg['sub'] = Functions::displayMenu(array(
                '<a href="">Банить</a>',
                '<a href="' . Vars::$HOME_URL . '/profile/edit?user=' . $res['id'] . '">Редактировать</a>',
                '<a href="">Удалить</a>',
            ));
        }
        echo Functions::displayUser($res, $arg) . '</div>';
    }
} else {
    echo '<div class="menu"><p>' . lng('list_empty') . '</p></div>';
}

echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="' . Vars::$URI . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/>' .
        '</form></p>';
}
echo '<p>' . (Vars::$USER_RIGHTS ? '<a href="' . Vars::$HOME_URL . '/admin">' . lng('admin_panel') . '</a><br />' : '') .
    '<a href="' . Vars::$MODULE_URI . '">' . lng('back') . '</a></p>';