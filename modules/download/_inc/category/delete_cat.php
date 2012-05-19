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
/*
-----------------------------------------------------------------
Удаление каталога
-----------------------------------------------------------------
*/
if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
    $req = mysql_query("SELECT * FROM `cms_download_files` WHERE `type` = 1 AND `id` = '" . VARS::$ID . "' LIMIT 1");
    $del_cat = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = 1 AND`refid` = '" . VARS::$ID . "'"), 0);
    if (!mysql_num_rows($req) || $del_cat) {
        echo Functions::displayError(($del_cat ? lng('sub_catalogs') : lng('not_found_dir')), '<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
        exit;
    }
	$res = mysql_fetch_assoc($req);
    if (isset($_GET['yes'])) {
        $req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `refid` = '" . VARS::$ID . "'");
        while ($res_down = mysql_fetch_assoc($req_down)) {
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
            $req_file_more = mysql_query("SELECT * FROM `cms_download_more` WHERE `refid` = '" . $res_down['id'] . "'");
            while ($res_file_more = mysql_fetch_assoc($req_file_more)) {
                @unlink($res_down['dir'] . '/' . $res_file_more['name']);
                @unlink(ROOTPATH . 'files/download/java_icons/' . $res_file_more['id'] . '.png');
            }
            @unlink($res_down['dir'] . '/' . $res_down['name']);
            mysql_query("DELETE FROM `cms_download_more` WHERE `refid`='" . $res_down['id'] . "'");
            mysql_query("DELETE FROM `cms_download_comments` WHERE `sub_id`='" . $res_down['id'] . "'");
            mysql_query("DELETE FROM `cms_download_bookmark` WHERE `file_id`='" . $res_down['id'] . "'");
        }
        mysql_query("DELETE FROM `cms_download_files` WHERE `refid` = '" . VARS::$ID . "' OR `id` = '" . VARS::$ID . "'");
        rmdir($res['dir'] . '/' . $res['name']);
        header('location: ' . Vars::$URI . '?id=' . $res['refid']);
	} else {
        echo '<div class="phdr"><b>' . lng('download_del_cat') . '</b></div>' .
        '<div class="rmenu"><p><a href="' . Vars::$URI . '?act=delete_cat&amp;id=' . Vars::$ID . '&amp;yes"><b>' . lng('delete') . '</b></a></p></div>' .
        '<div class="phdr"><a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . lng('back') . '</a></div>';
	}
} else {
	header('Location: ' . Vars::$HOME_URL . '/404');
}