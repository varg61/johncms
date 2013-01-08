<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_USERS') or die('Error: restricted access');

$tpl = Template::getInstance();

$tpl->url = Router::getUrl(3);
$tpl->count = new Counters();
$tpl->total_photo = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = " . Vars::$USER_ID), 0);
$tpl->contents = $tpl->includeTpl('user_menu');