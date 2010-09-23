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

defined('_IN_JOHNCMS') or die('Error: restricted access');
$lng_set = load_lng('set');
$textl = $lng['settings'] . ' | ' . $lng['common_settings'];
require('../../incfiles/head.php');

/*
-----------------------------------------------------------------
Проверяем права доступа
-----------------------------------------------------------------
*/
if ($user['id'] != $user_id) {
    echo display_error($lng['access_forbidden']);
    require('../incfiles/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Общие настройки
-----------------------------------------------------------------
*/
echo '<div class="phdr"><b>' . $lng['settings'] . '</b> | ' . $lng['common_settings'] . '</div>';
echo '<div class="topmenu"><b>' . $lng['common_settings'] . '</b> | <a href="index.php?act=set_forum">' . $lng['forum'] . '</a> | <a href="index.php?act=set_chat">' . $lng['chat'] . '</a></div>';
$set_user = array ();
$set_user = unserialize($datauser['set_user']);
if (isset($_POST['submit'])) {
    $set_user['sdvig'] = isset($_POST['sdvig']) ? intval($_POST['sdvig']) : 0;
    $set_user['avatar'] = isset($_POST['avatar']) ? 1 : 0;
    $set_user['smileys'] = isset($_POST['smileys']) ? 1 : 0;
    $set_user['translit'] = isset($_POST['translit']) ? 1 : 0;
    $set_user['digest'] = isset($_POST['digest']) ? 1 : 0;
    $set_user['field_w'] = isset($_POST['field_w']) ? abs(intval($_POST['field_w'])) : 20;
    $set_user['field_h'] = isset($_POST['field_h']) ? abs(intval($_POST['field_h'])) : 3;
    $set_user['kmess'] = isset($_POST['kmess']) ? abs(intval($_POST['kmess'])) : 10;
    $set_user['quick_go'] = isset($_POST['quick_go']) ? 1 : 0;
    $set_user['gzip'] = isset($_POST['gzip']) ? 1 : 0;
    $set_user['online'] = isset($_POST['online']) ? 1 : 0;
    $set_user['movings'] = isset($_POST['movings']) ? 1 : 0;
    if ($set_user['sdvig'] < -12)
        $set_user['sdvig'] = -12;
    elseif ($set_user['sdvig'] > 12)
        $set_user['sdvig'] = 12;
    if ($set_user['kmess'] < 5)
        $set_user['kmess'] = 5;
    elseif ($set_user['kmess'] > 99)
        $set_user['kmess'] = 99;
    if ($set_user['field_w'] < 10)
        $set_user['field_w'] = 10;
    elseif ($set_user['field_w'] > 80)
        $set_user['field_w'] = 80;
    if ($set_user['field_h'] < 1)
        $set_user['field_h'] = 1;
    elseif ($set_user['field_h'] > 9)
        $set_user['field_h'] = 9;
    $set_user['skin'] = isset($_POST['skin']) ? check(trim($_POST['skin'])) : 'default';
    $arr = array ();
    $dir = opendir('../../theme');
    while ($skindef = readdir($dir)) {
        if (($skindef != '.') && ($skindef != '..') && ($skindef != '.svn'))
            $arr[] = str_replace('.css', '', $skindef);
    }
    closedir($dir);
    if (!in_array($set_user['skin'], $arr))
        $set_user['skin'] = 'default';
    // Устанавливаем язык
    $lng_select = isset($_POST['lng']) ? check(trim($_POST['lng'])) : false;
    if ($lng_select && $lng_select != $language) {
        $req = mysql_query("SELECT * FROM `cms_languages` WHERE `iso` = '$lng_select' AND `var` = 'language_name' LIMIT 1");
        if (mysql_num_rows($req)) {
            $language = $lng_select;
            $lng = load_lng();
            $res = mysql_fetch_assoc($req);
            echo '<div class="gmenu">' . $lng['language_set'] . ': <b>' . $res['default'] . '</b></div>';
        }
    }
    // Записываем настройки в базу
    mysql_query("UPDATE `users` SET
                `set_user` = '" . mysql_real_escape_string(serialize($set_user)) . "',
                `set_language` = '$language'
                WHERE `id` = '$user_id' LIMIT 1");
    echo '<div class="rmenu">' . $lng['settings_saved'] . '</div>';
}
if (isset($_GET['reset']) || empty($set_user)) {
    $set_user = array ();
    $set_user['avatar'] = 1;
    $set_user['smileys'] = 1;
    $set_user['translit'] = 1;
    $set_user['quick_go'] = 1;
    $set_user['gzip'] = 1;
    $set_user['online'] = 1;
    $set_user['movings'] = 1;
    $set_user['digest'] = 0;
    $set_user['field_w'] = 20;
    $set_user['field_h'] = 3;
    $set_user['sdvig'] = 0;
    $set_user['kmess'] = 10;
    $set_user['skin'] = 'default';
    mysql_query("UPDATE `users` SET
        `set_user` = '" . mysql_real_escape_string(serialize($set_user)) . "',
        `set_language` = ''
        WHERE `id` = '$user_id' LIMIT 1
    ");
    $language = $sys_language;
    $lng = load_lng();
    echo '<div class="rmenu">' . $lng['settings_default'] . '</div>';
}
// Пользовательские настройки (общие)
echo '<form action="index.php?act=set_main" method="post" >' .
    '<div class="menu"><p><h3>' . $lng['settings_clock'] . '</h3>' .
    '<input type="text" name="sdvig" size="2" maxlength="2" value="' . $set_user['sdvig'] . '"/> ' . $lng['settings_clock_shift'] . ' (+-12)<br />' .
    '<span style="font-weight:bold; background-color:#CCC">' . date("H:i", $realtime + $set_user['sdvig'] * 3600) . '</span> ' . $lng['system_time'] .
    '</p><p><h3>' . $lng['system_functions'] . '</h3>' .
    '<input name="avatar" type="checkbox" value="1" ' . ($set_user['avatar'] ? 'checked="checked"' : '') . ' />&#160;' . $lng['avatars'] . '<br/>' .
    '<input name="smileys" type="checkbox" value="1" ' . ($set_user['smileys'] ? 'checked="checked"' : '') . ' />&#160;' . $lng['smileys'] . '<br/>' .
    '<input name="translit" type="checkbox" value="1" ' . ($set_user['translit'] ? 'checked="checked"' : '') . ' />&#160;' . $lng['translit'] . '<br/>' .
    '<input name="digest" type="checkbox" value="1" ' . ($set_user['digest'] ? 'checked="checked"' : '') . ' />&#160;' . $lng['digest'] .
    '</p><p><h3>' . $lng['text_input'] . '</h3>' .
    '<input type="text" name="field_w" size="2" maxlength="2" value="' . $set_user['field_w'] . '"/> ' . $lng['field_width'] . ' (10-80)<br />' .
    '<input type="text" name="field_h" size="2" maxlength="1" value="' . $set_user['field_h'] . '"/> ' . $lng['field_height'] . ' (1-9)<br />' .
    '</p><p><h3>' . $lng['apperance'] . '</h3>' .
    '<input type="text" name="kmess" size="2" maxlength="2" value="' . $set_user['kmess'] . '"/> ' . $lng['lines_on_page'] . ' (5-99)<br />' .
    '<input name="quick_go" type="checkbox" value="1" ' . ($set_user['quick_go'] ? 'checked="checked"' : '') . ' />&#160;' . $lng['quick_jump'] . '<br />' .
    '<input name="gzip" type="checkbox" value="1" ' . ($set_user['gzip'] ? 'checked="checked"' : '') . ' />&#160;' . $lng['gzip_show'] . '<br />' .
    '<input name="online" type="checkbox" value="1" ' . ($set_user['online'] ? 'checked="checked"' : '') . ' />&#160;' . $lng['time_online'] . '<br />' .
    '<input name="movings" type="checkbox" value="1" ' . ($set_user['movings'] ? 'checked="checked"' : '') . ' />&#160;' . $lng['transitions_counter'] .
    '</p><p><h3>' . $lng['design_template'] . '</h3><select name="skin">';
// Выбор темы оформления
$dir = opendir('../../theme');
while ($skindef = readdir($dir)) {
    if (($skindef != '.') && ($skindef != '..') && ($skindef != '.svn')) {
        $skindef = str_replace('.css', '', $skindef);
        echo '<option' . ($set_user['skin'] == $skindef ? ' selected="selected">' : '>') . $skindef . '</option>';
    }
}
closedir($dir);
echo '</select></p>';
// Выбор языка
$req = mysql_query("SELECT DISTINCT `iso` FROM `cms_languages`");
if (mysql_num_rows($req) > 1) {
    echo '<p><h3>' . $lng['language_select'] . '</h3>';
    while ($res = mysql_fetch_assoc($req)) {
        $req_l = mysql_query("SELECT * FROM `cms_languages` WHERE `iso` = '" . $res['iso'] . "' AND `var` = 'language_name' LIMIT 1");
        $res_l = mysql_fetch_assoc($req_l);
        echo '<div><input type="radio" value="' . $res['iso'] . '" name="lng" ' . ($res['iso'] == $language ? 'checked="checked"' : '') . '/>&#160;' . $res_l['default'] . '</div>';
    }
    echo '</p>';
}
echo '<p><input type="submit" name="submit" value="' . $lng['save'] . '"/></p></div></form>' .
    '<div class="phdr"><a href="index.php?act=set_main&amp;reset">' . $lng['reset_settings'] . '</a></div>';
?>