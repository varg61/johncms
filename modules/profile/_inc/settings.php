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

global $user, $tpl;

/*
-----------------------------------------------------------------
Проверяем права доступа
-----------------------------------------------------------------
*/
if ($user['id'] != Vars::$USER_ID) {
    echo Functions::displayError(lng('access_forbidden'));
    exit;
}

$tpl->menu = array(
    (!Vars::$MOD ? '<b>' . lng('common_settings') . '</b>' : '<a href="profile.php?act=settings">' . lng('common_settings') . '</a>'),
    (Vars::$MOD == 'forum' ? '<b>' . lng('forum') . '</b>' : '<a href="profile.php?act=settings&amp;mod=forum">' . lng('forum') . '</a>'),
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
        echo '<div class="phdr"><b>' . lng('settings') . '</b> | ' . lng('forum') . '</div>' .
            '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
        if (($set_forum = Vars::getUserData('set_forum')) === false) {
            $set_forum = array(
                'farea'    => 0,
                'upfp'     => 0,
                'preview'  => 1,
                'postclip' => 1,
                'postcut'  => 2
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
            echo '<div class="gmenu">' . lng('settings_saved') . '</div>';
        }
        if (isset($_GET['reset']) || empty($set_forum)) {
            Vars::setUserData('set_forum');
            $set_forum = array(
                'farea'    => 0,
                'upfp'     => 0,
                'preview'  => 1,
                'postclip' => 1,
                'postcut'  => 2
            );
            echo '<div class="rmenu">' . lng('settings_default') . '</div>';
        }
        echo'<form action="profile.php?act=settings&amp;mod=forum" method="post">' .
            '<div class="menu"><p><h3>' . lng('main_settings') . '</h3>' .
            '<input name="upfp" type="checkbox" value="1" ' . ($set_forum['upfp'] ? 'checked="checked"' : '') . ' />&#160;' . lng('sorting_return') . '<br/>' .
            '<input name="farea" type="checkbox" value="1" ' . ($set_forum['farea'] ? 'checked="checked"' : '') . ' />&#160;' . lng('field_on') . '<br/>' .
            '<input name="preview" type="checkbox" value="1" ' . ($set_forum['preview'] ? 'checked="checked"' : '') . ' />&#160;' . lng('preview') . '<br/>' .
            '</p><p><h3>' . lng('clip_first_post') . '</h3>' .
            '<input type="radio" value="2" name="postclip" ' . ($set_forum['postclip'] == 2 ? 'checked="checked"' : '') . '/>&#160;' . lng('always') . '<br />' .
            '<input type="radio" value="1" name="postclip" ' . ($set_forum['postclip'] == 1 ? 'checked="checked"' : '') . '/>&#160;' . lng('in_not_read') . '<br />' .
            '<input type="radio" value="0" name="postclip" ' . (!$set_forum['postclip'] ? 'checked="checked"' : '') . '/>&#160;' . lng('never') .
            '</p><p><h3>' . lng('scrap_of_posts') . '</h3>' .
            '<input type="radio" value="1" name="postcut" ' . ($set_forum['postcut'] == 1 ? 'checked="checked"' : '') . '/>&#160;' . lng('500_symbols') . '<br />' .
            '<input type="radio" value="2" name="postcut" ' . ($set_forum['postcut'] == 2 ? 'checked="checked"' : '') . '/>&#160;' . lng('1000_symbols') . '<br />' .
            '<input type="radio" value="3" name="postcut" ' . ($set_forum['postcut'] == 3 ? 'checked="checked"' : '') . '/>&#160;' . lng('3000_symbols') . '<br />' .
            '<input type="radio" value="0" name="postcut" ' . (!$set_forum['postcut'] ? 'checked="checked"' : '') . '/>&#160;' . lng('not_to_cut_off') . '<br />' .
            '</p><p><input type="submit" name="submit" value="' . lng('save') . '"/></p></div></form>' .
            '<div class="phdr"><a href="profile.php?act=settings&amp;mod=forum&amp;reset">' . lng('reset_settings') . '</a></div>' .
            '<p><a href="../forum/index.php">' . lng('to_forum') . '</a></p>';
        break;

    default:
//            // Устанавливаем скин
//            $theme_list = array();
//            foreach (glob('../theme/*/*.css') as $val)
//                $theme_list[] = array_pop(explode('/', dirname($val)));
//            $set_user['skin'] = isset($_POST['skin']) && in_array($_POST['skin'], $theme_list) ? Validate::filterString($_POST['skin']) : Vars::$SYSTEM_SET['skindef'];
//
//            // Устанавливаем язык
//            $lng_select = isset($_POST['iso']) ? trim($_POST['iso']) : false;
//            if ($lng_select && array_key_exists($lng_select, Vars::$LNG_LIST)) {
//                $set_user['lng'] = $lng_select;
//                unset($_SESSION['lng']);
//            }
//
//            // Записываем настройки
//            if (Vars::$USER_SET != $set_user) {
//                unset($_SESSION['settings']);
//                Vars::setUserData('set_user', $set_user);
//                $_SESSION['ok'] = lng('settings_saved');
//            }
//            header('Location: profile.php?act=settings');
//            exit;
//        } elseif (isset($_GET['reset'])) {
//            /*
//            -----------------------------------------------------------------
//            Задаем настройки по-умолчанию
//            -----------------------------------------------------------------
//            */
//            unset($_SESSION['settings']);
//            Vars::setUserData('set_user');
//            $_SESSION['ok'] = lng('settings_default');
//            header('Location: profile.php?act=settings');
//            exit;
//        } else {
//            $set_user = Vars::$USER_SET;
//        }

//        if (isset($_SESSION['ok'])) {
//            echo '<div class="rmenu">' . $_SESSION['ok'] . '</div>';
//            unset($_SESSION['ok']);
//        }

        if (isset($_POST['submit'])) {
            if (isset($_POST['timeshift']) && $_POST['timeshift'] > -13 && $_POST['timeshift'] < 13) {
                Vars::$USER_SET['timeshift'] = intval($_POST['timeshift']);
            }
            if (isset($_POST['field_h']) && $_POST['field_h'] > 0 && $_POST['field_h'] < 10) {
                Vars::$USER_SET['field_h'] = intval($_POST['field_h']);
            }
            if (isset($_POST['page_size']) && $_POST['page_size'] > 4 && $_POST['page_size'] < 100) {
                Vars::$USER_SET['page_size'] = intval($_POST['page_size']);
            }
            Vars::$USER_SET['avatar'] = isset($_POST['avatar']);
            Vars::$USER_SET['smileys'] = isset($_POST['smileys']);
            Vars::$USER_SET['translit'] = isset($_POST['translit']);
            Vars::$USER_SET['digest'] = isset($_POST['digest']);
            Vars::$USER_SET['direct_url'] = isset($_POST['direct_url']);
            Vars::$USER_SET['quick_go'] = isset($_POST['quick_go']);
        } elseif (isset($_POST['reset'])) {

        } elseif (isset($_GET['reset'])) {

        }

        $tpl_list = array();
        $templates = glob(TPLPATH . '*' . DIRECTORY_SEPARATOR . '*.css');
        foreach ($templates as $val) {
            $dir = explode(DIRECTORY_SEPARATOR, dirname($val));
            $tpl_list[] = array_pop($dir);
        }
        sort($tpl_list);
        $tpl->tpl_list = $tpl_list;

        $tpl->token = mt_rand(100, 10000);
        $_SESSION['form_token'] = $tpl->token;
        $tpl->contents = $tpl->includeTpl('settings_main');
}