<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Router extends Vars
{
    public static $ROUTE;
    private $module;
    private $uri = ''; //TODO: Удалить

    /**
     * Обрабатываем основную последовательность маршрутизации
     */
    public function __construct()
    {
        $this->_getRoute();
        if (isset(self::$ROUTE[0]) && !empty(self::$ROUTE[0])) {
            $this->_getModule(self::$ROUTE[0]);
        } else {
            $this->_getModule('homepage');
        }

        parent::$MODULE = $this->module['module'];
        parent::$MODULE_PATH = $this->module['path'];
        parent::$MODULE_URI = parent::$HOME_URL . '/' . $this->module['module'];
        parent::$URI = parent::$HOME_URL . '/' . $this->module['module'] . $this->uri; //TODO: Удалить
        parent::$PLACE = $this->_setPlace();
        $this->_include();
    }

    /**
     * Получаем сформированный для модуля URL заданного уровня
     *
     * @param int $level
     * @return string
     */
    public static function getUrl($level = 1)
    {
        $uri = '';
        $level = $level > 0 ? $level - 2 : -1;
        for ($i = 0; $i <= $level; $i++) {
            if (!isset(self::$ROUTE[$i])) {
                break;
            }
            $uri .= '/' . self::$ROUTE[$i];
        }
        return htmlspecialchars(Vars::$HOME_URL . $uri);
    }

    /**
     * Подключаем выбранный модуль
     */
    private function _include()
    {
        if (isset(self::$ROUTE[1]) && is_file(MODPATH . $this->module['path'] . DIRECTORY_SEPARATOR . self::$ROUTE[1] . '.php')) {
            include(MODPATH . $this->module['path'] . DIRECTORY_SEPARATOR . self::$ROUTE[1] . '.php');
        } elseif (is_file(MODPATH . $this->module['path'] . DIRECTORY_SEPARATOR . 'index.php')) {
            include(MODPATH . $this->module['path'] . DIRECTORY_SEPARATOR . 'index.php');
        } else {
            echo'File "index.php" not found';
        }
    }

    /**
     * Обрабатываем маршрут
     */
    private function _getRoute()
    {
        if (isset($_GET['route'])
            && strlen($_GET['route']) > 2
            && strlen($_GET['route']) < 30
            && !preg_match('/[^\da-z\/\_]+/i', $_GET['route'])
        ) {
            $route = explode('/', trim($_GET['route']));
            foreach ($route as $key => $val) {
                self::$ROUTE[$key] = trim($val);
            }
            unset($route);
        }
    }

    /**
     * Запрашиваем нужный модуль.
     * Если модуля нет, возвращаем страницу 404
     *
     * @param string $arg
     */
    private function _getModule($arg)
    {
        if (!$this->_query($arg)) {
            if (!$this->_query('404')) {
                echo'<pre>404: page not found</pre>';
                exit;
            }
        }
    }

    /**
     * Формируем строку для указания местоположения на сайте
     *
     * @return string
     */
    private function _setPlace()
    {
        $param = array();
        if (!empty(parent::$ACT)) {
            $param[] = 'act=' . parent::$ACT;
        }
        if (!empty(parent::$MOD)) {
            $param[] = 'mod=' . parent::$MOD;
        }
        if (parent::$ID) {
            $param[] = 'id=' . parent::$ID;
        }
        return implode('/', self::$ROUTE) . (!empty($param) ? '?' . implode('&', $param) : '');
    }

    /**
     * Запрос модуля в базе данных
     *
     * @param string $arg
     * @return bool
     */
    private function _query($arg)
    {
        $req = mysql_query("SELECT * FROM `cms_modules`
        WHERE `module` = '" . mysql_real_escape_string($arg) . "'");
        if (mysql_num_rows($req)) {
            $this->module = mysql_fetch_assoc($req);
            return TRUE;
        }

        return FALSE;
    }
}
