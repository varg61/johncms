<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

//TODO: Доработать!

/*
-----------------------------------------------------------------
Выводим список именинников
-----------------------------------------------------------------
*/
echo '<div class="phdr"><a href="index.php"><b>' . lng('community') . '</b></a> | ' . lng('birthday_men') . '</div>';
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `dayb` = '" . date('j', time()) . "' AND `monthb` = '" . date('n', time()) . "' AND `preg` = '1'"), 0);
if ($total) {
    $req = mysql_query("SELECT * FROM `users` WHERE `dayb` = '" . date('j', time()) . "' AND `monthb` = '" . date('n', time()) . "' AND `preg` = '1' LIMIT " . Vars::db_pagination());
    while ($res = mysql_fetch_assoc($req)) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        echo Functions::displayUser($res) . '</div>';
        ++$i;
    }
    echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<p>' . Functions::displayPagination('index.php?act=birth&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>';
        echo '<p><form action="index.php?act=birth" method="post">' .
             '<input type="text" name="page" size="2"/>' .
             '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/>' .
             '</form></p>';
    }
} else {
    echo '<div class="menu"><p>' . lng('list_empty') . '</p></div>';
}
echo '<p><a href="index.php">' . lng('back') . '</a></p>';