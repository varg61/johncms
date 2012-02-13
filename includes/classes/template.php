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
    public $template = true;
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
        if(!isset($this->vars[$name])){
            return false;
        }
        return $this->vars[$name];
    }

    public function includeTpl($tpl = null)
    {
        if ($this->extract) {
            extract($this->vars);
        }

        // Загружаем шаблоны
        if($tpl === true){
            $default_tpl = SYSPATH . 'template_default.php';
            $user_tpl = TPLPATH . parent::$USER_SET['skin'] . DIRECTORY_SEPARATOR . 'template_default.php';
        } else {
            $default_tpl = MODPATH . parent::$MODULE . DIRECTORY_SEPARATOR . '_tpl' . DIRECTORY_SEPARATOR . $tpl . '.php';
            $user_tpl = TPLPATH . parent::$USER_SET['skin'] . DIRECTORY_SEPARATOR . parent::$MODULE . DIRECTORY_SEPARATOR . $tpl . '.php';
        }

        ob_start();
        if (is_file($user_tpl)) {
            include_once($user_tpl);
        } elseif (is_file($default_tpl)) {
            include_once($default_tpl);
        } else {
            echo Functions::displayError('Template &laquo;<b>' . $tpl . '</b>&raquo; not found');
        }
        $contents = ob_get_contents();
        ob_end_clean();
        return trim($contents);
    }

    public function loadTemplate()
    {
        if($this->template === false){
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

        // Подключаем файл шаблона оформления
        return $this->includeTpl($this->template);
    }
}
