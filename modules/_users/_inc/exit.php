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
$uri = Router::getUrl(3);

$tpl = Template::getInstance();
$form = new Form($uri);

$form
    ->fieldsetStart(__('exit_warning'))

    ->add('checkbox', 'clear', array('label_inline' => __('clear_authorisation')))

    ->fieldsetStart()

    ->add('submit', 'submit', array(
    'value' => '   ' . __('exit') . '   ',
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . (isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : Vars::$HOME_URL) . 'option/">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted) {
    Vars::userUnset($form->validOutput['clear']);
    header('Location: ' . Vars::$HOME_URL);
    exit;
}

$tpl->contents = $tpl->includeTpl('exit');