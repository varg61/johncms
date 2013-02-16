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
    private static $_userData = NULL;

    public $error = array();
    public $is = FALSE;

    public function __construct($type, $value, array $option = array())
    {
        if (method_exists($this, $type)) {
            $option['value'] = $value;
            $this->is = call_user_func(array($this, $type), $option);
        } else {
            $this->error[] = 'Unknown Validator';
        }
    }

    public static function getUserData()
    {
        return (is_null(static::$_userData) ? FALSE : static::$_userData);
    }

    /**
     * Проверка Логина по базе пользователей
     *
     * @param array $option
     *
     * @return bool
     */
    private function login(array $option)
    {
        $STH = DB::PDO()->prepare('
            SELECT * FROM `users`
            WHERE `' . ($this->email($option, FALSE) ? 'email' : 'nickname') . '` = ?
            LIMIT 1
        ');

        $STH->execute(array($option['value']));

        if ($STH->rowCount()) {
            static::$_userData = $STH->fetch();
            $STH = NULL;

            return TRUE;
        } else {
            $this->error[] = __('error_user_not_exist');
            $STH = NULL;

            return FALSE;
        }
    }

    /**
     * Проверка пароля
     *
     * @param array $option
     *
     * @return bool
     */
    private function password(array $option)
    {
        if (is_null(static::$_userData) && Vars::$USER_ID) {
            static::$_userData =& Vars::$USER_DATA;
        }

        if (!is_null(static::$_userData)) {
            if (crypt($option['value'], static::$_userData['password']) === static::$_userData['password']) {
                return TRUE;
            } else {
                $this->error[] = __('error_wrong_password');

                // Накручиваем счетчик неудачных логинов
                if (!Vars::$USER_ID && static::$_userData['login_try'] < 3) {
                    DB::PDO()->exec("UPDATE `users` SET `login_try` = " . ++static::$_userData['login_try'] . " WHERE `id` = " . static::$_userData['id']);
                    static::$_userData = NULL;
                }
            }
        }

        return FALSE;
    }

    /**
     * Валидация длины строки
     *
     * @param array $option
     *
     * @return bool
     */
    private function lenght(array $option)
    {
        if (isset($option['min']) && mb_strlen($option['value']) < $option['min']) {
            $this->error[] = __('minimum') . '&#160;' . $option['min'] . ' ' . __('characters');

            return FALSE;
        } elseif (isset($option['max']) && mb_strlen($option['value']) > $option['max']) {
            $this->error[] = __('maximum') . '&#160;' . $option['max'] . ' ' . __('characters');

            return FALSE;
        }

        return TRUE;
    }

    /**
     * Валидация числового значения
     *
     * @param array $option
     *
     * @return bool
     */
    private function numeric(array $option)
    {
        if (isset($option['empty']) && $option['empty'] && empty($option['value'])) {
            return TRUE;
        }

        if (!is_numeric($option['value'])) {
            $this->error[] = __('must_be_a_number');

            return FALSE;
        }

        if (isset($option['min']) && $option['value'] < $option['min']) {
            $this->error[] = __('minimum') . '&#160;' . $option['min'];

            return FALSE;
        } elseif (isset($option['max']) && $option['value'] > $option['max']) {
            $this->error[] = __('maximum') . '&#160;' . $option['max'];

            return FALSE;
        }

        return TRUE;
    }

    /**
     * Валидация E-mail адреса
     *
     * @param array $option
     * @param bool  $log
     *
     * @return bool
     */
    protected function email(array $option, $log = TRUE)
    {
        if (!filter_var($option['value'], FILTER_VALIDATE_EMAIL)) {
            if ($log) {
                $this->error = __('error_email');
            }

            return FALSE;
        }

        return TRUE;
    }

    /**
     * Валидация IPv4 адреса
     *
     * @param array $option
     *
     * @return bool
     */
    protected function ip(array $option)
    {
        if (!filter_var($option['value'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $this->error[] = 'IP указан неверно';

            return FALSE;
        }

        return TRUE;
    }

    /**
     * Валидация Nickname
     *
     * @param array $option
     *
     * @return bool
     */
    private function nickname(array $option)
    {
        if (preg_match('/[^\da-zа-я\-\.\ \@\*\(\)\?\!\~\_\=\[\]]+/iu', $option['value'])) {
            $this->error[] = __('error_wrong_symbols');
        } elseif (preg_match('~(([a-z]+)([а-я]+)|([а-я]+)([a-z]+))~iu', $option['value'])) {
            $this->error[] = __('error_double_charset');
        } elseif (filter_var($option['value'], FILTER_VALIDATE_INT) !== FALSE && !Vars::$USER_SYS['digits_only']) {
            $this->error[] = __('error_digits_only');
        } elseif (preg_match("/(.)\\1\\1\\1/", $option['value'])) {
            $this->error[] = __('error_recurring_characters');
        } else {
            return TRUE;
        }

        return FALSE;
    }

    /*
     * Разобраться и переделать
     */
    protected function nicknameAvailability($var = '', $error_log = FALSE)
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

    /*
    * Разобраться и переделать
    */
    protected function emailAvailability($var, $error_log = FALSE)
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
}
