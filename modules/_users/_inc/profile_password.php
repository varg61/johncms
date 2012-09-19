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
Меняем пароль
-----------------------------------------------------------------
*/
if (Vars::$USER_ID == $user['id']
    || (Vars::$USER_RIGHTS == 7 && Vars::$USER_RIGHTS > $user['rights'])
    || Vars::$USER_RIGHTS == 9
) {
    if (isset($_POST['submit'])
        && isset($_POST['form_token'])
        && isset($_SESSION['form_token'])
        && $_POST['form_token'] == $_SESSION['form_token']
    ) {
        $error = array();
        $oldpass = isset($_POST['oldpass']) ? mb_substr(trim($_POST['oldpass']), 0, 50) : '';
        $newpass = isset($_POST['newpass']) ? mb_substr(trim($_POST['newpass']), 0, 50) : '';
        $newconf = isset($_POST['newconf']) ? mb_substr(trim($_POST['newconf']), 0, 50) : '';

        // Проверяем заполнение полей
        if (empty($oldpass)) {
            $error['oldpass'] = lng('error_empty_field');
        }
        if (empty($newpass)) {
            $error['newpass'] = lng('error_empty_field');
        }
        if (empty($newconf)) {
            $error['newconf'] = lng('error_empty_field');
        }

        if (empty($error)) {
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
                    WHERE `id` = " . $user['id']
                );
                if (Vars::$USER_ID == $user['id']) {
                    setcookie('token', $token, time() + 3600 * 24 * 31, '/');
                    $_SESSION['token'] = $token;
                }
                $tpl->continue = Vars::$MODULE_URI . '/settings&amp;user=' . $user['id'];
                $tpl->message = lng('password_changed');
                $tpl->contents = $tpl->includeTpl('message', 1);
                exit;
            }
        }
        $tpl->error = $error;
    }

    $tpl->form_token = mt_rand(100, 10000);
    $_SESSION['form_token'] = $tpl->form_token;
    $tpl->contents = $tpl->includeTpl('profile_change_password');
} else {
    echo Functions::displayError(lng('error_rights'));
    exit;
}