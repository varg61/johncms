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

// Проверяем версию PHP
if (version_compare(phpversion(), '5.3.0', '<') == TRUE) die ('ERROR: PHP5.3 > Only');

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
define('SYSPATH', realpath(__DIR__) . DIRECTORY_SEPARATOR);                    // Системная папка
define('ROOTPATH', dirname(SYSPATH) . DIRECTORY_SEPARATOR);                    // Корневая папка
define('CACHEPATH', SYSPATH . 'cache' . DIRECTORY_SEPARATOR);                  // Папка для кэша
define('CONFIGPATH', SYSPATH . 'config' . DIRECTORY_SEPARATOR);                // Папка с конфигурационными файлами

define('FILEPATH', ROOTPATH . 'files' . DIRECTORY_SEPARATOR);                  // Папка с пользовательскими файлами
define('MODPATH', ROOTPATH . 'modules' . DIRECTORY_SEPARATOR);                 // Папка с модулями
define('TPLPATH', ROOTPATH . 'templates' . DIRECTORY_SEPARATOR);               // Папка с шаблонами

/*
-----------------------------------------------------------------
Автозагрузка Классов
-----------------------------------------------------------------
*/
spl_autoload_register(
    function($name)
    {
        $name = strtolower($name);

        // Список системных классов
        $system = array(
            'advt'       => 'classes' . DIRECTORY_SEPARATOR . 'advt.php',
            'captcha'    => 'classes' . DIRECTORY_SEPARATOR . 'captcha.php',
            'comments'   => 'classes' . DIRECTORY_SEPARATOR . 'comments.php',
            'counters'   => 'classes' . DIRECTORY_SEPARATOR . 'counters.php',
            'finfo'      => 'lib'     . DIRECTORY_SEPARATOR . 'class.upload.php',
            'functions'  => 'classes' . DIRECTORY_SEPARATOR . 'functions.php',
            'homepage'   => 'classes' . DIRECTORY_SEPARATOR . 'homepage.php',
            'login'      => 'classes' . DIRECTORY_SEPARATOR . 'login.php',
            'network'    => 'classes' . DIRECTORY_SEPARATOR . 'network.php',
            'pclzip'     => 'lib'     . DIRECTORY_SEPARATOR . 'pclzip.lib.php',
            'session'    => 'classes' . DIRECTORY_SEPARATOR . 'session.php',
            'sitemap'    => 'classes' . DIRECTORY_SEPARATOR . 'sitemap.php',
            'system'     => 'classes' . DIRECTORY_SEPARATOR . 'system.php',
            'template'   => 'classes' . DIRECTORY_SEPARATOR . 'template.php',
            'textparser' => 'classes' . DIRECTORY_SEPARATOR . 'textparser.php',
            'upload'     => 'lib'     . DIRECTORY_SEPARATOR . 'class.upload.php',
            'validate'   => 'classes' . DIRECTORY_SEPARATOR . 'validate.php',
            'vars'       => 'classes' . DIRECTORY_SEPARATOR . 'vars.php',
            'download'   => 'classes' . DIRECTORY_SEPARATOR . 'download.php'
        );

        if (isset($system[$name])) {
            require_once(SYSPATH . $system[$name]);
        } elseif (is_file(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_classes' . DIRECTORY_SEPARATOR . $name . '.php')) {
            include_once(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_classes' . DIRECTORY_SEPARATOR . $name . '.php');
        } else {
            exit('ERROR: class <b><i>' . $name . '</i></b> not found');
        }
    }
);

/*
-----------------------------------------------------------------
Инициализируем Ядро системы
-----------------------------------------------------------------
*/
new Network;

require_once(CONFIGPATH . 'config.php');
$db_host = isset($db_host) ? $db_host : 'localhost';
$db_user = isset($db_user) ? $db_user : 'root';
$db_pass = isset($db_pass) ? $db_pass : '';
$db_name = isset($db_name) ? $db_name : 'johncms';
$connect = @mysql_connect($db_host, $db_user, $db_pass) or die('Error: cannot connect to database server');
@mysql_select_db($db_name) or die('Error: specified database does not exist');
@mysql_query("SET NAMES 'utf8'", $connect);

new Session;
new System;

/*
-----------------------------------------------------------------
Загрузка языков
-----------------------------------------------------------------
*/
function lng($key, $force_system = FALSE)
{
    static $system_lng = array();
    static $module_lng = array();
    static $edited_lng = array();

    $system_file = SYSPATH . 'languages' . DIRECTORY_SEPARATOR . Vars::$LNG_ISO . '.lng';
    $module_file = MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_lng' . DIRECTORY_SEPARATOR . Vars::$LNG_ISO . '.lng';

    if (empty($module_lng) && is_file($module_file)) {
        $module_lng = parse_ini_file($module_file);
    }

    if (!$force_system && isset($module_lng[$key])) {
        return $module_lng[$key];
    }

    if (empty($system_lng) && is_file($system_file)) {
        $system_lng = parse_ini_file($system_file);
    }

    if (isset($system_lng[$key])) {
        return $system_lng[$key];
    }

    return '# ' . $key . ' #';
}

/*
-----------------------------------------------------------------
Буферизация вывода, инициализация шаблонов, закрытие сессии
-----------------------------------------------------------------
*/
ob_start();
register_shutdown_function(function(){echo Template::getInstance()->loadTemplate();});
register_shutdown_function('session_write_close');
