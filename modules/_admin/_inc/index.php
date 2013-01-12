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
$tpl->uri = Router::getUri(2);

$tpl->usrTotal = DB::PDO()->query("SELECT COUNT(*) FROM `users` WHERE `level` > 0")->fetchColumn();
$tpl->regTotal = DB::PDO()->query("SELECT COUNT(*) FROM `users` WHERE `level` = '0'")->fetchColumn();
$tpl->banTotal = DB::PDO()->query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time` > " . time())->fetchColumn();

$tpl->contents = $tpl->includeTpl('index');