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
if (!Vars::$USER_ID && !Vars::$USER_SYS['view_userlist']) {
    echo Functions::displayError(lng('access_guest_forbidden'));
    exit;
}

/*
-----------------------------------------------------------------
Принимаем данные запросов на поиск
-----------------------------------------------------------------
*/
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search = trim($_POST['search']);
} elseif (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = rawurldecode(trim($_GET['search']));
} else {
    $search = false;
}

/*
-----------------------------------------------------------------
Проверяем поисковый запрос на ошибки
-----------------------------------------------------------------
*/
$error = false;
if ($search && Validate::nickname($search, 1) === false) {
    $error = Validate::$error['login'];
}

/*
-----------------------------------------------------------------
Выводим список пользователей
-----------------------------------------------------------------
*/
$menu = array(
    (Vars::$ACT != 'adm' ? lng('all_users') : '<a href="' . Vars::$URI . '">' . lng('all_users') . '</a>'),
    (Vars::$ACT == 'adm' ? lng('administration') : '<a href="' . Vars::$URI . '?act=adm">' . lng('administration') . '</a>')
);

if ($search && $error) {
    $style = ' style="background-color: #FFCCCC"';
} elseif ($search && !$error) {
    $style = ' style="background-color: #CCFFCC"';
} else {
    $style = '';
}

echo'<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . lng('community') . '</b></a> | ' . ($search ? lng('search_user') : lng('users_list')) . '</div>' .
    '<div class="topmenu">' .
    ($error ? '<p class="red">' . $error . '</p>' : '') .
    '<p><form action="' . Vars::$MODULE_URI . '/search" method="post">' .
    '<input type="text" name="search" value="' . htmlspecialchars($search) . '"' . $style . '/> ' .
    '<input type="submit" value="' . lng('search') . '" name="submit"/>' .
    '</form>' .
    '</p></div>' .
    '<div class="topmenu">' .
    ($search && !$error ? '<b>' . lng('search_results') . '</b>' : Functions::displayMenu($menu)) .
    '</div>';

if ($search && !$error) {
    $search_db = strtr($search, array(
        '_' => '\\_',
        '%' => '\\%'
    ));
    $search_db = '%' . $search_db . '%';
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `nickname` LIKE '" . mysql_real_escape_string($search_db) . "'"), 0);
    $query = "SELECT * FROM `users` WHERE `nickname` LIKE '" . mysql_real_escape_string($search_db) . "' ORDER BY `nickname` ASC";
} else {
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `" . (Vars::$ACT == 'adm' ? 'rights' : 'level') . "` > 0"), 0);
    $query = "SELECT * FROM `users` WHERE " . (Vars::$ACT == 'adm' ? '`rights` > 0 ORDER BY `rights`' : '`level` > 0 ORDER BY `id`') . " DESC";
}
Vars::fixPage($total);

if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
}

if ($total) {
    $req = mysql_query($query . Vars::db_pagination());
    for ($i = 0; $res = mysql_fetch_assoc($req); $i++) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        if ($search && !$error) {
            $res['nickname'] = preg_replace('|(' . preg_quote($search, '/') . ')|siu', '<span style="background-color: #FFFF33">$1</span>', $res['nickname']);
        }
        $arg = array();
        if (Vars::$USER_RIGHTS == 9 || Vars::$USER_RIGHTS > $res['rights']) {
            //TODO: Добавить ссылки на Бан и удаление профиля
            $arg['sub'] = Functions::displayMenu(array(
                '<a href="">' . lng('ban_do') . '</a>',
                '<a href="' . Vars::$HOME_URL . '/profile?act=edit&amp;user=' . $res['id'] . '">' . lng('edit') . '</a>',
                (Vars::$USER_RIGHTS >= 7 ? '<a href="">' . lng('delete') . '</a>' : ''),
            ));
        }
        echo Functions::displayUser($res, $arg) .
            '</div>';
    }
} else {
    echo '<div class="menu"><p>' . lng('search_results_empty') . '</p></div>';
}

echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="' . Vars::$URI . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/>' .
        '</form></p>';
}
echo'<p>' .
    ($search ? '<a href="' . Vars::$URI . '">' . lng('search_new') . '</a><br/>' : '') .
    (Vars::$USER_RIGHTS ? '<a href="' . Vars::$HOME_URL . '/admin">' . lng('admin_panel') . '</a><br />' : '') .
    '<a href="' . Vars::$MODULE_URI . '">' . lng('back') . '</a>' .
    '</p>';