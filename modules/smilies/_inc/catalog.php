<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

$tpl = Template::getInstance();


$link = Router::getUrl(2);

if(!isset($catalog)){
    $catalog = array();
}
asort($catalog);

foreach ($catalog as $key => $val) {
    $tpl->list[] = array(
        'link' => $link . '/' . urlencode($key),
        'name' => htmlspecialchars($val),
        'count' => count(glob(ROOTPATH . 'assets' . DIRECTORY_SEPARATOR . 'smilies' . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . '*.{gif,jpg,png}', GLOB_BRACE))
    );
}

$tpl->contents = $tpl->includeTpl('catalog');