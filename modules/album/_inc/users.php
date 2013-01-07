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

//TODO: Доработать!

/*
-----------------------------------------------------------------
Список посетителей. у которых есть фотографии
-----------------------------------------------------------------
*/
switch (Vars::$MOD) {
    case 'boys':
        $sql = "WHERE `users`.`sex` = 'm'";
        break;

    case 'girls':
        $sql = "WHERE `users`.`sex` = 'w'";
        break;
    default:
        $sql = "";
}

$menu = array(
    (!Vars::$MOD ? '<b>' . __('all') . '</b>' : '<a href="' . $url . '?act=users">' . __('all') . '</a>'),
    (Vars::$MOD == 'boys' ? '<b>' . __('mans') . '</b>' : '<a href="' . $url . '?act=users&amp;mod=boys">' . __('mans') . '</a>'),
    (Vars::$MOD == 'girls' ? '<b>' . __('womans') . '</b>' : '<a href="' . $url . '?act=users&amp;mod=girls">' . __('womans') . '</a>')
);
echo '<div class="phdr"><a href="' . $url . '"><b>' . __('photo_albums') . '</b></a> | ' . __('list') . '</div>' .
    '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
$total = mysql_result(mysql_query("SELECT COUNT(DISTINCT `user_id`)
    FROM `cms_album_files`
    LEFT JOIN `users` ON `cms_album_files`.`user_id` = `users`.`id`
" . $sql), 0);
if ($total) {
    $req = mysql_query("SELECT `cms_album_files`.*, COUNT(`cms_album_files`.`id`) AS `count`, `users`.`id` AS `uid`, `users`.`nickname`, `users`.`sex`
        FROM `cms_album_files`
        LEFT JOIN `users` ON `cms_album_files`.`user_id` = `users`.`id` $sql
        GROUP BY `cms_album_files`.`user_id` ORDER BY `users`.`nickname` ASC " . Vars::db_pagination()
    );
    $i = 0;
    for ($i = 0; $res = mysql_fetch_assoc($req); ++$i) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        echo Functions::loadImage(($res['sex'] == 'm' ? 'user.png' : 'user-female.png')) . '&#160;' .
            '<a href="' . $url . '?act=list&amp;user=' . $res['uid'] . '">' . $res['nickname'] . '</a> (' . $res['count'] . ')</div>';
    }
} else {
    echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
}
echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination($url . '?act=users' . (Vars::$MOD ? '&amp;mod=' . Vars::$MOD : '') . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="' . $url . '?act=users' . (Vars::$MOD ? '&amp;mod=' . Vars::$MOD : '') . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
        '</form></p>';
}