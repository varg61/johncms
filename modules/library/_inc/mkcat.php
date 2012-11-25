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
            echo Functions::displayError(__('error_empty_title'), '<a href="' . Vars::$URI . '?act=mkcat&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
            exit;
        }
        $text = Validate::checkout($_POST['text']);
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
        echo __('category_created') . "<br/><a href='" . Vars::$URI . "?id=" . $cid . "'>" . __('to_category') . "</a><br/>";
    } else {
        echo __('create_category') . '<br/>' .
             '<form action="' . Vars::$URI . '?act=mkcat&amp;id=' . Vars::$ID . '" method="post">' .
             __('title') . ':<br/>' .
             '<input type="text" name="text"/>' .
             '<p>' . __('category_type') . '<br/>' .
             '<select name="typs">' .
             '<option value="1">' . __('categories') . '</option>' .
             '<option value="0">' . __('articles') . '</option>' .
             '</select></p>' .
             '<p><input type="checkbox" name="user" value="1"/>' . __('if_articles') . '</p>' .
             '<p><input type="submit" name="submit" value="' . __('save') . '"/></p>' .
             '</form>' .
             '<p><a href ="' . Vars::$URI . '?id=' . Vars::$ID . '">' . __('back') . '</a></p>';
    }
} else {
    header("location: " . Vars::$URI);
}