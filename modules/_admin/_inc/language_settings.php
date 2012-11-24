<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_ADMIN') or die('Error: restricted access');

if (isset($_POST['submit'])
    && isset($_SESSION['form_token'])
    && $_POST['form_token'] == $_SESSION['form_token']
) {
    $iso = isset($_POST['iso']) ? Validate::checkin($_POST['iso']) : FALSE;
    if ($iso && (in_array($iso, Languages::getInstance()->getLngList()) || $iso = '#')) {
        Vars::$SYSTEM_SET['lng'] = $iso;
        mysql_query("REPLACE INTO `cms_settings` SET `key` = 'lng', `val` = '" . mysql_real_escape_string($iso) . "'");
        Template::getInstance()->save = 1;
    }
}

Template::getInstance()->form_token = mt_rand(100, 10000);
$_SESSION['form_token'] = Template::getInstance()->form_token;
Template::getInstance()->contents = Template::getInstance()->includeTpl('language_settings');