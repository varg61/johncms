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
    public $template = '_default';
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
        return $this->vars[$name];
    }

    public function includeTpl($tpl = null)
    {
        if ($this->extract) {
            extract($this->vars);
        }
        ob_start();
        // Загружаем шаблоны
        $system_template = SYSPATH . 'templates' . DIRECTORY_SEPARATOR . $tpl . '.php';
        $user_template = ROOTPATH . 'theme' . DIRECTORY_SEPARATOR . parent::$USER_SET['skin'] . DIRECTORY_SEPARATOR . $tpl . '.php';
        if (is_file($user_template)) {
            include_once($user_template);
        } elseif (is_file($system_template)) {
            include_once($system_template);
        } else {
            echo Functions::displayError('Template &laquo;<b>' . $tpl . '</b>&raquo; not found');
        }
        $contents = ob_get_contents();
        ob_end_clean();
        return trim($contents);
    }

    public function loadTemplate()
    {
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
        return $this->includeTpl($this->template, $this->vars);
    }

    /*
     * Вспомогательные функции, которые можно применять в шаблонах оформления
     */

    public function httpHeaders()
    {
        if (stristr(parent::$USER_AGENT, "msie") && stristr(parent::$USER_AGENT, "windows")) {
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header('Content-type: text/html; charset=UTF-8');
        } else {
            header("Cache-Control: public");
            header('Content-type: application/xhtml+xml; charset=UTF-8');
            echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        }
    }

    public function languageSwitch($show = false)
    {
        if ($show || (parent::$PLACE == 'index.php' && count(parent::$LNG_LIST) > 1)) {
            return '<a href="' . parent::$HOME_URL . '/go.php?lng"><b>' . strtoupper(parent::$LNG_ISO) . '</b></a>' .
                '&#160;<img src="' . parent::$HOME_URL . '/images/flags/' . parent::$LNG_ISO . '.gif" alt=""/>';
        }
        return '';
    }

    public function userMenu($delimiter = ' | ')
    {
        $menu = array();
        if (isset($_GET['err']) || parent::$PLACE != 'index.php' || (parent::$PLACE == 'index.php' && parent::$ACT)) {
            $menu[] = '<a href="' . parent::$SYSTEM_SET['homeurl'] . '">' . parent::$LNG['homepage'] . '</a>';
        }

        if (parent::$USER_ID) {
            $menu[] = '<a href="' . parent::$HOME_URL . '/users/profile.php?act=office">' . parent::$LNG['personal'] . '</a>';
            $menu[] = '<a href="' . parent::$HOME_URL . '/exit.php">' . parent::$LNG['exit'] . '</a>';
        } else {
            $menu[] = '<a href="' . Vars::$HOME_URL . '/login.php">' . Vars::$LNG['login'] . '</a>';
            $menu[] = '<a href="' . Vars::$HOME_URL . '/index.php?act=registration">' . Vars::$LNG['registration'] . '</a>';
        }

        return Functions::displayMenu($menu, $delimiter);
    }

    public function homeLink()
    {
        if (Vars::$PLACE != 'index.php' || (Vars::$PLACE == 'index.php' && Vars::$ACT)) {
            return '<div><a href="' . Vars::$HOME_URL . '">' . Vars::$LNG['homepage'] . '</a></div>';
        }
        return '';
    }

    public function quickGo()
    {
        if (Vars::$USER_SET['quick_go']) {
            return '<form action="' . Vars::$HOME_URL . '/go.php" method="post">' .
                '<div><select name="adres" style="font-size:x-small">' .
                '<option selected="selected">' . Vars::$LNG['quick_jump'] . '</option>' .
                '<option value="guest">' . Vars::$LNG['guestbook'] . '</option>' .
                '<option value="forum">' . Vars::$LNG['forum'] . '</option>' .
                '<option value="news">' . Vars::$LNG['news'] . '</option>' .
                '<option value="gallery">' . Vars::$LNG['gallery'] . '</option>' .
                '<option value="down">' . Vars::$LNG['downloads'] . '</option>' .
                '<option value="lib">' . Vars::$LNG['library'] . '</option>' .
                '<option value="gazen">Gazenwagen :)</option>' .
                '</select><input type="submit" value="Go!" style="font-size:x-small"/>' .
                '</div></form>';
        }
        return '';
    }

    public function userGreeting()
    {
        return Vars::$LNG['hi'] . ', ' .
            (Vars::$USER_ID
                ? '<b>' . Vars::$USER_DATA['nickname'] . '</b>'
                : Vars::$LNG['guest']);
    }
}
