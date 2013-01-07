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
$url = Router::getUrl(2);

/*
-----------------------------------------------------------------
Обновление файлов
-----------------------------------------------------------------
*/
if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
    set_time_limit(99999);
    $do = isset($_GET['do']) ? trim($_GET['do']) : '';
    $mod = isset($_GET['mod']) ? intval($_GET['mod']) : '';
    switch ($do) {
        case 'clean':
            /*
               -----------------------------------------------------------------
               Удаляем отсутствующие файлы
               -----------------------------------------------------------------
               */
            $query = mysql_query("SELECT `id`, `dir`, `name`, `type` FROM `cms_download_files`");
            while ($result = mysql_fetch_assoc($query)) {
                if (!file_exists($result['dir'] . '/' . $result['name'])) {
                    $req = mysql_query("SELECT `id` FROM `cms_download_more` WHERE `refid` = '" . $result['id'] . "'");
                    while ($res = mysql_fetch_assoc($req)) {
                    	@unlink($result['dir'] . '/' . $res['name']);
					}
     				mysql_query("DELETE FROM `cms_download_bookmark` WHERE `file_id`='" . $result['id'] . "'");
                    mysql_query("DELETE FROM `cms_download_more` WHERE `refid` = '" . $result['id'] . "'");
                    mysql_query("DELETE FROM `cms_download_comments` WHERE `sub_id`='" . $result['id'] . "'");
                    mysql_query("DELETE FROM `cms_download_files` WHERE `id` = '" . $result['id'] . "' LIMIT 1");

                }
            }
            $query = mysql_query("SELECT `id`, `dir`, `name` FROM `cms_download_category`");
            while ($result = mysql_fetch_assoc($query)) {
                if (!file_exists($result['dir'])) {
                    $arrayClean = array();
                    $req = mysql_query("SELECT `id` FROM `cms_download_files` WHERE `refid` = '" . $result['id'] . "'");
                    while ($res = mysql_fetch_assoc($req)) {
                    	$arrayClean = $res['id'];
                    }
                    $idClean = implode(',', $arrayClean);
                    mysql_query("DELETE FROM `cms_download_bookmark` WHERE `file_id` IN (" . $idClean . ")");
                    mysql_query("DELETE FROM `cms_download_comments` WHERE `sub_id` IN (" . $idClean . ")");
                    mysql_query("DELETE FROM `cms_download_more` WHERE `refid` IN (" . $idClean . ")");
                    mysql_query("DELETE FROM `cms_download_files` WHERE `refid` = '" . $result['id'] . "'");
                    mysql_query("DELETE FROM `cms_download_category` WHERE `id` = '" . $result['id'] . "'");

                }
            }
            $req_down = mysql_query("SELECT `dir`, `name`, `id` FROM `cms_download_category`");
			while ($res_down = mysql_fetch_assoc($req_down)) {
				$dir_files = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2' AND `dir` LIKE '" . ($res_down['dir']) . "%'"), 0);
				mysql_query("UPDATE `cms_download_category` SET `total` = '$dir_files' WHERE `id` = '" . $res_down['id'] . "'");
			}
   			mysql_query("OPTIMIZE TABLE `cms_download_bookmark`");
            mysql_query("OPTIMIZE TABLE `cms_download_files`");
            mysql_query("OPTIMIZE TABLE `cms_download_comments`");
            mysql_query("OPTIMIZE TABLE `cms_download_more`");
            echo '<div class="phdr"><b>' . __('scan_dir_clean') . '</b></div>' .
                '<div class="rmenu"><p>' . __('scan_dir_clean_ok') . '</p></div>' .
                '<div class="phdr"><a href="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a></div>';
            break;

        default:
            /*
               -----------------------------------------------------------------
               Обновление файлов
               -----------------------------------------------------------------
               */
            if (Vars::$ID) {
                $cat = mysql_query("SELECT `dir`, `name`, `rus_name` FROM `cms_download_category` WHERE	`id` = '" . Vars::$ID . "' LIMIT 1");
                $res_down_cat = mysql_fetch_assoc($cat);
                $scan_dir = $res_down_cat['dir'];
                if (mysql_num_rows($cat) == 0 || !is_dir($scan_dir)) {
                    echo Functions::displayError(__('not_found_dir'), '<a href="' . $url . '">' . __('download_title') . '</a>');
                    exit;
                }
            } else {
                $scan_dir = $files_path;
            }
            echo '<div class="phdr"><b>' . __('download_scan_dir') . '</b>' . (Vars::$ID ? ': ' . Validate::checkout($res_down_cat['rus_name']) : '') . '</div>';
            if (isset($_GET['yes'])) {
                /*
                    -----------------------------------------------------------------
                    Сканирование папок
                    -----------------------------------------------------------------
                    */
                $array_dowm = array();
                $array_id = array();
                $array_more = array();
                $query = mysql_query("SELECT `dir`, `name`, `id` FROM `cms_download_files`");
                while ($result = mysql_fetch_assoc($query)) {
                    $array_dowm[] = $result['dir'] . '/' . $result['name'];
                    $array_id[$result['dir'] . '/' . $result['name']] = $result['id'];
                }
				$queryCat = mysql_query("SELECT `dir`, `id` FROM `cms_download_category`");
                while ($resultCat = mysql_fetch_assoc($queryCat)) {
                    $array_dowm[] = $resultCat['dir'];
                    $array_id[$resultCat['dir']] = $resultCat['id'];
				}
				$query_more = mysql_query("SELECT `name` FROM `cms_download_more`");
                while ($result_more = mysql_fetch_assoc($query_more)) {
                    $array_more[] = $result_more['name'];
                }
                $array_scan = array();
                function scan_dir($dir = '')
                {
                    static $array_scan;
                    global $mod;
                    $arr_dir = glob($dir . '/*');

                    foreach ($arr_dir as $val) {
                        if (is_dir($val)) {
                            $array_scan[] = $val;
                            if (!$mod)
                                scan_dir($val);
                        } else {
                            $file_name = basename($val);
                            if ($file_name != '.' && $file_name != '..' && $file_name != 'index.php' && $file_name != '.htaccess' && $file_name != '.svn')
                                $array_scan[] = $val;
                        }
                    }
                    return $array_scan;
                }
                $i = 0;
                $i_two = 0;
                $i_three = 0;
                $arr_scan_dir = scan_dir($scan_dir);
                if ($arr_scan_dir) {
                    foreach ($arr_scan_dir as $val) {
                        if (!in_array($val, $array_dowm)) {
                            if (is_dir($val)) {
                                $name = mysql_real_escape_string(basename($val));
                                $dir = mysql_real_escape_string(dirname($val));
                                $refid = isset($array_id[$dir]) ? (int)$array_id[$dir] : 0;
                            	$sort = isset($sort) ? ($sort+1) : time();
                            	mysql_query("INSERT INTO `cms_download_category` SET
                                    `refid` = '$refid',
                                    `dir` = '" . $dir . "/" . $name . "',
                                    `sort` =  '$sort',
                                    `name` = '$name',
                                    `field` = '0',
                                    `rus_name` = '$name',
                                    `text` = '',
                                    `desc` = ''
                                ") or die('144: ' . mysql_error());
                                $array_id[$dir . "/" . $name] = mysql_insert_id();
                                ++$i;
                            } else {
                                $name = basename($val);
                                if (preg_match("/^file([0-9]+)_/", $name)) {
                                    if (!in_array($name, $array_more)) {
                                        $refid = (int)str_replace('file', '', $name);
                                        $name_link = mysql_real_escape_string(Validate::checkout(mb_substr(str_replace('file' . $refid . '_', __('download') . ' ', $name), 0, 200)));
                                        $name = mysql_real_escape_string($name);
                                        $size = filesize($val);
                                        mysql_query("INSERT INTO `cms_download_more` SET
                                            `refid` = '$refid',
                                            `time` = '" . time() . "',
                                            `name` = '$name',
                                            `rus_name` = '$name_link',
                                            `size` = '$size'
                                        ") or die('161: ' . mysql_error());
                                        ++$i_two;
                                    }
                                } else {
                                    $isFile = Vars::$START ? is_file($val) : TRUE;
                                    if ($isFile) {
                                        $name = mysql_real_escape_string($name);
                                        $dir = mysql_real_escape_string(dirname($val));
                                        $refid = (int)$array_id[$dir];
                                        mysql_query("INSERT INTO `cms_download_files` SET `refid`='$refid', `dir`='$dir', `time`='" . time() . "',`name`='$name', `text` = 'Скачать файл',`rus_name`='$name', `type` = '2',`user_id`='" . Vars::$USER_ID . "'");
                                        if (Vars::$START) {
                                            $fileId = mysql_insert_id();
                                            $screenFile = FALSE;
                                            if (is_file($val . '.jpg')) $screenFile = $val . '.jpg';
                                            elseif (is_file($val . '.gif')) $screenFile = $val . '.gif';
                                            elseif (is_file($val . '.png')) $screenFile = $val . '.png';
                                            if ($screenFile) {
                                                $is_dir = mkdir($screens_path . '/' . $fileId, 0777);
                                                if ($is_dir == TRUE) @chmod($screens_path . '/' . $fileId, 0777);
                                                @copy($screenFile, $screens_path . '/' . $fileId . '/' . str_replace($val, $fileId, $screenFile));
                                                unlink($screenFile);
                                            }
                                            if (is_file($val . '.txt')) {
                                                @copy($val . '.txt', $down_path . '/about/' . $fileId . '.txt');
                                                unlink($val . '.txt');
                                            }
                                        }
                                        ++$i_three;
                                    }
                                }
                            }
                        }
                    }
                }
                if (Vars::$ID) {
                    $dir_files = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2' AND `dir` LIKE '" . ($res_down_cat['dir'] . '/' . $res_down_cat['name']) . "%'"), 0);
                    mysql_query("UPDATE `cms_download_files` SET `total` = '$dir_files' WHERE `id` = '" . Vars::$ID . "'");
                } else {
                    $req_down = mysql_query("SELECT `dir`, `name`, `id` FROM `cms_download_files` WHERE `type` = 1");
                    while ($res_down = mysql_fetch_assoc($req_down)) {
                        $dir_files = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2' AND `dir` LIKE '" . ($res_down['dir'] . '/' . $res_down['name']) . "%'"), 0);
                        mysql_query("UPDATE `cms_download_files` SET `total` = '$dir_files' WHERE `id` = '" . $res_down['id'] . "'");
                    }
                }
                echo '<div class="menu"><b>' . __('scan_dir_add') . ':</b><br />' .
                    __('scan_dir_add_cat') . ': ' . $i . '<br />' .
                    __('scan_dir_add_files') . ': ' . $i_three . '<br />' .
                    __('scan_dir_add_files_more') . ': ' . $i_two . '</div>';
                if (Vars::$START) echo '<div class="gmenu"><a href="' . $url . '?act=scan_about&amp;id=' . Vars::$ID . '">' . __('download_scan_about') . '</div>';
                echo '<div class="rmenu">' .
                    '<a href="' . $url . '?act=scan_dir&amp;do=clean&amp;id=' . Vars::$ID . '">' . __('scan_dir_clean') . '</a><br />' .
                    '<a href="' . $url . '?act=recount&amp;do=clean&amp;id=' . Vars::$ID . '">' . __('download_recount') . '</a></div>';
            } else {
                /*
                    -----------------------------------------------------------------
                    Выбор режима обновление
                    -----------------------------------------------------------------
                    */
                echo '<div class="menu"><b><a href="' . $url . '?act=scan_dir&amp;yes&amp;id=' . Vars::$ID . '">' . (Vars::$ID ? __('download_scan_dir2') : __('download_scan_dir4')) . '</a></b>' .
                    (Vars::$ID ? '<br /><a href="' . $url . '?act=scan_dir&amp;yes&amp;id=' . Vars::$ID . '&amp;mod=1">' . __('download_scan_dir3') . '</a>' : '') . '</div>';
                if (Vars::$ID)
                    echo '<div class="rmenu"><a href="' . $url . '?act=scan_dir&amp;yes">' . __('download_scan_dir4') . '</a></div>';
                echo '<div class="phdr"><b>' . __('scan_dir_v2') . '</b> beta</div>' .
                    '<div class="topmenu">' . __('scan_dir_about') . '</div><div class="menu">' .
                    '<a href="' . $url . '?act=scan_dir&amp;yes&amp;id=' . Vars::$ID . '&amp;start=1"><b>' . (Vars::$ID ? __('download_scan_dir2') : __('download_scan_dir4')) . '</b></a> ' .
                    (Vars::$ID ? '<br /><a href="' . $url . '?act=scan_dir&amp;yes&amp;id=' . Vars::$ID . '&amp;mod=1&amp;start=1">' . __('download_scan_dir3') . '</a>' : '') .
                    '<div class="sub"><small>' . __('scan_dir_v2_faq') . '</small></div>' .
                    '</div><div class="rmenu">';
                if (Vars::$ID)
                    echo ' <a href="' . $url . '?act=scan_dir&amp;yes&amp;start=1">' . __('download_scan_dir4') . '</a><br />';
                echo '<a href="' . $url . '?act=scan_dir&amp;do=clean&amp;id=' . Vars::$ID . '">' . __('scan_dir_clean') . '</a></div>';
            }
            echo '<div class="phdr"><a href="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a></div>';
    }
} else {
    header('Location: ' . Vars::$HOME_URL . '/404');
}