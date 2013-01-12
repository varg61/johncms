<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_NEWS') or die('Error: restricted access');

if (Vars::$USER_RIGHTS >= 7) {
    if (Vars::$ID && mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_news` WHERE `id` = " . Vars::$ID), 0)) {
        if (!Vars::$ID) {
            echo Functions::displayError(__('error_wrong_data'), '<a href="' . $url . '">' . __('to_news') . '</a>');
            exit;
        }
        if (isset($_POST['submit'])
            && isset($_POST['form_token'])
            && isset($_SESSION['form_token'])
            && $_POST['form_token'] == $_SESSION['form_token']
        ) {
            $error = array();
            if (empty($_POST['name']))
                $error[] = __('error_title');
            if (empty($_POST['text']))
                $error[] = __('error_text');
            $name = Validate::checkout($_POST['name']);
            $text = mysql_real_escape_string(trim($_POST['text']));
            if (!$error) {
                mysql_query("UPDATE `cms_news` SET
                        `name` = '" . mysql_real_escape_string($name) . "',
                        `text` = '$text'
                        WHERE `id` = " . Vars::$ID
                );
            } else {
                echo Functions::displayError($error, '<a href="' . $url . '?act=edit&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
            }
            $tpl->continue = $url;
            $tpl->message = __('article_changed');
            $tpl->contents = $tpl->includeTpl('message', 1);
            exit;
        } else {
            // Выводим форму добавления новости
            $req = mysql_query("SELECT * FROM `cms_news` WHERE `id` = " . Vars::$ID);
            $res = mysql_fetch_assoc($req);
            $tpl->title = Validate::checkout($res['name']);
            $tpl->text = Validate::checkout($res['text']);
            $tpl->form_token = mt_rand(100, 10000);
            $_SESSION['form_token'] = $tpl->form_token;
            $tpl->contents = $tpl->includeTpl('news_edit');
        }
    } else {
        echo Functions::displayError(__('error_wrong_data'));
    }
} else {
    $tpl->back = $url;
    $tpl->message = __('access_forbidden');
    $tpl->contents = $tpl->includeTpl('message', 1);
}