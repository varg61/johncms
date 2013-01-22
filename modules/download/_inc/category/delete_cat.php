<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

//TODO: Добавить проверку, пустой ли каталог, перед удалением
defined('_IN_JOHNCMS') or die('Error: restricted access');
$url = Router::getUri(2);

/*
-----------------------------------------------------------------
Удаление каталога
-----------------------------------------------------------------
*/
if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
    $del_cat = DB::PDO()->query("SELECT COUNT(*) FROM `cms_download_category` WHERE `refid` = " . VARS::$ID)->fetchColumn();
    $req = DB::PDO()->query("SELECT * FROM `cms_download_category` WHERE `id` = " . VARS::$ID);
    if (!$req->rowCount() || $del_cat) {
        echo Functions::displayError(($del_cat ? __('sub_catalogs') : __('not_found_dir')), '<a href="' . $url . '">' . __('download_title') . '</a>');
        exit;
    }
	$res = $req->fetch();
    if (isset($_GET['yes'])) {
        $req_down = DB::PDO()->query("SELECT * FROM `cms_download_files` WHERE `refid` = " . VARS::$ID);
        while ($res_down = $req_down->fetch()) {
            if (is_dir($screens_path . '/' . $res_down['id'])) {
                $dir_clean = opendir($screens_path . '/' . $res_down['id']);
                while ($file = readdir($dir_clean)) {
                    if ($file != '.' && $file != '..') {
                        @unlink($screens_path . '/' . $res_down['id'] . '/' . $file);
                    }
                }
                closedir($dir_clean);
                rmdir($screens_path . '/' . $res_down['id']);
            }
            @unlink(ROOTPATH . 'files/download/java_icons/' . $res_down['id'] . '.png');
            $req_file_more = DB::PDO()->query("SELECT * FROM `cms_download_more` WHERE `refid` = " . $res_down['id']);
            while ($res_file_more = $req_file_more->fetch()) {
                @unlink($res_down['dir'] . '/' . $res_file_more['name']);
                @unlink(ROOTPATH . 'files/download/java_icons/' . $res_file_more['id'] . '.png');
            }
            @unlink($res_down['dir'] . '/' . $res_down['name']);
            DB::PDO()->exec("DELETE FROM `cms_download_more` WHERE `refid` = " . $res_down['id']);
            DB::PDO()->exec("DELETE FROM `cms_download_comments` WHERE `sub_id` = " . $res_down['id']);
            DB::PDO()->exec("DELETE FROM `cms_download_bookmark` WHERE `file_id` = " . $res_down['id']);
        }
        DB::PDO()->exec("DELETE FROM `cms_download_files` WHERE `refid` = " . VARS::$ID);
        DB::PDO()->exec("DELETE FROM `cms_download_category` WHERE `id` = " . VARS::$ID);

        DB::PDO()->exec("OPTIMIZE TABLE `cms_download_bookmark`, `cms_download_files`, `cms_download_comments`, `cms_download_more`, `cms_download_category`");

        rmdir($res['dir']);
        header('location: ' . $url . '?id=' . $res['refid']);
	} else {
        echo '<div class="phdr"><b>' . __('download_del_cat') . '</b></div>' .
        '<div class="rmenu"><p><a href="' . $url . '?act=delete_cat&amp;id=' . Vars::$ID . '&amp;yes"><b>' . __('delete') . '</b></a></p></div>' .
        '<div class="phdr"><a href="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a></div>';
	}
} else {
	header('Location: ' . Vars::$HOME_URL . '404');
}