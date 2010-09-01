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
Настройки Форума
-----------------------------------------------------------------
*/
echo '<div class="phdr"><b>' . $lng['settings'] . '</b> | ' . $lng['forum'] . '</div>';
echo '<div class="topmenu"><a href="index.php?act=set_main">' . $lng['common_settings'] . '</a> | <b>' . $lng['forum'] . '</b> | <a href="index.php?act=set_chat">' . $lng['chat'] . '</a></div>';
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
echo '<form action="index.php?act=set_forum" method="post">' .
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
    '<div class="phdr"><a href="index.php?act=set_forum&amp;reset">' . $lng['reset_settings'] . '</a></div>' .
    '<p><a href="../../forum">' . $lng['to_forum'] . '</a></p>';
?>