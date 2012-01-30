<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require_once('../includes/core.php');
$lng_profile = Vars::loadLanguage('profile');
$user = isset($_REQUEST['user']) ? abs(intval($_REQUEST['user'])) : Vars::$USER_ID;

/*
-----------------------------------------------------------------
Закрываем от неавторизованных юзеров
-----------------------------------------------------------------
*/
if (!Vars::$USER_ID) {
    echo Functions::displayError(Vars::$LNG['access_guest_forbidden']);
    exit;
}

/*
-----------------------------------------------------------------
Получаем данные пользователя
-----------------------------------------------------------------
*/
$user = Functions::getUser($user);
if (!$user) {
    echo Functions::displayError(Vars::$LNG['user_does_not_exist']);
    exit;
}

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$array = array (
    'activity' => 'includes/profile',
    'ban' => 'includes/profile',
    'edit' => 'includes/profile',
    'images' => 'includes/profile',
    'info' => 'includes/profile',
    'ip' => 'includes/profile',
    'guestbook' => 'includes/profile',
    'karma' => 'includes/profile',
    'password' => 'includes/profile',
    'reset' => 'includes/profile',
    'settings' => 'includes/profile',
    'stat' => 'includes/profile'
);
$path = !empty($array[Vars::$ACT]) ? $array[Vars::$ACT] . '/' : '';
if (array_key_exists(Vars::$ACT, $array) && file_exists($path . Vars::$ACT . '.php')) {
    require_once($path . Vars::$ACT . '.php');
} else {
    /*
    -----------------------------------------------------------------
    Анкета пользователя
    -----------------------------------------------------------------
    */
    $textl = Vars::$LNG['profile'] . ': ' . htmlspecialchars($user['nickname']);
    echo '<div class="phdr"><b>' . ($user['user_id'] != Vars::$USER_ID ? $lng_profile['user_profile'] : $lng_profile['my_profile']) . '</b></div>';
    // Меню анкеты
    $menu = array ();
    if ($user['user_id'] == Vars::$USER_ID || Vars::$USER_RIGHTS == 9 || (Vars::$USER_RIGHTS == 7 && Vars::$USER_RIGHTS > $user['rights']))
        $menu[] = '<a href="profile.php?act=edit&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['edit'] . '</a>';
    if ($user['user_id'] != Vars::$USER_ID && Vars::$USER_RIGHTS >= 7 && Vars::$USER_RIGHTS > $user['rights'])
        $menu[] = '<a href="' . Vars::$HOME_URL . '/' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=usr_del&amp;id=' . $user['user_id'] . '">' . Vars::$LNG['delete'] . '</a>';
    if ($user['user_id'] != Vars::$USER_ID && Vars::$USER_RIGHTS > $user['rights'])
        $menu[] = '<a href="profile.php?act=ban&amp;mod=do&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['ban_do'] . '</a>';
    if (!empty($menu))
        echo '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
    //Уведомление о дне рожденья
    if ($user['dayb'] == date('j', time()) && $user['monthb'] == date('n', time())) {
        echo '<div class="gmenu">' . Vars::$LNG['birthday'] . '!!!</div>';
    }
    // Информация о юзере
    $arg = array (
        'lastvisit' => 1,
        'iphist' => 1,
        'header' => '<b>ID:' . $user['user_id'] . '</b>'
    );
    if($user['user_id'] != Vars::$USER_ID) $arg['footer'] = '<span class="gray">' . Vars::$LNG['where'] . ':</span> ' . Functions::displayPlace($user['user_id'], $user['place']);
    echo '<div class="user"><p>' . Functions::displayUser($user, $arg) . '</p></div>';
    // Если юзер ожидает подтверждения регистрации, выводим напоминание
    if (Vars::$USER_RIGHTS >= 7 && !$user['level']) {
        echo '<div class="rmenu">' . $lng_profile['awaiting_registration'] . '</div>';
    }
    // Карма
    if ($set_karma['on']) {
        $karma = $user['karma_plus'] - $user['karma_minus'];
        if ($karma > 0) {
            $images = ($user['karma_minus'] ? ceil($user['karma_plus'] / $user['karma_minus']) : $user['karma_plus']) > 10 ? '2' : '1';
            echo '<div class="gmenu">';
        } else if ($karma < 0) {
            $images = ($user['karma_plus'] ? ceil($user['karma_minus'] / $user['karma_plus']) : $user['karma_minus']) > 10 ? '-2' : '-1';
            echo '<div class="rmenu">';
        } else {
            $images = 0;
            echo '<div class="menu">';
        }
        echo '<table  width="100%"><tr><td width="22" valign="top">' . Functions::getImage('karma_' . $images . '.gif') . '</td><td>' .
            '<b>' . Vars::$LNG['karma'] . ' (' . $karma . ')</b>' .
            '<div class="sub">' .
            '<span class="green"><a href="profile.php?act=karma&amp;user=' . $user['user_id'] . '&amp;type=1">' . Vars::$LNG['vote_for'] . ' (' . $user['karma_plus'] . ')</a></span> | ' .
            '<span class="red"><a href="profile.php?act=karma&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['vote_against'] . ' (' . $user['karma_minus'] . ')</a></span>';
        if ($user['user_id'] != Vars::$USER_ID) {
            if (!Vars::$USER_DATA['karma_off'] && (!$user['rights'] || ($user['rights'] && !$set_karma['adm'])) && $user['ip'] != Vars::$USER_DATA['ip']) {
                $sum = mysql_result(mysql_query("SELECT SUM(`points`) FROM `karma_users` WHERE `user_id` = '" . Vars::$USER_ID . "' AND `time` >= '" . Vars::$USER_DATA['karma_time'] . "'"), 0);
                $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `user_id` = '" . Vars::$USER_ID . "' AND `karma_user` = '" . $user['user_id'] . "' AND `time` > '" . (time() - 86400) . "'"), 0);
                if (!Vars::$USER_BAN && Vars::$USER_DATA['postforum'] >= $set_karma['forum'] && Vars::$USER_DATA['total_on_site'] >= $set_karma['karma_time'] && ($set_karma['karma_points'] - $sum) > 0 && !$count) {
                    echo '<br /><a href="profile.php?act=karma&amp;mod=vote&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['vote'] . '</a>';
                }
            }
        } else {
            $total_karma = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `karma_user` = '" . Vars::$USER_ID . "' AND `time` > " . (time() - 86400)), 0);
            if ($total_karma > 0)
                echo '<br /><a href="profile.php?act=karma&amp;mod=new">' . Vars::$LNG['responses_new'] . '</a> (' . $total_karma . ')';
        }
        echo '</div></td></tr></table></div>';
    }
    // Меню выбора
    $total_photo = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . $user['user_id'] . "'"), 0);
    echo '<div class="list2"><p>' .
        '<div>' . Functions::getImage('contacts.png') . '&#160;<a href="profile.php?act=info&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['information'] . '</a></div>' .
        '<div>' . Functions::getImage('user_edit.png') . '&#160;<a href="profile.php?act=activity&amp;user=' . $user['user_id'] . '">' . $lng_profile['activity'] . '</a></div>' .
        '<div>' . Functions::getImage('rating.png') . '&#160;<a href="profile.php?act=stat&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['statistics'] . '</a></div>';
    $bancount = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $user['user_id'] . "'"), 0);
    if ($bancount)
        echo '<div>' . Functions::getImage('user_block.png') . '&#160;<a href="profile.php?act=ban&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['infringements'] . '</a> (' . $bancount . ')</div>';
    echo '<br />' .
        '<div>' . Functions::getImage('album_4.png') . '&#160;<a href="album.php?act=list&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['photo_album'] . '</a>&#160;(' . $total_photo . ')</div>' .
        '<div>' . Functions::getImage('comments.png') . '&#160;<a href="profile.php?act=guestbook&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['guestbook'] . '</a>&#160;(' . $user['comm_count'] . ')</div>';
    if ($user['user_id'] != Vars::$USER_ID) {
        echo '<br /><div>' . Functions::getImage('contacts.png') . '&#160;<a href="">' . Vars::$LNG['contacts_in'] . '</a></div>';
        if (!isset(Vars::$USER_BAN['1']) && !isset(Vars::$USER_BAN['3']))
            echo '<div>' . Functions::getImage('mail_write.png') . '&#160;<a href="pradd.php?act=write&amp;adr=' . $user['user_id'] . '"><b>' . Vars::$LNG['write'] . '</b></a></div>';
    }
    echo '</p></div>';
    echo '<div class="phdr"><a href="index.php">' . Vars::$LNG['users'] . '</a></div>';
}