<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

@ini_set("max_execution_time", "600");
defined('_IN_JOHNCMS') or die('Error: restricted access');
define('_IN_ADMIN', 1);

if (Vars::$USER_RIGHTS >= 2) {
    $sv_actions = array(
        'counters'        => 'counters.php',
        'firewall'        => 'firewall.php',
        'language'        => 'language.php',
        'scanner'         => 'scanner.php',
        'sitemap'         => 'sitemap.php',
        'smilies'         => 'smilies.php',
        'system_settings' => 'system_settings.php',
    );

    $admin_actions = array(
        'acl'            => 'acl.php',
        'users_settings' => 'users_settings.php',
    );

    $common_actions = array(
        'assets' => 'profile_assets.php',
        'whois'  => 'whois.php',
    );

    if (isset(Router::$ROUTE[1])) {
        if (Vars::$USER_RIGHTS == 9 && isset($sv_actions[Router::$ROUTE[1]])) {
            $include = $sv_actions[Router::$ROUTE[1]];
        } elseif ((Vars::$USER_RIGHTS == 7 || Router::$USER_RIGHTS == 9) && isset($admin_actions[Router::$ROUTE[1]])) {
            $include = $admin_actions[Router::$ROUTE[1]];
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
} else {
    echo Functions::displayError(__('access_forbidden'));
}