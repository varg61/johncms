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

$tpl->url = Router::getUri(3);
$tpl->count = new Counters();
$tpl->total_photo = DB::PDO()->query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = " . Vars::$USER_ID)->fetchColumn();
$tpl->contents = $tpl->includeTpl('menu');