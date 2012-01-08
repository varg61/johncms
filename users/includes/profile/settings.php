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

$lng_set = Vars::loadLanguage('settings');
$textl = Vars::$LNG['settings'];
require_once('../includes/head.php');

/*
-----------------------------------------------------------------
Проверяем права доступа
-----------------------------------------------------------------
*/
if ($user['user_id'] != Vars::$USER_ID) {
    echo Functions::displayError(Vars::$LNG['access_forbidden']);
    require_once('../includes/end.php');
    exit;
}

$menu = array(
    (!Vars::$MOD ? '<b>' . Vars::$LNG['common_settings'] . '</b>' : '<a href="profile.php?act=settings">' . Vars::$LNG['common_settings'] . '</a>'),
    (Vars::$MOD == 'forum' ? '<b>' . Vars::$LNG['forum'] . '</b>' : '<a href="profile.php?act=settings&amp;mod=forum">' . Vars::$LNG['forum'] . '</a>'),
);

/*
-----------------------------------------------------------------
Пользовательские настройки
-----------------------------------------------------------------
*/
switch (Vars::$MOD) {
    case 'forum':
        /*
        -----------------------------------------------------------------
        Настройки Форума
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . Vars::$LNG['settings'] . '</b> | ' . Vars::$LNG['forum'] . '</div>' .
            '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
        if (($set_forum = Vars::getUserData('set_forum')) === false) {
            $set_forum = array(
                'farea' => 0,
                'upfp' => 0,
                'preview' => 1,
                'postclip' => 1,
                'postcut' => 2
            );
        }
        if (isset($_POST['submit'])) {
            $set_forum['farea'] = isset($_POST['farea']);
            $set_forum['upfp'] = isset($_POST['upfp']);
            $set_forum['preview'] = isset($_POST['preview']);
            $set_forum['postclip'] = isset($_POST['postclip']) ? intval($_POST['postclip']) : 1;
            $set_forum['postcut'] = isset($_POST['postcut']) ? intval($_POST['postcut']) : 1;
            if ($set_forum['postclip'] < 0 || $set_forum['postclip'] > 2)
                $set_forum['postclip'] = 1;
            if ($set_forum['postcut'] < 0 || $set_forum['postcut'] > 3)
                $set_forum['postcut'] = 1;
            Vars::setUserData('set_forum', $set_forum);
            echo '<div class="gmenu">' . Vars::$LNG['settings_saved'] . '</div>';
        }
        if (isset($_GET['reset']) || empty($set_forum)) {
            Vars::setUserData('set_forum');
            $set_forum = array(
                'farea' => 0,
                'upfp' => 0,
                'preview' => 1,
                'postclip' => 1,
                'postcut' => 2
            );
            echo '<div class="rmenu">' . Vars::$LNG['settings_default'] . '</div>';
        }
        echo'<form action="profile.php?act=settings&amp;mod=forum" method="post">' .
            '<div class="menu"><p><h3>' . $lng_set['main_settings'] . '</h3>' .
            '<input name="upfp" type="checkbox" value="1" ' . ($set_forum['upfp'] ? 'checked="checked"' : '') . ' />&#160;' . $lng_set['sorting_return'] . '<br/>' .
            '<input name="farea" type="checkbox" value="1" ' . ($set_forum['farea'] ? 'checked="checked"' : '') . ' />&#160;' . $lng_set['field_on'] . '<br/>' .
            '<input name="preview" type="checkbox" value="1" ' . ($set_forum['preview'] ? 'checked="checked"' : '') . ' />&#160;' . Vars::$LNG['preview'] . '<br/>' .
            '</p><p><h3>' . $lng_set['clip_first_post'] . '</h3>' .
            '<input type="radio" value="2" name="postclip" ' . ($set_forum['postclip'] == 2 ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['always'] . '<br />' .
            '<input type="radio" value="1" name="postclip" ' . ($set_forum['postclip'] == 1 ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['in_not_read'] . '<br />' .
            '<input type="radio" value="0" name="postclip" ' . (!$set_forum['postclip'] ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['never'] .
            '</p><p><h3>' . $lng_set['scrap_of_posts'] . '</h3>' .
            '<input type="radio" value="1" name="postcut" ' . ($set_forum['postcut'] == 1 ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['500_symbols'] . '<br />' .
            '<input type="radio" value="2" name="postcut" ' . ($set_forum['postcut'] == 2 ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['1000_symbols'] . '<br />' .
            '<input type="radio" value="3" name="postcut" ' . ($set_forum['postcut'] == 3 ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['3000_symbols'] . '<br />' .
            '<input type="radio" value="0" name="postcut" ' . (!$set_forum['postcut'] ? 'checked="checked"' : '') . '/>&#160;' . $lng_set['not_to_cut_off'] . '<br />' .
            '</p><p><input type="submit" name="submit" value="' . Vars::$LNG['save'] . '"/></p></div></form>' .
            '<div class="phdr"><a href="profile.php?act=settings&amp;mod=forum&amp;reset">' . Vars::$LNG['reset_settings'] . '</a></div>' .
            '<p><a href="../forum/index.php">' . Vars::$LNG['to_forum'] . '</a></p>';
        break;

    default:
        echo'<div class="phdr"><b>' . Vars::$LNG['settings'] . '</b> | ' . Vars::$LNG['common_settings'] . '</div>' .
            '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
        if (isset($_POST['submit'])) {
            /*
            -----------------------------------------------------------------
            Записываем новые настройки, заданные пользователем
            -----------------------------------------------------------------
            */
            $set_user['timeshift'] = isset($_POST['timeshift']) ? intval($_POST['timeshift']) : 0;
            if ($set_user['timeshift'] < -12) $set_user['timeshift'] = -12;
            elseif ($set_user['timeshift'] > 12) $set_user['timeshift'] = 12;

            $set_user['page_size'] = isset($_POST['page_size']) ? abs(intval($_POST['page_size'])) : 10;
            if ($set_user['page_size'] < 5) $set_user['page_size'] = 5;
            elseif ($set_user['page_size'] > 99) $set_user['page_size'] = 99;

            $set_user['field_h'] = isset($_POST['field_h']) ? abs(intval($_POST['field_h'])) : 3;
            if ($set_user['field_h'] < 1) $set_user['field_h'] = 1;
            elseif ($set_user['field_h'] > 9) $set_user['field_h'] = 9;

            $set_user['avatar'] = isset($_POST['avatar']);
            $set_user['smileys'] = isset($_POST['smileys']);
            $set_user['translit'] = isset($_POST['translit']);
            $set_user['digest'] = isset($_POST['digest']);
            $set_user['direct_url'] = isset($_POST['direct_url']);
            $set_user['quick_go'] = isset($_POST['quick_go']);

            // Устанавливаем скин
            $theme_list = array();
            foreach (glob('../theme/*/*.css') as $val)
                $theme_list[] = array_pop(explode('/', dirname($val)));
            $set_user['skin'] = isset($_POST['skin']) && in_array($_POST['skin'], $theme_list) ? Validate::filterString($_POST['skin']) : Vars::$SYSTEM_SET['skindef'];

            // Устанавливаем язык
            $lng_select = isset($_POST['iso']) ? trim($_POST['iso']) : false;
            if ($lng_select && array_key_exists($lng_select, Vars::$LNG_LIST)) {
                $set_user['lng'] = $lng_select;
                unset($_SESSION['lng']);
            }

            // Записываем настройки
            if (Vars::$USER_SET != $set_user) {
                unset($_SESSION['settings']);
                Vars::setUserData('set_user', $set_user);
                $_SESSION['ok'] = Vars::$LNG['settings_saved'];
            }
            header('Location: profile.php?act=settings');
            exit;
        } elseif (isset($_GET['reset'])) {
            /*
            -----------------------------------------------------------------
            Задаем настройки по-умолчанию
            -----------------------------------------------------------------
            */
            unset($_SESSION['settings']);
            Vars::setUserData('set_user');
            $_SESSION['ok'] = Vars::$LNG['settings_default'];
            header('Location: profile.php?act=settings');
            exit;
        } else {
            $set_user = Vars::$USER_SET;
        }

        /*
        -----------------------------------------------------------------
        Форма ввода пользовательских настроек
        -----------------------------------------------------------------
        */
        if (isset($_SESSION['ok'])) {
            echo '<div class="rmenu">' . $_SESSION['ok'] . '</div>';
            unset($_SESSION['ok']);
        }
        echo '<form action="profile.php?act=settings" method="post" >' .
            '<div class="menu"><p><h3>' . Vars::$LNG['settings_clock'] . '</h3>' .
            '<input type="text" name="timeshift" size="2" maxlength="3" value="' . $set_user['timeshift'] . '"/> ' . Vars::$LNG['settings_clock_shift'] . ' (+-12)<br />' .
            '<span style="font-weight:bold; background-color:#CCC">' . date("H:i", time() + (Vars::$SYSTEM_SET['timeshift'] + $set_user['timeshift']) * 3600) . '</span> ' . Vars::$LNG['system_time'] .
            '</p><p><h3>' . Vars::$LNG['system_functions'] . '</h3>' .
            '<input name="direct_url" type="checkbox" value="1" ' . ($set_user['direct_url'] ? 'checked="checked"' : '') . ' />&#160;' . Vars::$LNG['direct_url'] . '<br />' .
            '<input name="avatar" type="checkbox" value="1" ' . ($set_user['avatar'] ? 'checked="checked"' : '') . ' />&#160;' . Vars::$LNG['avatars'] . '<br/>' .
            '<input name="smileys" type="checkbox" value="1" ' . ($set_user['smileys'] ? 'checked="checked"' : '') . ' />&#160;' . Vars::$LNG['smileys'] . '<br/>' .
            '<input name="digest" type="checkbox" value="1" ' . ($set_user['digest'] ? 'checked="checked"' : '') . ' />&#160;' . Vars::$LNG['digest'] .
            '</p><p><h3>' . Vars::$LNG['text_input'] . '</h3>' .
            '<input type="text" name="field_h" size="2" maxlength="1" value="' . $set_user['field_h'] . '"/> ' . Vars::$LNG['field_height'] . ' (1-9)<br />';
        if (Vars::$LNG_ISO == 'ru' || Vars::$LNG_ISO == 'uk') echo '<input name="translit" type="checkbox" value="1" ' . ($set_user['translit'] ? 'checked="checked"' : '') . ' />&#160;' . Vars::$LNG['translit'];
        echo '</p><p><h3>' . Vars::$LNG['apperance'] . '</h3>' .
            '<input type="text" name="kmess" size="2" maxlength="2" value="' . $set_user['kmess'] . '"/> ' . Vars::$LNG['lines_on_page'] . ' (5-99)<br />' .
            '<input name="quick_go" type="checkbox" value="1" ' . ($set_user['quick_go'] ? 'checked="checked"' : '') . ' />&#160;' . Vars::$LNG['quick_jump'] .
            '</p>';

        // Выбор темы оформления
        echo '<p><h3>' . Vars::$LNG['design_template'] . '</h3><select name="skin">';
        foreach (glob('../theme/*/*.css') as $val) {
            $dir = explode('/', dirname($val));
            $theme = array_pop($dir);
            echo '<option' . ($set_user['skin'] == $theme ? ' selected="selected">' : '>') . $theme . '</option>';
        }
        echo '</select></p>';

        // Выбор языка
        if (count(Vars::$LNG_LIST) > 1) {
            echo '<p><h3>' . Vars::$LNG['language_select'] . '</h3>';
            $user_lng = isset($set_user['lng']) ? $set_user['lng'] : Vars::$LNG_ISO;
            foreach (Vars::$LNG_LIST as $key => $val) {
                echo '<div><input type="radio" value="' . $key . '" name="iso" ' . ($key == $user_lng ? 'checked="checked"' : '') . '/>&#160;' .
                    (file_exists('../images/flags/' . $key . '.gif') ? '<img src="../images/flags/' . $key . '.gif" alt=""/>&#160;' : '') .
                    $val .
                    ($key == Vars::$SYSTEM_SET['lng'] ? ' <small class="red">[' . Vars::$LNG['default'] . ']</small>' : '') .
                    '</div>';
            }
            echo '</p>';
        }

        echo '<p><input type="submit" name="submit" value="' . Vars::$LNG['save'] . '"/></p></div></form>' .
            '<div class="phdr"><a href="profile.php?act=settings&amp;reset">' . Vars::$LNG['reset_settings'] . '</a></div>';
}