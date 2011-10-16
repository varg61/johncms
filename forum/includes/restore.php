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
    header('Location: http://johncms.com?act=404');
    exit;
}
$req = mysql_query("SELECT * FROM `forum` WHERE `id` = '$id' AND (`type` = 't' OR `type` = 'm')");
if (mysql_num_rows($req)) {
    $res = mysql_fetch_assoc($req);
    mysql_query("UPDATE `forum` SET `close` = '0', `close_who` = '$login' WHERE `id` = '$id'");
    if ($res['type'] == 't') {
        header('Location: index.php?id=' . $id);
    } else {
        $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '" . $id . "'"), 0) / $kmess);
        header('Location: index.php?id=' . $res['refid'] . '&page=' . $page);
    }
} else {
    header('Location: index.php');
}