<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require_once('includes/core.php');
$referer = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : Vars::$SYSTEM_SET['homeurl'];

if (isset($_POST['submit'])) {
    $login = new Login;
    $login->userUnset(isset($_POST['clear']));
    header('Location: index.php');
    exit;
} else {
    echo'<div class="rmenu">' .
        '<p><h3>' . Vars::$LNG['exit_warning'] . '</h3></p>' .
        '<form action="exit.php" method="post">' .
        '<p><input type="checkbox" name="clear" value="1"/>&#160;' . Vars::$LNG['clear_authorisation'] . '</p>' .
        '<p><input type="submit" name="submit" value="' . Vars::$LNG['exit'] . '" /></p>' .
        '</form>' .
        '<p><a href="' . $referer . '">' . Vars::$LNG['cancel'] . '</a></p>' .
        '</div>';
}