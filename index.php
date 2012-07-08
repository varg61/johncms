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
if (version_compare(phpversion(), '5.3.0', '<') == TRUE) die ('ERROR: PHP5.3 > Only');

require_once('includes/core.php');

include(Vars::$MODULE_INCLUDE);