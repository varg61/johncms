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
    switch ($ms['type']) {
        case "al":
            if (isset($_POST['submit'])) {
                $text = Validate::filterString($_POST['text']);
                mysql_query("update `gallery` set text='" . mysql_real_escape_string($text) . "' where `id` = " . Vars::$ID);
                header("location: index.php?id=" . Vars::$ID);
            } else {
                echo $lng_gal['edit_album'] . "<br/><form action='index.php?act=edit&amp;id=" . Vars::$ID . "' method='post'><input type='text' name='text' value='" . $ms['text'] .
                     "'/><br/><input type='submit' name='submit' value='Ok!'/></form><br/><a href='index.php?id=" . Vars::$ID . "'>" . Vars::$LNG['back'] . "</a><br/>";
            }
            break;

        case "rz":
            if (isset($_POST['submit'])) {
                $text = Validate::filterString($_POST['text']);
                if (!empty($_POST['user'])) {
                    $user = intval($_POST['user']);
                } else {
                    $user = 0;
                }
                mysql_query("update `gallery` set text='" . mysql_real_escape_string($text) . "', user='" . $user . "' where `id` = " . Vars::$ID);
                header("location: index.php?id=" . Vars::$ID);
            } else {
                echo $lng_gal['edit_section'] . "<br/><form action='index.php?act=edit&amp;id=" . Vars::$ID . "' method='post'><input type='text' name='text' value='" . $ms['text'] . "'/><br/>";
                echo "<input type='submit' name='submit' value='Ok!'/></form><br/><a href='index.php?id=" . Vars::$ID . "'>" . Vars::$LNG['back'] . "</a><br/>";
            }
            break;

        default:
            echo "ERROR<br/><a href='index.php'>Back</a><br/>";
            exit;
    }
} else {
    header("location: index.php");
}