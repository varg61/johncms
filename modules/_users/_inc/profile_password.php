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
    && isset($_POST['form_token'])
    && isset($_SESSION['form_token'])
    && $_POST['form_token'] == $_SESSION['form_token']
) {
    $error = array();
    $oldpass = mb_substr(trim($_POST['oldpass']), 0, 50);
    $newpass = mb_substr(trim($_POST['newpass']), 0, 50);
    $newconf = mb_substr(trim($_POST['newconf']), 0, 50);

    // Проверяем исходный пароль
    if (crypt($oldpass, Vars::$USER_DATA['password']) !== Vars::$USER_DATA['password']) {
        $error['oldpass'] = lng('error_wrong_password');
    }

    // Проверяем новый пароль
    if (Validate::password($newpass, TRUE) !== TRUE) {
        $error['newpass'] = Validate::$error['password'];
    } elseif ($newpass !== $newconf) {
        $error['newconf'] = lng('error_passwords_not_match');
    }

    if (empty($error)) {
        $token = Functions::generateToken();
        $password = crypt($newpass, '$2a$09$' . $token . '$');
        mysql_query("UPDATE `users` SET
            `password` = '" . mysql_real_escape_string($password) . "',
            `token` = '" . mysql_real_escape_string($token) . "'
            WHERE `id` = " . Vars::$USER_ID
        );
        setcookie('token', $token, time() + 3600 * 24 * 31, '/');
        $_SESSION['token'] = $token;
        echo'<div class="gmenu"><p>' . lng('password_changed') . '</p>' .
            '<p><a href="' . Vars::$HOME_URL . '/cabinet">' . lng('continue') . '</a></p></div>';
        exit;
    }
    $tpl->error = $error;
}

$tpl->form_token = mt_rand(100, 10000);
$_SESSION['form_token'] = $tpl->form_token;
$tpl->contents = $tpl->includeTpl('profile_password');