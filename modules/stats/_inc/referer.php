<?php

/**
 * @author simba
 * @copyright 2011
 */

defined('_IN_JOHNCMS') or die('Restricted access');
$url = Router::getUrl(2);

echo '<div class="phdr">' . __('whence_come') . '</div>';

$my_url = parse_url(Vars::$HOME_URL);

$count = mysql_result(mysql_query("SELECT COUNT(DISTINCT `site`) FROM `counter` WHERE `site` NOT LIKE '%" . $my_url['host'] . "';"), 0);
if ($count > 0) {
    $req = mysql_query("SELECT * FROM `counter` WHERE `site` NOT LIKE '%" . $my_url['host'] . "' GROUP BY `site` " . Vars::db_pagination());
    $i = 0;
    while ($arr = mysql_fetch_array($req)) {
        echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
        ++$i;
        $count_hits = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `site` = '" . $arr['site'] . "'"), 0);
        echo Functions::loadModuleImage('url.png') . ' <a href="?act=siteadr&amp;site=' . $arr['site'] . '">' . $arr['site'] . '</a>
        <div class="sub">' . Functions::displayDate($arr['date']) . ' | ' . __('movies') . ': ' . $count_hits . '</div>';
        echo '</div>';
    }

    echo '<div class="phdr">' . __('total') . ': ' . $count . '</div>';
    if ($count > Vars::$USER_SET['page_size']) {
        echo '<div class="topmenu">';
        echo Functions::displayPagination($url . '?act=referer&amp;', Vars::$START, $count, Vars::$USER_SET['page_size']) . '</div>';
        echo '<p><form action="' . $url . '" method="get"><input type="hidden" name="act" value="referer"/><input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
    }

} else {
    echo '<div class="rmenu">' . __('no_data') . '!</div>';
}