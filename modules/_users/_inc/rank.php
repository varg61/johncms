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
$uri = Router::getUri(4);

//TODO: Добавить проверку прав доступа

$tpl = Template::getInstance();
$form = new Form($uri);

$form
    ->fieldset(__('rank'))

    ->add('radio', 'rights', array(
    'checked' => Users::$data['rights'],
    'items'   => array(
        0 => __('rank_0'),
        3 => __('rank_3'),
        4 => __('rank_4'),
        5 => __('rank_5'),
        6 => __('rank_6'),
        7 => '<i class="icn-shield"></i>' . __('rank_7'),
        9 => '<i class="icn-shield-red"></i>' . __('rank_9'),
    )))

    ->add('text', 'password', array('label' => __('your_password')))

    ->fieldset()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUri(3) . 'option/">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted) {

}

$tpl->contents = $tpl->includeTpl('rank');

if (isset($_POST['submit'])
    && isset($_POST['rights'])
    && isset($_POST['password'])
    && isset($_POST['form_token'])
    && isset($_SESSION['form_token'])
    && $_POST['form_token'] == $_SESSION['form_token']
    && $_POST['rights'] != $tpl->user['rights']
    && $_POST['rights'] >= 0
    && $_POST['rights'] != 8
    && $_POST['rights'] <= 9
) {
    $rights = intval($_POST['rights']);
    $password = trim($_POST['password']);
    if (Validate::password($password) === TRUE
        && crypt($password, Vars::$USER_DATA['password']) === Vars::$USER_DATA['password']
        && (Vars::$USER_RIGHTS == 9 || (Vars::$USER_RIGHTS == 7 && $rights < 7))
    ) {
        // Если пароль совпадает, обрабатываем форму
        DB::PDO()->exec("UPDATE `users` SET `rights` = '$rights' WHERE `id` = " . $tpl->user['id']);
        $tpl->user['rights'] = $rights;
        $tpl->save = 1;
        if ($tpl->user['id'] == Vars::$USER_ID) {
            header('Location: ' . Router::getUri(3) . '?act=settings');
            exit;
        }
    } else {
        $tpl->error['password'] = __('error_wrong_password');
    }
}