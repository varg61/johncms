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
$uri = Router::getUri(3);

$tpl = Template::getInstance();
$form = new Form($uri);

$form
    ->fieldset(__('language_default'))

    ->add('radio', 'lng', array(
    'checked'     => Vars::$SYSTEM_SET['lng'],
    'description' => __('select_language_help'),
    'items'       => Languages::getInstance()->getLngDescription()))

    ->add('checkbox', 'lngswitch', array(
    'checked'      => Vars::$SYSTEM_SET['lngswitch'],
    'label_inline' => __('allow_choose'),
    'description' => __('allow_choose_help')))

    ->fieldset()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUri(2) . '">' . __('back') . '</a>');

$tpl->form = $form->build();

if ($form->isSubmitted) {
    // Записываем настройки в базу
    $STH = DB::PDO()->prepare('
        REPLACE INTO `cms_settings` SET
        `key` = ?,
        `val` = ?
    ');

    foreach ($form->validOutput as $key => $val) {
        $STH->execute(array($key, $val));
    }

    unset($_SESSION['lng']);
    header('Location: ' . $uri . '?save');
}

$tpl->contents = $tpl->includeTpl('language');