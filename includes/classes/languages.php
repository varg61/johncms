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

class Languages
{
    private static $instance = NULL;
    private $system_language = NULL;
    private $module_language = NULL;

    /**
     * Инициализация объекта класса Languages
     * @return Languages|null
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Languages;
        }
        return self::$instance;
    }

    /**
     * Выдача фразы системного языка
     * @param string $key    Ключ для фразы
     * @return string        Фраза по ключу
     */
    public function getSystemPhrase($key)
    {
        if (is_null(self::getInstance()->system_language)) {
            self::getInstance()->system_language = self::getInstance()->parseSystemLng();
        }

        if (self::getInstance()->system_language === FALSE) {
            Vars::$SYSTEM_ERRORS['system_language'] = 'System language error';
        } elseif (isset(self::getInstance()->system_language[$key])) {
            return self::getInstance()->system_language[$key];
        }

        return '# ' . $key . ' #';
    }

    /**
     * Выдача фразы модульного языка
     * @param string $key    Ключ для фразы
     * @return string|bool   Фраза по ключу
     */
    public function getModulePhrase($key)
    {
        if (is_null(self::getInstance()->module_language)) {
            self::getInstance()->module_language = self::getInstance()->parseModuleLng();
        }

        if (self::getInstance()->module_language === FALSE) {
            Vars::$SYSTEM_ERRORS['module_language'] = 'Module language error';
        } elseif (isset(self::getInstance()->module_language[$key])) {
            return self::getInstance()->module_language[$key];
        }

        return FALSE;
    }

    /**
     * Парсинг системного языка
     * @return array|bool    Массив с фразами
     */
    private function parseSystemLng()
    {
        $file = SYSPATH . 'languages' . DIRECTORY_SEPARATOR . Vars::$LNG_ISO . '.lng';
        $file_en = SYSPATH . 'languages' . DIRECTORY_SEPARATOR . 'en.lng';
        if (is_file($file)) {
            return parse_ini_file($file);
        } elseif (is_file($file_en)) {
            return parse_ini_file($file_en);
        }

        return FALSE;
    }

    /**
     * Парсинг языка модуля
     * @return array         Массив с фразами
     */
    private function parseModuleLng()
    {
        $file = MODPATH . Vars::$MODULE_PATH . DIRECTORY_SEPARATOR . '_lng' . DIRECTORY_SEPARATOR . Vars::$LNG_ISO . '.lng';
        $file_en = MODPATH . Vars::$MODULE_PATH . DIRECTORY_SEPARATOR . '_lng' . DIRECTORY_SEPARATOR . Vars::$LNG_ISO . 'en.lng';
        $file_ru = MODPATH . Vars::$MODULE_PATH . DIRECTORY_SEPARATOR . '_lng' . DIRECTORY_SEPARATOR . Vars::$LNG_ISO . 'ru.lng';
        if (is_file($file)) {
            return parse_ini_file($file);
        } elseif (is_file($file_en)) {
            return parse_ini_file($file_en);
        } elseif (is_file($file_ru)) {
            return parse_ini_file($file_ru);
        }

        return array();
    }
}
