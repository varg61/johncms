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
$tpl->settings = unserialize(Vars::$SYSTEM_SET['news']);

$form = new Form(Vars::$URI);

$form
    ->addField('radio', 'view', array(
    'label'   => __('apperance'),
    'checked' => $tpl->settings['view'],
    'items'   => array(
        '1' => __('heading_and_text'),
        '2' => __('heading'),
        '3' => __('text'),
        '0' => __('dont_display')
    )))

    ->addHtml('<br/>')

    ->addField('checkbox', 'breaks', array(
    'label_inline' => __('line_foldings'),
    'checked'      => $tpl->settings['breaks']))

    ->addField('checkbox', 'smileys', array(
    'label_inline' => __('smileys'),
    'checked'      => $tpl->settings['smileys']))

    ->addField('checkbox', 'tags', array(
    'label_inline' => __('bbcode'),
    'checked'      => $tpl->settings['tags']))

    ->addField('checkbox', 'comments', array(
    'label_inline' => __('comments'),
    'checked'      => $tpl->settings['comments']))

    ->addHtml('<br/>')

    ->addField('text', 'size', array(
    'label_inline' => __('text_size') . ' (100 - 5000)',
    'value'        => $tpl->settings['size'],
    'maxlength'    => '4',
    'class'        => 'small'))

    ->addField('text', 'quantity', array(
    'label_inline' => __('news_count') . ' (1 - 15)',
    'value'        => $tpl->settings['quantity'],
    'maxlength'    => '2',
    'class'        => 'mini'))

    ->addField('text', 'days', array(
    'label_inline' => __('news_howmanydays_display') . ' (1 - 30)',
    'value'        => $tpl->settings['days'],
    'maxlength'    => '2',
    'class'        => 'mini'))

    ->addHtml('<br/>')

    ->addField('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml(' <a class="btn" href="' . Vars::$URI . '?reset">' . __('reset_settings') . '</a>')

    ->addHtml('<a class="btn" href="' . Vars::$MODULE_URI . '">' . __('back') . '</a>');

$tpl->form = $form->display();

/*
-----------------------------------------------------------------
Настройки Новостей
-----------------------------------------------------------------
*/
if (!isset(Vars::$SYSTEM_SET['news']) || isset($_GET['reset'])) {
    // Задаем настройки по умолчанию
    $tpl->settings = array(
        'view'     => '1',
        'size'     => '500',
        'quantity' => '3',
        'days'     => '7',
        'breaks'   => '1',
        'smileys'  => '1',
        'tags'     => '1',
        'comments' => '1'
    );
    mysql_query("INSERT INTO `cms_settings` SET
        `key` = 'news',
        `val` = '" . mysql_real_escape_string(serialize($tpl->settings)) . "'
        ON DUPLICATE KEY UPDATE
        `val` = '" . mysql_real_escape_string(serialize($tpl->settings)) . "'
    ");
    $tpl->default = 1;
} elseif (isset($_POST['submit'])) {
    // Принимаем настройки из формы
    $settings['view'] = isset($_POST['view']) && $_POST['view'] >= 0 && $_POST['view'] < 4 ? intval($_POST['view']) : 1;
    $settings['size'] = isset($_POST['size']) && $_POST['size'] >= 100 && $_POST['size'] <= 1000 ? intval($_POST['size']) : 500;
    $settings['quantity'] = isset($_POST['quantity']) && $_POST['quantity'] > 0 && $_POST['quantity'] < 16 ? intval($_POST['quantity']) : 3;
    $settings['days'] = isset($_POST['days']) && $_POST['days'] > 0 && $_POST['days'] < 16 ? intval($_POST['days']) : 7;
    $settings['breaks'] = isset($_POST['breaks']);
    $settings['smileys'] = isset($_POST['smileys']);
    $settings['tags'] = isset($_POST['tags']);
    $settings['comments'] = isset($_POST['comments']);
    mysql_query("UPDATE `cms_settings` SET
        `val` = '" . mysql_real_escape_string(serialize($settings)) . "'
        WHERE `key` = 'news'
    ");
    $tpl->settings = $settings;
    $tpl->saved = 1;
} else {
    // Получаем сохраненные настройки
    $tpl->settings = unserialize(Vars::$SYSTEM_SET['news']);
}

$tpl->contents = $tpl->includeTpl('admin');