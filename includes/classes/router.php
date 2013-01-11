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
    public static $ROUTE = array();
    public static $PATH;

    /**
     * Обрабатываем основную последовательность маршрутизации
     */
    public function __construct()
    {
        $this->_getRoute();
        if (isset(static::$ROUTE[0]) && !empty(static::$ROUTE[0])) {
            $this->_getModule(static::$ROUTE[0]);
        } else {
            $this->_getModule('homepage');
        }

        static::$PLACE = $this->_setPlace();
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
            if (!isset(static::$ROUTE[$i])) {
                break;
            }
            $uri .= static::$ROUTE[$i] . '/';
        }
        return htmlspecialchars(Vars::$HOME_URL . $uri);
    }

    /**
     * Подключаем выбранный модуль
     */
    private function _include()
    {
        if (isset(static::$ROUTE[1]) && is_file(MODPATH . static::$PATH . DIRECTORY_SEPARATOR . static::$ROUTE[1] . '.php')) {
            include(MODPATH . static::$PATH . DIRECTORY_SEPARATOR . static::$ROUTE[1] . '.php');
        } elseif (is_file(MODPATH . static::$PATH . DIRECTORY_SEPARATOR . 'index.php')) {
            include(MODPATH . static::$PATH . DIRECTORY_SEPARATOR . 'index.php');
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
        ) {
            if (preg_match('/[^\da-z\/\_\.]+/i', $_GET['route'])) {
                static::$ROUTE[0] = '404';
            } else {
                $route = explode('/', trim($_GET['route']));
                foreach ($route as $key => $val) {
                    if (empty($val)) {
                        break;
                    }
                    static::$ROUTE[$key] = trim($val);
                }
                unset($route);
            }
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
        if (!empty(static::$ACT)) {
            $param[] = 'act=' . static::$ACT;
        }
        if (!empty(static::$MOD)) {
            $param[] = 'mod=' . static::$MOD;
        }
        if (static::$ID) {
            $param[] = 'id=' . static::$ID;
        }
        return implode('/', static::$ROUTE) . (!empty($param) ? '?' . implode('&', $param) : '');
    }

    /**
     * Запрос модуля в базе данных
     *
     * @param string $arg
     * @return bool
     */
    private function _query($arg)
    {
        $STH = DB::PDO()->prepare('
            SELECT * FROM `cms_modules`
            WHERE `module` = :mod
        ');

        $STH->bindParam(':mod', $arg, PDO::PARAM_STR);
        $STH->execute();

        if ($STH->rowCount()) {
            $result = $STH->fetch();
            static::$PATH = $result['path'];
            return TRUE;
        }

        return FALSE;
    }
}
