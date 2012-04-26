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
$req = mysql_query("select * from `lib` where `type` = 'bk' and `moder`='1' and `count`>'0' ORDER BY `count` DESC LIMIT 50");
$totalnew = mysql_num_rows($req);
$start = Vars::$PAGE * 10 - 10;
if ($totalnew < $start + 10) {
    $end = $totalnew;
} else {
    $end = $start + 10;
}
if ($totalnew != 0) {
    while ($res = mysql_fetch_array($req)) {
        if ($i >= $start && $i < $end) {
            $d = $i / 2;
            $d1 = ceil($d);
            $d2 = $d1 - $d;
            $d3 = ceil($d2);
            if ($d3 == 0) {
                $div = "<div class='c'>";
            } else {
                $div = "<div class='b'>";
            }
            echo $div;
            echo '<b><a href="?id=' . $res['id'] . '">' . htmlentities($res['name'], ENT_QUOTES, 'UTF-8') . '</a></b><br/>';
            echo htmlentities($res['announce'], ENT_QUOTES, 'UTF-8') . '<br />';
            echo lng('reads') . ': ' . $res['count'] . '<br/>';
            $nadir = $res['refid'];
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
            echo '[<a href="' . Vars::$URI . '?id=' . $dirlink . '">' . $pat1 . '</a>]</div>';
        }
        ++$i;
    }
    echo "<hr/><p>";
    if ($totalnew > 10) {
        //TODO: Добавить новую навигацию по страницам
    }
} else {
    echo "<p>" . lng('list_empty') . "<br/>";
}
echo '<a href="' . Vars::$URI . '">' . lng('to_library') . '</a></p>';