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
require_once('../includes/core.php');
$textl = Vars::$LNG['forum'];
require_once('../includes/head.php');
$map = new SiteMap();
echo $map->mapForum();
require_once('../includes/end.php');