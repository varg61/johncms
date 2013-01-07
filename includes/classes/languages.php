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
    private static $_instance = NULL;

    private $_systemLanguage = NULL;
    private $_moduleLanguage = NULL;
    private $_lngDescription = NULL;
    private $_lngList = NULL;

    /**
     * Инициализация объекта класса Languages
     * @return Languages|null
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Languages;
        }
        return self::$_instance;
    }

    /**
     * Переключатель языков
     * @return bool
     */
    public function lngSwitch()
    {
        $setLng = isset($_POST['setlng']) ? substr(Validate::checkin($_POST['setlng']), 0, 2) : FALSE;
        if ($setLng && in_array($setLng, $this->_lngList)) {
            $_SESSION['lng'] = $setLng;
            return TRUE;
        }

        return FALSE;
    }

    public function lngDetect()
    {
        //TODO: Доработать
        if (isset($_SESSION['lng'])) {
            parent::$LNG_ISO = $_SESSION['lng'];
        } elseif (parent::$USER_ID && isset(parent::$USER_SET['lng']) && array_key_exists(parent::$USER_SET['lng'], parent::$LNG_LIST)) {
            parent::$LNG_ISO = parent::$USER_SET['lng'];
        } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $accept = explode(',', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE'])));
            foreach ($accept as $var) {
                $lng = substr($var, 0, 2);
                if (in_array($lng, Languages::getInstance()->getLngList())) {
                    parent::$LNG_ISO = $lng;
                    break;
                }
            }
        }
    }

    /**
     * Получаем список ISO кодов имеющихся в системе языков
     * @return array Список ISO кодов языков
     */
    public function getLngList()
    {
        if (is_null($this->_lngList)) {
            foreach (glob(SYSPATH . 'languages' . DIRECTORY_SEPARATOR . '*.lng') as $val) {
                $this->_lngList[] = basename($val, '.lng');
            }
        }

        return $this->_lngList;
    }

    /**
     * Получаем список языков вместе с названиями
     * @return array ISO код => название
     */
    public function getLngDescription()
    {
        if (is_null($this->_lngDescription)) {
            foreach ($this->getLngList() as $iso) {
                $file = SYSPATH . 'languages' . DIRECTORY_SEPARATOR . $iso . '.ini';
                if (is_file($file) && ($desc = parse_ini_file($file)) !== FALSE) {
                    $this->_lngDescription[$iso] = isset($desc['name']) && !empty($desc['name']) ? $desc['name'] : $iso;
                }
            }
        }

        return $this->_lngDescription;
    }

    /**
     * Выдача фразы системного языка
     * @param string $key Ключ для фразы
     * @return string Фраза по ключу
     */
    public function getSystemPhrase($key)
    {
        if (is_null($this->_systemLanguage)) {
            $this->_systemLanguage = $this->_parseSystemLng();
        }

        if ($this->_systemLanguage === FALSE) {
            Vars::$SYSTEM_ERRORS['system_language'] = 'System language error';
        } elseif (isset($this->_systemLanguage[$key])) {
            return $this->_systemLanguage[$key];
        }

        return '# ' . $key . ' #';
    }

    /**
     * Выдача фразы модульного языка
     * @param string $key Ключ для фразы
     * @return string|bool Фраза по ключу
     */
    public function getModulePhrase($key)
    {
        if (is_null($this->_moduleLanguage)) {
            $this->_moduleLanguage = $this->_parseModuleLng();
        }

        if ($this->_moduleLanguage === FALSE) {
            Vars::$SYSTEM_ERRORS['module_language'] = 'Module language error';
        } elseif (isset($this->_moduleLanguage[$key])) {
            return $this->_moduleLanguage[$key];
        }

        return FALSE;
    }

    /**
     * Парсинг системного языка
     * @return array|bool Массив с фразами
     */
    private function _parseSystemLng()
    {
        $file = SYSPATH . 'languages' . DIRECTORY_SEPARATOR . Vars::$LNG_ISO . '.lng';
        $fileEN = SYSPATH . 'languages' . DIRECTORY_SEPARATOR . 'en.lng';
        if (is_file($file)) {
            return parse_ini_file($file);
        } elseif (is_file($fileEN)) {
            return parse_ini_file($fileEN);
        }

        return FALSE;
    }

    /**
     * Парсинг языка модуля
     * @return array Массив с фразами
     */
    private function _parseModuleLng()
    {
        $file = MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_lng' . DIRECTORY_SEPARATOR . Vars::$LNG_ISO . '.lng';
        $fileEN = MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_lng' . DIRECTORY_SEPARATOR . Vars::$LNG_ISO . 'en.lng';
        $fileRU = MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_lng' . DIRECTORY_SEPARATOR . Vars::$LNG_ISO . 'ru.lng';
        if (is_file($file)) {
            return parse_ini_file($file);
        } elseif (is_file($fileEN)) {
            return parse_ini_file($fileEN);
        } elseif (is_file($fileRU)) {
            return parse_ini_file($fileRU);
        }

        return array();
    }
}
