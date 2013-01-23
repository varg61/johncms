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
    ->fieldsetStart(__('forum'))

    ->add('radio', 'forum', array(
    'checked' => Vars::$ACL['forum'],
    'items'   => array(
        '2' => __('access_enabled'),
        '1' => __('access_authorised'),
        '3' => __('read_only'),
        '0' => __('access_disabled')
    )))

    ->fieldsetStart(__('photo_albums'))

    ->add('radio', 'album', array(
    'checked' => Vars::$ACL['album'],
    'items'   => array(
        '2' => __('access_enabled'),
        '1' => __('access_authorised'),
        '0' => __('access_disabled')
    )))

    ->add('checkbox', 'albumcomm', array(
    'label_inline' => __('comments'),
    'checked'      => Vars::$ACL['albumcomm']))

    ->fieldsetStart(__('guestbook'))

    ->add('radio', 'guestbook', array(
    'checked' => Vars::$ACL['guestbook'],
    'items'   => array(
        '2' => __('access_enabled_for_guests'),
        '1' => __('access_enabled'),
        '0' => __('access_disabled')
    )))

    ->fieldsetStart(__('library'))

    ->add('radio', 'library', array(
    'checked' => Vars::$ACL['library'],
    'items'   => array(
        '2' => __('access_enabled'),
        '1' => __('access_authorised'),
        '0' => __('access_disabled')
    )))

    ->add('checkbox', 'libcomm', array(
    'label_inline' => __('comments'),
    'checked'      => Vars::$ACL['libcomm']))

    ->fieldsetStart(__('downloads'))

    ->add('radio', 'downloads', array(
    'checked' => Vars::$ACL['downloads'],
    'items'   => array(
        '2' => __('access_enabled'),
        '1' => __('access_authorised'),
        '0' => __('access_disabled')
    )))

    ->add('checkbox', 'downcomm', array(
    'label_inline' => __('comments'),
    'checked'      => Vars::$ACL['downcomm']))

    ->fieldsetStart()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUri(2) . '">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted) {
    foreach ($form->validOutput as $key => $val) {
        Vars::$ACL[$key] = $val;
    }

    // Записываем настройки в базу
    $STH = DB::PDO()->prepare('
        REPLACE INTO `cms_settings` SET
        `key` = :key,
        `val` = :val
    ');

    $STH->bindValue(':key', 'acl');
    $STH->bindValue(':val', serialize(Vars::$ACL));
    $STH->execute();

    $tpl->save = TRUE;
}

$tpl->contents = $tpl->includeTpl('access_control');