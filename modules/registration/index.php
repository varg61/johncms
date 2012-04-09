<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 *
 * Главное меню сайта
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

$tpl = Template::getInstance();

if (!Vars::$USER_SYS['reg_open']) {
    // Если регистрация закрыта, выводим сообщение
    $tpl->contents = $tpl->includeTpl('default');
    exit;
}

$reg_step = isset($_SESSION['reg']) ? intval($_SESSION['reg']) : 1;
switch ($reg_step) {
    case 2:
        $tpl->contents = $tpl->includeTpl('step2');
        break;

    default:
        /*
        -----------------------------------------------------------------
        Форма регистрации новых пользователей
        -----------------------------------------------------------------
        */
        $error = array();

        $reg_data['login'] = isset($_POST['login']) ? trim($_POST['login']) : '';
        $reg_data['password'] = isset($_POST['password']) ? trim($_POST['password']) : '';
        $reg_data['password_confirm'] = isset($_POST['password_confirm']) ? trim($_POST['password_confirm']) : '';
        $reg_data['captcha'] = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
        $reg_data['email'] = isset($_POST['email']) ? trim($_POST['email']) : '';
        $reg_data['about'] = isset($_POST['about']) ? trim($_POST['about']) : '';
        $reg_data['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
        $reg_data['sex'] = isset($_POST['sex']) ? intval($_POST['sex']) : 0;

        if (isset($_POST['check_login']) && Validate::nickname($reg_data['login'], true) === true) {
            // Проверка доступности Ника для регистрации
            Validate::nicknameAvailability($reg_data['login'], true);
        } elseif (isset($_POST['submit'])) {
            // Проверяем данные
            if (Validate::nickname($reg_data['login'], true) === true) {
                Validate::nicknameAvailability($reg_data['login'], true);
            }
            if (Validate::password($reg_data['password'], true) === true) {
                if ($reg_data['password'] != $reg_data['password_confirm']) {
                    $error['password_confirm'] = lng('error_passwords_not_match');
                }
            }
            if (Vars::$USER_SYS['reg_email'] && Validate::email($reg_data['email'], true) === true) {
                Validate::emailAvailability($reg_data['email'], true);
            }
            if ($reg_data['sex'] < 1 || $reg_data['sex'] > 2) {
                $error['sex'] = lng('error_sex_unknown');
            }
            if (mb_strlen($reg_data['captcha']) < 3 || $reg_data['captcha'] != $_SESSION['captcha']) {
                $error['captcha'] = lng('error_wrong_captcha');
            }
            unset($_SESSION['captcha']);

            // Регистрируем пользователя
            if (empty(Validate::$error) && empty($error)) {
                // Формируем Хэш пароля
                $password = crypt($reg_data['password'], '$2a$09$' . md5(uniqid()) . '$');

                // Формируем Токен
                $token = Functions::generateToken();

                // Добавляем пользователя в базу данных
                mysql_query("INSERT INTO `users` SET
                    `nickname` = '" . mysql_real_escape_string($reg_data['login']) . "',
                    `password` = '" . mysql_real_escape_string($password) . "',
                    `token` = '" . mysql_real_escape_string($token) . "',
                    `email` = '" . mysql_real_escape_string($reg_data['email']) . "',
                    `rights` = 0,
                    `level` = " . (Vars::$USER_SYS['reg_moderation'] ? 0 : 1) . ",
                    `sex` = '" . ($reg_data['sex'] == 1 ? 'm' : 'w') . "',
                    `join_date` = " . time() . ",
                    `last_visit` = " . time() . ",
                    `about` = ''
                ") or exit(mysql_error());
                $new_user_id = mysql_insert_id();

                // Отправляем приветственное письмо
                if (Vars::$USER_SYS['reg_welcome']) {
                    Functions::notificationSis($new_user_id, lng('welcome_msg_head'), lng('welcome_msg_body'));
                }

                // Запускаем пользователя на сайт
                setcookie('uid', $new_user_id, time() + 3600 * 24 * 31, '/');
                setcookie('token', $token, time() + 3600 * 24 * 31, '/');
                $_SESSION['uid'] = $new_user_id;
                $_SESSION['token'] = $token;

                // Пересылка на заполнение анкеты
                $_SESSION['reg'] = 2;
                $_SESSION['password'] = $reg_data['password'];
                header('Location: ' . Vars::$URI);
                return true;
            }
        }

        $tpl->reg_data = $reg_data;
        $tpl->error = array_merge($error, Validate::$error);
        $tpl->contents = $tpl->includeTpl('step1');
}