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
    if ($_GET['id'] == "") {
        echo "ERROR<br/><a href='index.php'>Back</a><br/>";
        exit;
    }
    $typ = mysql_query("select * from `gallery` where `id` = " . Vars::$ID);
    $ms = mysql_fetch_array($typ);
    if ($ms['type'] != "ft") {
        echo "ERROR<br/><a href='index.php'>Back</a><br/>";
        exit;
    }
    if (isset($_GET['yes'])) {
        $km = mysql_query("select * from `gallery` where type='km' and `refid` = " . Vars::$ID);
        while ($km1 = mysql_fetch_array($km)) {
            mysql_query("delete from `gallery` where `id`='" . $km1['id'] . "';");
        }
        unlink("foto/$ms[name]");
        mysql_query("delete from `gallery` where `id` = " . Vars::$ID);
        header("location: index.php?id=$ms[refid]");
    } else {
        echo Vars::$LNG['delete_confirmation'] . "<br/>";
        echo "<a href='index.php?act=delf&amp;id=" . Vars::$ID . "&amp;yes'>" . Vars::$LNG['delete'] . "</a> | <a href='index.php?id=" . $ms['refid'] . "'>" . Vars::$LNG['cancel'] . "</a><br/>";
    }
}