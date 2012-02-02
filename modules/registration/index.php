<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 *
 * Главное меню сайта
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

/*
-----------------------------------------------------------------
Регистрация новых пользователей
-----------------------------------------------------------------
*/
$reg_data['login'] = isset($_POST['login']) ? trim($_POST['login']) : '';
$reg_data['password'] = isset($_POST['password']) ? trim($_POST['password']) : '';
$reg_data['password_confirm'] = isset($_POST['password_confirm']) ? trim($_POST['password_confirm']) : '';
$reg_data['captcha'] = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
$reg_data['email'] = isset($_POST['email']) ? trim($_POST['email']) : '';
$reg_data['about'] = isset($_POST['about']) ? trim($_POST['about']) : '';
$reg_data['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
$reg_data['sex'] = isset($_POST['sex']) ? intval($_POST['sex']) : 0;

$tpl = Template::getInstance();
$tpl->login = new Login;
$tpl->reg_data = $reg_data;
$tpl->lng_reg = Vars::loadLanguage(Vars::$MODULE_PATH);
$tpl->mode = $tpl->login->userRegistration($reg_data);
$tpl->contents = $tpl->includeTpl('registration');