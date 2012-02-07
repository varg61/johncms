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

if (isset($_POST['submit'])) {
    $login = new Login;
    $login->userUnset(isset($_POST['clear']));
    header('Location: ' . Vars::$HOME_URL);
    exit;
} else {
    echo'<div class="rmenu">' .
        '<p><h3>' . Vars::$LNG['exit_warning'] . '</h3></p>' .
        '<form action="' . Vars::$HOME_URL . '/exit' . '" method="post">' .
        '<p><input type="checkbox" name="clear" value="1"/>&#160;' . Vars::$LNG['clear_authorisation'] . '</p>' .
        '<p><input type="submit" name="submit" value="' . Vars::$LNG['exit'] . '" /></p>' .
        '</form>' .
        '<p><a href="' . $referer . '">' . Vars::$LNG['cancel'] . '</a></p>' .
        '</div>';
}