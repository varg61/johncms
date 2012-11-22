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

// Рейтинг самых читаемых статей
echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('library') . '</b></a> | ' . lng('top_read') . '</div>';
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = '1' AND `count`>'0'"), 0);
if ($total > 100) {
    $total = 100;
}
$req = mysql_query("select * from `lib` where `type` = 'bk' and `moder`='1' and `count`>'0' ORDER BY `count` DESC " . Vars::db_pagination());
if ($total) {
    if ($total > Vars::$USER_SET['page_size']) {
        echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=topread&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    }
    for ($i = 0; $res = mysql_fetch_assoc($req); ++$i) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        echo '<b><a href="?id=' . $res['id'] . '">' . htmlspecialchars($res['name']) . '</a></b>';
        echo '<div class="sub">' . Validate::checkout($res['announce']) . '<br/>';
        echo '<span class="gray">' . lng('reads') . ': ' . $res['count'] . '</span>';
        echo'</div></div>';
    }

} else {
    echo "<p>" . lng('list_empty') . "<br/>";
}
echo'<div class="phdr">' . lng('total') . ': ' . $total . '</div>';

if ($total > Vars::$USER_SET['page_size']) {
    echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=topread&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="' . Vars::$URI . '?act=topread" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/>' .
        '</form></p>';
}

echo '<a href="' . Vars::$URI . '">' . lng('to_library') . '</a></p>';