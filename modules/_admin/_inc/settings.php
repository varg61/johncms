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

global $tpl;

if (isset($_POST['submit'])
    && isset($_POST['form_token'])
    && isset($_SESSION['form_token'])
    && $_POST['form_token'] == $_SESSION['form_token']
) {
    Vars::$SYSTEM_SET['timeshift'] = isset($_POST['timeshift']) ? intval($_POST['timeshift']) : 0;
    if (Vars::$SYSTEM_SET['timeshift'] < -12 || Vars::$SYSTEM_SET['timeshift'] > 12) {
        $tpl->error['timeshift'] = lng('error_timeshift');
    }

    Vars::$SYSTEM_SET['copyright'] = isset($_POST['copyright']) ? Validate::checkin($_POST['copyright']) : '';
    if (mb_strlen(Vars::$SYSTEM_SET['copyright']) > 5000) {
        $tpl->error['copyright'] = lng('error_toolong');
    }

    Vars::$SYSTEM_SET['email'] = isset($_POST['email']) ? Validate::checkin($_POST['email']) : '';
    if (!Validate::email(Vars::$SYSTEM_SET['email'], 1, 1)) {
        $tpl->error['email'] = Validate::$error['email'];
    }

    Vars::$SYSTEM_SET['filesize'] = isset($_POST['filesize']) ? abs(intval($_POST['filesize'])) : 1000;
    if (Vars::$SYSTEM_SET['filesize'] < 100 || Vars::$SYSTEM_SET['filesize'] > 50000) {
        $tpl->error['filesize'] = lng('error_wrong_data');
    }

    Vars::$SYSTEM_SET['gzip'] = isset($_POST['gzip']);

    Vars::$SYSTEM_SET['keywords'] = isset($_POST['keywords']) ? Validate::checkin($_POST['keywords']) : '';
    if (mb_strlen(Vars::$SYSTEM_SET['keywords']) > 250) {
        $tpl->error['keywords'] = lng('error_toolong');
    }

    Vars::$SYSTEM_SET['description'] = isset($_POST['description']) ? Validate::checkin($_POST['description']) : '';
    if (mb_strlen(Vars::$SYSTEM_SET['description']) > 250) {
        $tpl->error['description'] = lng('error_toolong');
    }

    if (empty($tpl->error)) {
        mysql_query("REPLACE INTO `cms_settings` SET `key` = 'timeshift', `val` = '" . Vars::$SYSTEM_SET['timeshift'] . "'");
        mysql_query("REPLACE INTO `cms_settings` SET `key` = 'copyright', `val` = '" . mysql_real_escape_string(Vars::$SYSTEM_SET['copyright']) . "'");
        mysql_query("REPLACE INTO `cms_settings` SET `key` = 'email', `val` = '" . mysql_real_escape_string(Vars::$SYSTEM_SET['email']) . "'");
        mysql_query("REPLACE INTO `cms_settings` SET `key` = 'filesize', `val` = '" . Vars::$SYSTEM_SET['filesize'] . "'");
        mysql_query("REPLACE INTO `cms_settings` SET `key` = 'gzip', `val` = '" . Vars::$SYSTEM_SET['gzip'] . "'");
        mysql_query("REPLACE INTO `cms_settings` SET `key` = 'keywords', `val` = '" . mysql_real_escape_string(Vars::$SYSTEM_SET['keywords']) . "'");
        mysql_query("REPLACE INTO `cms_settings` SET `key` = 'description', `val` = '" . mysql_real_escape_string(Vars::$SYSTEM_SET['description']) . "'");
        $tpl->save = 1;
    }
}

$tpl->form_token = mt_rand(100, 10000);
$_SESSION['form_token'] = $tpl->form_token;
$tpl->contents = $tpl->includeTpl('settings');