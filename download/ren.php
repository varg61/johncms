<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

require_once("../incfiles/head.php");
if ($rights == 4 || $rights >= 6) {
    if (empty($_GET['cat'])) {
        echo "ERROR<br /><a href='?'>Back</a><br/>";
        require_once('../incfiles/end.php');
        exit;
    }
    $cat = intval(trim($_GET['cat']));
    provcat($cat);
    $cat1 = mysql_query("select * from `download` where type = 'cat' and id = '" . $cat . "';");
    $adrdir = mysql_fetch_array($cat1);
    $namedir = "$adrdir[adres]/$adrdir[name]";
    if (isset($_POST['submit'])) {
        if (!empty($_POST['newrus'])) {
            $newrus = functions::check($_POST['newrus']);
        } else {
            $newrus = "$adrdir[text]";
        }
        if (mysql_query("update `download` set text='" . $newrus . "' where id='" . $cat . "';")) {
            echo '<p>' . $lng_dl['name_changed'] . '</p>';
        }
    } else {
        echo "<form action='?act=ren&amp;cat=" . $cat . "' method='post'><p>";
        echo $lng_dl['folder_name_for_list'] . "<br/><input type='text' name='newrus' value='" . $adrdir[text] . "'/></p>";
        echo "<p><input type='submit' name='submit' value='" . $lng_dl['change'] . "'/></p></form>";
    }
}
echo "<p><a href='?cat=" . $cat . "'>" . $lng['back'] . "</a></p>";