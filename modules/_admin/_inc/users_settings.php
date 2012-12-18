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

        ->addLabel(__('for_users'))

        ->addField('checkbox', 'autologin', array(
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

        ->addLabel(__('for_guests'))

        ->addField('checkbox', 'view_online', array(
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

        ->addLabel(__('antiflood'))

        ->addField('radio', 'flood_mode', array(
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

        ->addHtml(' <a class="btn" href="' . Vars::$URI . '?act=users_settings&amp;reset">' . __('reset_settings') . '</a>')

        ->addHtml(' <a class="btn" href="' . Vars::$URI . '">' . __('back') . '</a>');
}

$tpl->form = $form->display();

if ($form->submit) {
    Vars::$USER_SYS['registration'] = isset($_POST['registration']) && $_POST['registration'] >= 0 && $_POST['registration'] <= 2 ? intval($_POST['registration']) : 0;
    Vars::$USER_SYS['reg_welcome'] = isset($_POST['reg_welcome']);
    Vars::$USER_SYS['reg_email'] = isset($_POST['reg_email']);
    Vars::$USER_SYS['reg_quarantine'] = isset($_POST['reg_quarantine']);

    if (Vars::$USER_RIGHTS == 9) {
        Vars::$USER_SYS['flood_mode'] = isset($_POST['flood_mode']) && $_POST['flood_mode'] > 0 && $_POST['flood_mode'] < 5 ? intval($_POST['flood_mode']) : 1;
        Vars::$USER_SYS['flood_day'] = isset($_POST['flood_day']) ? intval($_POST['flood_day']) : 10;
        Vars::$USER_SYS['flood_night'] = isset($_POST['flood_night']) ? intval($_POST['flood_night']) : 30;
        Vars::$USER_SYS['autologin'] = isset($_POST['autologin']);
        Vars::$USER_SYS['change_nickname'] = isset($_POST['change_nickname']);
        Vars::$USER_SYS['change_period'] = isset($_POST['change_period']) ? intval($_POST['change_period']) : 7;
        Vars::$USER_SYS['change_sex'] = isset($_POST['change_sex']);
        Vars::$USER_SYS['change_status'] = isset($_POST['change_status']);
        Vars::$USER_SYS['digits_only'] = isset($_POST['digits_only']);
        Vars::$USER_SYS['upload_avatars'] = isset($_POST['upload_avatars']);
        Vars::$USER_SYS['view_online'] = isset($_POST['view_online']);
        Vars::$USER_SYS['viev_history'] = isset($_POST['viev_history']);
        Vars::$USER_SYS['view_userlist'] = isset($_POST['view_userlist']);
        Vars::$USER_SYS['view_profiles'] = isset($_POST['view_profiles']);

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
    }

    // Записываем настройки в базу
    mysql_query("REPLACE INTO `cms_settings` SET
        `key` = 'users',
        `val` = '" . mysql_real_escape_string(serialize(Vars::$USER_SYS)) . "'
    ");

    // Подтверждение сохранения настроек
    $tpl->save = 1;
} elseif (isset($_POST['reset'])
    && Vars::$USER_RIGHTS == 9
    && isset($_POST['form_token'])
    && isset($_SESSION['form_token'])
    && $_POST['form_token'] == $_SESSION['form_token']
) {
    @mysql_query("DELETE FROM `cms_settings` WHERE `key` = 'users'");
    header('Location: ' . Vars::$URI . '?act=users_settings&default');
} elseif (isset($_GET['reset']) && Vars::$USER_RIGHTS == 9) {
    $tpl->form_token = mt_rand(100, 10000);
    $_SESSION['form_token'] = $tpl->form_token;
    $tpl->contents = $tpl->includeTpl('users_settings_reset');
    exit;
}
if (isset($_GET['default'])) {
    $tpl->reset = 1;
}

$tpl->contents = $tpl->includeTpl('users_settings');