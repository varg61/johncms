<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
$uri = Router::getUri(3);

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 7) {
    echo Functions::displayError(__('access_forbidden'));
    exit;
}

if (!isset(Vars::$SYSTEM_SET['download'])) {
    // Задаем настройки по умолчанию
    $settings = array(
        'mod'           => 1,
        'theme_screen'  => 1,
        'top'           => 25,
        'icon_java'     => 1,
        'video_screen'  => 1,
        'screen_resize' => 1
    );
    $data = DB::PDO()->quote(serialize($settings));
    DB::PDO()->exec("INSERT INTO `cms_settings` SET `key` = 'download', `val` = " . $data);
} else {
    // Получаем имеющиеся настройки
    $settings = unserialize(Vars::$SYSTEM_SET['download']);
}

$tpl = Template::getInstance();
$form = new Form($uri);

$form
    ->fieldset(__('functions_download'))

    ->add('checkbox', 'mod', array(
    'checked'      => $settings['mod'],
    'label_inline' => __('set_files_mod')))

    ->add('checkbox', 'theme_screen', array(
    'checked'      => $settings['theme_screen'],
    'label_inline' => __('set_auto_screen')))

    ->add('checkbox', 'video_screen', array(
    'checked'      => $settings['video_screen'],
    'label_inline' => __('set_auto_screen_video')))

    ->add('checkbox', 'icon_java', array(
    'checked'      => $settings['icon_java'],
    'label_inline' => __('set_java_icons')))

    ->add('checkbox', 'screen_resize', array(
    'checked'      => $settings['screen_resize'],
    'label_inline' => __('set_screen_resize')))

    ->add('text', 'top', array(
    'value'        => $settings['top'],
    'label_inline' => __('set_top_files'),
    'class'        => 'small',
    'filter'       => array(
        'type' => 'int',
        'min'  => 25,
        'max'  => 100
    )))

    ->fieldset()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Vars::$HOME_URL . 'admin/' . '">' . __('back') . '</a>');

$tpl->form = $form->build();

if ($form->isValid) {
    // Записываем настройки в базу
    $data = DB::PDO()->quote(serialize($form->output));
    DB::PDO()->exec("UPDATE `cms_settings` SET `val` = " . $data . " WHERE `key` = 'download'");
    $tpl->save = 1;
}

$tpl->contents = $tpl->includeTpl('admin');