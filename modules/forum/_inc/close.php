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
    header('Location: ' . Router::getUri(2));
    exit;
}
if (DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 't'")->fetchColumn()) {
    if (isset($_GET['closed'])) {
        DB::PDO()->exec("UPDATE `forum` SET `edit` = '1' WHERE `id` = " . Vars::$ID);
    } else {
        DB::PDO()->exec("UPDATE `forum` SET `edit` = '0' WHERE `id` = " . Vars::$ID);
    }
}

header("Location: " . Router::getUri(2) . "?id=" . Vars::$ID);