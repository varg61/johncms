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

$tpl = Template::getInstance();
$tpl->mp = new HomePage();
$tpl->count = new Counters();

$tpl->load('_header');
$tpl->load('homepage'); // Шаблон Главной страницы сайта
$tpl->load('_footer');