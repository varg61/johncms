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
$textl = htmlspecialchars($user['nickname']) . ': ' . Vars::$LNG['ip_history'];

/*
-----------------------------------------------------------------
Проверяем права доступа
-----------------------------------------------------------------
*/
if (!Vars::$USER_RIGHTS && Vars::$USER_ID != $user['user_id']) {
    echo Functions::displayError(Vars::$LNG['access_forbidden']);
    exit;
}

/*
-----------------------------------------------------------------
История IP адресов
-----------------------------------------------------------------
*/
echo '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '"><b>' . Vars::$LNG['profile'] . '</b></a> | ' . Vars::$LNG['ip_history'] . '</div>';
echo '<div class="user"><p>';
$arg = array(
    'lastvisit' => 1,
    'header' => '<b>ID:' . $user['user_id'] . '</b>'
);
echo Functions::displayUser($user, $arg);
echo '</p></div>';
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_user_iphistory` WHERE `user_id` = '" . $user['user_id'] . "'"), 0);
if ($total) {
    $req = mysql_query("SELECT * FROM `cms_user_iphistory` WHERE `user_id` = '" . $user['user_id'] . "' ORDER BY `timestamp` DESC LIMIT " . Vars::db_pagination());
    $i = 0;
    while (($res = mysql_fetch_assoc($req)) !== false) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        $link = Vars::$USER_RIGHTS ? '<a href="' . Vars::$HOME_URL . '/' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=search_ip&amp;mod=history&amp;ip=' . long2ip($res['ip']) . '">' . long2ip($res['ip']) . '</a>' : long2ip($res['ip']);
        echo $link . ' <span class="gray">(' . date("d.m.Y / H:i", $res['time']) . ')</span></div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
}
echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo '<p>' . Functions::displayPagination('profile.php?act=ip&amp;user=' . $user['user_id'] . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>';
    echo '<p><form action="profile.php?act=ip&amp;user=' . $user['user_id'] . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/>' .
        '</form></p>';
}