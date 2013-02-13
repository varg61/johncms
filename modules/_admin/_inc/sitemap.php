<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_ADMIN') or die('Error: restricted access');

$tpl = Template::getInstance();
$form = new Form(Router::getUri(3));

$settings = isset(Vars::$SYSTEM_SET['sitemap'])
    ? unserialize(Vars::$SYSTEM_SET['sitemap'])
    : array('forum' => 0, 'lib' => 0, 'browsers' => 1, 'users' => 0);

$form
    ->fieldset(__('lng_on'))

    ->add('checkbox', 'forum', array(
    'label_inline' => __('sitemap_forum'),
    'checked'      => $settings['forum']))

    ->add('checkbox', 'lib', array(
    'label_inline' => __('sitemap_library'),
    'checked'      => $settings['lib']))

    ->fieldset(__('browsers'))

    ->add('radio', 'browsers', array(
    'checked' => $settings['browsers'],
    'items'   => array(
        '1' => __('show_all'),
        '0' => __('show_only_computers')
    )))

    ->fieldset(__('users'))

    ->add('radio', 'users', array(
    'checked' => $settings['users'],
    'items'   => array(
        '1' => __('show_all'),
        '0' => __('show_only_guests')
    )))

    ->fieldset()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUri(2) . '">' . __('back') . '</a>');

$tpl->form = $form->build();

if ($form->isSubmitted) {
    foreach ($form->output as $key => $val) {
        $settings[$key] = $val;
    }

    // Записываем настройки в базу
    $STH = DB::PDO()->prepare('
        REPLACE INTO `cms_settings` SET
        `key` = :key,
        `val` = :val
    ');

    $STH->bindValue(':key', 'sitemap');
    $STH->bindValue(':val', serialize($settings));
    $STH->execute();

    $tpl->save = TRUE;
}

$tpl->contents = $tpl->includeTpl('sitemap');