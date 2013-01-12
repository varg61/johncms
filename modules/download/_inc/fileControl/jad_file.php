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
$url = Router::getUri(2);

/*
-----------------------------------------------------------------
Качаем JAD файл
-----------------------------------------------------------------
*/
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . VARS::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
if (mysql_num_rows($req_down) == 0 || !is_file($res_down['dir'] . '/' . $res_down['name']) || (functions::format($res_down['name']) != 'jar' && !isset($_GET['more'])) || ($res_down['type'] == 3 && Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
    echo Functions::displayError(__('not_found_file'), '<a href="' . $url . '">' . __('download_title') . '</a>');
    exit;
}
if (isset($_GET['more'])) {
    $more = abs(intval($_GET['more']));
    $req_more = mysql_query("SELECT * FROM `cms_download_more` WHERE `id` = '$more' LIMIT 1");
    $res_more = mysql_fetch_assoc($req_more);
    if (!mysql_num_rows($req_more) || !is_file($res_down['dir'] . '/' . $res_more['name']) || functions::format($res_more['name']) != 'jar') {
        echo Functions::displayError(__('not_found_file'), '<a href="' . $url . '">' . __('download_title') . '</a>');
        exit;
    }
    $down_file = $res_down['dir'] . '/' . $res_more['name'];
    $jar_file = $res_more['name'];
} else {
    $down_file = $res_down['dir'] . '/' . $res_down['name'];
    $jar_file = $res_down['name'];
}
if (!isset($_SESSION['down_' . VARS::$ID])) {
    mysql_query("UPDATE `cms_download_files` SET `field`=`field`+1 WHERE `id`=" . VARS::$ID);
    $_SESSION['down_' . VARS::$ID] = 1;
}
$size = filesize($down_file);
require(SYSPATH . 'lib/pclzip.lib.php');
$zip = new PclZip($down_file);
$content = $zip->extract(PCLZIP_OPT_BY_NAME, 'META-INF/MANIFEST.MF', PCLZIP_OPT_EXTRACT_AS_STRING);
$tpl = Template::getInstance();
$tpl->template = FALSE;
$out = $content[0]['content'] . "\n" . 'MIDlet-Jar-Size: ' . $size . "\n" . 'MIDlet-Jar-URL: ' . Vars::$HOME_URL . $res_down['dir'] . '/' . $jar_file;
Functions::downloadFile($out, basename($down_file) . '.jad');