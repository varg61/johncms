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

$settings = isset(Vars::$SYSTEM_SET['sitemap'])
    ? unserialize(Vars::$SYSTEM_SET['sitemap'])
    : array('forum' => 0, 'lib' => 0, 'browsers' => 1, 'users' => 0);

$form = new Form(Vars::$URI . '?act=sitemap');

$form
    ->fieldsetStart(__('lng_on'))

    ->add('checkbox', 'forum', array(
    'label_inline' => __('sitemap_forum'),
    'checked'      => $settings['forum']))

    ->add('checkbox', 'lib', array(
    'label_inline' => __('sitemap_library'),
    'checked'      => $settings['lib']))

    ->fieldsetStart(__('browsers'))

    ->add('radio', 'browsers', array(
    'checked' => $settings['browsers'],
    'items'   => array(
        '1' => __('show_all'),
        '0' => __('show_only_computers')
    )))

    ->fieldsetStart(__('users'))

    ->add('radio', 'users', array(
    'checked' => $settings['users'],
    'items'   => array(
        '1' => __('show_all'),
        '0' => __('show_only_guests')
    )))

    ->fieldsetStart()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Vars::$URI . '">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted) {
    foreach ($form->validOutput as $key => $val) {
        $settings[$key] = $val;
    }

    // Записываем настройки в базу
    mysql_query("REPLACE INTO `cms_settings`
        SET `key` = 'sitemap',
        `val` = '" . mysql_real_escape_string(serialize($settings)) . "'
    ");
    $tpl->save = true;
}

$tpl->contents = $tpl->includeTpl('sitemap');