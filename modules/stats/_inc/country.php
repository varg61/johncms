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
$url = Router::getUri(2);

echo '<div class="phdr">' . __('countries') . ' (' . __('hits') . ')</div>';
$count = mysql_result(mysql_query("SELECT COUNT(DISTINCT `country`) FROM `counter`;"), 0);
if ($count > 0) {
    $req = mysql_query("SELECT * FROM `counter` GROUP BY `country`" . Vars::db_pagination());
    $i = 0;
    while ($arr = mysql_fetch_array($req)) {
        echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
        ++$i;
        $count_hits = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `country` = '" . $arr['country'] . "'"), 0);
        echo Functions::loadModuleImage('country.png') . ' ' . $arr['country'] . '
        <div class="sub">' . __('total_hits') . ': ' . $count_hits . '</div>';
        echo '</div>';
    }
    echo '<div class="phdr">' . __('total') . ': ' . $count . '</div>';
    if ($count > Vars::$USER_SET['page_size']) {
        echo '<div class="topmenu">';
        echo Functions::displayPagination($url . '?act=country&amp;', Vars::$START, $count, Vars::$USER_SET['page_size']) . '</div>';
        echo '<p><form action="' . $url . '" method="get"><input type="hidden" name="act" value="country"/><input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
    }

} else {
    echo '<div class="rmenu">' . __('no_data') . '!</div>';
}