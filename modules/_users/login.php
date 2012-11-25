<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

$tpl = Template::getInstance();

$error = array();
$autologin = FALSE;
$sql = FALSE;

$login_data['id'] = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : FALSE;
$login_data['token'] = isset($_REQUEST['token']) ? trim($_REQUEST['token']) : FALSE;
$login_data['login'] = isset($_POST['login']) ? mb_substr(trim($_POST['login']), 0, 50) : FALSE;
$login_data['password'] = isset($_POST['password']) ? mb_substr(trim($_POST['password']), 0, 50) : FALSE;

if (Vars::$USER_SYS['autologin'] && $login_data['id'] > 0 && strlen($login_data['token']) == 32) {
    /**
     * Авторизация через ссылку AutoLogin
     */
    $autologin = TRUE;
    $sql = "`id` = " . intval($_GET['id']);
} elseif ($login_data['login'] !== FALSE && $login_data['password'] !== FALSE) {
    /**
     * Авторизация через форму
     */
    if (Validate::email($login_data['login']) === TRUE) {
        // Авторизация по E-mail адресу
        $sql = "`email` = '" . mysql_real_escape_string($login_data['login']) . "'";
    } elseif (Validate::nickname($login_data['login'], TRUE) === TRUE) {
        // Авторизация по Нику
        $sql = "`nickname` = '" . mysql_real_escape_string($login_data['login']) . "'";
    }
    Validate::password($login_data['password'], TRUE);
}

$tpl->data = $login_data;

if ($sql && empty(Validate::$error)) {
    $req = mysql_query("SELECT * FROM `users` WHERE " . $sql . " LIMIT 1");
    if (mysql_num_rows($req)) {
        $res = mysql_fetch_assoc($req);

        /**
         * Обрабатываем CAPTCHA
         */
        if ($res['login_try'] > 2) {
            $captcha = TRUE;
            if (isset($_POST['captcha'])
                && isset($_POST['form_token'])
                && isset($_SESSION['form_token'])
                && $_POST['form_token'] == $_SESSION['form_token']
            ) {
                if (Captcha::check() === TRUE) {
                    $captcha = FALSE;
                } else {
                    $error['captcha'] = __('error_wrong_captcha');
                }
            }

            if ($captcha) {
                /**
                 * Показываем форму CAPTCHA
                 */
                $tpl->form_token = mt_rand(100, 10000);
                $_SESSION['form_token'] = $tpl->form_token;
                $tpl->error = $error;
                $tpl->contents = $tpl->includeTpl('login_captcha');
                exit;
            }
        }

        if (($autologin && $login_data['token'] === $res['token'])
            || (!$autologin && crypt($login_data['password'], $res['password']) === $res['password'])
        ) {
            /**
             * Если пароль, или токен совпадают, авторизуем пользователя
             */
            $sql_update = array();
            $token = $res['token'];

            if ($res['login_try']) {
                // Сбрасываем счетчик неудачных Логинов
                $sql_update[] = "`login_try` = 0";
            }

            if (empty($token)) {
                // Проверяем токен, если его нет, то генерируем и записываем в базу
                $token = Functions::generateToken();
                $sql_update[] = "`token` = '" . mysql_real_escape_string($token) . "'";
            }

            if (!empty($sql_update)) {
                // Обновляем данные в Базе
                mysql_query("UPDATE `users` SET " . implode(', ', $sql_update) . " WHERE `id` = " . $res['id']) or exit(mysql_error());
            }

            // Записываем сессию и COOKIE
            if (isset($_POST['remember'])) {
                setcookie('uid', $res['id'], time() + 3600 * 24 * 31, '/');
                setcookie('token', $token, time() + 3600 * 24 * 31, '/');
            }
            $_SESSION['uid'] = $res['id'];
            $_SESSION['token'] = $token;

            // Пересылка на Главную страницу
            header('Location: ' . Vars::$HOME_URL);
            exit;
        } else {
            /**
             * Если пароль, или токен не совпадает
             */
            $error['password'] = __('error_wrong_password');
            if ($res['login_try'] < 3) {
                // Накручиваем счетчик неудачных Логинов
                mysql_query("UPDATE `users` SET `login_try` = " . ++$res['login_try'] . " WHERE `id` = " . $res['id']);
            }
        }
    } else {
        /**
         * Если пользователь не найден
         */
        $error['login'] = __('error_user_not_exist');
    }
}

$tpl->error = array_merge($error, Validate::$error);
$tpl->contents = $tpl->includeTpl('login');