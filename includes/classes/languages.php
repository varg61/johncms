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

    private $_lng = NULL;
    private $_systemLanguage = NULL;
    private $_moduleLanguage = NULL;
    private $_lngList = NULL;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Инициализация объекта класса Languages
     *
     * @return Languages
     */
    public static function getInstance()
    {
        if (is_null(static::$_instance)) {
            static::$_instance = new Languages;
        }

        return static::$_instance;
    }

    /**
     * Переключатель языков
     *
     * @return bool
     */
    //TODO: Убрать и перенести в модуль переключения языков
    public function lngSwitch()
    {
        $setLng = isset($_POST['setlng']) ? substr(Validate::checkin($_POST['setlng']), 0, 2) : FALSE;
        if ($setLng && in_array($setLng, $this->_lngList)) {
            $_SESSION['lng'] = $setLng;

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Автоматическое определение языка
     *
     * @return string
     */
    private function _lngDetect()
    {
        if (is_null($this->_lng)) {
            $this->_lng = Vars::$SYSTEM_SET['lng'];

            if (Vars::$SYSTEM_SET['lngswitch']) {
                if (isset($_SESSION['lng'])) {
                    $this->_lng = $_SESSION['lng'];
                } else {
                    if (Vars::$USER_ID
                        && isset(Vars::$USER_SET['lng'])
                        && Vars::$USER_SET['lng'] != '#'
                        && in_array(Vars::$USER_SET['lng'], $this->getLngList())
                    ) {
                        $this->_lng = Vars::$USER_SET['lng'];
                    } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                        $accept = explode(',', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE'])));
                        foreach ($accept as $var) {
                            $iso = substr($var, 0, 2);
                            if (in_array($iso, $this->getLngList())) {
                                $this->_lng = $iso;
                                break;
                            }
                        }
                    }
                    $_SESSION['lng'] = $this->_lng;
                }
            }
        }

        return $this->_lng;
    }

    /**
     * Получаем список ISO кодов имеющихся в системе языков
     *
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
     * Получаем список языков вместе с названиями и флагами
     *
     * @return array ISO код => название
     */
    public function getLngDescription()
    {
        $description = array();
        foreach ($this->getLngList() as $iso) {
            $file = SYSPATH . 'languages' . DIRECTORY_SEPARATOR . $iso . '.ini';
            if (is_file($file) && ($desc = parse_ini_file($file)) !== FALSE) {
                $description[$iso] = Functions::loadImage('flag_' . $iso . '.gif') . '&#160; ';
                $description[$iso] .= isset($desc['name']) && !empty($desc['name']) ? $desc['name'] : $iso;
            }
        }

        return $description;
    }

    /**
     * Выдача фразы по заданному ключу
     *
     * @param string $key
     * @param bool   $forceSystem
     *
     * @return string
     */
    public function getPhrase($key, $forceSystem)
    {
        // Получаем фразы модуля
        if (is_null($this->_moduleLanguage) && $this->_moduleLanguage !== FALSE) {
            $this->_moduleLanguage = FALSE;

            $module = array(
                MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_lng' . DIRECTORY_SEPARATOR . $this->_lngDetect() . '.lng',
                MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_lng' . DIRECTORY_SEPARATOR . 'en.lng',
                MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_lng' . DIRECTORY_SEPARATOR . 'ru.lng'
            );

            foreach ($module as $file) {
                if (is_file($file) && ($this->_moduleLanguage = parse_ini_file($file)) !== FALSE) {
                    break;
                }
            }
        }

        // Получаем системные фразы
        if (is_null($this->_systemLanguage)) {
            $system = array(
                SYSPATH . 'languages' . DIRECTORY_SEPARATOR . $this->_lngDetect() . '.lng',
                SYSPATH . 'languages' . DIRECTORY_SEPARATOR . 'en.lng',
                SYSPATH . 'languages' . DIRECTORY_SEPARATOR . 'ru.lng'
            );

            foreach ($system as $file) {
                if (is_file($file) && ($this->_systemLanguage = parse_ini_file($file)) !== FALSE) {
                    break;
                }
            }
        }

        if ($this->_systemLanguage === FALSE) {
            exit('System language error');
        } elseif (!$forceSystem && isset($this->_moduleLanguage[$key])) {
            // Возвращаем фразу модуля
            return $this->_moduleLanguage[$key];
        } elseif (isset($this->_systemLanguage[$key])) {
            // Возвращаем системную фразу
            return $this->_systemLanguage[$key];
        }

        // если фразы не существует, возвращаем ключ
        return '# ' . $key . ' #';
    }
}
