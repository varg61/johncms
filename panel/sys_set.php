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
echo '<div class="phdr"><a href="index.php"><b>Админ панель</b></a> | Настройка системы</div>';
if (isset($_POST['submit'])) {
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
    $req = mysql_query("SELECT * FROM `cms_settings`");
    $set = array ();
    while ($res = mysql_fetch_row($req)) $set[$res[0]] = $res[1];
    echo '<div class="rmenu">Сайт настроен</div>';
}
echo '<form action="index.php?act=sys_set" method="post"><div class="menu"><p>';
echo '<h3>Настройка часов</h3>';
echo '&#160;<input type="text" name="sdvigclock" size="2" maxlength="2" value="' . $set['sdvigclock'] . '"/> Сдвиг времени (+-12)<br />';
echo '&#160;<span style="font-weight:bold; background-color:#CCC">' . date("H:i") . '</span> Системное время';
echo '</p><p><h3>Функции системы</h3>';
echo '&#160;Адрес сайта без слэша в конце:<br/>&#160;<input type="text" name="homeurl" value="' . htmlentities($set['homeurl']) . '"/><br/>';
echo '&#160;Копирайт сайта:<br/>&#160;<input type="text" name="copyright" value="' . htmlentities($set['copyright'], ENT_QUOTES, 'UTF-8') . '"/><br/>';
echo '&#160;E-mail сайта:<br/>&#160;<input name="madm" maxlength="50" value="' . htmlentities($set['emailadmina']) . '"/><br />';
echo '&#160;Макс. размер файлов(кб.):<br />&#160;<input type="text" name="flsz" value="' . intval($set['flsz']) . '"/><br />';
echo '&#160;<input name="gz" type="checkbox" value="1" ' . ($set['gzip'] ? 'checked="checked"' : '') . ' />&#160;GZIP сжатие';
echo '</p><p><h3>META тэги</h3>';
echo '&#160;Ключевые слова:<br />&#160;<textarea cols="20" rows="4" name="meta_key">' . $set['meta_key'] . '</textarea><br />';
echo '&#160;Описание:<br />&#160;<textarea cols="20" rows="4" name="meta_desc">' . $set['meta_desc'] . '</textarea>';
echo '</p><p><h3>Тема оформления</h3>&#160;<select name="skindef">';
$dir = opendir('../theme');
while ($skindef = readdir($dir)) {
    if (($skindef != '.') && ($skindef != '..') && ($skindef != '.svn')) {
        $skindef = str_replace('.css', '', $skindef);
        echo '<option' . ($set['skindef'] == $skindef ? ' selected="selected">' : '>') . $skindef . '</option>';
    }
}
closedir($dir);
echo '</select>';
echo '</p><p><input type="submit" name="submit" value="Сохранить"/></p></div></form>';
echo '<div class="phdr">&#160;</div>';
echo '<p><a href="index.php">Админ панель</a></p>';

?>