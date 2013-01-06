<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_PROFILE') or die('Error: restricted access');

$tpl = Template::getInstance();

$tpl->url = Router::getUrl(2);
if (is_file(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $tpl->user['id'] . '.gif')) {
    $tpl->avatar = TRUE;
}
$tpl->contents = $tpl->includeTpl('profile_settings');