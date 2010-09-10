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
Список недавно добавленных фотографий
-----------------------------------------------------------------
*/
echo '<div class="phdr"><a href="index.php"><b>' . $lng['photo_albums'] . '</b></a> | ' . $lng_profile['new_photo'] . '</div>';
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `time` > '" . ($realtime - 86400) . "'" . ($rights >=7 ? "" : " AND `access` > '1'")), 0);
if ($total) {
    if ($total > $kmess)
        echo '<div class="topmenu">' . display_pagination('index.php?act=new&amp;', $start, $total, $kmess) . '</div>';
    $req = mysql_query("SELECT `cms_album_files`.*, `users`.`name` AS `user_name`, `cms_album_cat`.`name` AS `album_name`
    FROM `cms_album_files`
    INNER JOIN `users` ON `cms_album_files`.`user_id` = `users`.`id`
    INNER JOIN `cms_album_cat` ON `cms_album_files`.`album_id` = `cms_album_cat`.`id`
    WHERE `cms_album_files`.`time` > '" . ($realtime - 86400) . "'" . ($rights >=7 ? "" : " AND `cms_album_files`.`access` > '1'") . "
    ORDER BY `cms_album_files`.`time` DESC
    LIMIT $start, $kmess");
    while ($res = mysql_fetch_assoc($req)) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        if ($res['access'] == 4 || $rights >= 7) {
            // Если доступ открыт всем, или смотрит Администратор
            echo '<a href="index.php?act=album&amp;al=' . $res['album_id'] . '&amp;img=' . $res['id'] . '&amp;id=' . $res['user_id'] . '"><img src="../../files/users/album/' . $res['user_id'] . '/' . $res['tmb_name'] . '" /></a>';
            if (!empty($res['description']))
                echo '<div class="gray">' . smileys(checkout($res['description'], 1)) . '</div>';
        } elseif ($res['access'] == 3) {
            // Если доступ открыт друзьям
            echo 'Только для друзей';
        } elseif ($res['access'] == 2) {
            // Если доступ по паролю
            echo '<a href="index.php?act=album&amp;al=' . $res['album_id'] . '"><img src="' . $home . '/images/stop.gif" width="50" height="50"/></a>';
        }
        echo '<div class="sub">';
        vote_photo($res);
        echo '<p><a href="../profile/index.php?id=' . $res['user_id'] . '">' . $res['user_name'] . '</a> | <a href="index.php?act=album&amp;al=' . $res['album_id'] . '&amp;id=' . $res['user_id'] . '">' . checkout($res['album_name']) . '</a><br />';
        echo '<a href="../../files/users/album/' . $res['user_id'] . '/' . $res['img_name'] . '">' . $lng['download'] . '</a></p>';
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