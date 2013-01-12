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
    echo '<div class="phdr"><a href="' . $url . '"><b>' . __('site_news') . '</b></a> | ' . __('delete') . '</div>';
    if (isset($_POST['submit'])
        && isset($_POST['form_token'])
        && isset($_SESSION['form_token'])
        && $_POST['form_token'] == $_SESSION['form_token']
    ) {
        mysql_query("DELETE FROM `cms_news` WHERE `id` = " . Vars::$ID);
        $tpl->continue = $url;
        $tpl->message = __('article_deleted');
        $tpl->contents = $tpl->includeTpl('message', 1);
    } else {
        $tpl->id = Vars::$ID;
        $tpl->form_token = mt_rand(100, 10000);
        $_SESSION['form_token'] = $tpl->form_token;
        $tpl->contents = $tpl->includeTpl('news_delete');
    }
} else {
    $tpl->back = $url;
    $tpl->message = __('access_forbidden');
    $tpl->contents = $tpl->includeTpl('message', 1);
}