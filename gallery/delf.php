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
    $id = intval($_GET['id']);
    $typ = mysql_query("select * from `gallery` where id='" . $id . "';");
    $ms = mysql_fetch_array($typ);
    if ($ms['type'] != "ft") {
        echo "ERROR<br/><a href='index.php'>Back</a><br/>";
        require_once('../incfiles/end.php');
        exit;
    }
    if (isset($_GET['yes'])) {
        $km = mysql_query("select * from `gallery` where type='km' and refid='" . $id . "';");
        while ($km1 = mysql_fetch_array($km)) {
            mysql_query("delete from `gallery` where `id`='" . $km1['id'] . "';");
        }
        unlink("foto/$ms[name]");
        mysql_query("delete from `gallery` where `id`='" . $id . "';");
        header("location: index.php?id=$ms[refid]");
    } else {
        echo $lng['delete_confirmation'] . "<br/>";
        echo "<a href='index.php?act=delf&amp;id=" . $id . "&amp;yes'>" . $lng['delete'] . "</a> | <a href='index.php?id=" . $ms['refid'] . "'>" . $lng['cancel'] . "</a><br/>";
    }
}