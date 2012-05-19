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
Удаление файл
-----------------------------------------------------------------
*/
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . VARS::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
if (mysql_num_rows($req_down) == 0 || !is_file($res_down['dir'] . '/' . $res_down['name'])) {
    echo Functions::displayError(lng('not_found_file'), '<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
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
        $req_file_more = mysql_query("SELECT * FROM `cms_download_more` WHERE `refid` = '" . Vars::$ID . "'");
        if (mysql_num_rows($req_file_more)) {
            while ($res_file_more = mysql_fetch_assoc($req_file_more)) {
                if (is_file($res_down['dir'] . '/' . $res_file_more['name']))
                    @unlink($res_down['dir'] . '/' . $res_file_more['name']);
            	@unlink(ROOTPATH . 'files/download/java_icons/' . $res_file_more['id'] . '_' . Vars::$ID . '.png');
            }
            mysql_query("DELETE FROM `cms_download_more` WHERE `refid` = '" . Vars::$ID . "'");
        }
		mysql_query("DELETE FROM `cms_download_bookmark` WHERE `file_id`='" . Vars::$ID . "'");
		mysql_query("DELETE FROM `cms_download_comments` WHERE `sub_id`='" . Vars::$ID . "'");
        @unlink($res_down['dir'] . '/' . $res_down['name']);
        $dirid = $res_down['refid'];
        $sql = '';
        $i = 0;
        while ($dirid != '0' && $dirid != "") {
            $res = mysql_fetch_assoc(mysql_query("SELECT `refid` FROM `cms_download_files` WHERE `type` = 1 AND `id` = '$dirid' LIMIT 1"));
            if ($i) $sql .= ' OR ';
            $sql .= '`id` = \'' . $dirid . '\'';
            $dirid = $res['refid'];
            ++$i;
        }
        mysql_query("UPDATE `cms_download_files` SET `total` = (`total`-1) WHERE $sql");
        mysql_query("DELETE FROM `cms_download_files` WHERE `id` = '" . Vars::$ID . "' LIMIT 1");
        mysql_query("OPTIMIZE TABLE `cms_download_files`");
        header('Location: ' . Vars::$URI . '?id=' . $res_down['refid']);
    } else {
        echo '<div class="phdr"><b>' . lng('delete_file') . '</b></div>' .
        '<div class="rmenu"><p><a href="' . Vars::$URI . '?act=delete_file&amp;id=' . Vars::$ID . '&amp;yes"><b>' . lng('delete') . '</b></a></p></div>' .
        '<div class="phdr"><a href="' . Vars::$URI . '?act=view&amp;id=' . Vars::$ID . '">' . lng('back') . '</a></div>';
    }
} else {
    header('Location: ' . Vars::$HOME_URL . '/404');
}