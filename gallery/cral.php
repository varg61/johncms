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

if (Vars::$USER_RIGHTS >= 6) {
    if (empty($_GET['id'])) {
        echo "ERROR<br/><a href='index.php'>Back</a><br/>";
        exit;
    }
    $type = mysql_query("select * from `gallery` where id=" . Vars::$ID);
    $ms = mysql_fetch_array($type);
    if ($ms['type'] != "rz") {
        echo "ERROR<br/><a href='index.php'>Back</a><br/>";
        exit;
    }
    if (isset($_POST['submit'])) {
        $text = Validate::filterString($_POST['text']);
        mysql_query("insert into `gallery` values(0,'" . Vars::$ID . "','" . time() . "','al','','" . mysql_real_escape_string($text) . "','','','','')");
        header("location: index.php?id=" . Vars::$ID);
    } else {
        echo $lng_gal['create_album'] . "<br/><form action='index.php?act=cral&amp;id=" . Vars::$ID .
             "' method='post'>" . Vars::$LNG['title'] . ":<br/><input type='text' name='text'/><br/><input type='submit' name='submit' value='" . Vars::$LNG['save'] . "'/></form><br/><a href='index.php?id=" . Vars::$ID . "'>" . $lng_gal['to_section'] . "</a><br/>";
    }
} else {
    header("location: index.php");
}