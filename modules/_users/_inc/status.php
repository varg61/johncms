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

if (!Vars::$USER_SYS['change_status']){
    header('Location: ' . Vars::$HOME_URL . '404');
    exit;
}

$tpl = Template::getInstance();
$form = new Form($uri);

$form
    ->fieldset(__('change_status'))

    ->addHtml('<br/>')

    ->add('text', 'status', array(
    'style'       => 'max-width: none',
    'value'       => Users::$data['status'],
    'description' => __('status_lenght'),
    'filter'      => array(
        'type' => 'str',
        'max'  => 50
    )))

    ->fieldset()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUri(3) . 'option/">' . __('back') . '</a>');

$tpl->form = $form->build();

if ($form->isSubmitted) {
    Users::$data['status'] = $form->validOutput['status'];

    $STH = DB::PDO()->prepare('
      UPDATE `users` SET
      `status`   = :status
      WHERE `id` = :id
    ');

    $STH->bindValue(':status', Users::$data['status']);
    $STH->bindValue(':id', Users::$data['id']);
    $STH->execute();
    $STH = null;

    $tpl->save = 1;
}

$tpl->contents = $tpl->includeTpl('status');