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
$uri = Router::getUrl(4);
//TODO: Добавить проверку на разрешение смены статуса
$tpl = Template::getInstance();
$form = new Form($uri);

$form
    ->fieldsetStart(__('change_status'))

    ->addHtml('<br/>')

    ->add('text', 'status', array(
    'style'       => 'max-width: none',
    'value'       => Users::$data['status'],
    'description' => __('status_lenght')))

    ->fieldsetStart()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUrl(3) . 'option/">' . __('back') . '</a>');

$tpl->form = $form->display();
//TODO: Добавить валидацию длины статуса

if ($form->isSubmitted) {
    Users::$data['status'] = $form->validOutput['status'];
    mysql_query("UPDATE `users` SET
    `status` = '" . mysql_real_escape_string(Users::$data['status']) . "'
    WHERE `id` = " . Users::$data['id']);
    $tpl->save = 1;
}

$tpl->contents = $tpl->includeTpl('edit_status');