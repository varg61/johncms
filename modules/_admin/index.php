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
    $tpl = Template::getInstance();
    $tpl->error = array();

    $sv_actions = array(
        'firewall'        => 'firewall.php',
        'language'        => 'language_settings.php',
        'scanner'         => 'spy_scanner.php',
        'sitemap'         => 'sitemap.php',
        'system_settings' => 'system_settings.php',
    );

    $admin_actions = array(
        'acl'            => 'access_control.php',
        'users_settings' => 'users_settings.php',
    );

    $common_actions = array(
        'assets' => 'profile_assets.php',
    );

    $include = FALSE;
    if (Vars::$ACT
        && Vars::$USER_RIGHTS == 9
        && isset($sv_actions[Vars::$ACT])
    ) {
        $include = $sv_actions[Vars::$ACT];
    } elseif (Vars::$ACT
        && (Vars::$USER_RIGHTS == 7 || Vars::$USER_RIGHTS == 9)
        && isset($admin_actions[Vars::$ACT])
    ) {
        $include = $admin_actions[Vars::$ACT];
    } elseif (Vars::$ACT
        && isset($common_actions[Vars::$ACT])
    ) {
        $include = $common_actions[Vars::$ACT];
    }

    if ($include && is_file(MODPATH . Vars::$MODULE_PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $include)) {
        require_once(MODPATH . Vars::$MODULE_PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $include);
    } else {
        // Главное меню Админ панели
        if (isset($_SESSION['form_token'])) {
            unset($_SESSION['form_token']);
        }
        $tpl->usrTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `level` > 0"), 0);
        $tpl->regTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `level` = '0'"), 0);
        //$tpl->banTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time` > '" . time() . "'"), 0);
        $tpl->contents = $tpl->includeTpl('index');
    }
} else {
    echo Functions::displayError(__('access_forbidden'));
}