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

// Подготавливаем список тем оформления
$tpl_list = array();
foreach (glob(TPLPATH . '*', GLOB_ONLYDIR) as $val) {
    $tpl_list[] = basename($val);
}
sort($tpl_list);
array_unshift($tpl_list, '--default--');

// Подготавливаем список имеющихся языков
$items['#'] = __('select_automatically');
foreach (Languages::getInstance()->getLngDescription() as $key => $val) {
    $items[$key] = Functions::loadImage('flag_' . $key . '.gif') . '&#160; ' . $val;
}

$tpl = Template::getInstance();
$form = new Form($uri);

$form
    ->fieldsetStart(__('system_settings'))

    ->add('text', 'timeshift', array(
    'value'        => Vars::$USER_SET['timeshift'],
    'label_inline' => '<span class="badge badge-large">' . date("H:i", time() + (Vars::$SYSTEM_SET['timeshift'] + Vars::$USER_SET['timeshift']) * 3600) . '</span> ' . __('settings_clock'),
    'description'  => __('settings_clock_shift') . ' (+ - 12)',
    'class'        => 'small',
    'maxlength'    => 3,
    'filter'       => array(
        'type' => 'int',
        'min'  => -12,
        'max'  => 13
    )))

    ->add('checkbox', 'direct_url', array(
    'checked'      => Vars::$USER_SET['direct_url'],
    'label_inline' => __('direct_url')))

    ->add('checkbox', 'avatar', array(
    'checked'      => Vars::$USER_SET['avatar'],
    'label_inline' => __('avatars')))

    ->add('checkbox', 'smilies', array(
    'checked'      => Vars::$USER_SET['smilies'],
    'label_inline' => __('smilies')))

    ->fieldsetStart(__('apperance'))

    ->add('text', 'page_size', array(
    'value'        => Vars::$USER_SET['page_size'],
    'label_inline' => __('list_size'),
    'description'  => __('list_size_help') . ' (5-99)',
    'class'        => 'small',
    'maxlength'    => 2,
    'filter'       => array(
        'type' => 'int',
        'min'  => 5,
        'max'  => 99
    )))

    ->add('text', 'field_h', array(
    'value'        => Vars::$USER_SET['field_h'],
    'label_inline' => __('field_height'),
    'description'  => __('field_height_help') . ' (2-9)',
    'class'        => 'small',
    'maxlength'    => 1,
    'filter'       => array(
        'type' => 'int',
        'min'  => 2,
        'max'  => 9
    )))

    ->add('select', 'skin', array(
    'selected' => Vars::$USER_SET['skin'],
    'label'    => __('design_template'),
    'items'    => $tpl_list))

    ->fieldsetStart(__('language'))

    //TODO: Добавить проверку на удаленный язык
    ->add('radio', 'iso', array(
    'checked' => Vars::$USER_SET['lng'],
    'items'   => $items))

    ->fieldsetStart()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUri(3) . 'option/">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted) {
    foreach ($form->validOutput as $key => $val) {
        Vars::$USER_SET[$key] = $val;
    }
    Vars::setUserData('user_set', Vars::$USER_SET);
    unset($_SESSION['user_set']);
    $tpl->save = 1;
}

$tpl->contents = $tpl->includeTpl('settings');