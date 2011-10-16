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

if ($rights == 3 || $rights >= 6) {
    if (empty($_GET['id'])) {
        require('../incfiles/head.php');
        echo functions::display_error($lng['error_wrong_data']);
        require('../incfiles/end.php');
        exit;
    }
    $req = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `id` = '" . $id . "' AND `type` = 't'");
    if (mysql_result($req, 0) > 0) {
        mysql_query("UPDATE `forum` SET  `vip` = '" . (isset($_GET['vip']) ? '1' : '0') . "' WHERE `id` = '$id'");
        header('Location: index.php?id=' . $id);
    } else {
        require('../incfiles/head.php');
        echo functions::display_error($lng['error_wrong_data']);
        require('../incfiles/end.php');
        exit;
    }
}