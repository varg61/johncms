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

if ($rights >= 6) {
    if ($_GET['id'] == "") {
        echo "ERROR<br/><a href='index.php'>Back</a><br/>";
        require_once('../incfiles/end.php');
        exit;
    }
    $typ = mysql_query("select * from `gallery` where id='" . $id . "';");
    $ms = mysql_fetch_array($typ);
    if ($ms['type'] != "ft") {
        echo "ERROR<br/><a href='index.php'>Back</a><br/>";
        require_once('../incfiles/end.php');
        exit;
    }
    if (isset($_POST['submit'])) {
        $text = functions::check($_POST['text']);
        mysql_query("update `gallery` set text='" . $text . "' where id='" . $id . "';");
        header("location: index.php?id=$ms[refid]");
    } else {
        echo $lng_gal['edit_description'] . "<br/><form action='index.php?act=edf&amp;id=" . $id . "' method='post'><input type='text' name='text' value='" . $ms['text'] .
             "'/><br/><input type='submit' name='submit' value='Ok!'/></form><br/><a href='index.php?id=" . $ms['refid'] . "'>" . $lng['back'] . "</a><br/>";
    }
} else {
    header("location: index.php");
}