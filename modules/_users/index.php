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
define('_IN_USERS', 1);

$admin_actions = array();

$personal_actions = array(
    'menu'   => 'user_menu.php',
    'edit'   => 'edit_profile.php',
    'option' => 'option_menu.php',
    'status' => 'edit_status.php',
);

$common_actions = array();

if (isset(Router::$ROUTE[1])) {
    if (ctype_digit(Router::$ROUTE[1]) && Router::$ROUTE[1] > 0) {
        if (Users::get(Router::$ROUTE[1]) === FALSE) {
            //TODO: Сделать пересылку на ошибку несуществующего юзера
            echo Functions::displayError(__('user_does_not_exist'));
            exit;
        }

        // Работа с профилями
        if (isset(Router::$ROUTE[2])
            && isset($personal_actions[Router::$ROUTE[2]])
        ) {
            //TODO: Добавить разделение прав
            $include = $personal_actions[Router::$ROUTE[2]];
        } else {
            $include = 'profile.php';
        }
    } else {
        // Работа с общими модулями
        if (isset($common_actions[Router::$ROUTE[1]])) {
            $include = $common_actions[Router::$ROUTE[1]];
        } else {
            $include = FALSE;
        }
    }
} else {
    $include = 'index.php';
}

if ($include && is_file(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $include)) {
    require_once(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $include);
} else {
    echo 'error: ' . $include . ' - ' . Router::$ROUTE[1]; //TODO: Убрать!
    //header('Location: ' . Vars::$HOME_URL . '/404');
}