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
    echo Functions::displayError(__('access_forbidden'));
    exit;
}

$tpl = Template::getInstance();

/*
-----------------------------------------------------------------
Настройки карты сайта
-----------------------------------------------------------------
*/
if (!isset(Vars::$SYSTEM_SET['sitemap']) || isset($_GET['reset'])) {
    // Задаем настройки по умолчанию
    $settings = array(
        'forum'    => 1,
        'lib'      => 1,
        'users'    => 0,
        'browsers' => 0
    );
    @mysql_query("DELETE FROM `cms_settings` WHERE `key` = 'sitemap'");
    mysql_query("INSERT INTO `cms_settings` SET
        `key` = 'sitemap',
        `val` = '" . mysql_real_escape_string(serialize($settings)) . "'
    ");
    $tpl->default = 1;
} elseif (isset($_POST['submit'])) {
    // Принимаем настройки из формы
    $settings['forum'] = isset($_POST['forum']);
    $settings['lib'] = isset($_POST['lib']);
    $settings['users'] = isset($_POST['users']) && $_POST['users'] == 1 ? 1 : 0;
    $settings['browsers'] = isset($_POST['browsers']) && $_POST['browsers'] == 1 ? 1 : 0;
    mysql_query("UPDATE `cms_settings` SET
        `val` = '" . mysql_real_escape_string(serialize($settings)) . "'
        WHERE `key` = 'sitemap'
    ");
    $tpl->saved = 1;
} else {
    // Получаем сохраненные настройки
    $settings = unserialize(Vars::$SYSTEM_SET['sitemap']);
}

$tpl->settings = $settings;
$tpl->contents = $tpl->includeTpl('sitemap');