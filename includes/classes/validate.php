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

    /**
     * Проверка корректности ввода NickName
     *
     * @param string $var
     * @param bool   $error_log
     *
     * @return bool
     */
    public static function nickname($var = '', $error_log = FALSE)
    {
        if (empty($var)) {
            $error = __('error_empty_nickname');
        } elseif (mb_strlen($var) < 2 || mb_strlen($var) > 20) {
            $error = __('error_wrong_lenght');
        } elseif (static::email($var) === TRUE) {
            $error = __('error_email_login');
        } elseif (preg_match('/[^\da-zа-я\-\.\ \@\*\(\)\?\!\~\_\=\[\]]+/iu', $var)) {
            $error = __('error_wrong_symbols');
        } elseif (preg_match('~(([a-z]+)([а-я]+)|([а-я]+)([a-z]+))~iu', $var)) {
            $error = __('error_double_charset');
        } elseif (filter_var($var, FILTER_VALIDATE_INT) !== FALSE && !Vars::$USER_SYS['digits_only']) {
            $error = __('error_digits_only');
        } elseif (preg_match("/(.)\\1\\1\\1/", $var)) {
            $error = __('error_recurring_characters');
        } else {
            return TRUE;
        }

        if ($error_log) {
            static::$error['login'] = $error;
        }

        return FALSE;
    }

    /**
     * Проверка занятости Ника
     *
     * @param string $var
     * @param bool   $error_log
     *
     * @return bool
     */
    public static function nicknameAvailability($var = '', $error_log = FALSE)
    {
        if (!static::email($var)
            || (static::email($var) && static::emailAvailability($var))
        ) {
            $STH = DB::PDO()->prepare('
                SELECT COUNT(*) FROM `users`
                WHERE `nickname` = :nickname
            ');

            $STH->bindValue(':nickname', $var, PDO::PARAM_STR);
            $STH->execute();

            if (!$STH->fetchColumn()) {
                return TRUE;
            }
        }

        if ($error_log) {
            static::$error['login'] = __('error_nick_occupied');
        }

        return FALSE;
    }

    /**
     * Проверка корректности ввода E-mail
     *
     * @param string $var
     * @param bool   $error_log
     * @param bool   $allow_empty
     *
     * @return bool
     */
    public static function email($var = '', $error_log = FALSE, $allow_empty = FALSE)
    {
        if ($allow_empty && empty($var)) {
            return TRUE;
        }

        if (empty($var)) {
            $error = __('error_email_empty');
        } elseif (mb_strlen($var) < 5 || mb_strlen($var) > 50) {
            $error = __('error_wrong_lenght');
        } elseif (filter_var($var, FILTER_VALIDATE_EMAIL) == FALSE) {
            $error = __('error_email');
        } else {
            return TRUE;
        }

        if ($error_log) {
            static::$error['email'] = $error;
        }

        return FALSE;
    }

    /**
     * Проверка занятости E-mail
     *
     * @param      $var
     * @param bool $error_log
     *
     * @return bool
     */
    public static function emailAvailability($var, $error_log = FALSE)
    {
        $STH = DB::PDO()->prepare('
            SELECT COUNT(*) FROM `users`
            WHERE `email` = :email
        ');

        $STH->bindParam(':email', $var, PDO::PARAM_STR);
        $STH->execute();

        if ($STH->fetchColumn()) {
            if ($error_log) {
                static::$error['email'] = __('error_email_occupied');
            }

            return FALSE;
        }

        return TRUE;
    }

    /**
     * Проверка пароля на допустимую длину
     *
     * @param      $var                     Пароль
     * @param bool $error_log               Включить журнал ошибок
     *
     * @return bool                    TRUE, если проверка прошла успешно
     */
    public static function password($var, $error_log = FALSE)
    {
        if (empty($var)) {
            $error = __('error_empty_password');
        } elseif (mb_strlen($var) < 3) {
            $error = __('error_wrong_lenght');
        } else {
            return TRUE;
        }

        if ($error_log) {
            static::$error['password'] = $error;
        }

        return FALSE;
    }

    /**
     * Проверка корректности IP адреса
     *
     * @param string $ip  Строка с IP адресом
     *
     * @return bool       TRUE, если проверка прошла успешно
     */
    public static function ip($ip)
    {
        if (preg_match('#^(?:(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$#', $ip)) {
            return TRUE;
        }

        return FALSE;
    }
}
