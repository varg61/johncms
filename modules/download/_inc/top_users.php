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
/*
-----------------------------------------------------------------
Топ юзеров
-----------------------------------------------------------------
*/
$textl = lng('top_users');
echo '<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . lng('downloads') . '</b></a> | ' . $textl . '</div>';
$req = mysql_query("SELECT * FROM `cms_download_files` WHERE `user_id` > 0 GROUP BY `user_id` ORDER BY COUNT(`user_id`)");
$total = mysql_num_rows($req);
/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
if ($total > Vars::$USER_SET['page_size'])
    echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=top_users&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
/*
-----------------------------------------------------------------
Список файлов
-----------------------------------------------------------------
*/
$i = 0;
if ($total) {
    $req_down = mysql_query("SELECT *, COUNT(`user_id`) AS `count` FROM `cms_download_files` WHERE `user_id` > 0 GROUP BY `user_id` ORDER BY `count` DESC " . Vars::db_pagination());
    while ($res_down = mysql_fetch_assoc($req_down)) {
        $user = mysql_fetch_assoc(mysql_query("SELECT * FROM `users` WHERE `id`=" . $res_down['user_id']));
        echo (($i++ % 2) ? '<div class="list2">' : '<div class="list1">') .
            functions::displayUser($user, array('iphide' => 0, 'sub' => '<a href="' . Vars::$URI . '?act=user_files&amp;id=' . $user['id'] . '">' . lng('user_files') . ':</a> ' . $res_down['count'])) . '</div>';
    }
} else {
    echo '<div class="menu"><p>' . lng('list_empty') . '</p></div>';
}
echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=top_users&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="' . Vars::$URI . '" method="get">' .
        '<input type="hidden" value="top_users" name="act" />' .
        '<input type="text" name="page" size="2"/><input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
}
echo '<p><a href="' . Vars::$URI . '">' . lng('download_title') . '</a></p>';