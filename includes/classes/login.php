<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Login extends Vars
{
    public $error = array();

    /*
    -----------------------------------------------------------------
    Уничтожаем данные авторизации юзера
    -----------------------------------------------------------------
    */
    public static function userUnset($clear_token = false)
    {
        if (parent::$USER_ID && $clear_token) {
            mysql_query("UPDATE `users` SET `token` = '' WHERE `id` = " . parent::$USER_ID);
        }
        parent::$USER_ID = false;
        parent::$USER_RIGHTS = 0;
        parent::$USER_DATA = array();
        setcookie('uid', '', time() - 3600, '/');
        setcookie('token', '', time() - 3600, '/');
        session_destroy();
    }
}
