<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Template extends Vars
{
    public $vars = array();
    public $template = 'template';
    public $extract = false;
    private static $instance;

    private function __construct()
    {
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

    public function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function __get($name)
    {
        if (!isset($this->vars[$name])) {
            return false;
        }
        return $this->vars[$name];
    }

    public function __isset($name)
    {
        return isset($this->vars[$name]);
    }

    public function includeTpl($tpl = null, $root = false)
    {
        if ($this->extract) {
            extract($this->vars);
        }

        // Загружаем шаблоны
        if ($root) {
            // Загружаем общие шаблоны
            $default_tpl = TPLDEFAULT . 'modules' . DIRECTORY_SEPARATOR . $tpl . '.php';
            $user_tpl = TPLPATH . parent::$USER_SET['skin'] . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $tpl . '.php';
        } else {
            // Загружаем шаблоны модулей
            $default_tpl = MODPATH . parent::$MODULE_PATH . DIRECTORY_SEPARATOR . '_tpl' . DIRECTORY_SEPARATOR . $tpl . '.php';
            $user_tpl = TPLPATH . parent::$USER_SET['skin'] . DIRECTORY_SEPARATOR . parent::$MODULE_PATH . DIRECTORY_SEPARATOR . $tpl . '.php';
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
        if ($this->template === false) {
            return false;
        }
        if (!isset($this->vars['contents'])) {
            // Получаем содержимое вывода старых модулей
            $this->vars['contents'] = ob_get_contents();
        }
        ob_end_clean();

        if (Vars::$SYSTEM_SET['gzip'] && @extension_loaded('zlib')) {
            // Буферизация вывода со сжатием
            @ini_set('zlib.output_compression_level', 3);
            ob_start('ob_gzhandler');
        }

        // Подключаем файл главного шаблона
        if (is_file(TPLPATH . parent::$USER_SET['skin'] . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $this->template . '.php')) {
            // Шаблон темы оформления
            include(TPLPATH . parent::$USER_SET['skin'] . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $this->template . '.php');
        } elseif (is_file(TPLDEFAULT . 'modules' . DIRECTORY_SEPARATOR . $this->template . '.php')) {
            // Шаблон по-умолчанию
            include(TPLDEFAULT . 'modules' . DIRECTORY_SEPARATOR . $this->template . '.php');
        } else {
            // Если шаблона нет, выводим сообщение об ошибке
            echo'ERROR: root template &laquo;' . htmlspecialchars($this->template) . '&raquo; not found';
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
