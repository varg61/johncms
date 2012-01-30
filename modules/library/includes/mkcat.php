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
    if ($_GET['id'] == "") {
        echo "";
        exit;
    }
    $typ = mysql_query("select * from `lib` where `id` = " . Vars::$ID);
    $ms = mysql_fetch_array($typ);
    if (Vars::$ID != 0 && ($ms['type'] == "bk" || $ms['type'] == "komm")) {
        echo "";
        exit;
    }
    if (isset($_POST['submit'])) {
        if (empty($_POST['text'])) {
            echo Functions::displayError(Vars::$LNG['error_empty_title'], '<a href="index.php?act=mkcat&amp;id=' . Vars::$ID . '">' . Vars::$LNG['repeat'] . '</a>');
            exit;
        }
        $text = Validate::filterString($_POST['text']);
        $user = isset($_POST['user']);
        $typs = intval($_POST['typs']);
        mysql_query("INSERT INTO `lib` SET
            `refid` = " . Vars::$ID . ",
            `time` = " . time() . ",
            `type` = 'cat',
            `text` = '" . mysql_real_escape_string($text) . "',
            `ip` = '$typs',
            `soft` = '$user'
        ");
        $cid = mysql_insert_id();
        echo $lng_lib['category_created'] . "<br/><a href='index.php?id=" . $cid . "'>" . $lng_lib['to_category'] . "</a><br/>";
    } else {
        echo $lng_lib['create_category'] . '<br/>' .
             '<form action="index.php?act=mkcat&amp;id=' . Vars::$ID . '" method="post">' .
             Vars::$LNG['title'] . ':<br/>' .
             '<input type="text" name="text"/>' .
             '<p>' . $lng_lib['category_type'] . '<br/>' .
             '<select name="typs">' .
             '<option value="1">' . $lng_lib['categories'] . '</option>' .
             '<option value="0">' . $lng_lib['articles'] . '</option>' .
             '</select></p>' .
             '<p><input type="checkbox" name="user" value="1"/>' . $lng_lib['if_articles'] . '</p>' .
             '<p><input type="submit" name="submit" value="' . Vars::$LNG['save'] . '"/></p>' .
             '</form>' .
             '<p><a href ="index.php?id=' . Vars::$ID . '">' . Vars::$LNG['back'] . '</a></p>';
    }
} else {
    header("location: index.php");
}