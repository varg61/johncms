<?php

/**
 * @author simba
 * @copyright 2011
 */

defined('_IN_JOHNCMS') or die('Restricted access');
$url = Router::getUrl(2);

echo '<div class="phdr">' . __('users') . '</div>';
$count = mysql_result(mysql_query("SELECT COUNT(DISTINCT `user`) FROM `counter`;"), 0);
if ($count > 0) {
    $req = mysql_query("SELECT * FROM `counter` GROUP BY `user`" . Vars::db_pagination());
    $i = 0;
    while ($arr = mysql_fetch_array($req)) {
        echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
        ++$i;
        $count_hits = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `user` = '" . $arr['user'] . "'"), 0);

        $user = mysql_query("SELECT * from `users` where id = '" . $arr['user'] . "';");
        $user = mysql_fetch_array($user);
        $arg = array('stshide' => 1,
                     'sub'     => __('movies') . ': ' . $count_hits);
        echo Functions::displayUser($user, $arg) . '</div>';
    }

    echo '<div class="phdr">' . __('total') . ': ' . $count . '</div>';
    if ($count > Vars::$USER_SET['page_size']) {
        echo '<div class="topmenu">';
        echo Functions::displayPagination($url . '?act=users&amp;', Vars::$START, $count, Vars::$USER_SET['page_size']) . '</div>';
        echo '<p><form action="' . $url . '" method="get"><input type="hidden" name="act" value="users"/><input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
    }

} else {
    echo '<div class="rmenu">' . __('no_data') . '!</div>';
}