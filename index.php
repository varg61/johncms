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

if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    die ('<center><h1>ERROR</h1><h3>Requires PHP 5.3 or newer</h3>Your version: ' . PHP_VERSION . '</center>');
}

require_once('includes/core.php');

new Router();