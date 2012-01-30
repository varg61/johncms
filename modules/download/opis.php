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

if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
    if ($_GET['file'] == "") {
        echo $lng_dl['file_not_selected'] . "<br/><a href='?'>" . Vars::$LNG['back'] . "</a><br/>";
        exit;
    }
    $file = intval($_GET['file']);
    $file1 = mysql_query("SELECT * FROM `download` WHERE `type` = 'file' AND `id` = '" . $file . "';");
    $file2 = mysql_num_rows($file1);
    $adrfile = mysql_fetch_array($file1);
    if (($file1 == 0) || (!is_file("$adrfile[adres]/$adrfile[name]"))) {
        echo $lng_dl['file_not_selected'] . "<br/><a href='?'>" . Vars::$LNG['back'] . "</a><br/>";
        exit;
    }
    $stt = "$adrfile[text]";
    if (isset ($_POST['submit'])) {
        $newt = Validate::filterString($_POST['newt']);
        mysql_query("update `download` set `text`='" . mysql_real_escape_string($newt) . "' where `id`='" . $file . "';");
        echo $lng_dl['description_changed'] . "<br/>";
    }
    else {
        $str = str_replace("<br/>", "\r\n", $adrfile['text']);
        echo "<form action='?act=opis&amp;file=" . $file . "' method='post'>";
        echo Vars::$LNG['description'] . ':<br/><textarea rows="4" name="newt">' . $str . '</textarea><br/>';
        echo "<input type='submit' name='submit' value='Изменить'/></form><br/>";
    }
}
else {
    echo "Нет доступа!";
}
echo "<p><a href='?act=view&amp;file=" . $file . "'>" . Vars::$LNG['back'] . "</a></p>";