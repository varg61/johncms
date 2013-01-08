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

$tpl = Template::getInstance();
$form = new Form($uri);

$form
    ->add('text', 'imname', array(
    'label'       => __('name'),
    'value'       => Users::$data['imname'],
    'description' => __('description_name')));

if (Vars::$USER_SYS['change_sex'] || Vars::$USER_RIGHTS >= 7) {
    $form
        ->add('radio', 'sex', array(
        'label'   => __('sex'),
        'checked' => Users::$data['sex'],
        'items'   => array(
            'm' => __('sex_m'),
            'w' => __('sex_w')
        )));
}

$form
    ->add('text', 'day', array(
    'label' => __('birthday'),
    'value' => date("d", strtotime(Users::$data['birth'])),
    'class' => 'mini'))

    ->add('text', 'month', array(
    'value' => date("m", strtotime(Users::$data['birth'])),
    'class' => 'mini'))

    ->add('text', 'year', array(
    'value'       => date("Y", strtotime(Users::$data['birth'])),
    'class'       => 'small',
    'description' => __('description_birth')))

    ->add('text', 'live', array(
    'label'       => __('live'),
    'value'       => Users::$data['live'],
    'description' => __('description_live')))

    ->add('textarea', 'about', array(
    'label'       => __('about'),
    'value'       => Users::$data['about'],
    'buttons'     => (Vars::$IS_MOBILE ? FALSE : TRUE),
    'description' => __('description_about')))

    ->add('text', 'tel', array(
    'label'       => __('phone_number'),
    'value'       => Users::$data['tel'],
    'description' => __('description_phone_number')))

    ->add('text', 'email', array(
    'label' => 'E-mail',
    'value' => Users::$data['email']))

    ->add('checkbox', 'mailvis', array(
    'label_inline' => __('show_in_profile'),
    'checked'      => Users::$data['mailvis'],
    'description'  => __('description_email')))

    ->add('text', 'siteurl', array(
    'label'       => __('site'),
    'value'       => Users::$data['siteurl'],
    'description' => __('description_siteurl')))

    ->add('text', 'skype', array(
    'label'       => 'Skype',
    'value'       => Users::$data['skype'],
    'description' => __('description_skype')))

    ->add('text', 'icq', array(
    'label'       => 'ICQ',
    'value'       => Users::$data['icq'],
    'description' => __('description_icq')))

    ->addHtml('<br/>')

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUrl(3) . 'settings/">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted) {
    foreach ($form->validOutput as $key => $val) {
        Users::$data[$key] = $val;
    }

    // Принимаем и обрабатываем дату рожденья
    if (empty($form->validOutput['day'])
        && empty($form->validOutput['month'])
        && empty($form->validOutput['year'])
    ) {
        // Удаляем дату рожденья
        Users::$data['birth'] = '00-00-0000';
    } else {
        Users::$data['birth'] = intval($tpl->year) . '-' . intval($tpl->month) . '-' . intval($tpl->day);
    }

    //TODO: Добавить валидацию даты
    //TODO: Добавить валидацию E-mail

    if (empty($tpl->error)) {
        mysql_query("UPDATE `users` SET
                `sex` = '" . Users::$data['sex'] . "',
                `imname` = '" . mysql_real_escape_string(Users::$data['imname']) . "',
                `birth` = '" . Users::$data['birth'] . "',
                `live` = '" . mysql_real_escape_string(Users::$data['live']) . "',
                `about` = '" . mysql_real_escape_string(Users::$data['about']) . "',
                `tel` = '" . mysql_real_escape_string(Users::$data['tel']) . "',
                `siteurl` = '" . mysql_real_escape_string(Users::$data['siteurl']) . "',
                `email` = '" . mysql_real_escape_string(Users::$data['email']) . "',
                `mailvis` = " . Users::$data['mailvis'] . ",
                `icq` = " . Users::$data['icq'] . ",
                `skype` = '" . mysql_real_escape_string(Users::$data['skype']) . "'
                WHERE `id` = " . Users::$data['id']
        ) or exit('MYSQL: ' . mysql_error());
        $tpl->save = 1;
    }
}

$tpl->contents = $tpl->includeTpl('edit_profile');
