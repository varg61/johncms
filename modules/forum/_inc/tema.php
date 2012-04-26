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

$delf = opendir('../files/forum/topics');
while ($tt = readdir($delf)) {
    if ($tt != "." && $tt != ".." && $tt != 'index.php' && $tt != '.svn') {
        $tm[] = $tt;
    }
}
closedir($delf);
$totalt = count($tm);
for ($it = 0; $it < $totalt; $it++) {
    $filtime[$it] = filemtime("../files/forum/topics/$tm[$it]");
    $tim = time();
    $ftime1 = $tim - 300;
    if ($filtime[$it] < $ftime1) {
        unlink("../files/forum/topics/$tm[$it]");
    }
}
if (!Vars::$ID) {
    echo Functions::displayError(lng('error_wrong_data'));
    exit;
}
$req = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 't' AND `close` != '1'");
if (!mysql_num_rows($req)) {
    echo Functions::displayError(lng('error_wrong_data'));
    exit;
}
if (isset($_POST['submit'])) {
    $type1 = mysql_fetch_assoc($req);
    $tema = mysql_query("SELECT * FROM `forum` WHERE `refid` = " . Vars::$ID . " AND `type` = 'm'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close` != '1'") . " ORDER BY `id` ASC");
    switch (Vars::$MOD) {
        case 1:
            ////////////////////////////////////////////////////////////
            // Сохраняем тему в текстовом формате                     //
            ////////////////////////////////////////////////////////////
            $text = $type1['text'] . "\r\n\r\n";
            while ($arr = mysql_fetch_assoc($tema)) {
                $txt_tmp = str_replace('[c]', lng('cytate') . ':{', $arr['text']);
                $txt_tmp = str_replace('[/c]', '}-' . lng('answer') . ':', $txt_tmp);
                $txt_tmp = str_replace("&quot;", "\"", $txt_tmp);
                $txt_tmp = str_replace("[l]", "", $txt_tmp);
                $txt_tmp = str_replace("[l/]", "-", $txt_tmp);
                $txt_tmp = str_replace("[/l]", "", $txt_tmp);
                $stroka = $arr['from'] . '(' . date("d.m.Y/H:i", $arr['time']) . ")\r\n" . $txt_tmp . "\r\n\r\n";
                $text .= $stroka;
            }
            $num = time() . Vars::$ID;
            $fp = fopen("../files/forum/topics/$num.txt", "a+");
            flock($fp, LOCK_EX);
            fputs($fp, "$text\r\n");
            fflush($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
            @chmod("$fp", 0777);
            @chmod("../files/forum/topics/$num.txt", 0777);
            echo '<a href="' . Vars::$URI . '?act=loadtem&amp;n=' . $num . '">' . lng('download') . '</a><br/>' . lng('download_topic_help') . '<br/><a href="' . Vars::$URI . '">' . lng('to_forum') . '</a><br/>';
            break;

        case 2:
            ////////////////////////////////////////////////////////////
            // Сохраняем тему в формате HTML                          //
            ////////////////////////////////////////////////////////////
            $text =
                    "<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'><html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title>" . lng('forum')
                    . "</title>
<style type='text/css'>
body { color: #000000; background-color: #FFFFFF }
div { margin: 1px 0px 1px 0px; padding: 5px 5px 5px 5px;}
.b {background-color: #FFFFFF; }
.c {background-color: #EEEEEE; }
.quote{font-size: x-small; padding: 2px 0px 2px 4px; color: #878787; border-left: 3px solid #c0c0c0;
}
</style></head>
      <body><p><b><u>$type1[text]</u></b></p>";
            $i = 1;
            while ($arr = mysql_fetch_array($tema)) {
                $d = $i / 2;
                $d1 = ceil($d);
                $d2 = $d1 - $d;
                $d3 = ceil($d2);
                if ($d3 == 0) {
                    $div = "<div class='b'>";
                } else {
                    $div = "<div class='c'>";
                }
                $txt_tmp = htmlentities($arr['text'], ENT_QUOTES, 'UTF-8');
                $txt_tmp = TextParser::tags($txt_tmp);
                $txt_tmp = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $txt_tmp);
                $txt_tmp = str_replace("\r\n", "<br/>", $txt_tmp);
                $stroka = "$div <b>$arr[from]</b>(" . date("d.m.Y/H:i", $arr['time']) . ")<br/>$txt_tmp</div>";
                $text = "$text $stroka";
                ++$i;
            }
            $text = $text . '<p>' . lng('download_topic_note') . ': <b>' . Vars::$SYSTEM_SET['copyright'] . '</b></p></body></html>';
            $num = time() . Vars::$ID;
            $fp = fopen("../files/forum/topics/$num.htm", "a+");
            flock($fp, LOCK_EX);
            fputs($fp, "$text\r\n");
            fflush($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
            @chmod("$fp", 0777);
            @chmod("../files/forum/topics/$num.htm", 0777);
            echo '<a href="' . Vars::$URI . '?act=loadtem&amp;n=' . $num . '">' . lng('download') . '</a><br/>' . lng('download_topic_help') . '<br/><a href="' . Vars::$URI . '">' . lng('to_forum') . '</a><br/>';
            break;
    }
} else {
    echo '<p>' . lng('download_topic_format') . '<br/>' .
         '<form action="' . Vars::$URI . '?act=tema&amp;id=' . Vars::$ID . '" method="post">' .
         '<select name="mod"><option value="1">.txt</option>' .
         '<option value="2">.htm</option></select>' .
         '<input type="submit" name="submit" value="' . lng('download') . '"/>' .
         '</form></p>';
}