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

$referer = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : Vars::$HOME_URL;

/*
-----------------------------------------------------------------
Переключатель языков
-----------------------------------------------------------------
*/
echo'<div class="menu"><form action="' . $referer . '" method="post"><p>' .
    '<p><h3>' . Vars::$LNG['language_select'] . '</h3>';
foreach (Vars::$LNG_LIST as $key => $val) {
    echo'<div><input type="radio" value="' . $key . '" name="setlng" ' . ($key == Vars::$LNG_ISO ? 'checked="checked"' : '') . '/>&#160;' .
        (file_exists('images/flags/' . $key . '.gif') ? '<img src="images/flags/' . $key . '.gif" alt=""/>&#160;' : '') .
        $val .
        ($key == Vars::$SYSTEM_SET['lng'] ? ' <small class="red">[' . Vars::$LNG['default'] . ']</small>' : '') .
        '</div>';
}
echo'</p>';
echo'</p><p><input type="submit" name="submit" value="' . Vars::$LNG['apply'] . '" /></p>' .
    '<p><a href="' . $referer . '">' . Vars::$LNG['back'] . '</a></p></form></div>';