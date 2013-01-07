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

/*
-----------------------------------------------------------------
Список альбомов юзера
-----------------------------------------------------------------
*/
if (isset($_SESSION['ap']))
    unset($_SESSION['ap']);
echo '<div class="phdr"><a href="' . $url . '"><b>' . __('photo_albums') . '</b></a> | ' . __('personal_2') . '</div>';
$req = mysql_query("SELECT * FROM `cms_album_cat` WHERE `user_id` = '" . $user['id'] . "' " . ($user['id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 6 ? "" : "AND `access` > 1") . " ORDER BY `sort` ASC");
$total = mysql_num_rows($req);
echo '<div class="user"><p>' . Functions::displayUser($user, array('iphide' => 1,)) . '</p></div>';
if ($total) {
    for ($i = 0; $res = mysql_fetch_assoc($req); ++$i) {
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `album_id` = '" . $res['id'] . "'"), 0);
        echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
            Functions::loadModuleImage('album_' . $res['access'] . '.png') . '&#160;' .
            '<a href="' . $url . '?act=show&amp;al=' . $res['id'] . '&amp;user=' . $user['id'] . '"><b>' . Validate::checkout($res['name']) . '</b></a>&#160;(' . $count . ')';
        if ($user['id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 6 || !empty($res['description'])) {
            $menu = array(
                '<a href="' . $url . '?act=sort&amp;mod=up&amp;al=' . $res['id'] . '&amp;user=' . $user['id'] . '">' . __('up') . '</a>',
                '<a href="' . $url . '?act=sort&amp;mod=down&amp;al=' . $res['id'] . '&amp;user=' . $user['id'] . '">' . __('down') . '</a>',
                '<a href="' . $url . '?act=edit&amp;al=' . $res['id'] . '&amp;user=' . $user['id'] . '">' . __('edit') . '</a>',
                '<a href="' . $url . '?act=delete&amp;al=' . $res['id'] . '&amp;user=' . $user['id'] . '">' . __('delete') . '</a>'
            );
            echo '<div class="sub">' .
                (!empty($res['description']) ? '<div class="gray">' . Validate::checkout($res['description'], 1, 1) . '</div>' : '') .
                ($user['id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 6 ? Functions::displayMenu($menu) : '') .
                '</div>';
        }
        echo '</div>';
    }
} else {
    echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
}
if ($user['id'] == Vars::$USER_ID && $total < $max_album || Vars::$USER_RIGHTS >= 7) {
    echo'<div class="gmenu">' .
        '<form action="' . $url . '?act=edit&amp;user=' . $user['id'] . '" method="post">' .
        '<p><input type="submit" value="' . __('album_create') . '"/></p>' .
        '</form></div>';
}
echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';