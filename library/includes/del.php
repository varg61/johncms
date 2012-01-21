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

if (Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) {
    if ($_GET['id'] == "" || $_GET['id'] == "0") {
        echo "";
        exit;
    }
    $typ = mysql_query("select * from `lib` where `id` = " . Vars::$ID);
    $ms = mysql_fetch_array($typ);
    $rid = $ms['refid'];
    if (isset($_GET['yes'])) {
        switch ($ms['type']) {
            case "komm":
                mysql_query("delete from `lib` where `id` = " . Vars::$ID);
                header("location: index.php?act=komm&id=$rid");
                break;

            case "bk":
                $km = mysql_query("select `id` from `lib` where `type` = 'komm' and `refid` = " . Vars::$ID);
                while ($km1 = mysql_fetch_array($km)) {
                    mysql_query("delete from `lib` where `id`='" . $km1['id'] . "';");
                }
                mysql_query("delete from `lib` where `id` = " . Vars::$ID);
                header("location: index.php?id=$rid");
                break;

            case "cat":
                $ct = mysql_query("select `id` from `lib` where `type` = 'cat' and `refid` = " . Vars::$ID);
                $ct1 = mysql_num_rows($ct);
                if ($ct1 != 0) {
                    echo $lng_lib['first_delete_category'] . "<br/><a href='index.php?id=" . Vars::$ID . "'>" . Vars::$LNG['back'] . "</a><br/>";
                    exit;
                }
                $st = mysql_query("select `id` from `lib` where `type` = 'bk' and `refid` = " . Vars::$ID);
                while ($st1 = mysql_fetch_array($st)) {
                    $km = mysql_query("select `id` from `lib` where type='komm' and refid='" . $st1['id'] . "';");
                    while ($km1 = mysql_fetch_array($km)) {
                        mysql_query("delete from `lib` where `id`='" . $km1['id'] . "';");
                    }

                    mysql_query("delete from `lib` where `id`='" . $st1['id'] . "';");
                }
                mysql_query("delete from `lib` where `id`='" . Vars::$ID . "';");
                header("location: index.php?id=$rid");
                break;
        }
    } else {
        switch ($ms['type']) {
            case "komm":
                header("location: index.php?act=del&id=" . Vars::$ID . "&yes");
                break;

            case "bk":
                echo Vars::$LNG['delete_confirmation'] . "<br/><a href='index.php?act=del&amp;id=" . Vars::$ID . "&amp;yes'>" . Vars::$LNG['delete'] .
                    "</a> | <a href='index.php?id=" . Vars::$ID . "'>" . Vars::$LNG['cancel'] . "</a><br/><a href='index.php'>" . $lng_lib['to_library'] . "</a><br/>";
                break;

            case "cat":
                $ct = mysql_query("select `id` from `lib` where `type` = 'cat' and `refid` = " . Vars::$ID);
                $ct1 = mysql_num_rows($ct);
                if ($ct1 != 0) {
                    echo $lng_lib['first_delete_category'] . "<br/><a href='index.php?id=" . Vars::$ID . "'>" . Vars::$LNG['back'] . "</a><br/>";
                    exit;
                }
                echo Vars::$LNG['delete_confirmation'] . "<br/><a href='index.php?act=del&amp;id=" . Vars::$ID . "&amp;yes'>" . Vars::$LNG['delete'] . "</a> | <a href='index.php?id=" . Vars::$ID .
                     "'>" . Vars::$LNG['cancel'] . "</a><br/><a href='index.php'>" . Vars::$LNG['back'] . "</a><br/>";
                break;
        }
    }
} else {
    header("location: index.php");
}