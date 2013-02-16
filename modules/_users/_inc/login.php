<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_USERS') or die('Error: restricted access');
$uri = Router::getUri(3);

$tpl = Template::getInstance();
$form = new Form($uri);

if (Vars::$USER_ID) {
    // Показываем форму выхода с сайта
    $form
        ->fieldset(__('exit_warning'))

        ->add('checkbox', 'clear', array('label_inline' => __('clear_authorisation')))

        ->addHtml('<br/>')

        ->add('submit', 'submit', array(
        'value' => '   ' . __('exit') . '   ',
        'class' => 'btn btn-primary btn-large btn-block'))

        ->addHtml('<br/><a class="btn btn-large btn-block" href="' . (isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : Vars::$HOME_URL) . 'option/">' . __('back') . '</a>');

    $tpl->form = $form->build();

    if ($form->isValid) {
        Vars::userUnset($form->output['clear']);
        header('Location: ' . Vars::$HOME_URL);
        exit;
    }
} else {
    // Показываем форму входа на сайт
    $form
        ->add('text', 'login', array(
        'label'    => __('login_caption'),
        'class'    => 'relative largetext',
        'required' => TRUE,
        'validate' => array(
            'lenght'   => array('min' => 2, 'max' => 20),
            'nickname' => array()
        )))

        ->add('password', 'password', array(
        'label'    => __('password'),
        'class'    => 'relative largetext',
        'required' => TRUE,
        'validate' => array(
            'lenght' => array('min' => 3),
        )))

        ->add('checkbox', 'remember', array(
        'checked'      => TRUE,
        'label_inline' => __('remember')))

        ->addHtml('<br/>')

        ->add('submit', 'submit', array(
        'value' => __('login'),
        'class' => 'btn btn-primary btn-large btn-block'))

        ->addHtml('<br/><a class="btn btn-large btn-block" href="' . Router::getUri(2) . 'registration/">' . __('registration') . '</a>')

        ->addHtml('<br/><a class="btn" href="#">' . __('forgotten_password') . '</a>')

        ->addRule('login', 'login')
        ->addRule('password', 'password');

    $tpl->form = $form->build();

    if ($form->isValid && ($user = Validate::getUserData()) !== FALSE) {
        // Авторизуем пользователя
        //TODO: Добавить Капчу

        if (empty($user['token'])) {
            $user['token'] = Functions::generateToken();
        }

        $STH = DB::PDO()->prepare('
            UPDATE `users` SET
            `login_try` = 0,
            `token` = ?
            WHERE `id` = ?
        ');

        $STH->execute(array($user['token'], $user['id']));

        if (isset($_POST['remember'])) {
            setcookie('uid', $user['id'], time() + 3600 * 24 * 31, '/');
            setcookie('token', $user['token'], time() + 3600 * 24 * 31, '/');
        }
        $_SESSION['token'] = $user['token'];
        $_SESSION['uid'] = $user['id'];

        header('Location: ' . Vars::$HOME_URL);
        exit;
    }

//    $error = array();
//    $autologin = FALSE;
//    $value = FALSE;
//    $sql = FALSE;

//    $login_data['id'] = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : FALSE;
//    $login_data['token'] = isset($_REQUEST['token']) ? trim($_REQUEST['token']) : FALSE;
//    $login_data['login'] = isset($_POST['login']) ? mb_substr(trim($_POST['login']), 0, 50) : FALSE;
//    $login_data['password'] = isset($_POST['password']) ? mb_substr(trim($_POST['password']), 0, 50) : FALSE;

//    if (Vars::$USER_SYS['autologin'] && $login_data['id'] > 0 && strlen($login_data['token']) == 32) {
//        // Авторизация через ссылку AutoLogin
//        $autologin = TRUE;
//        $value = intval($_GET['id']);
//        $sql = '`id` = ?';
//    } elseif ($login_data['login'] !== FALSE && $login_data['password'] !== FALSE) {
//        // Авторизация через форму
//        $value = $login_data['login'];
//        if (Validate::email($login_data['login']) === TRUE) {
//            // Авторизация по E-mail адресу
//            $sql = '`email` = ?';
//        } elseif (Validate::nickname($login_data['login'], TRUE) === TRUE) {
//            // Авторизация по Нику
//            $sql = '`nickname` = ?';
//        }
//        Validate::password($login_data['password'], TRUE);
//    }
//
//    $tpl->data = $login_data;
//
//    if ($sql && empty(Validate::$error)) {
//        $STH = DB::PDO()->prepare('SELECT * FROM `users` WHERE ' . $sql . ' LIMIT 1');
//        $STH->execute(array($value));
//
//        if ($STH->rowCount()) {
//            $result = $STH->fetch();
//            $token = empty($result['token']) ? Functions::generateToken() : $result['token'];
//
//            // Обрабатываем CAPTCHA
//            if ($result['login_try'] > 2) {
//                $captcha = TRUE;
//                if (isset($_POST['captcha'])
//                    && isset($_POST['form_token'])
//                    && isset($_SESSION['form_token'])
//                    && $_POST['form_token'] == $_SESSION['form_token']
//                ) {
//                    if (Captcha::check() === TRUE) {
//                        $captcha = FALSE;
//                    } else {
//                        $error['captcha'] = __('error_wrong_captcha');
//                    }
//                }
//
//                if ($captcha) {
//                    // Показываем форму CAPTCHA
//                    $tpl->form_token = md5(mt_rand(1000, 100000) . microtime(TRUE));
//                    $_SESSION['form_token'] = $tpl->form_token;
//                    $tpl->error = $error;
//                    $tpl->contents = $tpl->includeTpl('login_captcha');
//                    exit;
//                }
//            }
//
//            if (($autologin && $login_data['token'] === $token)
//                || (!$autologin && crypt($login_data['password'], $result['password']) === $result['password'])
//            ) {
//                // Если пароль, или токен совпадают, авторизуем пользователя
//                $STHU = DB::PDO()->prepare('
//                    UPDATE `users` SET
//                    `login_try` = 0,
//                    `token` = :token
//                    WHERE `id` = :id
//                ');
//
//                $STHU->bindValue(':token', $token, PDO::PARAM_STR);
//                $STHU->bindValue(':id', $result['id'], PDO::PARAM_INT);
//                $STHU->execute();
//
//                // Записываем сессию и COOKIE
//                if (isset($_POST['remember'])) {
//                    setcookie('uid', $result['id'], time() + 3600 * 24 * 31, '/');
//                    setcookie('token', $token, time() + 3600 * 24 * 31, '/');
//                }
//                $_SESSION['uid'] = $result['id'];
//                $_SESSION['token'] = $token;
//
//                // Пересылка на Главную страницу
//                header('Location: ' . Vars::$HOME_URL);
//                exit;
//            } else {
//                // Если пароль, или токен не совпадает
//                $error['password'] = __('error_wrong_password');
//                if ($result['login_try'] < 3) {
//                    // Накручиваем счетчик неудачных Логинов
//                    DB::PDO()->exec("UPDATE `users` SET `login_try` = " . ++$result['login_try'] . " WHERE `id` = " . $result['id']);
//                }
//            }
//        } else {
//            // Если пользователь не найден
//            $error['login'] = __('error_user_not_exist');
//        }
//    }
//
//    $tpl->error = $error;
}

$tpl->contents = $tpl->includeTpl('login');