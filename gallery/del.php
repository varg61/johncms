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
        require_once('../includes/end.php');
        exit;
    }
    $typ = mysql_query("select * from `gallery` where `id` = " . Vars::$ID);
    $ms = mysql_fetch_array($typ);
    if (isset($_GET['yes'])) {
        switch ($ms['type']) {
            case "al":
                $ft = mysql_query("select * from `gallery` where `type`='ft' and `refid` = " . Vars::$ID);
                while ($ft1 = mysql_fetch_array($ft)) {
                    $km = mysql_query("select * from `gallery` where type='km' and refid='" . $ft1['id'] . "';");
                    while ($km1 = mysql_fetch_array($km)) {
                        mysql_query("delete from `gallery` where `id`='" . $km1['id'] . "';");
                    }
                    unlink("foto/$ft1[name]");
                    mysql_query("delete from `gallery` where `id`='" . $ft1['id'] . "';");
                }
                mysql_query("delete from `gallery` where `id` = " . Vars::$ID);
                header("location: index.php?id=$ms[refid]");
                break;

            case "rz":
                $al = mysql_query("select * from `gallery` where type='al' and `refid` = " . Vars::$ID);
                while ($al1 = mysql_fetch_array($al)) {
                    $ft = mysql_query("select * from `gallery` where type='ft' and refid='" . $al1['id'] . "';");
                    while ($ft1 = mysql_fetch_array($ft)) {
                        $km = mysql_query("select * from `gallery` where type='km' and refid='" . $ft1['id'] . "';");
                        while ($km1 = mysql_fetch_array($km)) {
                            mysql_query("delete from `gallery` where `id`='" . $km1['id'] . "';");
                        }
                        unlink("foto/$ft1[name]");
                        mysql_query("delete from `gallery` where `id`='" . $ft1['id'] . "';");
                    }
                    mysql_query("delete from `gallery` where `id`='" . $al1['id'] . "';");
                }
                mysql_query("delete from `gallery` where `id` = " . Vars::$ID);
                header("location: index.php");
                break;

            default:
                echo "ERROR<br/><a href='index.php'>Back</a><br/>";
                require_once('../includes/end.php');
                exit;
                break;
        }
    } else {
        switch ($ms['type']) {
            case "al":
                echo Vars::$LNG['delete_confirmation'] . " $ms[text]?<br/>";
                break;

            case "rz":
                echo Vars::$LNG['delete_confirmation'] . " $ms[text]?<br/>";
                break;

            default:
                echo "ERROR<br/><a href='index.php'>" . Vars::$LNG['to_gallery'] . "</a><br/>";
                require_once('../includes/end.php');
                exit;
                break;
        }
        echo "<a href='index.php?act=del&amp;id=" . Vars::$ID . "&amp;yes'>" . Vars::$LNG['delete'] . "</a> | <a href='index.php?id=" . Vars::$ID . "'>" . Vars::$LNG['cancel'] . "</a><br/>";
    }
} else {
    header("location: index.php");
}