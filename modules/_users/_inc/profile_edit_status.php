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
Смена статуса
-----------------------------------------------------------------
*/
$tpl->status = Validate::checkout($tpl->user['status']);
if ($tpl->setUsers['change_status'] || Vars::$USER_RIGHTS >= 7) {
    if (isset($_POST['submit'])
        && isset($_POST['status'])
        && isset($_POST['form_token'])
        && isset($_SESSION['form_token'])
        && $_POST['form_token'] == $_SESSION['form_token']
        && $_POST['status'] != $tpl->user['status']
    ) {
        $status = trim($_POST['status']);
        $tpl->status = Validate::checkout($status);
        if (empty($status)
            || (mb_strlen($status) > 2 && mb_strlen($status) < 51)
        ) {
            mysql_query("UPDATE `users` SET `status` = '" . mysql_real_escape_string($status) . "' WHERE `id` = " . $tpl->user['id']);
            $tpl->user['status'] = $status;
            $tpl->save = 1;
        } else {
            $tpl->error['status'] = __('error_status_lenght');
        }
    }

    $tpl->form_token = mt_rand(100, 10000);
    $_SESSION['form_token'] = $tpl->form_token;
    $tpl->contents = $tpl->includeTpl('profile_edit_status');
} else {
    echo Functions::displayError(__('access_forbidden'));
}