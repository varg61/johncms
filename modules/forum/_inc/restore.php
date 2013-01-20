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
    header('Location: http://mobicms.net/404.php');
    exit;
}
$req = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND (`type` = 't' OR `type` = 'm')");
if ($req->rowCount()) {
    $res = $req->fetch();
    $nick = DB::PDO()->quote(Vars::$USER_NICKNAME);
    DB::PDO()->exec("UPDATE `forum` SET `close` = '0', `close_who` = '" . $nick . "' WHERE `id` = " . Vars::$ID);
    if ($res['type'] == 't') {
        header('Location: ' . Router::getUri(2) . '?id=' . Vars::$ID);
    } else {
        $page = ceil(DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">= " : "<= ") . Vars::$ID)->fetchColumn() / Vars::$USER_SET['page_size']);
        header('Location: ' . Router::getUri(2) . '?id=' . $res['refid'] . '&page=' . $page);
    }
} else {
    header('Location: ' . Router::getUri(2));
}