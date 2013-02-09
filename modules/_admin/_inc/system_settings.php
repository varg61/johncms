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

$form
    ->fieldset(__('system_time'))

    ->add('text', 'timeshift', array(
    'value'        => Vars::$SYSTEM_SET['timeshift'],
    'class'        => 'small',
    'label_inline' => '<span class="badge badge-green">' . date("H:i", time() + Vars::$SYSTEM_SET['timeshift'] * 3600) . '</span> ' . __('time_shift') . ' <span class="note">(+ - 12)</span>',
    'filter'       => array(
        'type' => 'int',
        'min'  => -12,
        'max'  => 13
    )))

    ->fieldset(__('file_upload'))

    ->add('text', 'filesize', array(
    'value'        => Vars::$SYSTEM_SET['filesize'],
    'label_inline' => __('file_maxsize') . ' kB <span class="note">(100-50000)</span>',
    'description'  => __('filesize_note'),
    'class'        => 'small',
    'filter'       => array(
        'type' => 'int',
        'min'  => 100,
        'max'  => 50000
    )))

    ->fieldset(__('profiling'))

    ->add('checkbox', 'generation', array(
    'checked'      => Vars::$SYSTEM_SET['generation'],
    'label_inline' => __('profiling_generation')))

    ->add('checkbox', 'memory', array(
    'checked'      => Vars::$SYSTEM_SET['memory'],
    'label_inline' => __('profiling_memory')))

    ->fieldset(__('site_details'))

    ->add('text', 'email', array(
    'value' => Vars::$SYSTEM_SET['email'],
    'label' => __('site_email')))

    ->add('textarea', 'copyright', array(
    'value' => Vars::$SYSTEM_SET['copyright'],
    'label' => __('site_copyright')))

    ->fieldset(__('seo_attributes'))

    ->add('text', 'hometitle', array(
    'value'       => Vars::$SYSTEM_SET['hometitle'],
    'style'       => 'max-width: none',
    'label'       => __('homepage_title'),
    'description' => __('homepage_title_help')))

    ->add('textarea', 'meta_key', array(
    'value'       => Vars::$SYSTEM_SET['meta_key'],
    'label'       => 'META Keywords',
    'description' => __('keywords_note'),
    'filter'      => array(
        'type' => 'str',
        'max'  => 250
    )))

    ->add('textarea', 'meta_desc', array(
    'value'       => Vars::$SYSTEM_SET['meta_desc'],
    'label'       => 'META Description',
    'description' => __('description_note'),
    'filter'      => array(
        'type' => 'str',
        'max'  => 250
    )))

    ->fieldset()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUri(2) . '">' . __('back') . '</a>');

$tpl->form = $form->build();

if ($form->isSubmitted) {
    // Записываем настройки в базу
    $STH = DB::PDO()->prepare('
        REPLACE INTO `cms_settings` SET
        `key` = ?,
        `val` = ?
    ');

    foreach ($form->validOutput as $key => $val) {
        $STH->execute(array($key, $val));
    }

    header('Location: ' . Router::getUri(3) . '?save');
}

$tpl->contents = $tpl->includeTpl('system_settings');