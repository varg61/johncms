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
            echo '<div class="gmenu">' . lng('settings_saved') . '</div>';
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
            '<p><a href="' . Vars::$URI . '">' . lng('to_forum') . '</a></p>';
        break;

    default:
}