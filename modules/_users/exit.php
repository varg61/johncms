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

$tpl = Template::getInstance();

if (isset($_POST['submit'])
    && isset($_POST['form_token'])
    && isset($_SESSION['form_token'])
    && $_POST['form_token'] == $_SESSION['form_token']
) {
    Vars::userUnset(isset($_POST['clear']));
    header('Location: ' . Vars::$HOME_URL);
    exit;
}

$tpl->token = mt_rand(100, 10000);
$_SESSION['form_token'] = $tpl->token;
$tpl->backlink = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : Vars::$HOME_URL;
$tpl->contents = $tpl->includeTpl('exit');