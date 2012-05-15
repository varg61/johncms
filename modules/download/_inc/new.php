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

echo '<div class="phdr">' . lng('new_files') . '</div>';

$req = mysql_query("SELECT COUNT(*) FROM `download` WHERE `time` > '" . (time() - 259200) . "' AND `type` = 'file'");
$total = mysql_result($req, 0);
if ($total > 0) {
    ////////////////////////////////////////////////////////////
    // Выводим список новых файлов                            //
    ////////////////////////////////////////////////////////////
    $req = mysql_query("SELECT * FROM `download` WHERE `time` > '" . (time() - 259200) . "' AND `type` = 'file' ORDER BY `time` DESC LIMIT " . Vars::db_pagination());
    while ($newf = mysql_fetch_array($req)) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        $fsz = filesize("$newf[adres]/$newf[name]");
        $fsz = round($fsz / 1024, 2);
        $ft = Functions::format("$newf[adres]/$newf[name]");
        switch ($ft) {
            case "mp3" :
                $imt = "download_mp3.png";
                break;
            case "zip" :
                $imt = "download_rar.png";
                break;
            case "jar" :
                $imt = "download_jar.png";
                break;
            case "gif" :
                $imt = "download_gif.png";
                break;
            case "jpg" :
                $imt = "download_jpg.png";
                break;
            case "png" :
                $imt = "download_png.png";
                break;
            default :
                $imt = "download_file.gif";
                break;
        }
        if ($newf['text'] != "") {
            $tx = $newf['text'];
            if (mb_strlen($tx) > 100) {
                $tx = mb_substr(strip_tags($tx), 0, 90);

                $tx = "<br/>$tx...";
            }
            else {
                $tx = "<br/>$tx";
            }
        }
        else {
            $tx = "";
        }
        echo Functions::getImage($imt) . '<a href="?act=view&amp;file=' . $newf['id'] . '">' . htmlentities($newf['name'], ENT_QUOTES, 'UTF-8') . '</a> (' . $fsz . ' кб)' . $tx . '<br/>';
        $nadir = $newf['refid'];
        $pat = "";
        while ($nadir != "") {
            $dnew = mysql_query("select * from `download` where type = 'cat' and id = '" . $nadir . "'");
            $dnew1 = mysql_fetch_array($dnew);
            $pat = "$dnew1[text]/$pat";
            $nadir = $dnew1['refid'];
        }
        $l = mb_strlen($pat);
        $pat1 = mb_substr($pat, 0, $l - 1);
        echo "[$pat1]</div>";
        ++$i;
    }
    echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
    if ($total > 10) {
        echo '<p>' . Functions::displayPagination(Vars::$URI . '?act=new&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>';
        echo '<p><form action="' . Vars::$URI . '" method="get"><input type="hidden" value="new" name="act" /><input type="text" name="page" size="2"/><input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
    }
}
else {
    echo '<p>' . lng('list_empty') . '</p>';
}
echo '<p><a href="' . Vars::$URI . '">' . lng('back') . '</a></p>';