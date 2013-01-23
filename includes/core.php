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
define('START_TIME', microtime(TRUE));
define('START_MEMORY', memory_get_usage());

/**
 * Задаем базовые параметры PHP
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

/**
 * Задаем пути
 */
define('SYSPATH', realpath(__DIR__) . DIRECTORY_SEPARATOR); // Системная папка
define('ROOTPATH', dirname(SYSPATH) . DIRECTORY_SEPARATOR); // Корневая папка
define('CACHEPATH', SYSPATH . 'cache' . DIRECTORY_SEPARATOR); // Папка для кэша
define('CONFIGPATH', SYSPATH . 'config' . DIRECTORY_SEPARATOR); // Папка с конфигурационными файлами

define('FILEPATH', ROOTPATH . 'files' . DIRECTORY_SEPARATOR); // Папка с пользовательскими файлами
define('MODPATH', ROOTPATH . 'modules' . DIRECTORY_SEPARATOR); // Папка с модулями
define('TPLPATH', ROOTPATH . 'templates' . DIRECTORY_SEPARATOR); // Папка с шаблонами
define('TPLDEFAULT', ROOTPATH . 'assets' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR);

/**
 * Автозагрузка Классов
 */
spl_autoload_register(
    function ($name) {
        $name = strtolower($name);
        $system = array(
            'advt'         => 'classes' . DIRECTORY_SEPARATOR . 'advt.php',
            'captcha'      => 'classes' . DIRECTORY_SEPARATOR . 'captcha.php',
            'comments'     => 'classes' . DIRECTORY_SEPARATOR . 'comments.php',
            'counters'     => 'classes' . DIRECTORY_SEPARATOR . 'counters.php',
            'db'           => 'classes' . DIRECTORY_SEPARATOR . 'db.php',
            'fields'       => 'classes' . DIRECTORY_SEPARATOR . 'fields.php',
            'finfo'        => 'lib' . DIRECTORY_SEPARATOR . 'class.upload.php',
            'form'         => 'classes' . DIRECTORY_SEPARATOR . 'form.php',
            'functions'    => 'classes' . DIRECTORY_SEPARATOR . 'functions.php',
            'languages'    => 'classes' . DIRECTORY_SEPARATOR . 'languages.php',
            'login'        => 'classes' . DIRECTORY_SEPARATOR . 'login.php',
            'network'      => 'classes' . DIRECTORY_SEPARATOR . 'network.php',
            'pclzip'       => 'lib' . DIRECTORY_SEPARATOR . 'pclzip.lib.php',
            'router'       => 'classes' . DIRECTORY_SEPARATOR . 'router.php',
            'session'      => 'classes' . DIRECTORY_SEPARATOR . 'session.php',
            'sitemap'      => 'classes' . DIRECTORY_SEPARATOR . 'sitemap.php',
            'system'       => 'classes' . DIRECTORY_SEPARATOR . 'system.php',
            'template'     => 'classes' . DIRECTORY_SEPARATOR . 'template.php',
            'textparser'   => 'classes' . DIRECTORY_SEPARATOR . 'textparser.php',
            'upload'       => 'lib' . DIRECTORY_SEPARATOR . 'class.upload.php',
            'validate'     => 'classes' . DIRECTORY_SEPARATOR . 'validate.php',
            'users'        => 'classes' . DIRECTORY_SEPARATOR . 'users.php',
            'vars'         => 'classes' . DIRECTORY_SEPARATOR . 'vars.php'
        );

        if (isset($system[$name])) {
            require_once(SYSPATH . $system[$name]);
        } elseif (is_file(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_classes' . DIRECTORY_SEPARATOR . $name . '.php')) {
            include_once(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_classes' . DIRECTORY_SEPARATOR . $name . '.php');
        } else {
            exit('ERROR: class "' . $name . '" not found');
        }
    }
);

/**
 * Инициализируем Ядро системы
 */
new Network;
new Session;
new System;

/**
 * Работа с языками
 *
 * @param string $key    Ключ
 * @param bool   $system Принудительно использовать системный язык
 *
 * @return string        Фраза, соответствующая переданному ключу
 */
function __($key, $system = FALSE)
{
    if (!$system && ($out = Languages::getInstance()->getModulePhrase($key)) !== FALSE) {
        return $out;
    } else {
        return Languages::getInstance()->getSystemPhrase($key);
    }
}

/**
 * Буфферизация вывода, инициализация шаблонов, закрытие сессии
 */
ob_start();
register_shutdown_function(function () {
    Template::getInstance()->loadTemplate();
});
register_shutdown_function('session_write_close');
