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
Удаление файл
-----------------------------------------------------------------
*/
$req_down = DB::PDO()->query("SELECT * FROM `cms_download_files` WHERE `id` = '" . VARS::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = $req_down->fetch();
if (!$req_down->rowCount() || !is_file($res_down['dir'] . '/' . $res_down['name'])) {
    echo Functions::displayError(__('not_found_file'), '<a href="' . $url . '">' . __('download_title') . '</a>');
    exit;
}
if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
    if (isset($_GET['yes'])) {
        if (is_dir($screens_path . '/' . Vars::$ID)) {
            $dir_clean = opendir($screens_path . '/' . Vars::$ID);
            while ($file = readdir($dir_clean)) {
                if ($file != '.' && $file != '..') {
                    @unlink($screens_path . '/' . Vars::$ID . '/' . $file);
                }
            }
            closedir($dir_clean);
            rmdir($screens_path . '/' . Vars::$ID);
        }
        @unlink(ROOTPATH . 'files/download/java_icons/' . Vars::$ID . '.png');
        $req_file_more = DB::PDO()->query("SELECT * FROM `cms_download_more` WHERE `refid` = " . Vars::$ID);
        if ($req_file_more->rowCount()) {
            while ($res_file_more = $req_file_more->fetch()) {
                if (is_file($res_down['dir'] . '/' . $res_file_more['name']))
                    @unlink($res_down['dir'] . '/' . $res_file_more['name']);
                @unlink(ROOTPATH . 'files/download/java_icons/' . $res_file_more['id'] . '_' . Vars::$ID . '.png');
            }
            DB::PDO()->exec("DELETE FROM `cms_download_more` WHERE `refid` = " . Vars::$ID);
        }
        DB::PDO()->exec("DELETE FROM `cms_download_bookmark` WHERE `file_id` = " . Vars::$ID);
        DB::PDO()->exec("DELETE FROM `cms_download_comments` WHERE `sub_id` = " . Vars::$ID);
        @unlink($res_down['dir'] . '/' . $res_down['name']);
        $dirid = $res_down['refid'];
        $sql = '';
        $i = 0;
        while ($dirid != '0' && $dirid != "") {
            $res = DB::PDO()->query("SELECT `refid` FROM `cms_download_category` WHERE `id` = '$dirid' LIMIT 1")->fetch();
            if ($i) $sql .= ' OR ';
            $sql .= '`id` = \'' . $dirid . '\'';
            $dirid = $res['refid'];
            ++$i;
        }
        DB::PDO()->exec("UPDATE `cms_download_category` SET `total` = (`total`-1) WHERE $sql");
        DB::PDO()->exec("DELETE FROM `cms_download_files` WHERE `id` = " . Vars::$ID);
        DB::PDO()->query("OPTIMIZE TABLE `cms_download_files`");
        header('Location: ' . $url . '?id=' . $res_down['refid']);
    } else {
        echo '<div class="phdr"><b>' . __('delete_file') . '</b></div>' .
            '<div class="rmenu"><p><a href="' . $url . '?act=delete_file&amp;id=' . Vars::$ID . '&amp;yes"><b>' . __('delete') . '</b></a></p></div>' .
            '<div class="phdr"><a href="' . $url . '?act=view&amp;id=' . Vars::$ID . '">' . __('back') . '</a></div>';
    }
} else {
    header('Location: ' . Vars::$HOME_URL . '404');
}