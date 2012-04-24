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

echo '<div class="phdr"><b>' . lng('new_articles') . '</b></div>';
$req = mysql_query("SELECT COUNT(*) FROM `lib` WHERE `time` > '" . (time() - 259200) . "' AND `type` = 'bk' AND `moder` = '1'");
$total = mysql_result($req, 0);
if ($total > 0) {
    $req = mysql_query("SELECT * FROM `lib` WHERE `time` > '" . (time() - 259200) . "' AND `type` = 'bk' AND `moder` = '1' ORDER BY `time` DESC " . Vars::db_pagination());
    $i = 0;
    while ($newf = mysql_fetch_array($req)) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        echo '<b><a href="?id=' . $newf['id'] . '">' . htmlentities($newf['name'], ENT_QUOTES, 'UTF-8') . '</a></b><br/>';
        echo htmlentities($newf['announce'], ENT_QUOTES, 'UTF-8') . '<br />';
        echo lng('added') . ': ' . $newf['avtor'] . ' (' . Functions::displayDate($newf['time']) . ')<br/>';
        $nadir = $newf['refid'];
        $dirlink = $nadir;
        $pat = "";
        while ($nadir != "0") {
            $dnew = mysql_query("select * from `lib` where type = 'cat' and id = '" . $nadir . "';");
            $dnew1 = mysql_fetch_array($dnew);
            $pat = $dnew1['text'] . '/' . $pat;
            $nadir = $dnew1['refid'];
        }
        $l = mb_strlen($pat);
        $pat1 = mb_substr($pat, 0, $l - 1);
        echo '[<a href="index.php?id=' . $dirlink . '">' . $pat1 . '</a>]</div>';
        ++$i;
    }
    echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
    // Навигация по страницам
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<p>' . Functions::displayPagination('index.php?act=new&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>';
        echo '<p><form action="index.php" method="get"><input type="hidden" name="act" value="new"/><input type="text" name="page" size="2"/><input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
    }
} else {
    echo '<p>' . lng('list_empty') . '</p>';
}
echo '<p><a href="index.php">' . lng('to_library') . '</a></p>';