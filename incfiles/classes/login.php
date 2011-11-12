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

    /*
    -----------------------------------------------------------------
    Авторизация (LOGIN) пользователя на сайте
    -----------------------------------------------------------------
    */
    public static function do_login($var)
    {
        $display = '';
        $login = false;
        $password = false;
        $sql = false;
        $captcha = false;

        if (isset($var['id']) && isset($var['password'])) {
            // Авторизация по AutoLogin
            $login = self::check_id($var['id'], true);
            $password = self::check_password($var['password'], true);
            $sql = "`id` = " . abs(intval($var['id']));
        } elseif (isset($var['login']) && isset($var['password'])) {
            // Авторизация через форму
            if (($login = self::check_email($var['login'])) !== false) {
                $sql = "`email` = '" . mysql_real_escape_string($var['login']) . "' OR `nickname` = '" . mysql_real_escape_string($var['login']) . "'";
            } else {
                $login = self::check_nickname($var['login'], true);
                $sql = "`nickname` = '" . mysql_real_escape_string($var['login']) . "' LIMIT 1";
            }
            $password = self::check_password($var['password'], true);
        }

        if ($login && $password && $sql && empty(self::$error)) {
            $req = mysql_query("SELECT * FROM `users` WHERE $sql");
            if (mysql_num_rows($req)) {
                while ($res = mysql_fetch_assoc($req)) {
                    self::$error = array();
                    if ($res['login_attempt'] > 2) {
                        // Обрабатываем CAPTCHA
                        if (isset($var['captcha'])) {
                            if (mb_strlen($var['captcha']) > 3 && $var['captcha'] == $_SESSION['captcha']) {
                                $captcha = true;
                            } else {
                                self::$error['captcha'] = core::$lng['error_wrong_captcha'];
                                $display = 'captcha';
                            }
                            unset($_SESSION['captcha']);
                        } else {
                            $display = 'captcha';
                        }
                    }

                    if ($res['login_attempt'] < 3 || $res['login_attempt'] > 2 && $captcha === true) {
                        if ($res['password'] == md5(md5($var['password']))) {
                            // Если пароль совпадает, записываем сессию и COOKIE
                            if (isset($var['remember'])) {
                                setcookie("cuid", base64_encode($res['id']), time() + 3600 * 24 * 365);
                                setcookie("cups", md5($var['password']), time() + 3600 * 24 * 365);
                            }
                            $_SESSION['uid'] = $res['id'];
                            $_SESSION['ups'] = md5(md5($var['password']));
                            $set_user = settings::user_data_get('set_user');
                            $display = $res['lastdate'] < (time() - 3600) && $set_user['digest'] ? 'digest' : 'homepage';
                            if ($res['login_attempt'] > 0) {
                                // Сбрасываем счетчик неудачных Логинов
                                mysql_query("UPDATE `users` SET `login_attempt` = 0 WHERE `id` = " . $res['id']);
                            }
                            break;
                        } else {
                            // Если пароль неверный
                            self::$error = core::$lng['error_wrong_password'];
                            if ($res['login_attempt'] < 3) {
                                // Накручиваем счетчик неудачных Логинов
                                mysql_query("UPDATE `users` SET `login_attempt` = " . ++$res['login_attempt'] . " WHERE `id` = " . $res['id']);
                            }
                        }
                    }
                }
            } else {
                // Если пользователь не найден
                self::$error = core::$lng['error_user_not_exist'];
            }
        }
        return $display;
    }

    /*
    -----------------------------------------------------------------
    Регистрация новых пользователей
    -----------------------------------------------------------------
    */
    public static function registration($var)
    {
        // Если регистрация закрыта
        if (core::$deny_registration || !core::$system_set['mod_reg']) return false;

        if (isset($_POST['check_login']) && self::check_nickname($var['login'], true) === true) {
            // Проверка доступности Ника
            self::check_nick_availability($var['login'], true);
        } elseif (isset($_POST['submit'])) {
            // Проверяем данные
            if (self::check_nickname($var['login'], true) === true) {
                self::check_nick_availability($var['login'], true);
            }
            if (self::check_password($var['password'], true) === true) {
                if ($var['password'] != $var['password_confirm']) self::$error['password_confirm'] = core::$lng['error_passwords_not_match'];
            }
            if (self::check_email($var['email'], true) === true) { //TODO: поставить в зависимость от настроек в админке
                self::check_email_availability($var['email'], true);
            }
            if ($var['sex'] < 1 || $var['sex'] > 2) {
                self::$error['sex'] = core::$lng['error_sex_unknown'];
            }
            if (mb_strlen($var['captcha']) < 3 || $var['captcha'] != $_SESSION['captcha']) {
                self::$error['captcha'] = core::$lng['error_wrong_captcha'];
            }
            unset($_SESSION['captcha']);

            // Регистрируем пользователя
            if (empty(self::$error)) {

            }
        }

        return 'login';
    }

    /*
    -----------------------------------------------------------------
    Проверка занятости Ника
    -----------------------------------------------------------------
    */
    public static function check_nick_availability($var = '', $error_log = false)
    {
        $sql = self::check_email($var) === true ? " OR `email` = '" . mysql_real_escape_string($var['login']) . "'" : "";
        $result = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `nickname` = '" . mysql_real_escape_string($var) . "'$sql"), 0);
        if ($result == 0) return true;
        if ($error_log) self::$error['login'] = core::$lng['error_nick_occupied'];
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка занятости E-mail
    -----------------------------------------------------------------
    */
    public static function check_email_availability($var = '', $error_log = false)
    {
        $result = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `email` = '" . mysql_real_escape_string($var) . "'"), 0);
        if ($result == 0) return true;
        if ($error_log) self::$error['email'] = core::$lng['error_email_occupied'];
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода User ID
    -----------------------------------------------------------------
    */
    public static function check_id($var = '', $error_log = false)
    {
        if (empty($var)) {
            $error = core::$lng['error_login_empty'];
        } elseif (filter_var($var, FILTER_VALIDATE_INT) == false || $var < 1) {
            $error = 'User ID: ' . core::$lng['error_wrong_data'];
        } else {
            return true;
        }
        if ($error_log) self::$error['login'] = $error;
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода NickName
    -----------------------------------------------------------------
    */
    public static function check_nickname($var = '', $error_log = false)
    {
        if (empty($var)) {
            $error = core::$lng['error_login_empty'];
        } elseif (mb_strlen($var) < 2 || mb_strlen($var) > 20) {
            $error = core::$lng['error_wrong_lenght'];
        } elseif (preg_match('/[^\da-zа-я\-\.\ \@\*\(\)\?\!\~\_\=\[\]]+/iu', $var)) {
            $error = core::$lng['error_wrong_symbols'];
        } elseif (preg_match('~(([a-z]+)([а-я]+)|([а-я]+)([a-z]+))~iu', $var)) {
            $error = core::$lng['error_double_charset'];
        } elseif (filter_var($var, FILTER_VALIDATE_INT) !== false) {
            $error = core::$lng['error_digits_only'];
        } elseif (preg_match("/(.)\\1\\1\\1/", $var)) {
            $error = core::$lng['error_recurring_characters'];
        } else {
            return true;
        }
        if ($error_log) self::$error['login'] = $error;
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода E-mail
    -----------------------------------------------------------------
    */
    public static function check_email($var = '', $error_log = false)
    {
        if (empty($var)) {
            $error = core::$lng['error_email_empty'];
        } elseif (mb_strlen($var) < 5 || mb_strlen($var) > 50) {
            $error = core::$lng['error_wrong_lenght'];
        } elseif (filter_var($var, FILTER_VALIDATE_EMAIL) == false) {
            $error = core::$lng['error_email'];
        } else {
            return true;
        }
        if ($error_log) self::$error['email'] = $error;
        return false;
    }

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода Пароля
    -----------------------------------------------------------------
    */
    public static function check_password($var = '', $error_log = false)
    {
        if (empty($var)) {
            $error = core::$lng['error_empty_password'];
        } elseif (mb_strlen($var) < 3 || mb_strlen($var) > 10) {
            $error = core::$lng['error_wrong_lenght'];
        } elseif (preg_match('/[^\da-z]+/i', $var)) {
            $error = core::$lng['error_wrong_symbols'];
        } else {
            return true;
        }
        if ($error_log) self::$error['password'] = $error;
        return false;
    }
}
