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
    if ($_GET['id'] == "" || $_GET['id'] == "0") {
        echo "";
        exit;
    }
    $ms = DB::PDO()->query("select * from `lib` where `id` = " . Vars::$ID)->fetch();
    $rid = $ms['refid'];
    if (isset($_GET['yes'])) {
        switch ($ms['type']) {
            case 'bk':
                DB::PDO()->exec("delete from `lib` where `id` = " . Vars::$ID);
                header("location: " . $url . "?id=$rid");
                break;

            case 'cat':
                if (DB::PDO()->query("select COUNT(*) from `lib` where `type` = 'cat' and `refid` = " . Vars::$ID)->fetchColumn()) {
                    echo __('first_delete_category') . "<br/><a href='" . $url . "?id=" . Vars::$ID . "'>" . __('back') . "</a><br/>";
                    exit;
                }
                DB::PDO()->exec("DELETE FROM `lib` WHERE `type` = 'bk' AND `refid` = " . Vars::$ID);
                DB::PDO()->exec("delete from `lib` where `id` = " . Vars::$ID);
                header("location: " . $url . "?id=$rid");
                break;
        }
    } else {
        switch ($ms['type']) {
            case 'komm':
                header("location: " . $url . "?act=del&id=" . Vars::$ID . "&yes");
                break;

            case 'bk':
                echo __('delete_confirmation') . "<br/><a href='" . $url . "?act=del&amp;id=" . Vars::$ID . "&amp;yes'>" . __('delete') .
                    "</a> | <a href='" . $url . "?id=" . Vars::$ID . "'>" . __('cancel') . "</a><br/><a href='" . $url . "'>" . __('to_library') . "</a><br/>";
                break;

            case 'cat':
                if (DB::PDO()->query("select COUNT(*) from `lib` where `type` = 'cat' and `refid` = " . Vars::$ID)->fetchColumn()) {
                    echo __('first_delete_category') . "<br/><a href='" . $url . "?id=" . Vars::$ID . "'>" . __('back') . "</a><br/>";
                    exit;
                }
                echo __('delete_confirmation') . "<br/><a href='" . $url . "?act=del&amp;id=" . Vars::$ID . "&amp;yes'>" . __('delete') . "</a> | <a href='" . $url . "?id=" . Vars::$ID .
                     "'>" . __('cancel') . "</a><br/><a href='" . $url . "'>" . __('back') . "</a><br/>";
                break;
        }
    }
} else {
    header("location: " . $url);
}