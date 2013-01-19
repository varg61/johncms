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
     * Фильтрация входящих строчных данных
     *
     * @param $str
     *
     * @return string
     */
    public static function checkin($str)
    {
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

    /**
     * Фильтрация и обработка строк
     *
     * Используется для обработки строк перед выводом в браузер.
     * Преобразует символы в HTML сущности, обрабатывает BBcode и смайлы.
     *
     * @param     $str      Необработанная строка
     * @param int $br       Переносы 0 - не обрабатывать, 1 - обрабатывать, 2 - подставлять пробел
     * @param int $tags     BBcode 0 - не обрабатывать, 1 - обрабатывать, 2 - удалять тэги
     * @param int $smilies  Смайлы 0 - не обрабатывать, 1 - обычные, 2 - обычные и админские
     *
     * @return string       Обработанная строка
     */
    public static function checkout($str, $br = 0, $tags = 0, $smilies = 0)
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
        if ($smilies) {
            $str = Functions::smilies($str, ($smilies == 2 ? 1 : 0));
        }

        return $str;
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
