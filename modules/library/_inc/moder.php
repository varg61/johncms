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

if (Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) {
    echo '<div class="phdr">' . __('articles_moderation') . '</div>';
    if (Vars::$ID && (isset($_GET['yes']))) {
        DB::PDO()->exec("UPDATE `lib` SET `moder` = '1' , `time` = '" . time() . "' WHERE `id` = " . Vars::$ID);
        $res = DB::PDO()->query("SELECT `name` FROM `lib` WHERE `id` = " . Vars::$ID)->fetch();
        echo '<div class="rmenu">' . __('article') . ' <b>' . $res['name'] . '</b> ' . __('added_to_database') . '</div>';
    }
    if (isset($_GET['all'])) {
        $req = DB::PDO()->query("SELECT `id` FROM `lib` WHERE `type` = 'bk' AND `moder` = '0'");
        while ($res = $req->fetch()) {
            DB::PDO()->exec("UPDATE `lib` SET `moder` = '1', `time` = '" . time() . "' WHERE `id` = '" . $res['id'] . "'");
        }
        echo '<p>' . __('added_all') . '</p>';
    }
    $total = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = '0'")->fetchColumn();
    if ($total > 0) {
        $req = DB::PDO()->query("SELECT * FROM `lib` WHERE `type` = 'bk' AND `moder` = '0' " . Vars::db_pagination());
        $i = 0;
        while ($res = $req->fetch()) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            $tx = $res['soft'];
            echo "<a href='" . $url . "?id=" . $res['id'] . "'>$res[name]</a><br/>" . __('added') . ": $res[avtor] (" . Functions::displayDate($res['time']) . ")<br/>$tx <br/>";
            $nadir = $res['refid'];
            $pat = "";
            while ($nadir != "0") {
                $dnew1 = DB::PDO()->query("select `id`, `refid`, `text` from `lib` where type = 'cat' and id = '" . $nadir . "'")->fetch();
                $pat = "$dnew1[text]/$pat";
                $nadir = $dnew1['refid'];
            }
            $l = mb_strlen($pat);
            $pat1 = mb_substr($pat, 0, $l - 1);
            echo "[$pat1]<br/><a href='" . $url . "?act=moder&amp;id=" . $res['id'] . "&amp;yes'> " . __('approve') . "</a></div>";
            ++$i;
        }
        echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo '<p>' . Functions::displayPagination($url . '?act=moder&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>';
            echo '<p><form action="' . $url . '" method="get"><input type="hidden" value="moder" name="act" /><input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
        }
        echo '<p><a href="' . $url . '?act=moder&amp;all">' . __('approve_all') . '</a><br />';
    } else {
        echo '<p>';
    }
    echo '<a href="?">' . __('to_library') . '</a></p>';
}
