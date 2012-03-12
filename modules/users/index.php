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

if (!Vars::$USER_ID && !Vars::$SYSTEM_SET['active']) {
    echo Functions::displayError(lng('access_guest_forbidden'));
    exit;
}

$tpl = Template::getInstance();
$tpl->count = new Counters();
$tpl->contents = $tpl->includeTpl('index');