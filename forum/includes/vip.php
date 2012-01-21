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

if (Vars::$USER_RIGHTS == 3 || Vars::$USER_RIGHTS >= 6) {
    if (empty($_GET['id'])) {
        echo Functions::displayError(Vars::$LNG['error_wrong_data']);
        exit;
    }
    $req = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 't'");
    if (mysql_result($req, 0) > 0) {
        mysql_query("UPDATE `forum` SET  `vip` = '" . (isset($_GET['vip']) ? '1' : '0') . "' WHERE `id` = " . Vars::$ID);
        header('Location: index.php?id=' . Vars::$ID);
    } else {
        echo Functions::displayError(Vars::$LNG['error_wrong_data']);
        exit;
    }
}