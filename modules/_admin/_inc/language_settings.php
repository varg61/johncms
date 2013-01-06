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

// Подготавливаем список имеющихся языков
$items['#'] = __('select_automatically');
foreach (Languages::getInstance()->getLngDescription() as $key => $val) {
    $items[$key] = Functions::loadImage('flag_' . $key . '.gif') . '&#160; ' . $val;
}

$tpl = Template::getInstance();
$form = new Form(Router::getUrl(3));

$form
    ->fieldsetStart(__('language_default'))

    ->add('radio', 'iso', array(
    'checked'     => Vars::$SYSTEM_SET['lng'],
    'description' => '<br/>' . __('select_language_help'),
    'items'       => $items))

    ->fieldsetStart()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUrl(2) . '">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted && isset($form->validOutput['iso'])) {
    if (in_array($form->validOutput['iso'], Languages::getInstance()->getLngList()) || $form->validOutput['iso'] == '#') {
        Vars::$SYSTEM_SET['lng'] = $form->validOutput['iso'];
        mysql_query("REPLACE INTO `cms_settings` SET `key` = 'lng', `val` = '" . mysql_real_escape_string($form->validOutput['iso']) . "'");
        $tpl->save = 1;
    }
}

$tpl->contents = $tpl->includeTpl('language_settings');