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
define('CMS_VERSION', 'JohnCMS 5.0.0');

/*
-----------------------------------------------------------------
Задаем базовые параметры PHP
-----------------------------------------------------------------
*/
//Error_Reporting(E_ALL & ~E_NOTICE);
Error_Reporting(E_ALL | E_STRICT);
@ini_set('session.use_trans_sid', '0');
@ini_set('arg_separator.output', '&amp;');
@ini_set('php_flag display_errors', '1'); //TODO: Вернуть значение к 0
@ini_set('register_globals', '0');
@ini_set('magic_quotes_gpc', '0');
@ini_set('magic_quotes_runtime', '0');
@ini_set('allow_url_fopen', '0');
date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

/*
-----------------------------------------------------------------
Задаем пути
-----------------------------------------------------------------
*/
define('SYSPATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR); // Системная папка
define('ROOTPATH', dirname(SYSPATH) . DIRECTORY_SEPARATOR); // Корневая папка
define('CACHEPATH', SYSPATH . 'cache' . DIRECTORY_SEPARATOR); // Папка для кэша
define('LNGPATH', SYSPATH . 'languages' . DIRECTORY_SEPARATOR); // Папка с языками
define('CONFIGPATH', SYSPATH . 'config' . DIRECTORY_SEPARATOR); // Папка с конфигурационными файлами

define('FILEPATH', ROOTPATH . 'files' . DIRECTORY_SEPARATOR); // Папка с пользовательскими файлами
define('MODPATH', ROOTPATH . 'modules' . DIRECTORY_SEPARATOR); // Папка с модулями
define('TPLPATH', ROOTPATH . 'templates' . DIRECTORY_SEPARATOR); // Папка с шаблонами

/*
-----------------------------------------------------------------
Автозагрузка Классов
-----------------------------------------------------------------
*/
spl_autoload_register('autoload');
function autoload($name)
{
    $name = strtolower($name);

    // Список системных классов
    $system = array(
        'advt'         => 'classes/advt.php',
        'captcha'      => 'classes/captcha.php',
        'comments'     => 'classes/comments.php',
        'counters'     => 'classes/counters.php',
        'finfo'        => 'lib/class.upload.php',
        'functions'    => 'classes/functions.php',
        'homepage'     => 'classes/homepage.php',
        'login'        => 'classes/login.php',
        'network'      => 'classes/network.php',
        'session'      => 'classes/session.php',
        'sitemap'      => 'classes/sitemap.php',
        'system'       => 'classes/system.php',
        'template'     => 'classes/template.php',
        'textparser'   => 'classes/textparser.php',
        'upload'       => 'lib/class.upload.php',
        'validate'     => 'classes/validate.php',
        'vars'         => 'classes/vars.php'
    );

    if (isset($system[$name])) {
        require_once(SYSPATH . $system[$name]);
    } elseif (is_file(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_classes' . DIRECTORY_SEPARATOR . $name . '.php')) {
        include_once(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_classes' . DIRECTORY_SEPARATOR . $name . '.php');
    } else {
        exit('ERROR: class <b><i>' . $name . '</i></b> not found');
    }
}

/*
-----------------------------------------------------------------
Инициализируем Ядро системы
-----------------------------------------------------------------
*/
$network = new Network;

require_once(CONFIGPATH . 'config.php');
$db_host = isset($db_host) ? $db_host : 'localhost';
$db_user = isset($db_user) ? $db_user : 'root';
$db_pass = isset($db_pass) ? $db_pass : '';
$db_name = isset($db_name) ? $db_name : 'johncms';
$connect = @mysql_connect($db_host, $db_user, $db_pass) or die('Error: cannot connect to database server');
@mysql_select_db($db_name) or die('Error: specified database does not exist');
@mysql_query("SET NAMES 'utf8'", $connect);

$session = new Session;
$system = new System;
unset($network, $system);

/*
-----------------------------------------------------------------
Буферизация вывода, инициализация шаблонов
-----------------------------------------------------------------
*/
ob_start();
register_shutdown_function(create_function('', 'echo Template::getInstance()->loadTemplate();'));
register_shutdown_function('session_write_close');