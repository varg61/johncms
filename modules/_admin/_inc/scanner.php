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
define('ROOT_DIR', '.');

$tpl = Template::getInstance();
$scanner = new Scanner();
$form = new Form(Router::getUrl(3));

if (file_exists(CONFIGPATH . 'scanner.php')) {
    include(CONFIGPATH . 'scanner.php');
    if (!isset($scanFolders)
        || empty($scanFolders)
        || !isset($whiteList)
        || empty($whiteList)
    ) {
        $tpl->errormsg = 'ERROR: Scanner database empty';
    } else {
        $scanner->folders = $scanFolders;
        $scanner->whiteList = $whiteList;
    }
} else {
    $tpl->errormsg = 'ERROR: Scanner configuration file missing';
}

$form
    ->fieldsetStart(__('select_action'))

    ->add('radio', 'mode', array(
    'checked' => 1,
    'items'   => array(
        '1' => __('antispy_dist_scan'),
        '2' => __('antispy_snapshot_scan'),
        '3' => __('antispy_snapshot_create')
    )))

    ->fieldsetStart()

    ->add('submit', 'submit', array(
    'value' => __('do'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUrl(2) . '">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted) {
    switch ($form->validOutput['mode']) {
        case 1:
            // Сканируем на соответствие дистрибутиву
            $scanner->scan();
            if (count($scanner->bad_files)) {
                $tpl->files = $scanner->bad_files;
                $tpl->errormsg = __('antispy_dist_inconsistency');
            } else {
                $tpl->ok = __('antispy_dist_scan_good');
            }
            break;

        case 2:
            // Сканируем на соответствие ранее созданному снимку
            $scanner->snapscan();
            if (count($scanner->track_files) == 0) {
                $tpl->errormsg = __('antispy_no_snapshot');
            } else {
                if (count($scanner->bad_files)) {
                    $tpl->files = $scanner->bad_files;
                    $tpl->errormsg = __('antispy_snp_inconsistency');
                } else {
                    $tpl->ok = __('antispy_snapshot_scan_ok');
                }
            }
            break;

        case 3:
            // Создаем снимок файлов
            $scanner->snap();
            $tpl->ok = __('antispy_snapshot_create_ok');
            break;
    }
}

$tpl->contents = $tpl->includeTpl('scanner');