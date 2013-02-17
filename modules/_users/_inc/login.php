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

    // Обрабатываем CAPTCHA
    if ($form->isSubmitted && ($user = Validate::getUserData()) !== FALSE) {
        if ($user['login_try'] > 2) {
            $captcha = TRUE;
            $tpl->data = $form->output;
            if (isset($_POST['captcha'])
                && isset($_POST['form_token'])
                && isset($_SESSION['form_token'])
                && $_POST['form_token'] == $_SESSION['form_token']
            ) {
                if (Captcha::check() === TRUE) {
                    $captcha = FALSE;
                } else {
                    $tpl->error = __('error_wrong_captcha');
                }
            }

            if ($captcha) {
                // Показываем форму CAPTCHA
                $tpl->form_token = $_SESSION['form_token'];
                $tpl->contents = $tpl->includeTpl('login_captcha');
                exit;
            }
        }
    }

    // Авторизуем пользователя
    if ($form->isValid && ($user = Validate::getUserData()) !== FALSE) {
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
}

$tpl->contents = $tpl->includeTpl('login');