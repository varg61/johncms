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

define('_IN_JOHNCMS', 1);
$headmod = 'userset';
require('../incfiles/core.php');
$lng_set = load_lng('set');
$textl = $lng_set['my_set'];
require('../incfiles/head.php');
if (!$user_id) {
    echo display_error($lng['access_guest_forbidden']);
    require('../incfiles/end.php');
    exit;
}

// Заголовок модуля
echo '<div class="phdr"><b>' . $lng['settings'] . '</b> | ';
switch ($act) {
    case 'forum':
        echo $lng['forum'];
        break;

    case 'chat':
        echo $lng['chat'];
        break;
        
    default:
        echo $lng['common_settings'];
}
echo '</div>';
// Главное меню
$menu = array();
$menu[] = !$act ? $lng['common_settings'] : '<a href="my_set.php">' . $lng['common_settings'] . '</a>';
$menu[] = $act == 'forum' ? $lng['forum'] : '<a href="my_set.php?act=forum">' . $lng['forum'] . '</a>';
$menu[] = $act == 'chat' ? $lng['chat'] : '<a href="my_set.php?act=chat">' . $lng['chat'] . '</a>';
echo '<div class="topmenu">' . display_menu($menu) . '</div>';

switch ($act) {
    case 'forum':
        /*
        -----------------------------------------------------------------
        Настройки Форума
        -----------------------------------------------------------------
        */
        $set_forum = array ();
        $set_forum = unserialize($datauser['set_forum']);
        if (isset($_POST['submit'])) {
            $set_forum['farea'] = isset($_POST['farea']) ? 1 : 0;
            $set_forum['upfp'] = isset($_POST['upfp']) ? 1 : 0;
            $set_forum['postclip'] = isset($_POST['postclip']) ? intval($_POST['postclip']) : 1;
            $set_forum['postcut'] = isset($_POST['postcut']) ? intval($_POST['postcut']) : 1;
            if ($set_forum['postclip'] < 0 || $set_forum['postclip'] > 2)
                $set_forum['postclip'] = 1;
            if ($set_forum['postcut'] < 0 || $set_forum['postcut'] > 3)
                $set_forum['postcut'] = 1;
            mysql_query("UPDATE `users` SET `set_forum` = '" . mysql_real_escape_string(serialize($set_forum)) . "' WHERE `id` = '$user_id' LIMIT 1");
            echo '<div class="gmenu">' . $lng['settings_saved'] . '</div>';
        }
        if (isset($_GET['reset']) || empty($set_forum)) {
            $set_forum = array ();
            $set_forum['farea'] = 0;
            $set_forum['upfp'] = 0;
            $set_forum['postclip'] = 1;
            $set_forum['postcut'] = 2;
            mysql_query("UPDATE `users` SET `set_forum` = '" . mysql_real_escape_string(serialize($set_forum)) . "' WHERE `id` = '$user_id' LIMIT 1");
            echo '<div class="rmenu">' . $lng['settings_default'] . '</div>';
        }
        echo '<form action="my_set.php?act=forum" method="post">' .
            '<div class="menu"><p><h3>' . $lng_set['main_settings'] . '</h3>' .
            '<input name="upfp" type="checkbox" value="1" ' . ($set_forum['upfp'] ? 'checked="checked"' : '') . ' />&#160;' . $lng_set['sorting_return'] . '<br/>' .
            '<input name="farea" type="checkbox" value="1" ' . ($set_forum['farea'] ? 'checked="checked"' : '') . ' />&#160;' . $lng_set['field_on'] . '<br/>' .
            '</p><p><h3>' . $lng_set['clip_first_post'] . '</h3>' .
            '<input type="radio" value="2" name="postclip" ' . ($set_forum['postclip'] == 2 ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['always'] . '<br />' .
            '<input type="radio" value="1" name="postclip" ' . ($set_forum['postclip'] == 1 ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['in_not_read'] . '<br />' .
            '<input type="radio" value="0" name="postclip" ' . (!$set_forum['postclip'] ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['never'] .
            '</p><p><h3>' . $lng_set['scrap_of_posts'] . '</h3>' .
            '<input type="radio" value="1" name="postcut" ' . ($set_forum['postcut'] == 1 ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['500_symbols'] . '<br />' .
            '<input type="radio" value="2" name="postcut" ' . ($set_forum['postcut'] == 2 ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['1000_symbols'] . '<br />' .
            '<input type="radio" value="3" name="postcut" ' . ($set_forum['postcut'] == 3 ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['3000_symbols'] . '<br />' .
            '<input type="radio" value="0" name="postcut" ' . (!$set_forum['postcut'] ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['not_to_cut_off'] . '<br />' .
            '</p><p><input type="submit" name="submit" value="' . $lng['save'] . '"/></p></div></form>' .
            '<div class="phdr"><a href="my_set.php?act=forum&amp;reset">' . $lng['reset_settings'] . '</a></div>' .
            '<p><a href="../forum">' . $lng['to_forum'] . '</a><br /><a href="my_cabinet.php">' . $lng['personal'] . '</a></p>';
        break;

    case 'chat':
        /*
        -----------------------------------------------------------------
        Настройки Чата
        -----------------------------------------------------------------
        */
        $set_chat = array ();
        $set_chat = unserialize($datauser['set_chat']);
        if (isset($_POST['submit'])) {
            $set_chat['refresh'] = isset($_POST['refresh']) ? intval($_POST['refresh']) : 20;
            $set_chat['chmes'] = isset($_POST['chmes']) ? intval($_POST['chmes']) : 10;
            $set_chat['carea'] = isset($_POST['carea']) ? 1 : 0;
            $set_chat['carea_w'] = isset($_POST['carea_w']) ? intval($_POST['carea_w']) : 20;
            $set_chat['carea_h'] = isset($_POST['carea_h']) ? intval($_POST['carea_h']) : 2;
            $set_chat['mood'] = (isset($_POST['mood']) && in_array(trim($_POST['mood']), $mood)) ? trim($_POST['mood']) : 'нейтральное';
            $mood_adm = isset($_POST['mood_adm']) ? check(mb_substr(trim($_POST['mood_adm']), 0, 30)) : '';
            if ($set_chat['refresh'] < 10)
                $set_chat['refresh'] = 10;
            elseif ($set_chat['refresh'] > 99)
                $set_chat['refresh'] = 99;
            if ($set_chat['chmes'] < 5)
                $set_chat['chmes'] = 5;
            elseif ($set_chat['chmes'] > 40)
                $set_chat['chmes'] = 40;
            if ($set_chat['carea_w'] < 10)
                $set_chat['carea_w'] = 10;
            elseif ($set_chat['carea_w'] > 80)
                $set_chat['carea_w'] = 80;
            if ($set_chat['carea_h'] < 1)
                $set_chat['carea_h'] = 1;
            elseif ($set_chat['carea_h'] > 9)
                $set_chat['carea_h'] = 9;
            if ($rights >= 7 && !empty($mood_adm))
                $set_chat['mood'] = $mood_adm;
            mysql_query("UPDATE `users` SET `set_chat` = '" . mysql_real_escape_string(serialize($set_chat)) . "' WHERE `id` = '$user_id' LIMIT 1");
            echo '<div class="rmenu">' . $lng['settings_saved'] . '</div>';
        }
        if (isset($_GET['reset']) || empty($set_chat)) {
            $set_chat = array ();
            $set_chat['refresh'] = 20;
            $set_chat['chmes'] = 10;
            $set_chat['carea'] = 0;
            $set_chat['carea_w'] = 20;
            $set_chat['carea_h'] = 2;
            $set_chat['mood'] = $lng_set['mood_1'];
            mysql_query("UPDATE `users` SET `set_chat` = '" . mysql_real_escape_string(serialize($set_chat)) . "' WHERE `id` = '$user_id' LIMIT 1");
            echo '<div class="rmenu">' . $lng['settings_default'] . '</div>';
        }
        echo '<form action="my_set.php?act=chat" method="post">' .
            '<div class="menu"><p><h3>' . $lng_set['main_settings'] . '</h3>' .
            '<input type="text" name="refresh" size="2" maxlength="2" value="' . $set_chat['refresh'] . '"/> ' . $lng_set['chat_refresh'] . '<br />' .
            '<input type="text" name="chmess" size="2" maxlength="2" value="' . $set_chat['chmes'] . '"/> ' . $lng_set['chat_msg_per_page'] . '<br />' .
            '</p><p><h3>' . $lng_set['message_input'] . '</h3>' .
            '<input type="text" name="carea_w" size="2" maxlength="2" value="' . $set_chat['carea_w'] . '"/> ' . $lng_set['chat_field_width'] . '<br />' .
            '<input type="text" name="carea_h" size="2" maxlength="1" value="' . $set_chat['carea_h'] . '"/> ' . $lng_set['chat_field_height'] . '<br />' .
            '<input name="carea" type="checkbox" value="1" ' . ($set_chat['carea'] ? 'checked="checked"' : '') . ' />&#160;' . $lng_set['field_on'] . '<br />' .
            '</p><p><h3>' . $lng_set['your_mood'] . '</h3>' .
            '<select name="mood">';
        $mood = array (
            $lng_set['mood_1'],
            $lng_set['mood_2'],
            $lng_set['mood_3'],
            $lng_set['mood_4'],
            $lng_set['mood_5'],
            $lng_set['mood_6'],
            $lng_set['mood_7'],
            $lng_set['mood_8'],
            $lng_set['mood_9'],
            $lng_set['mood_10'],
            $lng_set['mood_11'],
            $lng_set['mood_12'],
            $lng_set['mood_13'],
            $lng_set['mood_14'],
            $lng_set['mood_15'],
            $lng_set['mood_16'],
            $lng_set['mood_17'],
            $lng_set['mood_18'],
            $lng_set['mood_19'],
            $lng_set['mood_20'],
            $lng_set['mood_21'],
            $lng_set['mood_22'],
            $lng_set['mood_23'],
            $lng_set['mood_24'],
            $lng_set['mood_25'],
            $lng_set['mood_26'],
            $lng_set['mood_27']
        );
        foreach ($mood as $val) {
            echo '<option' . ($set_chat['mood'] == $val ? ' selected="selected">' : '>') . $val . '</option>';
        }
        echo '</select><br/>';
        if ($rights >= 7)
            echo $lng_set['or_specify_the'] . ':<br/><input type="text" name="mood_adm" value="' . (in_array($set_chat['mood'], $mood) ? '' : $set_chat['mood']) . '"/><br/>';
        echo '</p><p><input type="submit" name="submit" value="' . $lng['save'] . '"/></p></div></form>' .
            '<div class="phdr"><a href="my_set.php?act=chat&amp;reset">' . $lng['reset_settings'] . '</a></div>' .
            '<p><a href="../chat">' . $lng['to_chat'] . '</a><br /><a href="my_cabinet.php">' . $lng['personal'] . '</a></p>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Общие настройки
        -----------------------------------------------------------------
        */
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
            $dir = opendir('../theme');
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
            $set_user['digest'] = 1;
            $set_user['field_w'] = 20;
            $set_user['field_h'] = 3;
            $set_user['sdvig'] = 0;
            $set_user['kmess'] = 10;
            $set_user['skin'] = 'default';
            mysql_query("UPDATE `users` SET
                `set_user` = '" . mysql_real_escape_string(serialize($set_user)) . "',
                `set_language` = ''
                WHERE `id` = '$user_id' LIMIT 1");
            $language = $sys_language;
            $lng = load_lng();
            echo '<div class="rmenu">' . $lng['settings_default'] . '</div>';
        }
        // Пользовательские настройки (общие)
        echo '<form action="my_set.php" method="post" >' .
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
        $dir = opendir('../theme');
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
                echo '<div><input type="radio" value="' . $res['iso'] . '" name="lng" ' . ($res['iso'] == $language ? 'checked="checked"' : '') . '/>&#160;' .
                    $res_l['default'] . '</div>';
            }
            echo '</p>';
        }
        echo '<p><input type="submit" name="submit" value="' . $lng['save'] . '"/></p></div></form>' .
            '<div class="phdr"><a href="my_set.php?reset">' . $lng['reset_settings'] . '</a></div>' .
            '<p><a href="my_cabinet.php">' . $lng['personal'] . '</a></p>';
}

require('../incfiles/end.php');
?>