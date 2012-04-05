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
    Авторизация (LOGIN) пользователя на сайте
    -----------------------------------------------------------------
    */
    public function userLogin()
    {
        $display = '';
        $sql = false;
        $captcha = false;

        $user_id = isset($_REQUEST['id']) ? abs(intval($_REQUEST['id'])) : false;
        $user_token = isset($_REQUEST['token']) ? trim($_REQUEST['token']) : false;
        $user_login = isset($_POST['login']) ? mb_substr(trim($_POST['login']), 0, 50) :false;
        $user_password = isset($_POST['password']) ? trim($_POST['password']) : false;
        $user_captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : false;
        $user_remember = isset($_POST['remember']);

        if ($user_id
            && $user_token
            && self::checkId($user_id, true) === true
            && strlen($user_token) == 32
        ) {
            // Авторизация по AutoLogin
            $sql = "`id` = " . $user_id;
        } elseif (isset($_POST['login']) && isset($_POST['password'])) {
            // Авторизация через форму
            if (Validate::email($user_login) === true) {
                $sql = "`email` = '" . mysql_real_escape_string($user_login) . "'";
                if(Validate::nickname($user_login) === true){
                    $sql .= " OR `nickname` = '" . mysql_real_escape_string($user_login) . "'";
                }
            } elseif(Validate::nickname($user_login, true) === true) {
                $sql = "`nickname` = '" . mysql_real_escape_string($user_login) . "'";
            }
            Validate::password($user_password, true);
        }

        // Присоединяем ошибки валидатора
        $this->error = array_merge($this->error, Validate::$error);

        if ($sql && empty($this->error)) {
            // Запрашиваем пользователя в базе данных
            $req = mysql_query("SELECT * FROM `users` WHERE $sql");
            if (mysql_num_rows($req)) {
                while ($res = mysql_fetch_assoc($req)) {
                    $this->error = array();
                    if ($res['login_try'] > 2) {
                        // Обрабатываем CAPTCHA
                        if (isset($_POST['captcha'])) {
                            if (Captcha::check() === true) {
                                $captcha = true;
                            } else {
                                $this->error['captcha'] = lng('error_wrong_captcha');
                                $display = 'captcha';
                            }
                        } else {
                            $display = 'captcha';
                        }
                    }

                    if ($res['login_try'] < 3 || $res['login_try'] > 2 && $captcha === true) {
                        $salt_password = empty($res['salt']) ? md5(md5($user_password)) : md5(md5($user_password) . md5($res['salt']));

                        // Если пароль, или токен совпадает, впускаем пользователя на сайт
                        if ($res['password'] === $salt_password || (!empty($res['token']) && $res['token'] == $user_token)) {
                            $display = 'homepage';
                            $sql_update = array();
                            $token = $res['token'];

                            // Сбрасываем счетчик неудачных Логинов
                            if ($res['login_try'] > 0) {
                                $sql_update[] = "`login_try` = 0";
                            }

                            // Конвертируем пароли в новый формат
                            if ($user_password && empty($res['salt'])) {
                                $salt = self::_generateSalt();
                                $salt_password = md5(md5($user_password) . md5($salt));
                                $sql_update[] = "`password` = '" . mysql_real_escape_string($salt_password) . "'";
                                $sql_update[] = "`salt` = '" . mysql_real_escape_string($salt) . "'";
                            }

                            // Проверяем токен, если его нет, то генерируем и записываем в базу
                            if (empty($token)) {
                                $token = $this->_generateToken();
                                $sql_update[] = "`token` = '" . mysql_real_escape_string($token) . "'";
                            }

                            // Обновляем данные в Базе
                            if (!empty($sql_update)) {
                                mysql_query("UPDATE `users` SET " . implode(', ', $sql_update) . " WHERE `id` = " . $res['id']) or exit(mysql_error());
                            }

                            // Впускаем пользователя на сайт
                            $this->_userEnter($res['id'], $token, $user_remember);
                            parent::$USER_ID = $res['id'];
                            break;
                        } else {
                            // Если пароль неверный
                            $this->error['password'] = lng('error_wrong_password');
                            if ($res['login_try'] < 3) {
                                // Накручиваем счетчик неудачных Логинов
                                mysql_query("UPDATE `users` SET `login_try` = " . ++$res['login_try'] . " WHERE `id` = " . $res['id']);
                            }
                        }
                    }
                }
            } else {
                // Если пользователь не найден
                $this->error['login'] = lng('error_user_not_exist');
            }
        }
        return $display;
    }

    /*
    -----------------------------------------------------------------
    Запись данных сессии и COOKIE
    -----------------------------------------------------------------
    */
    private function _userEnter($user_id, $token, $remember = true)
    {
        if ($remember) {
            setcookie('uid', $user_id, time() + 3600 * 24 * 31, '/');
            setcookie('token', $token, time() + 3600 * 24 * 31, '/');
        }
        $_SESSION['uid'] = $user_id;
        $_SESSION['token'] = $token;
    }

    /*
    -----------------------------------------------------------------
    Проверка корректности ввода User ID
    -----------------------------------------------------------------
    */
    private function checkId($var = '', $error_log = false)
    {
        if (empty($var)) {
            $error = lng('error_login_empty');
        } elseif (filter_var($var, FILTER_VALIDATE_INT) == false || $var < 1) {
            $error = 'User ID: ' . lng('error_wrong_data');
        } else {
            return true;
        }
        if ($error_log) $this->error['login'] = $error;
        return false;
    }

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
