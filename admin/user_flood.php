<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require_once('../includes/core.php');

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 7) {
    header('Location: http://mobicms.org/404.php');
    exit;
}

require_once('../includes/head.php');
$lng_adm = Vars::loadLanguage('adm');

$set_af = isset(Vars::$SYSTEM_SET['antiflood']) ? unserialize(Vars::$SYSTEM_SET['antiflood']) : array();
echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['admin_panel'] . '</b></a> | ' . $lng_adm['antiflood_settings'] . '</div>';
if (isset($_POST['submit']) || isset($_POST['save'])) {
    // Принимаем данные из формы
    $set_af['mode'] = isset($_POST['mode']) && $_POST['mode'] > 0 && $_POST['mode'] < 5 ? intval($_POST['mode']) : 1;
    $set_af['day'] = isset($_POST['day']) ? intval($_POST['day']) : 10;
    $set_af['night'] = isset($_POST['night']) ? intval($_POST['night']) : 30;
    $set_af['dayfrom'] = isset($_POST['dayfrom']) ? intval($_POST['dayfrom']) : 10;
    $set_af['dayto'] = isset($_POST['dayto']) ? intval($_POST['dayto']) : 22;
    // Проверяем правильность ввода данных
    if ($set_af['day'] < 4)
        $set_af['day'] = 4;
    if ($set_af['day'] > 300)
        $set_af['day'] = 300;
    if ($set_af['night'] < 4)
        $set_af['night'] = 4;
    if ($set_af['night'] > 300)
        $set_af['night'] = 300;
    if ($set_af['dayfrom'] < 6)
        $set_af['dayfrom'] = 6;
    if ($set_af['dayfrom'] > 12)
        $set_af['dayfrom'] = 12;
    if ($set_af['dayto'] < 17)
        $set_af['dayto'] = 17;
    if ($set_af['dayto'] > 23)
        $set_af['dayto'] = 23;
    mysql_query("UPDATE `cms_settings` SET `val` = '" . serialize($set_af) . "' WHERE `key` = 'antiflood' LIMIT 1");
    echo '<div class="rmenu">' . Vars::$LNG['settings_saved'] . '</div>';
} elseif (empty($set_af) || isset($_GET['reset'])) {
    // Устанавливаем настройки по умолчанию (если не заданы в системе)
    echo '<div class="rmenu">' . Vars::$LNG['settings_default'] . '</div>';
    $set_af['mode'] = 2;
    $set_af['day'] = 10;
    $set_af['night'] = 30;
    $set_af['dayfrom'] = 10;
    $set_af['dayto'] = 22;
    @mysql_query("DELETE FROM `cms_settings` WHERE `key` = 'antiflood' LIMIT 1");
    mysql_query("INSERT INTO `cms_settings` SET `key` = 'antiflood', `val` = '" . serialize($set_af) . "'");
}

/*
-----------------------------------------------------------------
Форма ввода параметров Антифлуда
-----------------------------------------------------------------
*/
echo'<form action="user_flood.php" method="post">' .
    '<div class="gmenu"><p><h3>' . $lng_adm['operation_mode'] . '</h3><table cellspacing="2">' .
    '<tr><td valign="top"><input type="radio" name="mode" value="3" ' . ($set_af['mode'] == 3 ? 'checked="checked"' : '') . '/></td><td>' . Vars::$LNG['day'] . '</td></tr>' .
    '<tr><td valign="top"><input type="radio" name="mode" value="4" ' . ($set_af['mode'] == 4 ? 'checked="checked"' : '') . '/></td><td>' . Vars::$LNG['night'] . '</td></tr>' .
    '<tr><td valign="top"><input type="radio" name="mode" value="2" ' . ($set_af['mode'] == 2 ? 'checked="checked"' : '') . '/></td><td>' . Vars::$LNG['day'] . ' / ' . Vars::$LNG['night'] .
    '<br /><small>' . $lng_adm['antiflood_dn_help'] . '</small></td></tr>' .
    '<tr><td valign="top"><input type="radio" name="mode" value="1" ' . ($set_af['mode'] == 1 ? 'checked="checked"' : '') . '/></td><td>' . $lng_adm['adaptive'] .
    '<br /><small>' . $lng_adm['antiflood_ad_help'] . '</small></td></tr>' .
    '</table></p></div>' .
    '<div class="menu"><p><h3>' . $lng_adm['time_limit'] . '</h3>' .
    '<input name="day" size="3" value="' . $set_af['day'] . '" maxlength="3" />&#160;' . Vars::$LNG['day'] . '<br />' .
    '<input name="night" size="3" value="' . $set_af['night'] . '" maxlength="3" />&#160;' . Vars::$LNG['night'] .
    '<br /><small>' . $lng_adm['antiflood_tl_help'] . '</small></p>' .
    '<p><h3>' . $lng_adm['day_mode'] . '</h3>' .
    '<input name="dayfrom" size="2" value="' . $set_af['dayfrom'] . '" maxlength="2" style="text-align:right"/>:00&#160;' . $lng_adm['day_begin'] . ' <span class="gray">(6-12)</span><br />' .
    '<input name="dayto" size="2" value="' . $set_af['dayto'] . '" maxlength="2" style="text-align:right"/>:00&#160;' . $lng_adm['day_end'] . ' <span class="gray">(17-23)</span>' .
    '</p><p><br /><input type="submit" name="submit" value="' . Vars::$LNG['save'] . '"/></p></div></form>' .
    '<div class="phdr"><a href="user_flood.php?reset">' . Vars::$LNG['reset_settings'] . '</a></div>' .
    '<p><a href="index.php">' . Vars::$LNG['admin_panel'] . '</a></p>';

require_once('../includes/end.php');