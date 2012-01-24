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
    private $data = array();
    private $templates = array();
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
        if (!array_key_exists($name, $this->data)) {
            $this->data[$name] = $value;
        }
    }

    public function load($name)
    {
        if (!in_array($name, $this->templates)) {
            $this->templates[] = $name;
        }
    }

    public function display()
    {
        $contents = ob_get_contents();
        ob_end_clean();

        if (parent::$SYSTEM_SET['gzip'] && @extension_loaded('zlib')) {
            // Буферизация вывода со сжатием
            @ini_set('zlib.output_compression_level', 3);
            ob_start('ob_gzhandler');
        }

        if (!empty($this->data)) {
            // Импортируем переданные переменные
            extract($this->data);
        }

        if (empty($this->templates)) {
            // Если шаблон не был задан, подключаем по-умолчанию
            $this->templates[] = '_header';
            $this->templates[] = '_default';
            $this->templates[] = '_footer';
        }

        foreach ($this->templates as $template) {
            // Загружаем шаблоны
            $system_template = SYSPATH . 'templates' . DIRECTORY_SEPARATOR . $template . '.php';
            $user_template = ROOTPATH . 'theme' . DIRECTORY_SEPARATOR . parent::$USER_SET['skin'] . DIRECTORY_SEPARATOR . $template . '.php';
            if (is_file($user_template)) {
                include_once($user_template);
            } elseif (is_file($system_template)) {
                include_once($system_template);
            } else {
                echo Functions::displayError('Template &laquo;<b>' . $template . '</b>&raquo; not found');
            }
        }
    }
}
