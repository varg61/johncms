<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Error: restricted access');

/*
-----------------------------------------------------------------
Список посетителей. у которых есть фотографии
-----------------------------------------------------------------
*/
echo '<div class="phdr"><a href="album.php"><b>' . $lng['photo_albums'] . '</b></a> | ' . $lng['list'] . '</div>';
echo '<div class="topmenu">' . ($mod == 'girls' ? '<a href="album.php?act=users">' . $lng['mans'] . '</a> | <b>' . $lng['womans'] . '</b>' : '<b>' . $lng['mans'] . '</b> | <a href="album.php?act=users&amp;mod=girls">' . $lng['womans'] . '</a>') . '</div>';
$sex = $mod == 'girls' ? 'zh' : 'm';
$total = mysql_result(mysql_query("SELECT COUNT(DISTINCT `user_id`)
    FROM `cms_album_files`
    LEFT JOIN `users` ON `cms_album_files`.`user_id` = `users`.`id`
    WHERE `users`.`sex` = '$sex'
"), 0);
if ($total) {
    $req = mysql_query("SELECT `cms_album_files`.*, COUNT(`cms_album_files`.`id`) AS `count`, `users`.`id` AS `uid`, `users`.`name` AS `nick`
        FROM `cms_album_files`
        LEFT JOIN `users` ON `cms_album_files`.`user_id` = `users`.`id`
        WHERE `users`.`sex` = '$sex'
        GROUP BY `cms_album_files`.`user_id` ORDER BY `users`.`name` ASC LIMIT $start, $kmess
    ");
    while ($res = mysql_fetch_assoc($req)) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        echo '<a href="album.php?act=list&amp;user=' . $res['uid'] . '">' . $res['nick'] . '</a> (' . $res['count'] . ')</div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
}
echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
if ($total > $kmess) {
    echo '<p>' . display_pagination('album.php?act=users' . ($mod == 'girls' ? '&amp;mod=girls' : '') . '&amp;', $start, $total, $kmess) . '</p>' .
        '<p><form action="album.php?act=users' . ($mod == 'girls' ? '&amp;mod=girls' : '') . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
        '</form></p>';
}
?>