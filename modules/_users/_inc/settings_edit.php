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
Закрываем настройки от посторонних
-----------------------------------------------------------------
*/
if (!Vars::$USER_ID || $tpl->user['id'] != Vars::$USER_ID) {
    echo Functions::displayError(lng('access_forbidden'));
    exit;
}

/*
-----------------------------------------------------------------
Пользовательские настройки системы
-----------------------------------------------------------------
*/
$tpl_list = array();
foreach (glob(TPLPATH . '*', GLOB_ONLYDIR) as $val) {
    $tpl_list[] = basename($val);
}
sort($tpl_list);
array_unshift($tpl_list, '--default--');
$tpl->tpl_list = $tpl_list;

if (isset($_POST['submit'])
    && isset($_POST['form_token'])
    && isset($_SESSION['form_token'])
    && $_POST['form_token'] == $_SESSION['form_token']
) {
    // Принимаем данные из формы
    if (isset($_POST['timeshift']) && $_POST['timeshift'] > -13 && $_POST['timeshift'] < 13) {
        Vars::$USER_SET['timeshift'] = intval($_POST['timeshift']);
    }
    if (isset($_POST['field_h']) && $_POST['field_h'] > 0 && $_POST['field_h'] < 10) {
        Vars::$USER_SET['field_h'] = intval($_POST['field_h']);
    }
    if (isset($_POST['page_size']) && $_POST['page_size'] > 4 && $_POST['page_size'] < 100) {
        Vars::$USER_SET['page_size'] = intval($_POST['page_size']);
    }
    if (isset($_POST['skin']) && in_array($_POST['skin'], $tpl_list)) {
        Vars::$USER_SET['skin'] = trim($_POST['skin']);
    }
    $lng_select = isset($_POST['iso']) ? trim($_POST['iso']) : FALSE;
    if ($lng_select && array_key_exists($lng_select, Vars::$LNG_LIST)) {
        Vars::$USER_SET['lng'] = $lng_select;
        unset($_SESSION['lng']);
    }
    Vars::$USER_SET['avatar'] = isset($_POST['avatar']);
    Vars::$USER_SET['smileys'] = isset($_POST['smileys']);
    Vars::$USER_SET['translit'] = isset($_POST['translit']);
    Vars::$USER_SET['direct_url'] = isset($_POST['direct_url']);

    // Записываем настройки
    unset($_SESSION['user_set']);
    Vars::setUserData('user_set', Vars::$USER_SET);
    $tpl->save = 1;
}

$tpl->form_token = mt_rand(100, 10000);
$_SESSION['form_token'] = $tpl->form_token;
$tpl->contents = $tpl->includeTpl('settings_edit');