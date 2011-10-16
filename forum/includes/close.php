<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

if (($rights != 3 && $rights < 6) || !$id) {
    header('Location: index.php');
    exit;
}
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `id` = '$id' AND `type` = 't'"), 0)) {
    if (isset($_GET['closed']))
        mysql_query("UPDATE `forum` SET `edit` = '1' WHERE `id` = '$id'");
    else
        mysql_query("UPDATE `forum` SET `edit` = '0' WHERE `id` = '$id'");
}

header("Location: index.php?id=$id");