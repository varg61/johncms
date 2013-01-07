<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 7) {
    echo Functions::displayError(__('access_forbidden'));
    exit;
}

$tpl = Template::getInstance();
$settings = isset(Vars::$SYSTEM_SET['news'])
    ? unserialize(Vars::$SYSTEM_SET['news'])
    : array(
        'view'     => 1,
        'breaks'   => 1,
        'smileys'  => 1,
        'tags'     => 1,
        'comments' => 1,
        'size'     => 500,
        'quantity' => 3,
        'days'     => 7
    );

$form = new Form(Router::getUrl(3));

$form
    ->fieldsetStart(__('apperance'))

    ->add('radio', 'view', array(
    'checked' => $settings['view'],
    'items'   => array(
        '1' => __('heading_and_text'),
        '2' => __('heading'),
        '3' => __('text'),
        '0' => __('dont_display')
    )))

    ->fieldsetStart(__('text_processing'))

    ->add('checkbox', 'breaks', array(
    'label_inline' => __('line_foldings'),
    'checked'      => $settings['breaks']))

    ->add('checkbox', 'smileys', array(
    'label_inline' => __('smileys'),
    'checked'      => $settings['smileys']))

    ->add('checkbox', 'tags', array(
    'label_inline' => __('bbcode'),
    'checked'      => $settings['tags']))

    ->add('checkbox', 'comments', array(
    'label_inline' => __('comments'),
    'checked'      => $settings['comments']))

    ->fieldsetStart(__('output'))

    ->add('text', 'quantity', array(
    'label_inline' => __('news_count') . ' <span class="note">(1 - 15)</span>',
    'value'        => $settings['quantity'],
    'maxlength'    => '2',
    'class'        => 'small',
    'filter'       => array(
        'type' => 'int',
        'min'  => 1,
        'max'  => 15
    )))

    ->add('text', 'days', array(
    'label_inline' => __('news_howmanydays_display') . ' <span class="note">(1 - 30)</span>',
    'value'        => $settings['days'],
    'maxlength'    => '2',
    'class'        => 'small',
    'filter'       => array(
        'type' => 'int',
        'min'  => 1,
        'max'  => 30
    )))

    ->add('text', 'size', array(
    'label_inline' => __('text_size') . ' <span class="note">(100 - 5000)</span>',
    'value'        => $settings['size'],
    'maxlength'    => '4',
    'class'        => 'small',
    'filter'       => array(
        'type' => 'int',
        'min'  => 100,
        'max'  => 5000
    )))

    ->fieldsetStart()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->add('submit', 'reset', array(
    'value' => __('reset_settings'),
    'class' => 'btn'))

    ->addHtml('<a class="btn" href="' . Router::getUrl(2) . '">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted && isset($form->input['submit'])) {
    foreach ($form->validOutput as $key => $val) {
        $settings[$key] = $val;
    }

    mysql_query("REPLACE INTO `cms_settings` SET
        `key` = 'news',
        `val` = '" . mysql_real_escape_string(serialize($settings)) . "'
    ");

    $tpl->save = TRUE;
} elseif ($form->isSubmitted && isset($form->input['reset'])) {
    @mysql_query("DELETE FROM `cms_settings` WHERE `key` = 'news'");
    header('Location: ' . Router::getUrl(3) . '?default');
    exit;
}

$tpl->contents = $tpl->includeTpl('admin');