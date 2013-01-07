<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Template extends ArrayObject
{
    public $template = 'template';
    private static $instance = null;

    public function __construct($array = array(), $flags = parent::ARRAY_AS_PROPS)
    {
        parent::__construct($array, $flags);
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Template;
        }
        return self::$instance;
    }

    public function __set($index, $value)
    {
        self::getInstance()->offsetSet($index, $value);
    }

    public function __get($index)
    {
        $instance = self::getInstance();
        if (!$instance->offsetExists($index)) {
            return false;
        }
        return $instance->offsetGet($index);
    }

    public function __isset($index)
    {
        return self::getInstance()->offsetExists($index);
    }

    public function includeTpl($tpl = null, $root = false)
    {
        // Загружаем шаблоны
        if ($root) {
            // Загружаем общие шаблоны
            $default_tpl = TPLDEFAULT . 'modules' . DIRECTORY_SEPARATOR . $tpl . '.php';
            $user_tpl = TPLPATH . Vars::$USER_SET['skin'] . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $tpl . '.php';
        } else {
            // Загружаем шаблоны модулей
            $default_tpl = MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_tpl' . DIRECTORY_SEPARATOR . $tpl . '.php';
            $user_tpl = TPLPATH . Vars::$USER_SET['skin'] . DIRECTORY_SEPARATOR . Router::$PATH . DIRECTORY_SEPARATOR . $tpl . '.php';
        }

        ob_start();
        if (is_file($user_tpl)) {
            include_once($user_tpl);
        } elseif (is_file($default_tpl)) {
            include_once($default_tpl);
        } else {
            echo Functions::displayError('ERROR: template &laquo;' . $tpl . '&raquo; not found');
        }
        $contents = ob_get_contents();
        ob_end_clean();
        return trim($contents);
    }

    public function loadTemplate()
    {
        $instance = self::getInstance();
        if ($instance->template === false) {
            return false;
        }
        if (!$instance->offsetExists('contents')) {
            // Получаем содержимое вывода старых модулей
            $instance->offsetSet('contents', ob_get_contents());
        }
        ob_end_clean();

        if (@extension_loaded('zlib')) {
            // Буферизация вывода со сжатием
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }

        // Подключаем файл главного шаблона
        if (is_file(TPLPATH . Vars::$USER_SET['skin'] . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $instance->template . '.php')) {
            // Шаблон темы оформления
            include(TPLPATH . Vars::$USER_SET['skin'] . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $instance->template . '.php');
        } elseif (is_file(TPLDEFAULT . 'modules' . DIRECTORY_SEPARATOR . $instance->template . '.php')) {
            // Шаблон по-умолчанию
            include(TPLDEFAULT . 'modules' . DIRECTORY_SEPARATOR . $instance->template . '.php');
        } else {
            // Если шаблона нет, выводим сообщение об ошибке
            echo'ERROR: root template &laquo;' . htmlspecialchars($instance->template) . '&raquo; not found';
            return false;
        }
        return true;
    }

    public function loadCSS()
    {
        //TODO: Доработать функцию
        $out = '<meta name="Generator" content="JohnCMS, http://johncms.com"/>' . "\n";
        $out .= '    <link href="' . Vars::$HOME_URL . '/assets/template/css/style.css" rel="stylesheet" media="screen, handheld" type="text/css"/>' . "\n";
        return $out;
    }
}
