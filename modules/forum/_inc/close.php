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

if ((Vars::$USER_RIGHTS != 3 && Vars::$USER_RIGHTS < 6) || !Vars::$ID) {
    header('Location: ' . Router::getUrl(2));
    exit;
}
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 't'"), 0)) {
    if (isset($_GET['closed']))
        mysql_query("UPDATE `forum` SET `edit` = '1' WHERE `id` = " . Vars::$ID);
    else
        mysql_query("UPDATE `forum` SET `edit` = '0' WHERE `id` = " . Vars::$ID);
}

header("Location: " . Router::getUrl(2) . "?id=" . Vars::$ID);