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
if (Vars::$USER_RIGHTS < 7) {
    echo Functions::displayError(lng('access_forbidden'));
    exit;
}

$tpl = Template::getInstance();

if (isset($_POST['submit'])) {
    // Записываем настройки в базу
    mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['reg']) ? intval($_POST['reg']) : 0) . "' WHERE `key`='mod_reg'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['forum']) ? intval($_POST['forum']) : 0) . "' WHERE `key`='mod_forum'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['guest']) ? intval($_POST['guest']) : 0) . "' WHERE `key`='mod_guest'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['lib']) ? intval($_POST['lib']) : 0) . "' WHERE `key`='mod_lib'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['gal']) ? intval($_POST['gal']) : 0) . "' WHERE `key`='mod_gal'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['down']) ? intval($_POST['down']) : 0) . "' WHERE `key`='mod_down'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . isset($_POST['libcomm']) . "' WHERE `key`='mod_lib_comm'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . isset($_POST['galcomm']) . "' WHERE `key`='mod_gal_comm'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . isset($_POST['downcomm']) . "' WHERE `key`='mod_down_comm'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['active']) ? intval($_POST['active']) : 0) . "' WHERE `key`='active'");
    $req = mysql_query("SELECT * FROM `cms_settings`");
    $set = array();
    while ($res = mysql_fetch_row($req)) Vars::$SYSTEM_SET[$res[0]] = $res[1];
    mysql_free_result($req);
    $tpl->saved = 1;
}

$tpl->color = array('red', 'yelow', 'green', 'gray');
$tpl->contents = $tpl->includeTpl('acl');