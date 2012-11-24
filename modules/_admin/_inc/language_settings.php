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

/*
-----------------------------------------------------------------
Читаем каталог с файлами языков
-----------------------------------------------------------------
*/
$lng_list = array();
$lng_desc = array();
foreach (glob(SYSPATH . 'languages' . DIRECTORY_SEPARATOR . '*.ini') as $val) {
    $iso = basename($val, '.ini');
    $desc = parse_ini_file($val);
    $lng_list[$iso] = isset($desc['name']) && !empty($desc['name']) ? $desc['name'] : $iso;
    $lng_desc[$iso] = $desc;
}

/*
-----------------------------------------------------------------
Автоустановка языков
-----------------------------------------------------------------
*/
if (isset($_GET['refresh'])) {
    mysql_query("DELETE FROM `cms_settings` WHERE `key` = 'lng_list'");
    Vars::$LNG_LIST = array();
}
$lng_add = array_diff(array_keys($lng_list), array_keys(Vars::$LNG_LIST));
$lng_del = array_diff(array_keys(Vars::$LNG_LIST), array_keys($lng_list));
if (!empty($lng_add) || !empty($lng_del)) {
    if (!empty($lng_del) && in_array(Vars::$SYSTEM_SET['lng'], $lng_del)) {
        // Если удаленный язык был системный, то меняем на первый доступный
        mysql_query("UPDATE `cms_settings` SET `val` = '" . key($lng_list) . "' WHERE `key` = 'lng' LIMIT 1");
    }
    $req = mysql_query("SELECT * FROM `cms_settings` WHERE `key` = 'lng_list'");
    if (mysql_num_rows($req)) {
        mysql_query("UPDATE `cms_settings` SET `val` = '" . mysql_real_escape_string(serialize($lng_list)) . "' WHERE `key` = 'lng_list' LIMIT 1");
    } else {
        mysql_query("INSERT INTO `cms_settings` SET `key` = 'lng_list', `val` = '" . mysql_real_escape_string(serialize($lng_list)) . "'");
    }
}

if (isset($_POST['submit'])
    && isset($_POST['form_token'])
    && isset($_SESSION['form_token'])
    && $_POST['form_token'] == $_SESSION['form_token']
) {
    $iso = isset($_POST['iso']) ? trim($_POST['iso']) : false;
    if ($iso && array_key_exists($iso, $lng_list)) {
        mysql_query("REPLACE INTO `cms_settings` SET `key` = 'lng', `val` = '" . mysql_real_escape_string($iso) . "'");
        $tpl->save = 1;
    }
}

/*
-----------------------------------------------------------------
Выводим список доступных языков
-----------------------------------------------------------------
*/
foreach ($lng_desc as $key => $val) {
    echo'<tr>' .
        '<td valign="top"><input type="radio" value="' . $key . '" name="iso" ' . (isset(Vars::$SYSTEM_SET['lng']) && $key == Vars::$SYSTEM_SET['lng'] ? 'checked="checked"' : '') . '/></td>' .
        '<td style="padding-bottom:6px">' .
        Functions::getImage('flag_' . $key . '.gif') .
        '&#160;<a href="index.php?act=languages&amp;mod=module&amp;language=' . $key . '"><b>' . $val['name'] . '</b></a>&#160;<span class="green">[' . $key . ']</span>' .
        '</tr>';
}
echo'<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="' . lng('save') . '" /></td></tr>' .
    '</table></p>' .
    '</form></div>' .
    '<div class="phdr">' . lng('total') . ': <b>' . count($lng_desc) . '</b></div>' .
    '<p><a href="' . Vars::$URI . '?refresh">' . lng('refresh_descriptions') . '</a><br />' .
    '<a href="' . Vars::$MODULE_URI . '">' . lng('admin_panel') . '</a></p>';

$tpl->form_token = mt_rand(100, 10000);
$_SESSION['form_token'] = $tpl->form_token;
$tpl->contents = $tpl->includeTpl('language_settings');