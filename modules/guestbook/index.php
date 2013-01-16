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
define('_IN_GUESTBOOK', 1);

$admin_actions = array(
    'clean'  => 'clean.php',
    'delete' => 'delete.php',
    'edit'   => 'edit.php',
    'reply'  => 'reply.php',
);

$common_actions = array();

if (isset(Router::$ROUTE[1])) {
    if (Vars::$USER_RIGHTS >= 6 && isset($admin_actions[Router::$ROUTE[1]])) {
        $include = $admin_actions[Router::$ROUTE[1]];
    } else {
        $include = FALSE;
    }
} else {
    $include = 'index.php';
}

if ($include && is_file(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $include)) {
    require_once(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $include);
} else {
    header('Location: ' . Vars::$HOME_URL . '404');
}