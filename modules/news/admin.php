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

$form = new Form(Vars::$URI);
$form
    ->addField('radio', 'view', array(
    'label'   => __('apperance'),
    'checked' => $settings['view'],
    'items'   => array(
        '1' => __('heading_and_text'),
        '2' => __('heading'),
        '3' => __('text'),
        '0' => __('dont_display')
    )))

    ->addHtml('<br/>')

    ->addField('checkbox', 'breaks', array(
    'label_inline' => __('line_foldings'),
    'checked'      => $settings['breaks']))

    ->addField('checkbox', 'smileys', array(
    'label_inline' => __('smileys'),
    'checked'      => $settings['smileys']))

    ->addField('checkbox', 'tags', array(
    'label_inline' => __('bbcode'),
    'checked'      => $settings['tags']))

    ->addField('checkbox', 'comments', array(
    'label_inline' => __('comments'),
    'checked'      => $settings['comments']))

    ->addHtml('<br/>')

    ->addField('text', 'size', array(
    'label_inline' => __('text_size') . ' (100 - 5000)',
    'value'        => $settings['size'],
    'maxlength'    => '4',
    'class'        => 'small'))

    ->addField('text', 'quantity', array(
    'label_inline' => __('news_count') . ' (1 - 15)',
    'value'        => $settings['quantity'],
    'maxlength'    => '2',
    'class'        => 'mini'))

    ->addField('text', 'days', array(
    'label_inline' => __('news_howmanydays_display') . ' (1 - 30)',
    'value'        => $settings['days'],
    'maxlength'    => '2',
    'class'        => 'mini'))

    ->addHtml('<br/>')

    ->addField('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addField('submit', 'reset', array(
    'value' => __('reset_settings'),
    'class' => 'btn'))

    ->addHtml('<a class="btn" href="' . Vars::$MODULE_URI . '">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->submit && isset($form->input['submit'])) {
    foreach ($form->validInput as $key => $val) {
        $settings[$key] = $val;
    }

    // Проверяем принятые данные
    if ($settings['size'] < 100) {
        $settings['size'] = 100;
    } elseif ($settings['size'] > 5000) {
        $settings['size'] = 5000;
    }

    if ($settings['quantity'] < 1) {
        $settings['quantity'] = 1;
    } elseif ($settings['quantity'] > 15) {
        $settings['quantity'] = 15;
    }

    if ($settings['days'] < 1) {
        $settings['days'] = 1;
    } elseif ($settings['days'] > 30) {
        $settings['days'] = 30;
    }

    mysql_query("REPLACE INTO `cms_settings` SET
        `key` = 'news',
        `val` = '" . mysql_real_escape_string(serialize($settings)) . "'
    ");

    $tpl->save = true;
} elseif ($form->submit && isset($form->input['reset'])) {
    @mysql_query("DELETE FROM `cms_settings` WHERE `key` = 'news'");
    header('Location: ' . Vars::$URI . '?default');
    exit;
}

$tpl->contents = $tpl->includeTpl('admin');