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
Список альбомов юзера
-----------------------------------------------------------------
*/
if (isset($_SESSION['ap']))
    unset($_SESSION['ap']);
echo '<div class="phdr"><a href="../profile.php?id=' . $user['id'] . '"><b>' . ($id && $id != $user_id ? $lng_profile['user_profile'] : $lng_profile['my_profile']) . '</b></a> | ' . $lng['photo_album'] . '</div>';
$req = mysql_query("SELECT * FROM `cms_album_cat` WHERE `user_id` = '" . $user['id'] . "' " . ($user['id'] == $user_id || $rights >= 6 ? "" : "AND `access` > 1") . " ORDER BY `sort` ASC");
$total = mysql_num_rows($req);
if ($user['id'] == $user_id && $total < $max_album || $rights >= 7) {
    echo '<div class="topmenu"><a href="index.php?act=album_edit&amp;id=' . $user['id'] . '">' . $lng_profile['album_create'] . '</a></div>';
}
echo '<div class="user"><p>' . display_user($user, array ('iphide' => 1,)) . '</p></div>';
if ($total) {
    while ($res = mysql_fetch_assoc($req)) {
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `album_id` = '" . $res['id'] . "'"), 0);
        echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
            '<img src="' . $home . '/images/album-' . $res['access'] . '.gif" width="16" height="16" class="left" />&#160;' .
            '<a href="index.php?act=album&amp;al=' . $res['id'] . '&amp;id=' . $user['id'] . '"><b>' . checkout($res['name']) . '</b></a>&#160;(' . $count . ')' .
            '<div class="sub">';
        if ($user['id'] == $user_id || $rights >= 6) {
            $menu = array (
                '<a href="index.php?act=album_up&amp;al=' . $res['id'] . '&amp;id=' . $user['id'] . '">' . $lng['up'] . '</a>',
                '<a href="index.php?act=album_down&amp;al=' . $res['id'] . '&amp;id=' . $user['id'] . '">' . $lng['down'] . '</a>',
                '<a href="index.php?act=album_edit&amp;al=' . $res['id'] . '&amp;id=' . $user['id'] . '">' . $lng['edit'] . '</a>',
                '<a href="index.php?act=album_delete&amp;al=' . $res['id'] . '&amp;id=' . $user['id'] . '">' . $lng['delete'] . '</a>'
            );
            echo display_menu($menu) . '<br />';
        }
        echo checkout($res['description'], 1, 1, 1) . '</div></div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
}
echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>' .
    '<p><a href="../profile.php?id=' . $user['id'] . '">' . $lng['profile'] . '</a></p>';
?>