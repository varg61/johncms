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
            $login = self::check_id($var['id']);
            $password = self::check_password($var['password']);
            $sql = "`id` = " . abs(intval($var['id']));
        } elseif (isset($var['login']) && isset($var['password'])) {
            // Авторизация через форму
            if (($login = self::check_email($var['login'])) !== false) {
                $sql = "`email` = '" . mysql_real_escape_string($var['login']) . "' OR `nickname` = '" . mysql_real_escape_string($var['login']) . "'";
            } else {
                self::$error = array();
                $login = self::check_nickname($var['login']);
                $sql = "`nickname` = '" . mysql_real_escape_string($var['login']) . "' LIMIT 1";
            }
            $password = self::check_password($var['password']);
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
    Проверка корректности ввода NickName
    -----------------------------------------------------------------
    */
    public static function check_nickname($var = '')
    {
        if (empty($var)) {
            self::$error['login'] = core::$lng['error_login_empty'];
        } elseif (mb_strlen($var) < 2 || mb_strlen($var) > 20) {
            self::$error['login'] = core::$lng['nick'] . ': ' . core::$lng['error_wrong_lenght'];
        } elseif (preg_match('/[^\da-zа-я\-\@\*\(\)\?\!\~\_\=\[\]]+/iu', $var)) {
            self::$error['login'] = core::$lng['nick'] . ': ' . core::$lng['error_wrong_symbols'];
        } elseif (preg_match('~(([a-z]+)([а-я]+)|([а-я]+)([a-z]+))~iu', $var)) {
            self::$error['login'] = core::$lng['error_double_charset'];
        } elseif (filter_var($var, FILTER_VALIDATE_INT) !== false) {
            self::$error['login'] = core::$lng['error_digits_only'];
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
