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
Топ фотографий с лучшим рейтингом
-----------------------------------------------------------------
*/
echo '<div class="phdr"><a href="index.php"><b>' . $lng['photo_albums'] . '</b></a> | ' . $lng_profile['top_votes'] . '</div>';
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE (`vote_plus` - `vote_minus`) > 0" . ($rights >=7 ? "" : " AND `cms_album_files`.`access` > '1'")), 0);
if ($total) {
    $req = mysql_query("SELECT `cms_album_files`.*, `users`.`name` AS `user_name`, `cms_album_cat`.`name` AS `album_name`, (`vote_plus` - `vote_minus`) AS `rating`
    FROM `cms_album_files`
    INNER JOIN `users` ON `cms_album_files`.`user_id` = `users`.`id`
    INNER JOIN `cms_album_cat` ON `cms_album_files`.`album_id` = `cms_album_cat`.`id`
    WHERE (`vote_plus` - `vote_minus`) > 0" . ($rights >=7 ? "" : " AND `cms_album_files`.`access` > '1'") . "
    ORDER BY `rating` DESC
    LIMIT $start, $kmess");
    while ($res = mysql_fetch_assoc($req)) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        if ($res['access'] == 4 || $rights >= 7) {
            // Если доступ открыт всем, или смотрит Администратор
            echo '<a href="index.php?act=album&amp;al=' . $res['album_id'] . '&amp;img=' . $res['id'] . '&amp;id=' . $res['user_id'] . '"><img src="../../files/users/album/' . $res['user_id'] . '/' . $res['tmb_name'] . '" /></a>';
        } elseif ($res['access'] == 3) {
            // Если доступ открыт друзьям
            echo 'Только для друзей';
        } elseif ($res['access'] == 2) {
            // Если доступ по паролю
            echo '<a href="index.php?act=album&amp;al=' . $res['album_id'] . '"><img src="' . $home . '/images/stop.gif" width="50" height="50"/></a>';
        }
        echo '<div class="sub">';
        vote_photo($res);
        if ($res['access'] == 4) {
            if (!empty($res['description']))
                echo checkout($res['description'], 1) . '<br />';
            echo '<a href="">' . $lng['comments'] . '</a><br />';
        }
        echo '<a href="../profile/index.php?id=' . $res['user_id'] . '">' . $res['user_name'] . '</a> | <a href="index.php?act=album&amp;al=' . $res['album_id'] . '&amp;id=' . $res['user_id'] . '">' . checkout($res['album_name']) . '</a>';
        echo '</div></div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
}
echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
if ($total > $kmess) {
    echo '<div class="topmenu">' . display_pagination('index.php?act=new&amp;', $start, $total, $kmess) . '</div>' .
        '<p><form action="index.php?act=new" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
        '</form></p>';
}
?>