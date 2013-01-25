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
define('_IN_FORUM', 1);

$admin_actions = array(
    'admin' => 'admin.php',
);

$moder_actions = array(
    'ff' => 'admin.php',
);

$users_action = array(
    'acl'            => 'acl.php',
    'users_settings' => 'users_settings.php',
);

$common_actions = array(
    'assets' => 'profile_assets.php',
);

if (isset(Router::$ROUTE[1])) {
    if (Vars::$USER_RIGHTS >= 7 && isset($admin_actions[Router::$ROUTE[1]])) {
        $include = $admin_actions[Router::$ROUTE[1]];
    } elseif ((Vars::$USER_RIGHTS >= 6 || Vars::$USER_RIGHTS == 3) && isset($moder_actions[Router::$ROUTE[1]])) {
        $include = $moder_actions[Router::$ROUTE[1]];
    } elseif (Vars::$USER_ID && isset($users_action[Router::$ROUTE[1]])) {
        $include = $users_action[Router::$ROUTE[1]];
    } elseif (isset($common_actions[Router::$ROUTE[1]])) {
        $include = $common_actions[Router::$ROUTE[1]];
    } else {
        $include = FALSE;
    }
} else {
    $include = 'index.php';
}

if ($include && is_file(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $include)) {
    require_once(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $include);
} else {
    header('Location: ' . Vars::$HOME_URL . '404/');
}