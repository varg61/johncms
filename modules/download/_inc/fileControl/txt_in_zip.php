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
Скачка TXT файла в ZIP
-----------------------------------------------------------------
*/
$dir_clean = opendir(ROOTPATH . 'files/download/temp/created_zip');
while ($file = readdir($dir_clean)) {
    if ($file != 'index.php' && $file != '.htaccess' && $file != '.' && $file != '..') {
        $time_file = filemtime(ROOTPATH . 'files/download/temp/created_zip/' . $file);
        if ($time_file < (time() - 300))
            @unlink(ROOTPATH . 'files/download/temp/created_zip/' . $file);
    }
}
closedir($dir_clean);
$req_down = DB::PDO()->query("SELECT * FROM `cms_download_files` WHERE `id` = '" . VARS::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = $req_down->fetch();
if (!$req_down->rowCount() || !is_file($res_down['dir'] . '/' . $res_down['name']) || (functions::format($res_down['name']) != 'txt' && !isset($_GET['more'])) || ($res_down['type'] == 3 && Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
    echo Functions::displayError(__('not_found_file'), '<a href="' . $url . '">' . __('download_title') . '</a>');
    exit;
}
if (isset($_GET['more'])) {
    $more = abs(intval($_GET['more']));
    $req_more = DB::PDO()->query("SELECT * FROM `cms_download_more` WHERE `id` = '$more' LIMIT 1");
    $res_more = $req_more->fetch();
    if (!$req_more->rowCount() || !is_file($res_down['dir'] . '/' . $res_more['name']) || functions::format($res_more['name']) != 'txt') {
        echo Functions::displayError(__('not_found_file'), '<a href="' . $url . '">' . __('download_title') . '</a>');
        exit;
    }
    $down_file = $res_down['dir'] . '/' . $res_more['name'];
    $title_pages = $res_more['rus_name'];
    $txt_file = $res_more['name'];
} else {
    $down_file = $res_down['dir'] . '/' . $res_down['name'];
    $title_pages = $res_down['rus_name'];
    $txt_file = $res_down['name'];
}
if (!isset($_SESSION['down_' . VARS::$ID])) {
    DB::PDO()->exec("UPDATE `cms_download_files` SET `field`=`field`+1 WHERE `id`=" . VARS::$ID);
    $_SESSION['down_' . VARS::$ID] = 1;
}
$file = 'files/download/temp/created_zip/' . $txt_file . '.zip';
if (!file_exists($file)) {
    require(SYSPATH . 'lib/pclzip.lib.php');
    $zip = new PclZip($file);
    function w($event, &$header)
    {
        $header['stored_filename'] = basename($header['filename']);
        return 1;
    }

    $zip->create($down_file, PCLZIP_CB_PRE_ADD, 'w');
    chmod($file, 0644);
}
/*
-----------------------------------------------------------------
Ссылка на файл
-----------------------------------------------------------------
*/
echo '<div class="phdr"><b>' . Validate::checkout($title_pages) . '</b></div>' .
    '<div class="menu"><a href="' . Validate::checkout($file) . '">' . __('download_in') . ' ZIP</a></div>' .
    '<div class="rmenu"><input type="text" value="' . Vars::$HOME_URL . Validate::checkout($file) . '"/><b></b></div>' .
    '<div class="phdr">' . __('time_limit') . '</div>' .
    '<p><a href="' . $url . '?act=view&amp;id=' . Vars::$ID . '">' . __('back') . '</a></p>';