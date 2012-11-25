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

global $tpl;

/*
-----------------------------------------------------------------
Удаление аватара
-----------------------------------------------------------------
*/
if (isset($_POST['submit'])
    && isset($_POST['form_token'])
    && isset($_SESSION['form_token'])
    && $_POST['form_token'] == $_SESSION['form_token']
) {
    @unlink(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $tpl->user['id'] . '.gif');
    $tpl->hbar = __('delete_avatar');
    $tpl->message = __('avatar_deleted');
    $tpl->continue = Vars::$URI . '?act=settings&amp;user=' . $tpl->user['id'];
    $tpl->contents = $tpl->includeTpl('message', 1);
} else {
    $tpl->form_token = mt_rand(100, 10000);
    $_SESSION['form_token'] = $tpl->form_token;
    $tpl->contents = $tpl->includeTpl('profile_avatar_delete');
}