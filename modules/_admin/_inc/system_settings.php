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

global $tpl;

$form = new Form(Vars::$URI . '?act=system_settings');

$form
    ->add('text', 'timeshift', array(
    'value'        => Vars::$SYSTEM_SET['timeshift'],
    'class'        => 'small',
    'label'        => __('system_time'),
    'label_inline' => '<span class="badge badge-green">' . date("H:i", time() + Vars::$SYSTEM_SET['timeshift'] * 3600) . '</span> ' . __('time_shift') . ' <span class="note">(+ - 12)</span>',
    'filter'       => array(
        'type' => 'int',
        'min'  => -12,
        'max'  => 13
    )))

    ->add('text', 'email', array(
    'value' => Vars::$SYSTEM_SET['email'],
    'label' => __('site_email')))

    ->add('textarea', 'copyright', array(
    'value' => Vars::$SYSTEM_SET['copyright'],
    'label' => __('site_copyright')))

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

    ->add('checkbox', 'gzip', array(
    'checked'      => Vars::$SYSTEM_SET['gzip'],
    'label_inline' => __('gzip_compress')))

    ->addHtml('<br/>')

    ->add('checkbox', 'generation', array(
    'checked'      => Vars::$SYSTEM_SET['generation'],
    'label'        => __('profiling'),
    'label_inline' => __('profiling_generation')))

    ->add('checkbox', 'memory', array(
    'checked'      => Vars::$SYSTEM_SET['memory'],
    'label_inline' => __('profiling_memory')))

    ->addHtml('<br/>')

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

    ->addHtml('<br/>')

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Vars::$URI . '">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted) {
    foreach ($form->validOutput as $key => $val) {
        mysql_query("REPLACE INTO `cms_settings` SET `key` = '$key', `val` = '" . mysql_real_escape_string($val) . "'");
        header('Location: ' . Vars::$URI . '?act=system_settings&save');
    }
}

$tpl->contents = $tpl->includeTpl('system_settings');