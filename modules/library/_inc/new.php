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
$url = Router::getUri(2);

echo '<div class="phdr"><b>' . __('new_articles') . '</b></div>';
$total = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `time` > '" . (time() - 259200) . "' AND `type` = 'bk' AND `moder` = '1'")->fetchColumn();
if ($total > 0) {
    $req = DB::PDO()->query("SELECT * FROM `lib` WHERE `time` > '" . (time() - 259200) . "' AND `type` = 'bk' AND `moder` = '1' ORDER BY `time` DESC " . Vars::db_pagination());
    $i = 0;
    while ($newf = $req->fetch()) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        echo '<b><a href="?id=' . $newf['id'] . '">' . htmlentities($newf['name'], ENT_QUOTES, 'UTF-8') . '</a></b><br/>';
        echo htmlentities($newf['announce'], ENT_QUOTES, 'UTF-8') . '<br />';
        echo __('added') . ': ' . $newf['avtor'] . ' (' . Functions::displayDate($newf['time']) . ')<br/>';
        $nadir = $newf['refid'];
        $dirlink = $nadir;
        $pat = "";
        while ($nadir != "0") {
            $dnew1 = DB::PDO()->query("select * from `lib` where type = 'cat' and id = '" . $nadir . "'")->fetch();
            $pat = $dnew1['text'] . '/' . $pat;
            $nadir = $dnew1['refid'];
        }
        $l = mb_strlen($pat);
        $pat1 = mb_substr($pat, 0, $l - 1);
        echo '[<a href="' . $url . '?id=' . $dirlink . '">' . $pat1 . '</a>]</div>';
        ++$i;
    }
    echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
    // Навигация по страницам
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<p>' . Functions::displayPagination($url . '?act=new&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>';
        echo '<p><form action="' . $url . '" method="get"><input type="hidden" name="act" value="new"/><input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
    }
} else {
    echo '<p>' . __('list_empty') . '</p>';
}
echo '<p><a href="' . Router::getUri(2) . '">' . __('to_library') . '</a></p>';