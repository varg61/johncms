<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_NEWS') or die('Error: restricted access');
$uri = Router::getUri(3);
//TODO: Добавить валидацию
//TODO: Добавить обсуждение
$tpl = Template::getInstance();
$form = new Form($uri);

$form
    ->fieldset(__('add_article'))

    ->add('text', 'name', array(
    'label'    => __('article_title'),
    'required' => TRUE))

    ->add('textarea', 'text', array(
    'label'   => __('text'),
    'toolbar' => !Vars::$IS_MOBILE,
    'required' => TRUE))

    ->fieldset()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUri(2) . '">' . __('back') . '</a>');

$tpl->form = $form->build();

if ($form->isValid) {
    $STH = DB::PDO()->prepare('
        INSERT INTO `cms_news` SET
        `time`      = ?,
        `author`    = ?,
        `author_id` = ?,
        `name`      = ?,
        `text`      = ?
    ');

    $STH->execute(array(
        time(),
        Vars::$USER_NICKNAME,
        Vars::$USER_ID,
        $form->output['name'],
        $form->output['text']
    ));
    $STH = NULL;

    DB::PDO()->query('UPDATE `users` SET `lastpost` = ' . time() . ' WHERE `id` = ' . Vars::$USER_ID);

    header('Location: ' . Router::getUri(2));
    exit;
}

$tpl->contents = $tpl->includeTpl('add');