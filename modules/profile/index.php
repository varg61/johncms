<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

//TODO: Добавить информацию о дне рождении
//TODO: Добавить информацию о подтверждении регистрации
//TODO: Добавить ссылки "Написать" и "В контакты"
//TODO: Возможность из админки открывать просмотр профилей для гостей

defined('_IN_JOHNCMS') or die('Error: restricted access');

/*
-----------------------------------------------------------------
Получаем данные пользователя
-----------------------------------------------------------------
*/
if (Vars::$USER_ID) {
    if (($user = Vars::getUser()) === false) {
        echo Functions::displayError(Vars::$LNG['user_does_not_exist']);
        exit;
    }
} else {
    echo Functions::displayError(Vars::$LNG['access_guest_forbidden']);
    exit;
}

/*
-----------------------------------------------------------------
Анкета пользователя
-----------------------------------------------------------------
*/
$tpl              = Template::getInstance();
$tpl->lng         = Vars::loadLanguage(1);
$tpl->user        = $user;
$tpl->total_photo = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . $user['id'] . "'"), 0);
$tpl->bancount    = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $user['id'] . "'"), 0);

if ($user['id'] == Vars::$USER_ID || Vars::$USER_RIGHTS == 9 || (Vars::$USER_RIGHTS == 7 && Vars::$USER_RIGHTS > $user['rights'])) {
    $menu[] = '<a href="' . Vars::$HOME_URL . '/profile/edit?user=' . $user['id'] . '">' . Vars::$LNG['edit'] . '</a>';
}
if ($user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS >= 7 && Vars::$USER_RIGHTS > $user['rights']) {
    $menu[] = '<a href="' . Vars::$HOME_URL . '/' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=usr_del&amp;id=' . $user['id'] . '">' . Vars::$LNG['delete'] . '</a>';
}
if ($user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS > $user['rights']) {
    $menu[] = '<a href="profile.php?act=ban&amp;mod=do&amp;user=' . $user['id'] . '">' . Vars::$LNG['ban_do'] . '</a>';
}
if (isset($menu)) {
    $tpl->menu = Functions::displayMenu($menu);
}

$tpl->userarg = array(
    'lastvisit' => 1,
    'iphist'    => 1,
    'header'    => '<b>ID:' . $user['id'] . '</b>',
    'footer'    => ($user['id'] != Vars::$USER_ID
        ? '<span class="gray">' . Vars::$LNG['where'] . ':</span> ' . Functions::displayPlace($user['id'], $user['place'])
        : false)
);

$tpl->contents = $tpl->includeTpl('profile');