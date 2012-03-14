<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNADM') or die('Error: restricted access');

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 9) {
    header('Location: http://mobicms.net/404.php');
    exit;
}
echo '<div class="phdr"><a href="index.php"><b>' . lng('admin_panel') . '</b></a> | ' . lng('site_settings') . '</div>';
if (isset($_POST['submit'])) {
    /*
    -----------------------------------------------------------------
    Сохраняем настройки системы
    -----------------------------------------------------------------
    */
    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(Validate::filterString($_POST['skindef'])) . "' WHERE `key` = 'skindef'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(htmlspecialchars($_POST['madm'])) . "' WHERE `key` = 'email'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . intval($_POST['timeshift']) . "' WHERE `key` = 'timeshift'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(Validate::filterString($_POST['copyright'])) . "' WHERE `key` = 'copyright'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(Validate::filterString(preg_replace("#/$#", '', trim($_POST['homeurl'])))) . "' WHERE `key` = 'homeurl'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . intval($_POST['flsz']) . "' WHERE `key` = 'flsz'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . isset($_POST['gz']) . "' WHERE `key` = 'gzip'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(Validate::filterString($_POST['meta_key'])) . "' WHERE `key` = 'meta_key'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(Validate::filterString($_POST['meta_desc'])) . "' WHERE `key` = 'meta_desc'");
    $req = mysql_query("SELECT * FROM `cms_settings`");
    $set = array();
    while ($res = mysql_fetch_row($req)) Vars::$SYSTEM_SET[$res[0]] = $res[1];
    echo '<div class="rmenu">' . lng('settings_saved') . '</div>';
}
/*
-----------------------------------------------------------------
Форма ввода параметров системы
-----------------------------------------------------------------
*/
echo '<form action="index.php?act=settings" method="post"><div class="menu">';
// Общие настройки
echo '<p>' .
     '<h3>' . lng('common_settings') . '</h3>' .
     lng('site_url') . ':<br/>' . '<input type="text" name="homeurl" value="' . htmlentities(Vars::$HOME_URL) . '"/><br/>' .
     lng('site_copyright') . ':<br/>' . '<input type="text" name="copyright" value="' . htmlentities(Vars::$SYSTEM_SET['copyright'], ENT_QUOTES, 'UTF-8') . '"/><br/>' .
     lng('site_email') . ':<br/>' . '<input name="madm" maxlength="50" value="' . htmlentities(Vars::$SYSTEM_SET['email']) . '"/><br />' .
     lng('file_maxsize') . ' (kb):<br />' . '<input type="text" name="flsz" value="' . intval(Vars::$SYSTEM_SET['flsz']) . '"/><br />' .
     '<input name="gz" type="checkbox" value="1" ' . (Vars::$SYSTEM_SET['gzip'] ? 'checked="checked"' : '') . ' />&#160;' . lng('gzip_compress') .
     '</p>';
// Настройка времени
echo '<p>' .
     '<h3>' . lng('clock_settings') . '</h3>' .
     '<input type="text" name="timeshift" size="2" maxlength="3" value="' . Vars::$SYSTEM_SET['timeshift'] . '"/> ' . lng('time_shift') . ' (+-12)<br />' .
     '<span style="font-weight:bold; background-color:#C0FFC0">' . date("H:i", time() + Vars::$SYSTEM_SET['timeshift'] * 3600) . '</span> ' . lng('system_time') .
     '<br /><span style="font-weight:bold; background-color:#FFC0C0">' . date("H:i") . '</span> ' . lng('server_time') .
     '</p>';
// META тэги
echo '<p>' .
     '<h3>' . lng('meta_tags') . '</h3>' .
     '&#160;' . lng('meta_keywords') . ':<br />&#160;<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="meta_key">' . Vars::$SYSTEM_SET['meta_key'] . '</textarea><br />' .
     '&#160;' . lng('meta_description') . ':<br />&#160;<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="meta_desc">' . Vars::$SYSTEM_SET['meta_desc'] . '</textarea>' .
     '</p>';
// Выбор темы оформления
echo '<p><h3>' . lng('design_template') . '</h3>&#160;<select name="skindef">';
$dir = opendir('../theme');
while ($skindef = readdir($dir)) {
    if (($skindef != '.') && ($skindef != '..') && ($skindef != '.svn')) {
        $skindef = str_replace('.css', '', $skindef);
        echo '<option' . (Vars::$SYSTEM_SET['skindef'] == $skindef ? ' selected="selected">' : '>') . $skindef . '</option>';
    }
}
closedir($dir);
echo '</select>' .
     '</p><p><input type="submit" name="submit" value="' . lng('save') . '"/></p></div></form>' .
     '<div class="phdr">&#160;</div>' .
     '<p><a href="index.php">' . lng('admin_panel') . '</a></p>';