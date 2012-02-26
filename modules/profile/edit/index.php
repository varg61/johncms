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
Проверяем права доступа для редактирования Профиля
-----------------------------------------------------------------
*/
if ($user['id'] != Vars::$USER_ID && (Vars::$USER_RIGHTS < 7 || $user['rights'] > Vars::$USER_RIGHTS)) {
    echo Functions::displayError(Vars::$LNG['error_rights']);
    exit;
}

$tpl = Template::getInstance();
$tpl->lng = Vars::loadLanguage(1);
$tpl->user = $user;

$menu[] = '<a href="' . Vars::$URI . '?act=nick&amp;user=' . $user['id'] . '">' . $tpl->lng['change_nick'] . '</a>';
$menu[] = '<a href="' . Vars::$URI . '?act=status&amp;user=' . $user['id'] . '">' . $tpl->lng['change_status'] . '</a>';
$menu[] = '<a href="' . Vars::$URI . '?act=avatar&amp;user=' . $user['id'] . '">' . $tpl->lng['change_avatar'] . '</a>';
$arg['sub'] = '<p><b>' . $tpl->lng['change'] . '</b>: ' . Functions::displayMenu($menu) . '</p>';
$tpl->userarg = $arg;

switch (Vars::$ACT) {
    case'delete_avatar':
        // Удаляем аватар
        if (isset($_POST['submit'])) {
            @unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.gif');
            header('Location: ' . Vars::$URI . '?user=' . $user['id']);
        } else {
            $tpl->contents = $tpl->includeTpl('delete_avatar');
        }
        break;

    case'delete_photo':
        // Удаляем фото
        if (isset($_POST['submit'])) {
            @unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '.jpg');
            @unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '_small.jpg');
            header('Location: ' . Vars::$URI . '?user=' . $user['id']);
        } else {
            $tpl->contents = $tpl->includeTpl('delete_photo');
        }
        break;

    case 'administration':
        $tpl->contents = $tpl->includeTpl('profile_edit_adm');
        break;

    case 'nick':
        $tpl->contents = $tpl->includeTpl('change_nickname');
        break;

    case 'status':
        $tpl->contents = $tpl->includeTpl('change_status');
        break;

    case'avatar':
        $tpl->contents = $tpl->includeTpl('change_avatar');
        break;

    default:
        // Редактируем анкету
        if (is_file(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.gif')) {
            $tpl->avatar = true;
        }
        if (is_file(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '_small.jpg')) {
            $tpl->photo = true;
        }
        $tpl->contents = $tpl->includeTpl('profile_edit');
}
