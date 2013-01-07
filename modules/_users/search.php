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
$url = Router::getUrl(2);

/*
-----------------------------------------------------------------
Закрываем от неавторизованных юзеров
-----------------------------------------------------------------
*/
if (!Vars::$USER_ID && !Vars::$USER_SYS['view_userlist']) {
    echo Functions::displayError(__('access_guest_forbidden'));
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
    $search = FALSE;
}

/*
-----------------------------------------------------------------
Проверяем поисковый запрос на ошибки
-----------------------------------------------------------------
*/
$error = FALSE;
if ($search && Validate::nickname($search, 1) === FALSE) {
    $error = Validate::$error['login'];
}

/*
-----------------------------------------------------------------
Выводим список пользователей
-----------------------------------------------------------------
*/
$menu = array(
    (Vars::$ACT != 'adm' ? __('all_users') : '<a href="' . $url . '">' . __('all_users') . '</a>'),
    (Vars::$ACT == 'adm' ? __('administration') : '<a href="' . $url . '?act=adm">' . __('administration') . '</a>')
);

if ($search && $error) {
    $style = ' style="background-color: #FFCCCC"';
} elseif ($search && !$error) {
    $style = ' style="background-color: #CCFFCC"';
} else {
    $style = '';
}

echo'<div class="phdr"><a href="' . $url . '"><b>' . __('community') . '</b></a> | ' . ($search ? __('search_user') : __('users_list')) . '</div>' .
    '<div class="topmenu">' .
    ($error ? '<p class="red">' . $error . '</p>' : '') .
    '<p><form action="' . $url . '/search" method="post">' .
    '<input type="text" name="search" value="' . htmlspecialchars($search) . '"' . $style . '/> ' .
    '<input type="submit" value="' . __('search') . '" name="submit"/>' .
    '</form>' .
    '</p></div>' .
    '<div class="topmenu">' .
    ($search && !$error ? '<b>' . __('search_results') . '</b>' : Functions::displayMenu($menu)) .
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
    echo '<div class="topmenu">' . Functions::displayPagination($url . '?search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
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
                '<a href="">' . __('ban_do') . '</a>',
                '<a href="' . Vars::$HOME_URL . '/profile?act=edit&amp;user=' . $res['id'] . '">' . __('edit') . '</a>',
                (Vars::$USER_RIGHTS >= 7 ? '<a href="">' . __('delete') . '</a>' : ''),
            ));
        }
        echo Functions::displayUser($res, $arg) .
            '</div>';
    }
} else {
    echo '<div class="menu"><p>' . __('search_results_empty') . '</p></div>';
}

echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo'<div class="topmenu">' . Functions::displayPagination($url . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="' . $url . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
        '</form></p>';
}
echo'<p>' .
    ($search ? '<a href="' . $url . '">' . __('search_new') . '</a><br/>' : '') .
    (Vars::$USER_RIGHTS ? '<a href="' . Vars::$HOME_URL . '/admin">' . __('admin_panel') . '</a><br />' : '') .
    '<a href="' . $url . '">' . __('back') . '</a>' .
    '</p>';