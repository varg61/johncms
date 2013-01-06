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
Административные функции
-----------------------------------------------------------------
*/
if (Vars::$USER_RIGHTS >= 7) {
    if (isset($_POST['submit'])
        && isset($_POST['rights'])
        && isset($_POST['password'])
        && isset($_POST['form_token'])
        && isset($_SESSION['form_token'])
        && $_POST['form_token'] == $_SESSION['form_token']
        && $_POST['rights'] != $tpl->user['rights']
        && $_POST['rights'] >= 0
        && $_POST['rights'] != 8
        && $_POST['rights'] <= 9
    ) {
        $rights = intval($_POST['rights']);
        $password = trim($_POST['password']);
        if (Validate::password($password) === TRUE
            && crypt($password, Vars::$USER_DATA['password']) === Vars::$USER_DATA['password']
            && (Vars::$USER_RIGHTS == 9 || (Vars::$USER_RIGHTS == 7 && $rights < 7))
        ) {
            // Если пароль совпадает, обрабатываем форму
            mysql_query("UPDATE `users` SET `rights` = '$rights' WHERE `id` = " . $tpl->user['id']);
            $tpl->user['rights'] = $rights;
            $tpl->save = 1;
            if ($tpl->user['id'] == Vars::$USER_ID) {
                header('Location: ' . Vars::$URI . '?act=settings');
                exit;
            }
        } else {
            $tpl->error['password'] = __('error_wrong_password');
        }
    }

    $tpl->form_token = mt_rand(100, 10000);
    $_SESSION['form_token'] = $tpl->form_token;
    $tpl->contents = $tpl->includeTpl('profile_edit_admin');
} else {
    echo Functions::displayError(__('access_forbidden'));
}