<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

class login
{
    public static $error = array();
    public $display_mode = '';

    function __construct($var = array())
    {
        if (isset($var['login']) && isset($var['password']) && isset($var['mode'])) {
            $login_query = false;
            if ($var['mode'] == 1 && self::check_login($var['login']) === true) {
                $login_query = "`name` = '" . mysql_real_escape_string($var['login']) . "'";
            } elseif ($var['mode'] == 2 && self::check_id($var['login']) === true) {
                $login_query = "`id` = " . abs(intval($var['login']));
            } elseif ($var['mode'] == 3 && self::check_email($var['login']) === true) {
                $login_query = "`email` = '" . mysql_real_escape_string($var['login']) . "'";
            }

            if (self::check_password($var['password']) === true && $login_query) {
                $req = mysql_query("SELECT * FROM `users` WHERE $login_query LIMIT 1");
                if (mysql_num_rows($req)) {
                    $res = mysql_fetch_assoc($req);
                    //TODO: Написать обработку CAPTCHA
                    //if ($res['failed_login'] > 2) {
                    //    $this->display_mode = 'captcha';
                    //}
                    if (md5(md5($var['password'])) == $res['password']) {
                        if (isset($var['remember'])) {
                            // Установка данных COOKIE
                            setcookie("cuid", base64_encode($res['id']), time() + 3600 * 24 * 365);
                            setcookie("cups", md5($var['password']), time() + 3600 * 24 * 365);
                        }
                        $_SESSION['uid'] = $res['id'];
                        $_SESSION['ups'] = md5(md5($var['password']));
                        $set_user = settings::user_data_get('set_user');
                        $this->display_mode = $res['lastdate'] < (time() - 3600) && $set_user['digest'] ? 'digest' : 'homepage';
                    } else {
                        self::$error['password'] = core::$lng['error_wrong_password'];
                    }
                }
            }
        }
    }

    /*
    -----------------------------------------------------------------
    Обрабатываем CAPTCHA
    -----------------------------------------------------------------
    */
    private function do_captcha()
    {

    }

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода NickName
    -----------------------------------------------------------------
    */
    public static function check_login($var = '')
    {
        if (empty($var)) {
            self::$error['login'] = core::$lng['error_login_empty'];
        } elseif (mb_strlen($var) < 2 || mb_strlen($var) > 20) {
            self::$error['login'] = core::$lng['nick'] . ': ' . core::$lng['error_wrong_lenght'];
        } elseif (preg_match('/[^\da-zа-я\-\@\*\(\)\?\!\~\_\=\[\]]+/iu', $var)) {
            self::$error['login'] = core::$lng['nick'] . ': ' . core::$lng['error_wrong_symbols'];
        } elseif (preg_match('~(([a-z]+)([а-я]+)|([а-я]+)([a-z]+))~iu', $var)) {
            self::$error['login'] = core::$lng['error_double_charset'];
        } else {
            return true;
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода User ID
    -----------------------------------------------------------------
    */
    public static function check_id($var = '')
    {
        if (empty($var)) {
            self::$error['login'] = core::$lng['error_login_empty'];
        } elseif (filter_var($var, FILTER_VALIDATE_INT) == false || $var < 1) {
            self::$error['login'] = 'User ID: ' . core::$lng['error_wrong_data'];
        } else {
            return true;
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода E-mail
    -----------------------------------------------------------------
    */
    public static function check_email($var = '')
    {
        if (empty($var)) {
            self::$error['login'] = core::$lng['error_login_empty'];
        } elseif (filter_var($var, FILTER_VALIDATE_EMAIL) == false) {
            self::$error['login'] = core::$lng['error_email'];
        } else {
            return true;
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода Пароля
    -----------------------------------------------------------------
    */
    public static function check_password($var = '')
    {
        if (empty($var)) {
            self::$error['password'] = core::$lng['error_empty_password'];
        } elseif (preg_match('/[^\da-z]+/i', $var)) {
            self::$error['password'] = core::$lng['error_wrong_symbols'];
        } else {
            return true;
        }
        return false;
    }
}
