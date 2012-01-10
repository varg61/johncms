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
define('SYSPATH', realpath(__DIR__) . DIRECTORY_SEPARATOR);                    // Системная папка
define('ROOTPATH', dirname(realpath(__DIR__)) . DIRECTORY_SEPARATOR);          // Корневая папка
define('CACHEPATH', SYSPATH . 'cache' . DIRECTORY_SEPARATOR);                  // Папка для кэша
define('LNGPATH', SYSPATH . 'languages' . DIRECTORY_SEPARATOR);                // Папка с языками
define('CONFIGPATH', SYSPATH . 'config' . DIRECTORY_SEPARATOR);                // Папка с конфигурационными файлами

/*
-----------------------------------------------------------------
Автозагрузка Классов
-----------------------------------------------------------------
*/
spl_autoload_register('autoload');
function autoload($name)
{
    $file = SYSPATH . 'classes/' . strtolower($name) . '.php';
    if (file_exists($file)) {
        require_once($file);
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
Получаем и фильтруем основные переменные для системы
-----------------------------------------------------------------
*/
//$user    = isset($_REQUEST['user']) ? abs(intval($_REQUEST['user'])) : false;
//$headmod = isset($headmod) ? $headmod : '';

/*
-----------------------------------------------------------------
Показываем Дайджест
-----------------------------------------------------------------
*/
//if (Vars::$USER_ID && Vars::$USER_DATA['last_visit'] < (time() - 3600) && Vars::$USER_SET['digest'] && $headmod == 'mainpage') {
//    header('Location: ' . Vars::$SYSTEM_SET['homeurl'] . '/index.php?act=digest&last=' . Vars::$USER_DATA['last_visit']);
//}

/*
-----------------------------------------------------------------
Буфферизация вывода
-----------------------------------------------------------------
*/
//if (!isset(Vars::$SYSTEM_SET['gzip'])) {
//    mysql_query("INSERT INTO `cms_settings` SET `key` = 'gzip', `val` = '1'");
//    Vars::$SYSTEM_SET['gzip'] = 1;
//}
//if (Vars::$SYSTEM_SET['gzip'] && @extension_loaded('zlib')) {
//    @ini_set('zlib.output_compression_level', 3);
//    ob_start('ob_gzhandler');
//} else {
//    ob_start();
//}