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
        $sql = "WHERE `users`.`sex` = 'zh'";
        break;
    default:
        $sql = "WHERE `users`.`sex` != ''";
}
$menu = array(
    (!Vars::$MOD ? '<b>' . Vars::$LNG['all'] . '</b>' : '<a href="album.php?act=users">' . Vars::$LNG['all'] . '</a>'),
    (Vars::$MOD == 'boys' ? '<b>' . Vars::$LNG['mans'] . '</b>' : '<a href="album.php?act=users&amp;mod=boys">' . Vars::$LNG['mans'] . '</a>'),
    (Vars::$MOD == 'girls' ? '<b>' . Vars::$LNG['womans'] . '</b>' : '<a href="album.php?act=users&amp;mod=girls">' . Vars::$LNG['womans'] . '</a>')
);
echo '<div class="phdr"><a href="album.php"><b>' . Vars::$LNG['photo_albums'] . '</b></a> | ' . Vars::$LNG['list'] . '</div>' .
    '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
$total = mysql_result(mysql_query("SELECT COUNT(DISTINCT `user_id`)
    FROM `cms_album_files`
    LEFT JOIN `users` ON `cms_album_files`.`user_id` = `users`.`id` $sql
"), 0);
if ($total) {
    $req = mysql_query("SELECT `cms_album_files`.*, COUNT(`cms_album_files`.`id`) AS `count`, `users`.`id` AS `uid`, `users`.`name` AS `nick`, `users`.`sex`
        FROM `cms_album_files`
        LEFT JOIN `users` ON `cms_album_files`.`user_id` = `users`.`id` $sql
        GROUP BY `cms_album_files`.`user_id` ORDER BY `users`.`name` ASC LIMIT " . Vars::db_pagination()
    );
    $i = 0;
    while ($res = mysql_fetch_assoc($req)) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        echo Functions::getImage('usr_' . ($res['sex'] == 'm' ? 'm' : 'w') . '.png', '', 'align="middle"') . '&#160;<a href="album.php?act=list&amp;user=' . $res['uid'] . '">' . $res['nick'] . '</a> (' . $res['count'] . ')</div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
}
echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination('album.php?act=users' . (Vars::$MOD ? '&amp;mod=' . Vars::$MOD : '') . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="album.php?act=users' . (Vars::$MOD ? '&amp;mod=' . Vars::$MOD : '') . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/>' .
        '</form></p>';
}