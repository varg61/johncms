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
Если в выбранном шаблоне есть файл footer.php то используем его,
если нет, то используем системную шапку.
-----------------------------------------------------------------
*/
if (is_file(ROOTPATH . 'theme' . DIRECTORY_SEPARATOR . Vars::$USER_SET['skin'] . DIRECTORY_SEPARATOR . 'footer.php')) {
    include_once(ROOTPATH . 'theme' . DIRECTORY_SEPARATOR . Vars::$USER_SET['skin'] . DIRECTORY_SEPARATOR . 'footer.php');
} else {
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
}

/*
-----------------------------------------------------------------
ВНИМАНИЕ!!!
Данный копирайт нельзя убирать в течение 60 дней с момента установки скриптов
-----------------------------------------------------------------
ATTENTION!!!
The copyright could not be removed within 60 days of installation scripts
-----------------------------------------------------------------
*/
echo '<div><small>&copy; <a href="http://johncms.com">JohnCMS</a></small></div>';

echo '</div></body></html>';