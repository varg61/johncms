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
Обновление описаний
-----------------------------------------------------------------
*/
if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
    set_time_limit(99999);
    $dir = glob($down_path . '/about/*.txt');
    foreach ($dir as $val) {
        if (isset($_GET['clean'])) {
            @unlink($val);
        } else {
            $file_id = abs(intval(preg_replace('#' . $down_path . '/about/([0-9]+)\.txt#si', '\1', $val, 1)));
            if ($file_id) {
                $text = mysql_real_escape_string(file_get_contents($val));
                mysql_query("UPDATE `cms_download_files` SET `about` = '$text' WHERE `id` = '$file_id' LIMIT 1");
            }
        }
    }
    mysql_query("OPTIMIZE TABLE `cms_download_files`");
    echo '<div class="phdr"><b>' . __('download_scan_about') . '</b></div>';
    if (isset($_GET['clean'])) {
        echo '<div class="rmenu"><p>' . __('scan_about_clean_ok') . '</p></div>';
    } else {
        echo '<div class="gmenu"><p>' . __('scan_about_ok') . '</p></div>' .
        '<div class="rmenu"><a href="' . Router::getUrl(2) . '?act=scan_about&amp;clean&amp;id=' . Vars::$ID . '">' . __('scan_about_clean') . '</a></div>';
    }
	echo '<div class="phdr"><a href="' . Router::getUrl(2) . '?id=' . Vars::$ID . '">' . __('back') . '</a></div>';
} else {
    header('Location: ' . Vars::$HOME_URL . '/404');
}