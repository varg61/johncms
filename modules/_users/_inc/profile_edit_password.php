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

$tpl = Template::getInstance();

/*
-----------------------------------------------------------------
Меняем пароль
-----------------------------------------------------------------
*/
if (Vars::$USER_ID == $tpl->user['id']
    || (Vars::$USER_RIGHTS == 7 && Vars::$USER_RIGHTS > $tpl->user['rights'])
    || Vars::$USER_RIGHTS == 9
) {
    if (isset($_POST['submit'])
        && isset($_POST['form_token'])
        && isset($_SESSION['form_token'])
        && $_POST['form_token'] == $_SESSION['form_token']
    ) {
        $oldpass = isset($_POST['oldpass']) ? mb_substr(trim($_POST['oldpass']), 0, 50) : '';
        $newpass = isset($_POST['newpass']) ? mb_substr(trim($_POST['newpass']), 0, 50) : '';
        $newconf = isset($_POST['newconf']) ? mb_substr(trim($_POST['newconf']), 0, 50) : '';

        // Проверяем заполнение полей
        if (empty($oldpass)) {
            $tpl->error['oldpass'] = __('error_empty_field');
        }
        if (empty($newpass)) {
            $tpl->error['newpass'] = __('error_empty_field');
        }
        if (empty($newconf)) {
            $tpl->error['newconf'] = __('error_empty_field');
        }

        if (empty($tpl->error)) {
            // Проверяем исходный пароль
            if (crypt($oldpass, Vars::$USER_DATA['password']) !== Vars::$USER_DATA['password']) {
                $tpl->error['oldpass'] = __('error_wrong_password');
            }

            // Проверяем новый пароль
            if (Validate::password($newpass, TRUE) !== TRUE) {
                $tpl->error['newpass'] = Validate::$error['password'];
            } elseif ($newpass !== $newconf) {
                $tpl->error['newconf'] = __('error_passwords_not_match');
            }

            if (empty($tpl->error)) {
                $token = Functions::generateToken();
                $password = crypt($newpass, '$2a$09$' . $token . '$');
                mysql_query("UPDATE `users` SET
                    `password` = '" . mysql_real_escape_string($password) . "',
                    `token` = '" . mysql_real_escape_string($token) . "'
                    WHERE `id` = " . $tpl->user['id']
                );
                if (Vars::$USER_ID == $tpl->user['id']) {
                    setcookie('token', $token, time() + 3600 * 24 * 31, '/');
                    $_SESSION['token'] = $token;
                }

                $tpl->hbar = __('change_password');
                $tpl->continue = Vars::$MODULE_URI . '/profile?act=settings&amp;user=' . $tpl->user['id'];
                $tpl->message = __('password_changed');
                $tpl->contents = $tpl->includeTpl('message', 1);
                exit;
            }
        }
    }

    $tpl->form_token = mt_rand(100, 10000);
    $_SESSION['form_token'] = $tpl->form_token;
    $tpl->contents = $tpl->includeTpl('profile_edit_password');
} else {
    echo Functions::displayError(__('error_rights'));
}