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
$textl = $lng['settings'] . ' | ' . $lng['forum'];
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
Настройки Чата
-----------------------------------------------------------------
*/
echo '<div class="phdr"><b>' . $lng['settings'] . '</b> | ' . $lng['forum'] . '</div>';
echo '<div class="topmenu"><a href="index.php?act=set_main">' . $lng['common_settings'] . '</a> | <a href="index.php?act=set_forum">' . $lng['forum'] . '</a> | <b>' . $lng['chat'] . '</b></div>';
$set_chat = array ();
$set_chat = unserialize($datauser['set_chat']);
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
echo '<form action="index.php?act=set_chat" method="post">' .
    '<div class="menu"><p><h3>' . $lng_set['main_settings'] . '</h3>' .
    '<input type="text" name="refresh" size="2" maxlength="2" value="' . $set_chat['refresh'] . '"/> ' . $lng_set['chat_refresh'] . '<br />' .
    '<input type="text" name="chmess" size="2" maxlength="2" value="' . $set_chat['chmes'] . '"/> ' . $lng_set['chat_msg_per_page'] . '<br />' .
    '</p><p><h3>' . $lng_set['message_input'] . '</h3>' .
    '<input type="text" name="carea_w" size="2" maxlength="2" value="' . $set_chat['carea_w'] . '"/> ' . $lng_set['chat_field_width'] . '<br />' .
    '<input type="text" name="carea_h" size="2" maxlength="1" value="' . $set_chat['carea_h'] . '"/> ' . $lng_set['chat_field_height'] . '<br />' .
    '<input name="carea" type="checkbox" value="1" ' . ($set_chat['carea'] ? 'checked="checked"' : '') . ' />&#160;' . $lng_set['field_on'] . '<br />' .
    '</p><p><h3>' . $lng_set['your_mood'] . '</h3>' .
    '<select name="mood">';
foreach ($mood as $val) {
    echo '<option' . ($set_chat['mood'] == $val ? ' selected="selected">' : '>') . $val . '</option>';
}
echo '</select><br/>';
if ($rights >= 7)
    echo $lng_set['or_specify_the'] . ':<br/><input type="text" name="mood_adm" value="' . (in_array($set_chat['mood'], $mood) ? '' : $set_chat['mood']) . '"/><br/>';
echo '</p><p><input type="submit" name="submit" value="' . $lng['save'] . '"/></p></div></form>' .
    '<div class="phdr"><a href="index.php?act=set_chat&amp;reset">' . $lng['reset_settings'] . '</a></div>' .
    '<p><a href="../../chat">' . $lng['to_chat'] . '</a></p>';
?>