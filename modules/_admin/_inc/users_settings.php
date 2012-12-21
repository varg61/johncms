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

$form = new Form(Vars::$URI . '?act=users_settings');
$form
    ->addField('radio', 'registration', array(
    'label'   => __('registration'),
    'checked' => Vars::$USER_SYS['registration'],
    'items'   => array(
        '2' => __('registration_open'),
        '1' => __('registration_moderation'),
        '0' => __('registration_closed')
    )))

    ->addField('checkbox', 'reg_welcome', array(
    'label_inline' => __('welcome_message'),
    'checked'      => Vars::$USER_SYS['reg_welcome']))

    ->addField('checkbox', 'reg_email', array(
    'label_inline' => __('registration_email'),
    'checked'      => Vars::$USER_SYS['reg_email']))

    ->addField('checkbox', 'reg_quarantine', array(
    'label_inline' => __('registration_quarantine'),
    'checked'      => Vars::$USER_SYS['reg_quarantine']));

if (Vars::$USER_RIGHTS == 9) {
    $form
        ->addHtml('<br/>')

        ->addField('checkbox', 'autologin', array(
        'label'        => __('for_users'),
        'label_inline' => __('autologin'),
        'checked'      => Vars::$USER_SYS['autologin']))

        ->addField('checkbox', 'change_sex', array(
        'label_inline' => __('change_sex'),
        'checked'      => Vars::$USER_SYS['change_sex']))

        ->addField('checkbox', 'change_status', array(
        'label_inline' => __('change_status'),
        'checked'      => Vars::$USER_SYS['change_status']))

        ->addField('checkbox', 'upload_avatars', array(
        'label_inline' => __('upload_avatars'),
        'checked'      => Vars::$USER_SYS['upload_avatars']))

        ->addField('checkbox', 'digits_only', array(
        'label_inline' => __('digits_only'),
        'checked'      => Vars::$USER_SYS['digits_only']))

        ->addField('checkbox', 'change_nickname', array(
        'label_inline' => __('change_nickname_allow'),
        'checked'      => Vars::$USER_SYS['change_nickname']))

        ->addField('text', 'change_period', array(
        'label_inline' => __('how_many_days'),
        'value'        => Vars::$USER_SYS['change_period'],
        'class'        => 'mini'))

        ->addHtml('<br/>')

        ->addField('checkbox', 'view_online', array(
        'label'        => __('for_guests'),
        'label_inline' => __('view_online'),
        'checked'      => Vars::$USER_SYS['view_online']))

        ->addField('checkbox', 'viev_history', array(
        'label_inline' => __('viev_history'),
        'checked'      => Vars::$USER_SYS['viev_history']))

        ->addField('checkbox', 'view_userlist', array(
        'label_inline' => __('view_userlist'),
        'checked'      => Vars::$USER_SYS['view_userlist']))

        ->addField('checkbox', 'view_profiles', array(
        'label_inline' => __('view_profiles'),
        'checked'      => Vars::$USER_SYS['view_profiles']))

        ->addHtml('<br/>')

        ->addField('radio', 'flood_mode', array(
        'label'   => __('antiflood'),
        'checked' => Vars::$USER_SYS['flood_mode'],
        'items'   => array(
            '3' => __('day'),
            '4' => __('night'),
            '2' => __('autoswitch'),
            '1' => __('adaptive')
        )))

        ->addField('text', 'flood_day', array(
        'value'        => Vars::$USER_SYS['flood_day'],
        'class'        => 'small',
        'label_inline' => __('sec') . ', ' . __('day')))

        ->addField('text', 'flood_night', array(
        'value'        => Vars::$USER_SYS['flood_night'],
        'class'        => 'small',
        'label_inline' => __('sec') . ', ' . __('night')))

        ->addHtml('<br/>')

        ->addField('submit', 'submit', array(
        'value' => __('save'),
        'class' => 'btn btn-primary btn-large'))

        ->addField('submit', 'reset', array(
        'value' => __('reset_settings'),
        'class' => 'btn'))

        ->addHtml(' <a class="btn" href="' . Vars::$URI . '">' . __('back') . '</a>');
}

$tpl->form = $form->display();

if ($form->submit && isset($form->input['submit'])) {
    foreach ($form->validInput as $key => $val) {
        Vars::$USER_SYS[$key] = $val;
    }

    // Проверяем принятые данные
    if (Vars::$USER_SYS['flood_day'] < 5) {
        Vars::$USER_SYS['flood_day'] = 5;
    } elseif (Vars::$USER_SYS['flood_day'] > 300) {
        Vars::$USER_SYS['flood_day'] = 300;
    }

    if (Vars::$USER_SYS['flood_night'] < 4) {
        Vars::$USER_SYS['flood_night'] = 4;
    } elseif (Vars::$USER_SYS['flood_night'] > 300) {
        Vars::$USER_SYS['flood_night'] = 300;
    }

    if (Vars::$USER_SYS['change_period'] < 0) {
        Vars::$USER_SYS['change_period'] = 0;
    } elseif (Vars::$USER_SYS['change_period'] > 99) {
        Vars::$USER_SYS['change_period'] = 99;
    }

    // Записываем настройки в базу
    mysql_query("REPLACE INTO `cms_settings` SET
        `key` = 'users',
        `val` = '" . mysql_real_escape_string(serialize(Vars::$USER_SYS)) . "'
    ");

    $tpl->save = TRUE;
} elseif ($form->submit && isset($form->input['reset']) && Vars::$USER_RIGHTS == 9) {
    @mysql_query("DELETE FROM `cms_settings` WHERE `key` = 'users'");
    header('Location: ' . Vars::$URI . '?act=users_settings&default');
    exit;
}

$tpl->contents = $tpl->includeTpl('users_settings');