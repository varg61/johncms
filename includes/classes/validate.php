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
            $error = lng('error_empty_nickname');
        } elseif (mb_strlen($var) < 2 || mb_strlen($var) > 20) {
            $error = lng('error_wrong_lenght');
        } elseif (self::email($var) === true) {
            $error = lng('error_email_login');
        } elseif (preg_match('/[^\da-zа-я\-\.\ \@\*\(\)\?\!\~\_\=\[\]]+/iu', $var)) {
            $error = lng('error_wrong_symbols');
        } elseif (preg_match('~(([a-z]+)([а-я]+)|([а-я]+)([a-z]+))~iu', $var)) {
            $error = lng('error_double_charset');
        } elseif (filter_var($var, FILTER_VALIDATE_INT) !== false && !Vars::$USER_SYS['digits_only']) {
            $error = lng('error_digits_only');
        } elseif (preg_match("/(.)\\1\\1\\1/", $var)) {
            $error = lng('error_recurring_characters');
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
            self::$error['login'] = lng('error_nick_occupied');
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода E-mail
    -----------------------------------------------------------------
    */
    public static function email($var = '', $error_log = false, $allow_empty = false)
    {
        if ($allow_empty && empty($var)) {
            return true;
        }

        if (empty($var)) {
            $error = lng('error_email_empty');
        } elseif (mb_strlen($var) < 5 || mb_strlen($var) > 50) {
            $error = lng('error_wrong_lenght');
        } elseif (filter_var($var, FILTER_VALIDATE_EMAIL) == false) {
            $error = lng('error_email');
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
            self::$error['email'] = lng('error_email_occupied');
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
            $error = lng('error_empty_password');
        } elseif (mb_strlen($var) < 3) {
            $error = lng('error_wrong_lenght');
        } else {
            return true;
        }

        if ($error_log) {
            self::$error['password'] = $error;
        }
        return false;
    }

    /*
    * Фильтрация входящих строчных данных
    */
    public static function checkin($str){
        $str = trim($str);

        if (function_exists('iconv')) {
            $str = iconv('UTF-8', 'UTF-8', $str);
        }

        // Удаляем лишние знаки препинания
        $str = preg_replace('#(\.|\?|!|\(|\)){3,}#', '\1\1\1', $str);

        // Фильтруем символы
        $str = nl2br($str);
        $str = preg_replace('!\p{C}!u', '', $str);
        $str = str_replace('<br />', "\n", $str);

        // Удаляем лишние пробелы
        $str = preg_replace('# {2,}#', ' ', $str);

        // Удаляем более 2-х переносов строк подряд
        $str = preg_replace("/(\n)+(\n)/i", "\n\n", $str);

        return trim($str);
    }

    /*
     * Фильтрация и обработка текстовых данных перед выводом
     *
     * $br = 0          Обработка переносов выключена (по-умолчанию)
     * $br = 1          Обработка переносов строк
     * $br = 2          Подстановка пробела, вместо переноса
     *
     * $tags = 0        Обработка тэгов выключена (по-умолчанию)
     * $tags = 1        Обработка BBcode тэгов
     * $tags = 2        Вырезание BBcode тэгов
     *
     * $smileys = 0     Обработка смайлов выключена (по-умолчанию)
     * $smileys = 1     Обработка пользовательских смайлов
     * $smileys = 2     Обработка пользовательских и админских смайлов
     */
    public static function checkout($str, $br = 0, $tags = 0, $smileys = 0)
    {
        $str = htmlentities(trim($str), ENT_QUOTES, 'UTF-8');

        // Обработка переносов строк
        if ($br == 1) {
            $str = nl2br($str);
        } elseif ($br == 2) {
            $str = str_replace("\r\n", ' ', $str);
        }

        // обработка Bbcode тэгов
        if ($tags == 1) {
            $str = TextParser::tags($str);
        } elseif ($tags == 2) {
            $str = TextParser::noTags($str);
        }

        // Обработка смайлов
        if ($smileys) {
            $str = Functions::smileys($str, ($smileys == 2 ? 1 : 0));
        }

        return $str;
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
