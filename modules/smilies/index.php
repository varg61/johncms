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

$user_smileys = 20;
if (empty($_SESSION['ref'])) {
    //TODO: Переделать
    $_SESSION['ref'] = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : Vars::$HOME_URL;
}

$catalog = array();
foreach (glob(ROOTPATH . 'assets' . DIRECTORY_SEPARATOR . 'smilies' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) as $val) {
    $dir = basename($val);
    if (!Vars::$USER_RIGHTS && $dir == '_admin') {
        continue;
    }
    $catalog[$dir] = __($dir);
}

if (isset(Router::$ROUTE[1])) {
    if (Router::$ROUTE[1] == 'mysmilies') {
        $include = 'my_smilies.php';
    } elseif (isset($catalog[Router::$ROUTE[1]])) {
        $include = 'list.php';
    } else {
        header('Location: ' . Vars::$HOME_URL . '/404');
    }
} else {
    $include = 'catalog.php';
}

if ($include && is_file(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $include)) {
    require_once(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $include);
} else {
    header('Location: ' . Vars::$HOME_URL . '/404');
}