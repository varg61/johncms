<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_PROFILE') or die('Error: restricted access');

global $tpl, $user;

/*
-----------------------------------------------------------------
Проверяем права доступа для смены пароля
-----------------------------------------------------------------
*/
if ($user['id'] != Vars::$USER_ID
    && Vars::$USER_RIGHTS != 9
    && (Vars::$USER_RIGHTS < 7 || $user['rights'] >= Vars::$USER_RIGHTS)
) {
    echo Functions::displayError(lng('error_rights'));
    exit;
}

/*
-----------------------------------------------------------------
Меняем пароль
-----------------------------------------------------------------
*/
if (isset($_POST['submit'])
    && !empty($_POST['oldpass'])
    && !empty($_POST['newpass'])
    && !empty($_POST['newconf'])
    && isset($_POST['captcha'])
    && isset($_SESSION['captcha'])
    && isset($_POST['form_token'])
    && isset($_SESSION['form_token'])
    && $_POST['form_token'] == $_SESSION['form_token']
) {
    $error = array();
    $oldpass = trim($_POST['oldpass']);
    $newpass = trim($_POST['newpass']);
    $newconf = trim($_POST['newconf']);

    // Проверяем исходный пароль
    if (crypt($oldpass, Vars::$USER_DATA['password']) !== Vars::$USER_DATA['password']) {
        $error['oldpass'] = lng('error_wrong_password');
    }

    // Проверяем новый пароль

    if (empty($error)) {
        exit;
    } else {
        $tpl->error = $error;
    }
}

$tpl->form_token = mt_rand(100, 10000);
$_SESSION['form_token'] = $tpl->form_token;
$tpl->contents = $tpl->includeTpl('password');