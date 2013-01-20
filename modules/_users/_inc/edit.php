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

$tpl = Template::getInstance();
$form = new Form($uri);

$form
    ->fieldsetStart(__('profile_edit'))

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

    ->fieldsetStart(__('communication'))

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

    ->fieldsetStart()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUri(3) . 'option/">' . __('back') . '</a>');

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
        Users::$data['birth'] = intval($form->validOutput['year']) . '-' . intval($form->validOutput['month']) . '-' . intval($form->validOutput['day']);
    }

    //TODO: Добавить валидацию даты
    //TODO: Добавить валидацию E-mail

    $STH = DB::PDO()->prepare('
      UPDATE `users` SET
      `sex`      = :sex,
      `imname`   = :imname,
      `birth`    = :birth,
      `live`     = :live,
      `about`    = :about,
      `tel`      = :tel,
      `siteurl`  = :siteurl,
      `email`    = :email,
      `mailvis`  = :mailvis,
      `icq`      = :icq,
      `skype`    = :skype
      WHERE `id` = :id
    ');

    $STH->bindValue(':sex', Users::$data['sex']);
    $STH->bindValue(':imname', Users::$data['imname']);
    $STH->bindValue(':birth', Users::$data['birth']);
    $STH->bindValue(':live', Users::$data['live']);
    $STH->bindValue(':about', Users::$data['about']);
    $STH->bindValue(':tel', Users::$data['tel']);
    $STH->bindValue(':siteurl', Users::$data['siteurl']);
    $STH->bindValue(':email', Users::$data['email']);
    $STH->bindValue(':mailvis', Users::$data['mailvis']);
    $STH->bindValue(':icq', Users::$data['icq']);
    $STH->bindValue(':skype', Users::$data['skype']);
    $STH->bindValue(':id', Users::$data['id']);
    $STH->execute();
    $STH = null;

    $tpl->save = 1;
}

$tpl->contents = $tpl->includeTpl('edit');
