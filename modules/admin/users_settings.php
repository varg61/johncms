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

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 9) {
    header('Location: http://johncms.com/404');
    exit;
}

$set_af = isset(Vars::$SYSTEM_SET['antiflood']) ? unserialize(Vars::$SYSTEM_SET['antiflood']) : array();

echo'<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . lng('admin_panel') . '</b></a> | ' . lng('users') . '</div>';

if (isset($_POST['submit'])) {
    $set_af['mode'] = isset($_POST['mode']) && $_POST['mode'] > 0 && $_POST['mode'] < 5 ? intval($_POST['mode']) : 1;
    $set_af['day'] = isset($_POST['day']) ? intval($_POST['day']) : 10;
    $set_af['night'] = isset($_POST['night']) ? intval($_POST['night']) : 30;
    mysql_query("REPLACE INTO `cms_settings` SET
        `key` = 'antiflood',
        `val` = '" . mysql_real_escape_string(serialize($set_af)) . "'
    ");
    echo'<div class="gmenu"><p>' . lng('settings_saved') . '</p></div>';
} elseif (isset($_POST['reset'])) {

}

echo'<div class="menu">' .
    '<form action="' . Vars::$URI . '" method="post">' .

    // Управление регистрацией
    '<div class="formblock">' .
    '<label>' . lng('registration') . '</label>' .
    '<br/>' .
    '<input type="radio" value="2" name="reg" ' . (Vars::$SYSTEM_SET['mod_reg'] == 2 ? 'checked="checked"' : '') . '/>&#160;' .
    lng('access_enabled') . '<br />' .
    '<input type="radio" value="1" name="reg" ' . (Vars::$SYSTEM_SET['mod_reg'] == 1 ? 'checked="checked"' : '') . '/>&#160;' .
    lng('access_with_moderation') . '<br />' .
    '<input type="radio" value="0" name="reg" ' . (!Vars::$SYSTEM_SET['mod_reg'] ? 'checked="checked"' : '') . '/>&#160;' .
    lng('access_disabled') .
    '</div>' .

    // Управление аватарами
    '<div class="formblock">' .
    '<label>' . lng('avatars') . '</label>' .
    '<br/>' .
    '<input name="libcomm" type="checkbox" value="1" ' . (Vars::$SYSTEM_SET['mod_lib_comm'] ? 'checked="checked"' : '') . ' />&#160;' .
    lng('upload_avatars') .
    '<br/>' .
    '<input name="libcomm" type="checkbox" value="1" ' . (Vars::$SYSTEM_SET['mod_lib_comm'] ? 'checked="checked"' : '') . ' />&#160;' .
    lng('upload_animation') .
    '</div>' .

    // Управление Антифлудом
    '<div class="formblock">' .
    '<label>' . lng('antiflood') . '</label>' .
    '<br/>' .
    '<input type="radio" name="mode" value="3" ' . ($set_af['mode'] == 3 ? 'checked="checked"' : '') . '/>' .
    '<input name="day" size="3" value="' . $set_af['day'] . '" maxlength="3" />&#160;' .
    lng('sec') . ', ' . lng('day') .
    '<br/>' .
    '<input type="radio" name="mode" value="4" ' . ($set_af['mode'] == 4 ? 'checked="checked"' : '') . '/>' .
    '<input name="night" size="3" value="' . $set_af['night'] . '" maxlength="3" />&#160;' .
    lng('sec') . ', ' . lng('night') .
    '<br/>' .
    '<input type="radio" name="mode" value="2" ' . ($set_af['mode'] == 2 ? 'checked="checked"' : '') . '/>&#160;' .
    lng('autoswitch') .
    '<br/>' .
    '<input type="radio" name="mode" value="1" ' . ($set_af['mode'] == 1 ? 'checked="checked"' : '') . '/>&#160;' .
    lng('adaptive') .
    '</div>' .

    // Кнопка "сохранить"
    '<div class="formblock">' .
    '<input type="submit" name="submit" value="' . lng('save') . '"/>' .
    '</div>' .

    '</form>' .
    '</div>' .
    '<div class="phdr"><a href="' . Vars::$URI . '?reset">' . lng('reset_settings') . '</a></div>' .
    '<p><a href="' . Vars::$MODULE_URI . '">' . lng('admin_panel') . '</a></p>';