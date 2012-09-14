<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

@ini_set("max_execution_time", "600");
defined('_IN_JOHNCMS') or die('Error: restricted access');

//TODO: Распределить права доступа!!!

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 1) {
    echo Functions::displayError(lng('access_forbidden'));
    exit;
}

$tpl = Template::getInstance();

/*
-----------------------------------------------------------------
Главное меню Админ панели
-----------------------------------------------------------------
*/
unset($_SESSION['form_token']);
$tpl->usrTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `level` > 0"), 0);
$tpl->regTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `level` = '0'"), 0);
//$tpl->banTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time` > '" . time() . "'"), 0);

$tpl->contents = $tpl->includeTpl('index');