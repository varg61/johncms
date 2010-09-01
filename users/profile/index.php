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
$headmod = 'anketa';
$rootpath = '../../';
require('../../incfiles/core.php');
$lng_profile = load_lng('profile');

/*
-----------------------------------------------------------------
Закрываем доступ для неавторизованных
-----------------------------------------------------------------
*/
if (!$user_id) {
    display_error($lng['access_guest_forbidden']);
    require('../../incfiles/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Получаем данные пользователя
-----------------------------------------------------------------
*/
$user = get_user($id);
if (!$user) {
    echo display_error($lng['user_does_not_exist']);
    require('../../incfiles/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$array = array (
    'activity',
    'edit',
    'history_ip',
    'info',
    'office',
    'password',
    'set_chat',
    'set_forum',
    'set_main',
    'stat'
);
if (in_array($act, $array) && file_exists($act . '.php')) {
    require_once($act . '.php');
} else {
    /*
    -----------------------------------------------------------------
    Главная страница Анкеты пользователя
    -----------------------------------------------------------------
    */
    $textl = $lng['profile'] . ': ' . htmlspecialchars($user['name']);
    require('../../incfiles/head.php');
    echo '<div class="phdr"><b>' . ($id && $id != $user_id ? $lng_profile['user_profile'] : $lng_profile['my_profile']) . '</b></div>';
    // Меню анкеты
    $menu = array ();
    if ($user['id'] == $user_id || ($rights >= 7 && $rights > $user['rights']))
        $menu[] = '<a href="index.php?act=edit&amp;id=' . $user['id'] . '">' . $lng['edit'] . '</a>';
    if ($user['id'] != $user_id && $rights >= 7 && $rights > $user['rights'])
        $menu[] = '<a href="' . $home . '/' . $admp . '/index.php?act=usr_del&amp;id=' . $user['id'] . '">' . $lng['delete'] . '</a>';
    if ($user['id'] != $user_id && $rights > $user['rights'])
        $menu[] = '<a href="../users_ban.php?act=ban&amp;id=' . $user['id'] . '">' . $lng['ban_do'] . '</a>';
    if (!empty($menu))
        echo '<div class="topmenu">' . display_menu($menu) . '</div>';
    //Уведомление о дне рожденья
    if ($user['dayb'] == $day && $user['monthb'] == $mon) {
        echo '<div class="gmenu">' . $lng['birthday'] . '!!!</div>';
    }
    // Информация о юзере
    $arg = array (
        'lastvisit' => 1,
        'iphist' => 1,
        'header' => '<b>ID:' . $user['id'] . '</b>'
    );
    echo '<div class="user"><p>' . display_user($user, $arg) . '</p></div>';
    // Если юзер ожидает подтверждения регистрации, выводим напоминание
    if ($rights >= 7 && !$user['preg'] && empty($user['regadm'])) {
        echo '<div class="rmenu">' . $lng_profile['awaiting_registration'] . '</div>';
    }
    // Карма
    if ($set_karma['on']) {
        if ($user['karma'])
            $exp = explode('|', $user['plus_minus']);
        if ($exp[0] > $exp[1]) {
            $karma = $exp[1] ? ceil($exp[0] / $exp[1]) : $exp[0];
            $images = $karma > 10 ? '2' : '1';
            echo '<div class="gmenu">';
        } else if ($exp[1] > $exp[0]) {
            $karma = $exp[0] ? ceil($exp[1] / $exp[0]) : $exp[1];
            $images = $karma > 10 ? '-2' : '-1';
            echo '<div class="rmenu">';
        } else {
            $images = 0;
            echo '<div class="menu">';
        }
        echo '<table  width="100%"><tr><td width="22" valign="top"><img src="' . $home . '/images/k_' . $images . '.gif"/></td><td>' .
            '<b>' . $lng['karma'] . ' (' . $user['karma'] . ')</b>' .
            '<div class="sub"><span class="green"><a href="karma.php?id=' . $user['id'] . '&amp;type=1">' . $lng['vote_for'] . ' (' . ($exp[0] ? $exp[0] : '0') . ')</a></span> | ' .
            '<span class="red"><a href="karma.php?id=' . $user['id'] . '&amp;type=2">' . $lng['vote_against'] . ' (' . ($exp[1] ? $exp[1] : '0') . ')</a></span>';
        if ($id) {
            if (!$datauser['karma_off'] && (!$user['rights'] || ($user['rights'] && !$set_karma['adm'])) && $user['ip'] != $datauser['ip']) {
                $sum = mysql_result(mysql_query("SELECT SUM(`points`) FROM `karma_users` WHERE `user_id` = '$user_id' AND `time` >= '" . $datauser['karma_time'] . "'"), 0);
                $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `user_id` = '$user_id' AND `karma_user` = '$id' AND `time` > '" . ($realtime - 86400) . "'"), 0);
                if ($datauser['postforum'] >= $set_karma['forum'] && $datauser['total_on_site'] >= $set_karma['karma_time'] && ($set_karma['karma_points'] - $sum) > 0 && !$count) {
                    echo '<br /><a href="../karma.php?act=user&amp;id=' . $user['id'] . '">' . $lng['vote'] . '</a>';
                }
            }
        } else {
            $total_karma = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `karma_user` = '$user_id' AND `time` > " . ($realtime - 86400)), 0);
            if ($total_karma > 0)
                echo '<br /><a href="../karma.php?act=new">' . $lng['responses_new'] . '</a> (' . $total_karma . ')';
        }
        echo '</div></td></tr></table></div>';
    }
    // Меню выбора
    $total_photo = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . $user['id'] . "'"), 0);
    echo '<div class="list2"><p>' .
        '<div><img src="' . $home . '/images/contacts.png" width="16" height="16" class="left" />&#160;<a href="index.php?act=info&amp;id=' . $user['id'] . '">' . $lng['information'] . '</a></div>' .
        '<div><img src="' . $home . '/images/activity.gif" width="16" height="16" class="left" />&#160;<a href="index.php?act=activity&amp;id=' . $user['id'] . '">' . $lng_profile['activity'] . '</a></div>' .
        '<div><img src="' . $home . '/images/rate.gif" width="16" height="16" class="left" />&#160;<a href="index.php?act=stat&amp;id=' . $user['id'] . '">' . $lng['statistics'] . '</a></div>' .
        '</p><p>' .
        '<div><img src="' . $home . '/images/photo.gif" width="16" height="16" class="left" />&#160;<a href="../album/index.php?id=' . $user['id'] . '">' . $lng['photo_album'] . '</a>&#160;(' . $total_photo . ')</div>' .
        '<div><img src="' . $home . '/images/guestbook.gif" width="16" height="16" class="left" />&#160;<a href="../guestbook/index.php?id=' . $user['id'] . '">' . $lng['guestbook'] . '</a>&#160;(0)</div>' .
        '<div><img src="' . $home . '/images/pt.gif" width="16" height="16" class="left" />&#160;<a href="../blog/index.php?id=' . $user['id'] . '">' . $lng['blog'] . '</a>&#160;(0)</div>' .
        '</p><p>' .
        '<div><img src="' . $home . '/images/users.png" width="16" height="16" class="left" />&#160;<a href="">' . $lng['contacts_in'] . '</a></div>'
        . ($user['id'] != $user_id && !$ban['1'] && !$ban['3'] ? '<div><img src="' . $home . '/images/write.gif" width="16" height="16" class="left" />&#160;<a href="../pradd.php?act=write&amp;adr=' . $user['id'] . '"><b>' . $lng['write'] . '</b></a></div>' : '') .
        '</p></div>';
    echo '<div class="phdr">&nbsp;</div>';
}

require('../../incfiles/end.php');
?>