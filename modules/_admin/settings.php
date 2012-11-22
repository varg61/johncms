<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

// Проверяем права доступа
if (Vars::$USER_RIGHTS != 9) {
    echo Functions::displayError(lng('access_forbidden'));
    exit;
}

$tpl = Template::getInstance();

if (isset($_POST['submit'])) {
    /*
    -----------------------------------------------------------------
    Сохраняем настройки системы
    -----------------------------------------------------------------
    */
    Vars::$SYSTEM_SET['gzip'] = isset($_POST['gzip']);
    Vars::$SYSTEM_SET['email'] = isset($_POST['email']);
//    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(Validate::filterString($_POST['skindef'])) . "' WHERE `key` = 'skindef'");
//    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(htmlspecialchars($_POST['madm'])) . "' WHERE `key` = 'email'");
//    mysql_query("UPDATE `cms_settings` SET `val`='" . intval($_POST['timeshift']) . "' WHERE `key` = 'timeshift'");
//    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(Validate::filterString($_POST['copyright'])) . "' WHERE `key` = 'copyright'");
//    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(Validate::filterString(preg_replace("#/$#", '', trim($_POST['homeurl'])))) . "' WHERE `key` = 'homeurl'");
//    mysql_query("UPDATE `cms_settings` SET `val`='" . intval($_POST['flsz']) . "' WHERE `key` = 'flsz'");
//    mysql_query("UPDATE `cms_settings` SET `val`='" . isset($_POST['gz']) . "' WHERE `key` = 'gzip'");
//    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(Validate::filterString($_POST['meta_key'])) . "' WHERE `key` = 'meta_key'");
//    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(Validate::filterString($_POST['meta_desc'])) . "' WHERE `key` = 'meta_desc'");
//    $req = mysql_query("SELECT * FROM `cms_settings`");
//    $set = array();
//    while ($res = mysql_fetch_row($req)) Vars::$SYSTEM_SET[$res[0]] = $res[1];
//    echo '<div class="rmenu">' . lng('settings_saved') . '</div>';
}

$tpl->contents = $tpl->includeTpl('settings');