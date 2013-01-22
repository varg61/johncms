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
Топ юзеров
-----------------------------------------------------------------
*/
$textl = __('top_users');
echo '<div class="phdr"><a href="' . Router::getUri(2) . '"><b>' . __('downloads') . '</b></a> | ' . $textl . '</div>';
$req = DB::PDO()->query("SELECT * FROM `cms_download_files` WHERE `user_id` > 0 GROUP BY `user_id` ORDER BY COUNT(`user_id`)");
$total = $req->rowCount();

/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
if ($total > Vars::$USER_SET['page_size'])
    echo '<div class="topmenu">' . Functions::displayPagination($url . '?act=top_users&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
/*
-----------------------------------------------------------------
Список файлов
-----------------------------------------------------------------
*/
$i = 0;
if ($total) {
    $req_down = DB::PDO()->query("SELECT *, COUNT(`user_id`) AS `count` FROM `cms_download_files` WHERE `user_id` > 0 GROUP BY `user_id` ORDER BY `count` DESC " . Vars::db_pagination());
    while ($res_down = $req_down->fetch()) {
        $user = DB::PDO()->query("SELECT * FROM `users` WHERE `id`=" . $res_down['user_id'])->fetch();
        echo (($i++ % 2) ? '<div class="list2">' : '<div class="list1">') .
            functions::displayUser($user, array('iphide' => 0, 'sub' => '<a href="' . $url . '?act=user_files&amp;id=' . $user['id'] . '">' . __('user_files') . ':</a> ' . $res_down['count'])) . '</div>';
    }
} else {
    echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
}
echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination($url . '?act=top_users&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="' . $url . '" method="get">' .
        '<input type="hidden" value="top_users" name="act" />' .
        '<input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
}
echo '<p><a href="' . $url . '">' . __('download_title') . '</a></p>';