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

$tpl = Template::getInstance();
$form = new Form(Router::getUri(3));

$form
    ->fieldset(__('update_cache'))

    ->addHtml('<br/>')

    ->add('submit', 'submit', array(
    'value' => __('do'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUri(2) . '">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted) {
    $cache = array();
    $smilies = glob(ROOTPATH . 'assets' . DIRECTORY_SEPARATOR . 'smilies' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*.{gif,jpg,png}', GLOB_BRACE);
    foreach ($smilies as $val) {
        $file = basename($val);
        $name = explode(".", $file);
        $parent = basename(dirname($val));
        $image = '<img src="' . Vars::$HOME_URL . 'assets/smilies/' . $parent . '/' . $file . '" alt="" />';
        if ($parent == '_admin') {
            $cache['adm_s'][] = '/:' . preg_quote($name[0]) . ':/';
            $cache['adm_r'][] = $image;
            $cache['adm_s'][] = '/:' . preg_quote(Functions::translit($name[0])) . ':/';
            $cache['adm_r'][] = $image;
        } elseif ($parent == '_simply') {
            $cache['usr_s'][] = '/:' . preg_quote($name[0]) . '/';
            $cache['usr_r'][] = $image;
        } else {
            $cache['usr_s'][] = '/:' . preg_quote($name[0]) . ':/';
            $cache['usr_r'][] = $image;
            $cache['usr_s'][] = '/:' . preg_quote(Functions::translit($name[0])) . ':/';
            $cache['usr_r'][] = $image;
        }
    }

    if (file_put_contents(CACHEPATH . 'smilies.dat', serialize($cache))) {
        $tpl->save = __('cache_updated');
    } else {
        $tpl->error = __('error_cache_update');
    }
}

$tpl->contents = $tpl->includeTpl('smilies');