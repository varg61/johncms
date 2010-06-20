<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNADM') or die('Error: restricted access');
if ($rights != 9)
    die('Error: restricted access');
echo '<div class="phdr"><a href="index.php"><b>' . $lng['admin_panel'] . '</b></a> | ' . $lng['site_settings'] . '</div>';

if (isset($_POST['submit'])) {
    /*
    -----------------------------------------------------------------
    Сохраняем настройки системы
    -----------------------------------------------------------------
    */
    mysql_query("UPDATE `cms_settings` SET `val`='" . check($_POST['skindef']) . "' WHERE `key` = 'skindef'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(htmlspecialchars($_POST['madm'])) . "' WHERE `key` = 'emailadmina'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . intval($_POST['sdvigclock']) . "' WHERE `key` = 'sdvigclock'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . check($_POST['copyright']) . "' WHERE `key` = 'copyright'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . check($_POST['homeurl']) . "' WHERE `key` = 'homeurl'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . intval($_POST['flsz']) . "' WHERE `key` = 'flsz'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . intval($_POST['gz']) . "' WHERE `key` = 'gzip'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . intval($_POST['fm']) . "' WHERE `key` = 'fmod'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . check($_POST['meta_key']) . "' WHERE `key` = 'meta_key'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . check($_POST['meta_desc']) . "' WHERE `key` = 'meta_desc'");
    mysql_query("UPDATE `cms_settings` SET `val`='" . check($_POST['language']) . "' WHERE `key` = 'language'");
    $req = mysql_query("SELECT * FROM `cms_settings`");
    $set = array ();
    while ($res = mysql_fetch_row($req)) $set[$res[0]] = $res[1];
    echo '<div class="rmenu">' . $lng['settings_saved'] . '</div>';
}
/*
-----------------------------------------------------------------
Форма ввода параметров системы
-----------------------------------------------------------------
*/
echo '<form action="index.php?act=sys_set" method="post"><div class="menu"><p>';
// Настройка времени
echo '<h3>' . $lng['clock_settings'] . '</h3>';
echo '&#160;<input type="text" name="sdvigclock" size="2" maxlength="2" value="' . $set['sdvigclock'] . '"/> ' . $lng['time_shift'] . ' (+-12)<br />';
echo '&#160;<span style="font-weight:bold; background-color:#CCC">' . date("H:i") . '</span> ' . $lng['system_time'];
// Общие настройки
echo '</p><p><h3>' . $lng['common_settings'] . '</h3>';
echo '&#160;'. $lng['site_url'] . ':<br/>&#160;<input type="text" name="homeurl" value="' . htmlentities($set['homeurl']) . '"/><br/>';
echo '&#160;'. $lng['site_copyright'] . ':<br/>&#160;<input type="text" name="copyright" value="' . htmlentities($set['copyright'], ENT_QUOTES, 'UTF-8') . '"/><br/>';
echo '&#160;'. $lng['site_email'] . ':<br/>&#160;<input name="madm" maxlength="50" value="' . htmlentities($set['emailadmina']) . '"/><br />';
echo '&#160;'. $lng['file_maxsize'] . ' (kb):<br />&#160;<input type="text" name="flsz" value="' . intval($set['flsz']) . '"/><br />';
echo '&#160;<input name="gz" type="checkbox" value="1" ' . ($set['gzip'] ? 'checked="checked"' : '') . ' />&#160;' . $lng['gzip_compress'];
// META тэги
echo '</p><p><h3>' . $lng['meta_tags'] . '</h3>';
echo '&#160;' . $lng['meta_keywords'] . ':<br />&#160;<textarea cols="20" rows="4" name="meta_key">' . $set['meta_key'] . '</textarea><br />';
echo '&#160;' . $lng['meta_description'] . ':<br />&#160;<textarea cols="20" rows="4" name="meta_desc">' . $set['meta_desc'] . '</textarea>';
// Выбор языка
echo '</p><p><h3>' . $lng['system_language'] . '</h3>&#160;<select name="language">';
$dir = glob($rootpath . 'incfiles/languages/*', GLOB_ONLYDIR);
foreach ($dir as $val) {
    if (file_exists($val . '/name.dat')) {
        $lngdir = substr(strrchr($val, "/"), 1);
        echo '<option value="' . $lngdir . '"' . ($set['language'] == $lngdir ? ' selected="selected"' : '') . '>' . file_get_contents($val . '/name.dat') . '</option>';
    }
}
echo '</select>';
// Выбор темы оформления
echo '</p><p><h3>' . $lng['design_template'] . '</h3>&#160;<select name="skindef">';
$dir = opendir('../theme');
while ($skindef = readdir($dir)) {
    if (($skindef != '.') && ($skindef != '..') && ($skindef != '.svn')) {
        $skindef = str_replace('.css', '', $skindef);
        echo '<option' . ($set['skindef'] == $skindef ? ' selected="selected">' : '>') . $skindef . '</option>';
    }
}
closedir($dir);
echo '</select>';
echo '</p><p><input type="submit" name="submit" value="' . $lng['save'] . '"/></p></div></form>';
echo '<div class="phdr">&#160;</div>';
echo '<p><a href="index.php">' . $lng['admin_panel'] . '</a></p>';
?>