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

/*
-----------------------------------------------------------------
Список альбомов юзера
-----------------------------------------------------------------
*/
if (isset($_SESSION['ap']))
    unset($_SESSION['ap']);
echo '<div class="phdr"><a href="album.php"><b>' . Vars::$LNG['photo_albums'] . '</b></a> | ' . Vars::$LNG['personal_2'] . '</div>';
$req = mysql_query("SELECT * FROM `cms_album_cat` WHERE `user_id` = '" . $user['user_id'] . "' " . ($user['user_id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 6 ? "" : "AND `access` > 1") . " ORDER BY `sort` ASC");
$total = mysql_num_rows($req);
if ($user['user_id'] == Vars::$USER_ID && $total < $max_album || Vars::$USER_RIGHTS >= 7) {
    echo '<div class="topmenu"><a href="album.php?act=edit&amp;user=' . $user['user_id'] . '">' . $lng_profile['album_create'] . '</a></div>';
}
echo '<div class="user"><p>' . Functions::displayUser($user, array ('iphide' => 1,)) . '</p></div>';
if ($total) {
    $i = 0;
    while ($res = mysql_fetch_assoc($req)) {
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `album_id` = '" . $res['id'] . "'"), 0);
        echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
            Functions::getImage('album_' . $res['access'] . '.png', '', 'align="middle"') . '&#160;' .
            '<a href="album.php?act=show&amp;al=' . $res['id'] . '&amp;user=' . $user['user_id'] . '"><b>' . Validate::filterString($res['name']) . '</b></a>&#160;(' . $count . ')';
        if ($user['user_id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 6 || !empty($res['description'])) {
            $menu = array (
                '<a href="album.php?act=sort&amp;mod=up&amp;al=' . $res['id'] . '&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['up'] . '</a>',
                '<a href="album.php?act=sort&amp;mod=down&amp;al=' . $res['id'] . '&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['down'] . '</a>',
                '<a href="album.php?act=edit&amp;al=' . $res['id'] . '&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['edit'] . '</a>',
                '<a href="album.php?act=delete&amp;al=' . $res['id'] . '&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['delete'] . '</a>'
            );
            echo '<div class="sub">' .
                (!empty($res['description']) ? '<div class="gray">' . Validate::filterString($res['description'], 1, 1) . '</div>' : '') .
                ($user['user_id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 6 ? Functions::displayMenu($menu) : '') .
                '</div>';
        }
        echo '</div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
}
echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';