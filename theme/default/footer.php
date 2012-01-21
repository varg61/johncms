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