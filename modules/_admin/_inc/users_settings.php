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
    ->fieldset(__('registration'))

    ->add('radio', 'registration', array(
    'checked' => Vars::$USER_SYS['registration'],
    'items'   => array(
        '2' => __('registration_open'),
        '1' => __('registration_moderation'),
        '0' => __('registration_closed')
    )))

    ->addHtml('<br/>')

    ->add('checkbox', 'reg_welcome', array(
    'label_inline' => __('welcome_message'),
    'checked'      => Vars::$USER_SYS['reg_welcome']))

    ->add('checkbox', 'reg_email', array(
    'label_inline' => __('registration_email'),
    'checked'      => Vars::$USER_SYS['reg_email']))

    ->add('checkbox', 'reg_quarantine', array(
    'label_inline' => __('registration_quarantine'),
    'checked'      => Vars::$USER_SYS['reg_quarantine']));

if (Vars::$USER_RIGHTS == 9) {
    $form
        ->fieldset(__('for_users'))

        ->add('checkbox', 'autologin', array(
        'label_inline' => __('autologin'),
        'checked'      => Vars::$USER_SYS['autologin']))

        ->add('checkbox', 'change_sex', array(
        'label_inline' => __('change_sex'),
        'checked'      => Vars::$USER_SYS['change_sex']))

        ->add('checkbox', 'change_status', array(
        'label_inline' => __('change_status'),
        'checked'      => Vars::$USER_SYS['change_status']))

        ->add('checkbox', 'upload_avatars', array(
        'label_inline' => __('upload_avatars'),
        'checked'      => Vars::$USER_SYS['upload_avatars']))

        ->add('checkbox', 'digits_only', array(
        'label_inline' => __('digits_only'),
        'checked'      => Vars::$USER_SYS['digits_only']))

        ->add('checkbox', 'change_nickname', array(
        'label_inline' => __('change_nickname_allow'),
        'checked'      => Vars::$USER_SYS['change_nickname']))

        ->add('text', 'change_period', array(
        'label_inline' => __('how_many_days') . ' <span class="note">(0-30)</span>',
        'value'        => Vars::$USER_SYS['change_period'],
        'class'        => 'mini',
        'filter'       => array(
            'type' => 'int',
            'min'  => 0,
            'max'  => 30
        )))

        ->fieldset(__('for_guests'))

        ->add('checkbox', 'view_online', array(
        'label_inline' => __('view_online'),
        'checked'      => Vars::$USER_SYS['view_online']))

        ->add('checkbox', 'viev_history', array(
        'label_inline' => __('viev_history'),
        'checked'      => Vars::$USER_SYS['viev_history']))

        ->add('checkbox', 'view_userlist', array(
        'label_inline' => __('view_userlist'),
        'checked'      => Vars::$USER_SYS['view_userlist']))

        ->add('checkbox', 'view_profiles', array(
        'label_inline' => __('view_profiles'),
        'checked'      => Vars::$USER_SYS['view_profiles']))

        ->fieldset(__('antiflood'))

        ->add('radio', 'flood_mode', array(
        'checked' => Vars::$USER_SYS['flood_mode'],
        'items'   => array(
            '3' => __('day'),
            '4' => __('night'),
            '2' => __('autoswitch'),
            '1' => __('adaptive')
        )))

        ->add('text', 'flood_day', array(
        'value'        => Vars::$USER_SYS['flood_day'],
        'class'        => 'small',
        'label_inline' => __('sec') . ', ' . __('day') . ' <span class="note">(3-300)</span>',
        'filter'       => array(
            'type' => 'int',
            'min'  => 3,
            'max'  => 300
        )))

        ->add('text', 'flood_night', array(
        'value'        => Vars::$USER_SYS['flood_night'],
        'class'        => 'small',
        'label_inline' => __('sec') . ', ' . __('night') . ' <span class="note">(3-300)</span>',
        'filter'       => array(
            'type' => 'int',
            'min'  => 3,
            'max'  => 300
        )));
}

$form
    ->fieldset()
    ->add('submit', 'submit', array('value' => __('save'), 'class' => 'btn btn-primary btn-large'));
if (Vars::$USER_RIGHTS == 9) {
    $form->add('submit', 'reset', array('value' => __('reset_settings'), 'class' => 'btn'));
}
$form->addHtml(' <a class="btn" href="' . Router::getUri(2) . '">' . __('back') . '</a>');
$tpl->form = $form->build();

if ($form->isSubmitted && isset($form->input['submit'])) {
    // Записываем настройки в базу
    foreach ($form->output as $key => $val) {
        Vars::$USER_SYS[$key] = $val;
    }

    // Записываем настройки в базу
    $STH = DB::PDO()->prepare('
        REPLACE INTO `cms_settings` SET
        `key` = :key,
        `val` = :val
    ');

    $STH->bindValue(':key', 'users');
    $STH->bindValue(':val', serialize(Vars::$USER_SYS));
    $STH->execute();

    $tpl->save = TRUE;
} elseif ($form->isSubmitted && isset($form->input['reset']) && Vars::$USER_RIGHTS == 9) {
    // Сбрасываем настройки на значения по-умолчанию
    DB::PDO()->exec("DELETE FROM `cms_settings` WHERE `key` = 'users'");
    header('Location: ' . Router::getUri(3) . '?default');
    exit;
}

$tpl->contents = $tpl->includeTpl('users_settings');