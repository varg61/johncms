<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_ADMIN') or die('Error: restricted access');

$tpl = Template::getInstance();

$tpl->usrTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `level` > 0"), 0);
$tpl->regTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `level` = '0'"), 0);
//$tpl->banTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time` > '" . time() . "'"), 0);
$tpl->contents = $tpl->includeTpl('index');

$tpl->contents = $tpl->includeTpl('mainmenu');