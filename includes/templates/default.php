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

/*
-----------------------------------------------------------------
Рекламный блок сайта
-----------------------------------------------------------------
*/
if (isset($cms_ads[0])) echo $cms_ads[0];

/*
-----------------------------------------------------------------
Выводим логотип и переключатель языков
-----------------------------------------------------------------
*/
echo '<table style="width: 100%;"><tr>' .
    '<td valign="bottom"><a href="' . Vars::$HOME_URL . '">' . Functions::getImage('logo.gif', Vars::$SYSTEM_SET['copyright']) . '</a></td>' .
    (Vars::$PLACE == 'index.php' && count(Vars::$LNG_LIST) > 1 ? '<td align="right"><a href="' . Vars::$HOME_URL . '/go.php?lng"><b>' . strtoupper(Vars::$LNG_ISO) . '</b></a>&#160;<img src="' . Vars::$HOME_URL . '/images/flags/' . Vars::$LNG_ISO . '.gif" alt=""/>&#160;</td>' : '') .
    '</tr></table>';

/*
-----------------------------------------------------------------
Выводим верхний блок с приветствием
-----------------------------------------------------------------
*/
echo '<div class="header"> ' . Vars::$LNG['hi'] . ', ' . (Vars::$USER_ID ? '<b>' . Vars::$USER_DATA['nickname'] . '</b>!' : Vars::$LNG['guest'] . '!') . '</div>';

/*
-----------------------------------------------------------------
Главное меню пользователя
-----------------------------------------------------------------
*/
echo '<div class="tmn">' .
    (isset($_GET['err']) || Vars::$PLACE != 'index.php' || (Vars::$PLACE == 'index.php' && Vars::$ACT) ? '<a href=\'' . Vars::$SYSTEM_SET['homeurl'] . '\'>' . Vars::$LNG['homepage'] . '</a> | ' : '') .
    (Vars::$USER_ID ? '<a href="' . Vars::$HOME_URL . '/users/profile.php?act=office">' . Vars::$LNG['personal'] . '</a> | ' : '') .
    (Vars::$USER_ID ? '<a href="' . Vars::$HOME_URL . '/exit.php">' . Vars::$LNG['exit'] . '</a>' : '<a href="' . Vars::$HOME_URL . '/login.php">' . Vars::$LNG['login'] . '</a> | <a href="' . Vars::$HOME_URL . '/registration.php">' . Vars::$LNG['registration'] . '</a>') .
    '</div><div class="maintxt">';

/*
-----------------------------------------------------------------
Рекламный блок сайта
-----------------------------------------------------------------
*/
if (!empty($cms_ads[1])) echo '<div class="gmenu">' . $cms_ads[1] . '</div>';

/*
-----------------------------------------------------------------
Выводим сообщение о Бане
-----------------------------------------------------------------
*/
if (!empty(Vars::$USER_BAN)) echo '<div class="alarm">' . Vars::$LNG['ban'] . '&#160;<a href="' . Vars::$HOME_URL . '/users/profile.php?act=ban">' . Vars::$LNG['in_detail'] . '</a></div>';

/*
-----------------------------------------------------------------
Блок оповещений
-----------------------------------------------------------------
*/
if (Vars::$USER_ID) {
    //$list = array();
    //$new_mail = mysql_result(mysql_query("SELECT COUNT(*) FROM `privat` WHERE `user` = '$login' AND `type` = 'in' AND `chit` = 'no'"), 0);
    //if ($new_mail) $list[] = '<a href="' . Vars::$system_set['homeurl'] . '/users/pradd.php?act=in&amp;new">' . Vars::$lng['mail'] . '</a>&#160;(' . $new_mail . ')';
    //if ($datauser['comm_count'] > $datauser['comm_old']) $list[] = '<a href="' . Vars::$system_set['homeurl'] . '/users/profile.php?act=guestbook&amp;user=' . $user_id . '">' . Vars::$lng['guestbook'] . '</a> (' . ($datauser['comm_count'] - $datauser['comm_old']) . ')';
    //$new_album_comm = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . Vars::$user_id . "' AND `unread_comments` = 1"), 0);
    //if($new_album_comm) $list[] = '<a href="' . Vars::$system_set['homeurl'] . '/users/album.php?act=top&amp;mod=my_new_comm">' . Vars::$lng['albums_comments'] . '</a>';

    //if (!empty($list)) echo '<div class="rmenu">' . Vars::$lng['unread'] . ': ' . Functions::display_menu($list, ', ') . '</div>';
}

/*
-----------------------------------------------------------------
Выводим основное содержание
-----------------------------------------------------------------
*/
echo $contents;

/*
-----------------------------------------------------------------
Низ сайта (ноги)
-----------------------------------------------------------------
*/

// Рекламный блок сайта
if (!empty($cms_ads[2]))
    echo '<div class="gmenu">' . $cms_ads[2] . '</div>';

echo '</div><div class="fmenu">';
if (Vars::$PLACE != 'index.php' || (Vars::$PLACE == 'index.php' && Vars::$ACT))
    echo '<a href="' . Vars::$HOME_URL . '">' . Vars::$LNG['homepage'] . '</a><br/>';

// Меню быстрого перехода
if (Vars::$USER_SET['quick_go']) {
    echo '<form action="' . Vars::$HOME_URL . '/go.php" method="post">';
    echo '<div><select name="adres" style="font-size:x-small">
    <option selected="selected">' . Vars::$LNG['quick_jump'] . '</option>
    <option value="guest">' . Vars::$LNG['guestbook'] . '</option>
    <option value="forum">' . Vars::$LNG['forum'] . '</option>
    <option value="news">' . Vars::$LNG['news'] . '</option>
    <option value="gallery">' . Vars::$LNG['gallery'] . '</option>
    <option value="down">' . Vars::$LNG['downloads'] . '</option>
    <option value="lib">' . Vars::$LNG['library'] . '</option>
    <option value="gazen">Gazenwagen :)</option>
    </select><input type="submit" value="Go!" style="font-size:x-small"/>';
    echo '</div></form>';
}
// Счетчик посетителей онлайн
echo '</div><div class="footer">' . Counters::usersOnline() . '</div>';
echo '<div style="text-align:center">';
echo '<p><b>' . Vars::$SYSTEM_SET['copyright'] . '</b></p>';

// Счетчики каталогов
Functions::displayCounters();

// Рекламный блок сайта
if (!empty($cms_ads[3]))
    echo '<br />' . $cms_ads[3];
echo '</div>';