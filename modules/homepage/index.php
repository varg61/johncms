<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 *
 * Главное меню сайта
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

if (isset($_SESSION['ref']))
    unset($_SESSION['ref']);

// Загружаем шаблон вывода
$tpl = Template::getInstance();
$tpl->mp = new HomePage();
$tpl->count = new Counters();

if (isset(Vars::$SYSTEM_SET['sitemap'])) {
    $set_map = unserialize(Vars::$SYSTEM_SET['sitemap']);
    if (($set_map['forum'] || $set_map['lib']) && ($set_map['users'] || !Vars::$USER_ID) && ($set_map['browsers'] || !Vars::$IS_MOBILE)) {
        $map = new SiteMap();
        $tpl->sitemap = $map->mapGeneral();
        //echo '<div class="sitemap">' . $map->mapGeneral() . '</div>';
    }
}

$tpl->contents = $tpl->includeTpl('homepage');