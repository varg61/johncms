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
//TODO: Возможность из админки открывать просмотр профилей для гостей

defined('_IN_JOHNCMS') or die('Error: restricted access');

/*
-----------------------------------------------------------------
Получаем данные пользователя
-----------------------------------------------------------------
*/
if (Vars::$USER_ID) {
    if (($user = Vars::getUser()) === false) {
        echo Functions::displayError(lng('user_does_not_exist'));
        exit;
    }
} else {
    echo Functions::displayError(lng('access_guest_forbidden'));
    exit;
}

/*
-----------------------------------------------------------------
Анкета пользователя
-----------------------------------------------------------------
*/
$tpl              = Template::getInstance();
$tpl->user        = $user;
$tpl->total_photo = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . $user['id'] . "'"), 0);
$tpl->bancount    = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $user['id'] . "'"), 0);

if ($user['id'] == Vars::$USER_ID || Vars::$USER_RIGHTS == 9 || (Vars::$USER_RIGHTS == 7 && Vars::$USER_RIGHTS > $user['rights'])) {
    $menu[] = '<a href="' . Vars::$HOME_URL . '/profile/edit?user=' . $user['id'] . '">' . lng('edit') . '</a>';
}
if ($user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS >= 7 && Vars::$USER_RIGHTS > $user['rights']) {
    $menu[] = '<a href="' . Vars::$HOME_URL . '/admin?act=usr_del&amp;id=' . $user['id'] . '">' . lng('delete') . '</a>';
}
if ($user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS > $user['rights']) {
    $menu[] = '<a href="profile.php?act=ban&amp;mod=do&amp;user=' . $user['id'] . '">' . lng('ban_do') . '</a>';
}
if (isset($menu)) {
    $tpl->menu = Functions::displayMenu($menu);
}

$tpl->userarg = array(
    'lastvisit' => 1,
    'iphist'    => 1,
    'header'    => '<b>ID:' . $user['id'] . '</b>',
    'footer'    => ($user['id'] != Vars::$USER_ID
        ? '<span class="gray">' . lng('where') . ':</span> ' . Functions::displayPlace($user['id'], $user['place'])
        : false)
);

//Управление контактами
$contacts = mysql_query("SELECT * FROM `cms_mail_contacts` WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='" . $user['id'] . "'");
$num_cont = mysql_num_rows($contacts);
if($num_cont) {
	$rows = mysql_fetch_assoc($contacts);
	$tpl->banned = $rows['banned'];
	if($rows['delete'] == 1) $num_cont = 0;
}
$tpl->textbanned = $num_cont && $rows['banned'] == 1 ? lng('contact_delete_ignor') : lng('contact_add_ignor');
$tpl->textcontact = $num_cont ? lng('contact_delete') : lng('contact_add');
//Подключаем шаблон profile
$tpl->contents = $tpl->includeTpl('profile');