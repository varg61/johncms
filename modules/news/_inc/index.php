<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_NEWS') or die('Error: restricted access');

$tpl = Template::getInstance();
$tpl->uri = Router::getUri(2);

$tpl->total = DB::PDO()->query('SELECT COUNT(*) FROM `cms_news`')->fetchColumn();

if ($tpl->total) {
    $query = DB::PDO()->query('SELECT * FROM `cms_news` ORDER BY `id` DESC ' . Vars::db_pagination());
    foreach($query as $val){
        $val['text'] = Validate::checkout($val['text'], 1, 1, 1);
        $tpl->list[] = $val;
    }
}

$tpl->contents = $tpl->includeTpl('index');