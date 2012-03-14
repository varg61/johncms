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

$error_style = 'style="background-color: #FFCCCC"';

$tpl = Template::getInstance();
$tpl->login = new Login;

switch ($tpl->login->userLogin()) {
    case 'homepage':
        /*
        -----------------------------------------------------------------
        Редирект на главную
        -----------------------------------------------------------------
        */
        $tpl = Template::getInstance();
        $tpl->template = false;
        header('Location: ' . Vars::$HOME_URL);
        echo'<div class="gmenu"><p><a href="' . Vars::$HOME_URL . '">' . lng('enter_on_site') . '</a></p></div>';
        break;

    case 'captcha':
        // Показываем CAPTCHA
        $tpl->contents = $tpl->includeTpl('captcha');
        break;

    default:
        // LOGIN форма
        $tpl->contents = $tpl->includeTpl('login');
}