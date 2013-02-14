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

$admin_actions = array(
    'rank' => 'rank.php'
);

$personal_actions = array(
    'avatar'   => 'avatar.php',
    'edit'     => 'edit.php',
    'menu'     => 'menu.php',
    'option'   => 'option.php',
    'settings' => 'settings.php',
    'status'   => 'status.php',
);

$common_actions = array(
    'login' => 'login.php'
);

if (isset(Router::$ROUTE[1])) {
    if (ctype_digit(Router::$ROUTE[1]) && Router::$ROUTE[1] > 0) {
        if (Users::get(Router::$ROUTE[1]) === FALSE) {
            //TODO: Сделать пересылку на ошибку несуществующего юзера
            echo Functions::displayError(__('user_does_not_exist'));
            exit;
        }

        // Работа с профилями
        if (isset(Router::$ROUTE[2])) {
            if (Vars::$USER_RIGHTS >= 7
                && isset($admin_actions[Router::$ROUTE[2]])
            ) {
                $include = $admin_actions[Router::$ROUTE[2]];
            } elseif (Vars::$USER_RIGHTS == 9
                || (Vars::$USER_RIGHTS == 7 && Vars::$USER_RIGHTS > Users::$data['rights'])
                || (Vars::$USER_ID && Vars::$USER_ID == Users::$data['id'])
                && isset($personal_actions[Router::$ROUTE[2]])
            ) {
                $include = $personal_actions[Router::$ROUTE[2]];
            } else {
                $include = FALSE;
            }
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
    header('Location: ' . Vars::$HOME_URL . '404/');
}