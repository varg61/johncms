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
    echo Functions::displayError(__('access_forbidden'));
    exit;
}

$tpl = Template::getInstance();

if (isset($_POST['submit'])) {
    $acl = array();
    $fields = array(
        'forum',
        'album',
        'albumcomm',
        'guestbook',
        'library',
        'libcomm',
        'downloads',
        'downcomm',
        'stat'
    );

    foreach ($fields as $val) {
        $acl[$val] = isset($_POST[$val]) ? intval($_POST[$val]) : 0;
    }

    // Записываем настройки в базу
    mysql_query("INSERT INTO `cms_settings`
        SET `key` = 'acl',
        `val` = '" . mysql_real_escape_string(serialize($acl)) . "'
        ON DUPLICATE KEY UPDATE
        `val` = '" . mysql_real_escape_string(serialize($acl)) . "'
    ");
    $req = mysql_query("SELECT * FROM `cms_settings` WHERE `key` = 'acl'");
    $res = mysql_fetch_assoc($req);
    Vars::$ACL = unserialize($res['val']);
    $tpl->saved = 1;
}

$tpl->color = array('red', 'yelow', 'green', 'gray');
$tpl->contents = $tpl->includeTpl('acl');