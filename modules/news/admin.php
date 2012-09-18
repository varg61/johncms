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
$tpl->settings = unserialize(Vars::$SYSTEM_SET['news']);

/*
-----------------------------------------------------------------
Настройки Новостей
-----------------------------------------------------------------
*/
if (!isset(Vars::$SYSTEM_SET['news']) || isset($_GET['reset'])) {
    // Задаем настройки по умолчанию
    $tpl->settings = array(
        'view'     => '1',
        'size'     => '500',
        'quantity' => '3',
        'days'     => '7',
        'breaks'   => '1',
        'smileys'  => '1',
        'tags'     => '1',
        'kom'      => '1'
    );
    mysql_query("INSERT INTO `cms_settings` SET
        `key` = 'news',
        `val` = '" . mysql_real_escape_string(serialize($tpl->settings)) . "'
        ON DUPLICATE KEY UPDATE
        `val` = '" . mysql_real_escape_string(serialize($tpl->settings)) . "'
    ");
    $tpl->default = 1;
} elseif (isset($_POST['submit'])) {
    // Принимаем настройки из формы
    $settings['view'] = isset($_POST['view']) && $_POST['view'] >= 0 && $_POST['view'] < 4 ? intval($_POST['view']) : 1;
    $settings['size'] = isset($_POST['size']) && $_POST['size'] >= 100 && $_POST['size'] <= 1000 ? intval($_POST['size']) : 500;
    $settings['quantity'] = isset($_POST['quantity']) && $_POST['quantity'] > 0 && $_POST['quantity'] < 16 ? intval($_POST['quantity']) : 3;
    $settings['days'] = isset($_POST['days']) && $_POST['days'] > 0 && $_POST['days'] < 16 ? intval($_POST['days']) : 7;
    $settings['breaks'] = isset($_POST['breaks']);
    $settings['smileys'] = isset($_POST['smileys']);
    $settings['tags'] = isset($_POST['tags']);
    $settings['kom'] = isset($_POST['kom']);
    mysql_query("UPDATE `cms_settings` SET
        `val` = '" . mysql_real_escape_string(serialize($settings)) . "'
        WHERE `key` = 'news'
    ");
    $tpl->settings = $settings;
    $tpl->saved = 1;
} else {
    // Получаем сохраненные настройки
    $tpl->settings = unserialize(Vars::$SYSTEM_SET['news']);
}

$tpl->contents = $tpl->includeTpl('admin');