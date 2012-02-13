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
$array = array(
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
    $menu = array();
    if ($user['id'] == Vars::$USER_ID || Vars::$USER_RIGHTS == 9 || (Vars::$USER_RIGHTS == 7 && Vars::$USER_RIGHTS > $user['rights'])) {
        $menu[] = '<a href="profile.php?act=edit&amp;user=' . $user['id'] . '">' . Vars::$LNG['edit'] . '</a>';
    }
    if ($user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS >= 7 && Vars::$USER_RIGHTS > $user['rights']) {
        $menu[] = '<a href="' . Vars::$HOME_URL . '/' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=usr_del&amp;id=' . $user['id'] . '">' . Vars::$LNG['delete'] . '</a>';
    }
    if ($user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS > $user['rights']) {
        $menu[] = '<a href="profile.php?act=ban&amp;mod=do&amp;user=' . $user['id'] . '">' . Vars::$LNG['ban_do'] . '</a>';
    }

    $tpl = Template::getInstance();
    $tpl->lng = Vars::loadLanguage(1);
    $tpl->user = $user;
    if (!empty($menu)) {
        $tpl->menu = $menu;
    }
    $tpl->userarg = array(
        'lastvisit' => 1,
        'iphist' => 1,
        'header' => '<b>ID:' . $user['id'] . '</b>',
        'footer' => ($user['id'] != Vars::$USER_ID
            ? '<span class="gray">' . Vars::$LNG['where'] . ':</span> ' . Functions::displayPlace($user['id'], $user['place'])
            : false)
    );
    $tpl->total_photo = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . $user['user_id'] . "'"), 0);
    $tpl->contents = $tpl->includeTpl('index');

    //TODO: Добавить информацию о дне рождении
    //TODO: Добавить информацию о подтверждении регистрации

    $bancount = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $user['user_id'] . "'"), 0);
    if ($bancount)
        echo '<div>' . Functions::getImage('user_block.png') . '&#160;<a href="profile.php?act=ban&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['infringements'] . '</a> (' . $bancount . ')</div>';
    echo '<br/>' .
        '<div>' . Functions::getImage('album_4.png') . '&#160;<a href="album.php?act=list&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['photo_album'] . '</a>&#160;(' . $total_photo . ')</div>' .
        '<div>' . Functions::getImage('comments.png') . '&#160;<a href="profile.php?act=guestbook&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['guestbook'] . '</a>&#160;(' . $user['comm_count'] . ')</div>';
    if ($user['user_id'] != Vars::$USER_ID) {
        echo '<br/>
            <div>' . Functions::getImage('contacts.png') . '&#160;<a href="">' . Vars::$LNG['contacts_in'] . '</a></div>
            ';
        if (!isset(Vars::$USER_BAN['1']) && !isset(Vars::$USER_BAN['3']))
            echo '
            <div>' . Functions::getImage('mail_write.png') . '&#160;<a href="pradd.php?act=write&amp;adr=' . $user['user_id'] . '"><b>' . Vars::$LNG['write'] . '</b></a></div>
            ';
    }
    echo '</p></div>
        ';
    echo '
        <div class="phdr"><a href="index.php">' . Vars::$LNG['users'] . '</a></div>
        ';
}