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
echo'<div class="phdr"><a href="' . Vars::$HOME_URL . '/admin"><b>' . __('admin_panel') . '</b></a> | ' . __('downloads') . '</div>';

/*
-----------------------------------------------------------------
Настройки Загруз-центра
-----------------------------------------------------------------
*/
if (!isset(Vars::$SYSTEM_SET['download']) || isset($_GET['reset'])) {
    // Задаем настройки по умолчанию
    $settings = array('mod'           => 1,
                      'theme_screen'  => 1,
                      'top'           => 25,
                      'icon_java'     => 1,
                      'video_screen'  => 1,
                      'screen_resize' => 1);
    mysql_query("INSERT INTO `cms_settings` SET `key` = 'download', `val` = '" . mysql_real_escape_string(serialize($settings)) . "'");
    echo '<div class="rmenu"><p>' . __('settings_default') . '</p></div>';
} elseif (isset($_POST['submit'])) {
    // Принимаем настройки из формы
    $settings['mod'] = isset($_POST['mod']) ? 1 : 0;
    $settings['icon_java'] = isset($_POST['icon_java']) ? 1 : 0;
    $settings['theme_screen'] = isset($_POST['theme_screen']) ? 1 : 0;
    $settings['video_screen'] = isset($_POST['video_screen']) ? 1 : 0;
    $settings['screen_resize'] = isset($_POST['screen_resize']) ? 1 : 0;
    $settings['top'] = isset($_POST['top']) ? intval($_POST['top']) : 25;
    if ($settings['top'] < 25 || $settings['top'] > 100) $settings['top'] = 25;
    mysql_query("UPDATE `cms_settings` SET `val` = '" . serialize($settings) . "' WHERE `key` = 'download'");
    echo '<div class="gmenu"><p>' . __('settings_saved') . '</p></div>';
} else {
    // Получаем сохраненные настройки
    $settings = unserialize(Vars::$SYSTEM_SET['download']);
}

/*
-----------------------------------------------------------------
Форма ввода настроек
-----------------------------------------------------------------
*/
echo'<form action="' . Vars::$URI . '?act=download" method="post">' .
    '<div class="menu"><p><h3>' . __('functions_download') . '</h3></p>' .
    '<p>&nbsp;<input name="mod" type="checkbox" value="1" ' . ($settings['mod'] ? 'checked="checked"' : '') . ' />&nbsp;' . __('set_files_mod') . '<br />' .
    '&nbsp;<input name="theme_screen" type="checkbox" value="1" ' . ($settings['theme_screen'] ? 'checked="checked"' : '') . ' />&nbsp;' . __('set_auto_screen') . '<br />' .
    '&nbsp;<input name="video_screen" type="checkbox" value="1" ' . ($settings['video_screen'] ? 'checked="checked"' : '') . ' />&nbsp;' . __('set_auto_screen_video') . '<br />' .
    '&nbsp;<input name="icon_java" type="checkbox" value="1" ' . ($settings['icon_java'] ? 'checked="checked"' : '') . ' />&nbsp;' . __('set_java_icons') . '<br />' .
    '&nbsp;<input name="screen_resize" type="checkbox" value="1" ' . ($settings['screen_resize'] ? 'checked="checked"' : '') . ' />&nbsp;' . __('set_screen_resize') . '</p>' .
    '<p><h3>' . __('set_top_files') . '</h3>&nbsp;<input type="text" size="3" maxlength="3" name="top" value="' . $settings['top'] . '" />&nbsp;(25 - 100)</p>' .
    '<p><input type="submit" value="' . __('save') . '" name="submit" /></p></div>' .
    '<div class="phdr"><a href="' . Vars::$URI . '?reset">' . __('reset_settings') . '</a>' .
    '</div></form>' .
    '<p><a href="' . Vars::$HOME_URL . '/admin">' . __('admin_panel') . '</a><br />' .
    '<a href="' . Vars::$MODULE_URI . '">' . __('downloads') . '</a></p>';