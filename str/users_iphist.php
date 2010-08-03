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

define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
if (!$user_id) {
    require('../incfiles/head.php');
    display_error($lng['access_guest_forbidden']);
    require('../incfiles/end.php');
    exit;
}
if ($id && $id != $user_id && $rights > 0) {
    // Если был запрос на юзера, то получаем его данные
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
    if (mysql_num_rows($req)) {
        $user = mysql_fetch_assoc($req);
        $textl = $lng['ip_history'] . ': ' . $user['name'];
    } else {
        require('../incfiles/head.php');
        echo display_error($lng['user_does_not_exist']);
        require('../incfiles/end.php');
        exit;
    }
} else {
    $textl = $lng['ip_history'];
    $user = $datauser;
}
require('../incfiles/head.php');
echo '<div class="phdr"><a href="anketa.php?id=' . $user['id'] . '"><b>' . $lng['profile'] . '</b></a> | ' . $lng['ip_history'] . '</div>';
echo '<div class="user"><p>';
$arg = array (
    'lastvisit' => 1,
    'header' => '<b>ID:' . $user['id'] . '</b>'
);
echo display_user($user, $arg);
echo '</p></div>';
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_users_iphistory` WHERE `user_id` = '" . $user['id'] . "'"), 0);
if ($total) {
    $req = mysql_query("SELECT * FROM `cms_users_iphistory` WHERE `user_id` = '" . $user['id'] . "' ORDER BY `time` DESC LIMIT $start,$kmess");
    while ($res = mysql_fetch_assoc($req)) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        $iptime = $user['ip'] == $res['user_ip'] ? $user['lastdate'] : $res['time'];
        $link = $rights > 0 ? '<a href="../' . $admp . '/index.php?act=usr_search_ip&amp;ip=' . $res['user_ip'] . '">' . long2ip($res['user_ip']) . '</a>' : long2ip($res['user_ip']);
        echo $link . ' <span class="gray">(' . date("d.m.Y / H:i", $iptime) . ')</span></div>';
        ++$i;
    }
}
echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
if ($total > $kmess) {
    echo '<p>' . display_pagination('users_iphist.php?id=' . $user['id'] . '&amp;', $start, $total, $kmess) . '</p>';
    echo '<p><form action="users_iphist.php?id=' . $user['id'] . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
        '</form></p>';
}

require('../incfiles/end.php');
?>