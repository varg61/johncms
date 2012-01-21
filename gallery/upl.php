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

if (!Vars::$USER_ID || Vars::$USER_RIGHTS < 6) {
    header("location: index.php");
    exit;
}
if (empty($_GET['id'])) {
    echo "ERROR<br/><a href='index.php'>Back</a><br/>";
    exit;
}

$type = mysql_query("select * from `gallery` where `id` = " . Vars::$ID);
$ms = mysql_fetch_array($type);
if ($ms['type'] != "al") {
    echo "ERROR<br/><a href='index.php'>Back</a><br/>";
    exit;
}
$rz = mysql_query("select * from `gallery` where type='rz' and id='" . $ms['refid'] . "'");
$rz1 = mysql_fetch_array($rz);
if ((!empty($_SESSION['uid']) && $rz1['user'] == 1 && $ms['text'] == Vars::$USER_NICKNAME) || Vars::$USER_RIGHTS >= 6) {
    $dopras = array(
        "gif",
        "jpg",
        "png"
    );
    $tff = implode(" ,", $dopras);
    $fotsize = Vars::$SYSTEM_SET['flsz'] / 5;
    echo '<h3>' . $lng_gal['upload_photo'] . "</h3>" . $lng_gal['allowed_types'] . ": $tff<br/>" . $lng_gal['maximum_weight'] . ": $fotsize кб.<br/><form action='index.php?act=load&amp;id=" . Vars::$ID .
         "' method='post' enctype='multipart/form-data'><p>" . $lng_gal['select_photo'] . ":<br/><input type='file' name='fail'/></p><p>" . Vars::$LNG['description'] . ":<br/><textarea name='text'></textarea></p><p><input type='submit' value='" . Vars::$LNG['sent'] . "'/></p></form><a href='index.php?id="
         . Vars::$ID . "'>" . Vars::$LNG['back'] . "</a>";
} else {
    header("location: index.php");
}