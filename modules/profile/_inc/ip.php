<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

$url = Router::getUri(2);

/*
-----------------------------------------------------------------
Проверяем права доступа
-----------------------------------------------------------------
*/
if (!Vars::$USER_RIGHTS && Vars::$USER_ID != $user['id']) {
    echo Functions::displayError(__('access_forbidden'));
    exit;
}

/*
-----------------------------------------------------------------
История IP адресов
-----------------------------------------------------------------
*/
//TODO: Переделать ссылку
echo '<div class="phdr"><a href="' . Vars::$HOME_URL . '/profile?user=' . $user['id'] . '"><b>' . __('profile') . '</b></a> | ' . __('ip_history') . '</div>';
echo '<div class="user"><p>';
$arg = array(
    'lastvisit' => 1,
    'header'    => '<b>ID:' . $user['id'] . '</b>'
);
echo Functions::displayUser($user, $arg);
echo '</p></div>';
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_user_ip` WHERE `user_id` = '" . $user['id'] . "'"), 0);
if ($total) {
    $req = mysql_query("SELECT * FROM `cms_user_ip` WHERE `user_id` = '" . $user['id'] . "' ORDER BY `timestamp` DESC " . Vars::db_pagination());
    for ($i = 0; $res = mysql_fetch_assoc($req); ++$i) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        //TODO: Переделать ссылку
        $link = Vars::$USER_RIGHTS ? '<a href="' . Vars::$HOME_URL . '/admin?act=search_ip&amp;mod=history&amp;ip=' . long2ip($res['ip']) . '">' . long2ip($res['ip']) . '</a>' : long2ip($res['ip']);
        echo $link . ' <span class="gray">(' . date("d.m.Y / H:i", $res['timestamp']) . ')</span></div>';
    }
} else {
    echo'<div class="menu"><p>' . __('list_empty') . '</p></div>';
}
echo'<div class="phdr">' . __('total') . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo'<p>' . Functions::displayPagination($url . '?act=ip&amp;user=' . $user['id'] . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>' .
        '<p><form action="' . $url . '?act=ip&amp;user=' . $user['id'] . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
        '</form></p>';
}