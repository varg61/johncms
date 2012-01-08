<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Validate
{
    public static $error = array();

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода NickName
    -----------------------------------------------------------------
    */
    public static function nickname($var = '', $error_log = false)
    {
        if (empty($var)) {
            $error = Vars::$LNG['error_login_empty'];
        } elseif (mb_strlen($var) < 2 || mb_strlen($var) > 20) {
            $error = Vars::$LNG['error_wrong_lenght'];
        } elseif (preg_match('/[^\da-zа-я\-\.\ \@\*\(\)\?\!\~\_\=\[\]]+/iu', $var)) {
            $error = Vars::$LNG['error_wrong_symbols'];
        } elseif (preg_match('~(([a-z]+)([а-я]+)|([а-я]+)([a-z]+))~iu', $var)) {
            $error = Vars::$LNG['error_double_charset'];
        } elseif (filter_var($var, FILTER_VALIDATE_INT) !== false) {
            $error = Vars::$LNG['error_digits_only'];
        } elseif (preg_match("/(.)\\1\\1\\1/", $var)) {
            $error = Vars::$LNG['error_recurring_characters'];
        } else {
            return true;
        }

        if ($error_log) {
            self::$error['login'] = $error;
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка занятости Ника
    -----------------------------------------------------------------
    */
    public static function nicknameAvailability($var = '', $error_log = false)
    {
        $sql = self::email($var) === true ? " OR `email` = '" . mysql_real_escape_string($var) . "'" : "";
        $result = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `nickname` = '" . mysql_real_escape_string($var) . "'$sql"), 0);
        if ($result == 0) return true;

        if ($error_log) {
            self::$error['login'] = Vars::$LNG['error_nick_occupied'];
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода E-mail
    -----------------------------------------------------------------
    */
    public static function email($var = '', $error_log = false)
    {
        if (empty($var)) {
            $error = Vars::$LNG['error_email_empty'];
        } elseif (mb_strlen($var) < 5 || mb_strlen($var) > 50) {
            $error = Vars::$LNG['error_wrong_lenght'];
        } elseif (filter_var($var, FILTER_VALIDATE_EMAIL) == false) {
            $error = Vars::$LNG['error_email'];
        } else {
            return true;
        }

        if ($error_log) {
            self::$error['email'] = $error;
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка занятости E-mail
    -----------------------------------------------------------------
    */
    public static function emailAvailability($var = '', $error_log = false)
    {
        $result = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `email` = '" . mysql_real_escape_string($var) . "'"), 0);
        if ($result == 0) {
            return true;
        }

        if ($error_log) {
            self::$error['email'] = Vars::$LNG['error_email_occupied'];
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода Пароля
    -----------------------------------------------------------------
    */
    public static function password($var = '', $error_log = false)
    {
        if (empty($var)) {
            $error = Vars::$LNG['error_empty_password'];
        } elseif (mb_strlen($var) < 3 || mb_strlen($var) > 20) {
            $error = Vars::$LNG['error_wrong_lenght'];
        } elseif (preg_match('/[^\da-z]+/i', $var)) {
            $error = Vars::$LNG['error_wrong_symbols'];
        } else {
            return true;
        }

        if ($error_log) {
            self::$error['password'] = $error;
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Фильтрация и обработка текстовых данных
    -----------------------------------------------------------------
    $br=1           обработка переносов строк
    $br=2           подстановка пробела, вместо переноса
    $tags=1         обработка тэгов
    $tags=2         вырезание тэгов
    -----------------------------------------------------------------
    */
    public static function filterString($str, $br = 0, $tags = 0)
    {
        $str = htmlentities(trim($str), ENT_QUOTES, 'UTF-8');
        if ($br == 1) {
            $str = nl2br($str);
        } elseif ($br == 2) {
            $str = str_replace("\r\n", ' ', $str);
        }

        if ($tags == 1) {
            $str = TextParser::tags($str);
        } elseif ($tags == 2) {
            $str = TextParser::noTags($str);
        }

        $replace = array(
            chr(0) => '',
            chr(1) => '',
            chr(2) => '',
            chr(3) => '',
            chr(4) => '',
            chr(5) => '',
            chr(6) => '',
            chr(7) => '',
            chr(8) => '',
            chr(9) => '',
            chr(11) => '',
            chr(12) => '',
            chr(13) => '',
            chr(14) => '',
            chr(15) => '',
            chr(16) => '',
            chr(17) => '',
            chr(18) => '',
            chr(19) => '',
            chr(20) => '',
            chr(21) => '',
            chr(22) => '',
            chr(23) => '',
            chr(24) => '',
            chr(25) => '',
            chr(26) => '',
            chr(27) => '',
            chr(28) => '',
            chr(29) => '',
            chr(30) => '',
            chr(31) => ''
        );
        return strtr($str, $replace);
    }

    /*
    -----------------------------------------------------------------
    Валидация IP адреса
    -----------------------------------------------------------------
    */
    public static function ip($ip)
    {
        if (preg_match('#^(?:(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$#', $ip)) {
            return true;
        }
        return false;
    }
}
