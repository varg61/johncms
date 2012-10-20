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

/*
-----------------------------------------------------------------
Проверяем права доступа
-----------------------------------------------------------------
*/
//TODO: Установить права доступа!!!
if (!Vars::$USER_ID) {
    echo Functions::displayError(lng('access_forbidden'));
    exit;
}

/*
-----------------------------------------------------------------
Получаем данные пользователя
-----------------------------------------------------------------
*/
if (Vars::$USER_ID || Vars::$USER_SYS['view_profiles']) {
    if (($user = Vars::getUser()) === FALSE) {
        echo Functions::displayError(lng('user_does_not_exist'));
        exit;
    }
} else {
    echo Functions::displayError(lng('access_guest_forbidden'));
    exit;
}

$tpl = Template::getInstance();
$tpl->contents = $tpl->includeTpl('settings');