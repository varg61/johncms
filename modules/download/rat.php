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

if ($_GET['id'] == "") {
    echo "ERROR<br/><a href='index.php?'>Back</a><br/>";
    exit;
}
$typ = mysql_query("select * from `download` where `id` = " . Vars::$ID);
$ms = mysql_fetch_array($typ);
if ($ms[type] != "file") {
    echo "ERROR<br/><a href='index.php?'>Back</a><br/>";
    exit;
}
if ($_SESSION['rat'] == Vars::$ID) {
    echo $lng_dl['already_rated'] . "<br/><a href='index.php?act=view&amp;file=" . Vars::$ID . "'>" . Vars::$LNG['back'] . "</a><br/>";
    exit;
}
$rat = intval($_POST['rat']);
if (!empty($ms[soft])) {
    $rt = explode(",", $ms[soft]);
    $rt1 = $rt[0] + $rat;
    $rt2 = $rt[1] + 1;
    $rat1 = "$rt1,$rt2";
} else {
    $rat1 = "$rat,1";
}
$_SESSION['rat'] = Vars::$ID;
mysql_query("update `download` set soft = '" . $rat1 . "' where id = '" . Vars::$ID . "';");
echo $lng_dl['vote_adopted'] . "<br/><a href='index.php?act=view&amp;file=" . Vars::$ID . "'>" . Vars::$LNG['back'] . "</a><br/>";