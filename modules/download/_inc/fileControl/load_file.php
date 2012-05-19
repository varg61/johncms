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
/*
-----------------------------------------------------------------
Скачка файла
-----------------------------------------------------------------
*/
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . VARS::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
if (!mysql_num_rows($req_down) || !is_file($res_down['dir'] . '/' . $res_down['name']) || ($res_down['type'] == 3 && Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
    $error = true;
} else {
    $link = $res_down['dir'] . '/' . $res_down['name'];
}
$more = isset($_GET['more']) ? abs(intval($_GET['more'])) : false;
if ($more) {
    $req_more = mysql_query("SELECT * FROM `cms_download_more` WHERE `refid` = '" . VARS::$ID . "' AND `id` = '$more' LIMIT 1");
    $res_more = mysql_fetch_assoc($req_more);
    if (!mysql_num_rows($req_more) || !is_file($res_down['dir'] . '/' . $res_more['name'])) {
        $error = true;
    } else {
        $link = $res_down['dir'] . '/' . $res_more['name'];
    }
}
if ($error) {
    header('Location: ' . Vars::$HOME_URL . '/404');
} else {
    if (!isset($_SESSION['down_' . VARS::$ID])) {
        mysql_query("UPDATE `cms_download_files` SET `field`=`field`+1 WHERE `id`=" . VARS::$ID);
    	$_SESSION['down_' . VARS::$ID] = 1;
	}
    header('Location: ' . $link);
}