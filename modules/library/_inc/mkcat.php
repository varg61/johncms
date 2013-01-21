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
$url = Router::getUri(2);

if (Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) {
    if ($_GET['id'] == "") {
        echo "";
        exit;
    }
    $ms = DB::PDO()->query("select * from `lib` where `id` = " . Vars::$ID)->fetch();
    if (Vars::$ID != 0 && ($ms['type'] == "bk" || $ms['type'] == "komm")) {
        echo "";
        exit;
    }
    if (isset($_POST['submit'])) {
        if (empty($_POST['text'])) {
            echo Functions::displayError(__('error_empty_title'), '<a href="' . $url . '?act=mkcat&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
            exit;
        }
        $text = Validate::checkout($_POST['text']);
        $user = isset($_POST['user']);
        $typs = intval($_POST['typs']);

        $STH = $STH = DB::PDO()->prepare('
            INSERT INTO `lib` SET
            `refid` = :refid,
            `time` = :time,
            `type` = "cat",
            `text` = :text,
            `ip` = :ip,
            `soft` = :soft
        ');

        $STH->bindValue(':refid', Vars::$ID);
        $STH->bindValue(':time', time());
        $STH->bindValue(':text', $text);
        $STH->bindValue(':ip', $typs);
        $STH->bindValue(':soft', $user);
        $STH->execute();
        $cid = DB::PDO()->lastInsertId();
        $STH = NULL;

        echo __('category_created') . "<br/><a href='" . $url . "?id=" . $cid . "'>" . __('to_category') . "</a><br/>";
    } else {
        echo __('create_category') . '<br/>' .
            '<form action="' . $url . '?act=mkcat&amp;id=' . Vars::$ID . '" method="post">' .
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
            '<p><a href ="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a></p>';
    }
} else {
    header("location: " . $url);
}