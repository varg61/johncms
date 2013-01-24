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
            $query = DB::PDO()->query("SELECT `id`, `dir`, `name`, `type` FROM `cms_download_files`");
            while ($result = $query->fetch()) {
                if (!file_exists($result['dir'] . '/' . $result['name'])) {
                    $req = DB::PDO()->query("SELECT `id` FROM `cms_download_more` WHERE `refid` = '" . $result['id'] . "'");
                    while ($res = $req->fetch()) {
                        @unlink($result['dir'] . '/' . $res['name']);
                    }
                    DB::PDO()->exec("DELETE FROM `cms_download_bookmark` WHERE `file_id`='" . $result['id'] . "'");
                    DB::PDO()->exec("DELETE FROM `cms_download_more` WHERE `refid` = '" . $result['id'] . "'");
                    DB::PDO()->exec("DELETE FROM `cms_download_comments` WHERE `sub_id`='" . $result['id'] . "'");
                    DB::PDO()->exec("DELETE FROM `cms_download_files` WHERE `id` = '" . $result['id'] . "' LIMIT 1");
                }
            }

            $query = DB::PDO()->query("SELECT `id`, `dir`, `name` FROM `cms_download_category`");
            while ($result = $query->fetch()) {
                if (!file_exists($result['dir'])) {
                    $arrayClean = array();
                    $req = DB::PDO()->query("SELECT `id` FROM `cms_download_files` WHERE `refid` = '" . $result['id'] . "'");
                    while ($res = $req->fetch()) {
                        $arrayClean = $res['id'];
                    }
                    $idClean = implode(',', $arrayClean);
                    DB::PDO()->exec("DELETE FROM `cms_download_bookmark` WHERE `file_id` IN (" . $idClean . ")");
                    DB::PDO()->exec("DELETE FROM `cms_download_comments` WHERE `sub_id` IN (" . $idClean . ")");
                    DB::PDO()->exec("DELETE FROM `cms_download_more` WHERE `refid` IN (" . $idClean . ")");
                    DB::PDO()->exec("DELETE FROM `cms_download_files` WHERE `refid` = '" . $result['id'] . "'");
                    DB::PDO()->exec("DELETE FROM `cms_download_category` WHERE `id` = '" . $result['id'] . "'");
                }
            }

            $req_down = DB::PDO()->query("SELECT `dir`, `name`, `id` FROM `cms_download_category`");
            while ($res_down = $req_down->fetch()) {
                $dir_files = DB::PDO()->query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2' AND `dir` LIKE '" . ($res_down['dir']) . "%'")->fetchColumn();
                DB::PDO()->exec("UPDATE `cms_download_category` SET `total` = '$dir_files' WHERE `id` = '" . $res_down['id'] . "'");
            }

            DB::PDO()->query("OPTIMIZE TABLE `cms_download_bookmark`, `cms_download_files`, `cms_download_comments`,`cms_download_more`");

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
                $cat = DB::PDO()->query("SELECT `dir`, `name`, `rus_name` FROM `cms_download_category` WHERE	`id` = '" . Vars::$ID . "' LIMIT 1");
                $res_down_cat = $cat->fetch();
                $scan_dir = $res_down_cat['dir'];
                if (!$cat->rowCount() || !is_dir($scan_dir)) {
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

                $query = DB::PDO()->query("SELECT `dir`, `name`, `id` FROM `cms_download_files`");
                while ($result = $query->fetch()) {
                    $array_dowm[] = $result['dir'] . '/' . $result['name'];
                    $array_id[$result['dir'] . '/' . $result['name']] = $result['id'];
                }

                $queryCat = DB::PDO()->query("SELECT `dir`, `id` FROM `cms_download_category`");
                while ($resultCat = $queryCat->fetch()) {
                    $array_dowm[] = $resultCat['dir'];
                    $array_id[$resultCat['dir']] = $resultCat['id'];
                }

                $query_more = DB::PDO()->query("SELECT `name` FROM `cms_download_more`");
                while ($result_more = $query_more->fetch()) {
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
                    $STH_C = DB::PDO()->prepare('
                        INSERT INTO `cms_download_category`
                        (refid, dir, sort, name, field, rus_name, text, desc)
                        VALUES (?, ?, ?, ?, 0, ?, "", "")
                    ');

                    $STH_M = DB::PDO()->prepare('
                        INSERT INTO `cms_download_more`
                        (refid, time, name, rus_name, size)
                        VALUES (?, ?, ?, ?, ?)
                    ');

                    $STH_F = DB::PDO()->prepare('
                        INSERT INTO `cms_download_files`
                        (refid, dir, time, name, text, rus_name, type, user_id)
                        VALUES (?, ?, ?, ?, "Download", ?, 2, ?)
                    ');

                    foreach ($arr_scan_dir as $val) {
                        if (!in_array($val, $array_dowm)) {
                            if (is_dir($val)) {
                                $name = basename($val);
                                $dir = dirname($val);
                                $refid = isset($array_id[$dir]) ? (int)$array_id[$dir] : 0;
                                $sort = isset($sort) ? ($sort + 1) : time();

                                $STH_C->execute(array(
                                    $refid,
                                    $dir . "/" . $name,
                                    $sort,
                                    $name,
                                    $name
                                ));

                                $array_id[$dir . "/" . $name] = DB::PDO()->lastInsertId();

                                ++$i;
                            } else {
                                $name = basename($val);
                                if (preg_match("/^file([0-9]+)_/", $name)) {
                                    if (!in_array($name, $array_more)) {
                                        $refid = (int)str_replace('file', '', $name);
                                        $name_link = Validate::checkout(mb_substr(str_replace('file' . $refid . '_', __('download') . ' ', $name), 0, 200));
                                        $size = filesize($val);

                                        $STH_M->execute(array(
                                            $refid,
                                            time(),
                                            $name,
                                            $name_link,
                                            $size
                                        ));

                                        ++$i_two;
                                    }
                                } else {
                                    $isFile = Vars::$START ? is_file($val) : TRUE;
                                    if ($isFile) {
                                        $dir = dirname($val);
                                        $refid = (int)$array_id[$dir];

                                        $STH_F->execute(array(
                                            $refid,
                                            $dir,
                                            time(),
                                            $name,
                                            $name,
                                            Vars::$USER_ID
                                        ));

                                        if (Vars::$START) {
                                            $fileId = DB::PDO()->lastInsertId();
                                            $screenFile = FALSE;
                                            if (is_file($val . '.jpg')) $screenFile = $val . '.jpg';
                                            elseif (is_file($val . '.gif')) $screenFile = $val . '.gif'; elseif (is_file($val . '.png')) $screenFile = $val . '.png';
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

                    $STH_C = NULL;
                    $STH_M = NULL;
                    $STH_F = NULL;
                }
                if (Vars::$ID) {
                    $dir_files = DB::PDO()->query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2' AND `dir` LIKE '" . ($res_down_cat['dir'] . '/' . $res_down_cat['name']) . "%'")->fetchColumn();
                    DB::PDO()->exec("UPDATE `cms_download_files` SET `total` = '$dir_files' WHERE `id` = '" . Vars::$ID . "'");
                } else {
                    $req_down = DB::PDO()->query("SELECT `dir`, `name`, `id` FROM `cms_download_files` WHERE `type` = 1");
                    while ($res_down = $req_down->fetch()) {
                        $dir_files = DB::PDO()->query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2' AND `dir` LIKE '" . ($res_down['dir'] . '/' . $res_down['name']) . "%'")->fetchColumn();
                        DB::PDO()->exec("UPDATE `cms_download_files` SET `total` = '$dir_files' WHERE `id` = '" . $res_down['id'] . "'");
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
    header('Location: ' . Vars::$HOME_URL . '404');
}