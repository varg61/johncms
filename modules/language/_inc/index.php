<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_LNG') or die('Error: restricted access');
$uri = Router::getUri(2);

$tpl = Template::getInstance();
$form = new Form($uri);

$form
    ->fieldset(__('language_select'))

    ->add('radio', 'lng', array(
    'checked'     => Languages::getInstance()->getCurrentISO(),
    'items'       => Languages::getInstance()->getLngDescription()))

    ->fieldset()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Vars::$HOME_URL . '">' . __('back') . '</a>');

$tpl->form = $form->build();

if($form->isSubmitted){
    $_SESSION['lng'] = $form->validOutput['lng'];
    header('Location: ' . $uri . '?save');
}

$tpl->contents = $tpl->includeTpl('index');