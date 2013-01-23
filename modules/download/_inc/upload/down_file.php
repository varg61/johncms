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

$req = DB::PDO()->query("SELECT * FROM `cms_download_category` WHERE `id` = '" . Vars::$ID . "' LIMIT 1");
$res = $req->fetch();
if ($req->rowCount() && is_dir($res['dir'])) {
    if (($res['field'] && Vars::$USER_ID) || (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6)) {
        $al_ext = $res['field'] ? explode(', ', $res['text']) : $defaultExt;
        if (isset($_POST['submit'])) {
            $load_cat = $res['dir'];
            $do_file = FALSE;
            if ($_FILES['fail']['size'] > 0) {
                $do_file = TRUE;
                $fname = strtolower($_FILES['fail']['name']);
                $fsize = $_FILES['fail']['size'];
            }
            if ($do_file) {
                $error = array();
                $new_file = isset($_POST['new_file']) ? trim($_POST['new_file']) : NULL;
                $name = isset($_POST['text']) ? trim($_POST['text']) : NULL;
                $name_link = isset($_POST['name_link']) ? Validate::checkout(mb_substr($_POST['name_link'], 0, 200)) : NULL;
                $text = isset($_POST['opis']) ? trim($_POST['opis']) : NULL;
                $ext = explode(".", $fname);
                if (!empty($new_file)) {
                    $fname = strtolower($new_file . '.' . $ext[1]);
                    $ext = explode(".", $fname);
                }
                if (empty($name))
                    $name = $fname;
                if (empty($name_link))
                    $error[] = __('error_empty_fields');
                if ($fsize > 1024 * Vars::$SYSTEM_SET['filesize'])
                    $error[] = __('error_file_size') . ' ' . Vars::$SYSTEM_SET['filesize'] . 'kb.';
                if (!in_array($ext[(count($ext) - 1)], $al_ext))
                    $error[] = __('error_file_ext') . ': ' . implode(', ', $al_ext);
                if (strlen($fname) > 100)
                    $error[] = __('error_file_name_size ');
                if (preg_match("/[^\da-z_\-.]+/", $fname))
                    $error[] = __('error_file_symbols');
                if ($error) {
                    $error[] = '<a href="' . $url . '?act=down_file&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>';
                    echo functions::displayError($error);
                } else {
                    if (file_exists("$load_cat/$fname"))
                        $fname = time() . $fname;
                    if ((move_uploaded_file($_FILES["fail"]["tmp_name"], "$load_cat/$fname")) == TRUE) {
                        echo '<div class="phdr"><b>' . __('download_upload_file') . ': ' . Validate::checkout($res['rus_name']) . '</b></div>';
                        @chmod("$fname", 0777);
                        @chmod("$load_cat/$fname", 0777);
                        echo '<div class="gmenu">' . __('upload_file_ok');
                        if ($set_down['mod'] && (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
                            echo __('upload_file_mod');
                            $type = 3;
                        } else $type = 2;
                        echo '</div>';

                        $STH = DB::PDO()->prepare('
                            INSERT INTO `cms_download_files`
                            (refid, dir, time, name, text, rus_name, type, user_id, about, desc)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "")
                        ');

                        $STH->execute(array(
                            Vars::$ID,
                            $load_cat,
                            time(),
                            $fname,
                            $name_link,
                            mb_substr($name, 0, 200),
                            $type,
                            Vars::$USER_ID,
                            $text
                        ));
                        $file_id = DB::PDO()->lastInsertId();
                        $STH = NULL;

                        $handle = new upload($_FILES['screen']);
                        if ($handle->uploaded) {
                            if (mkdir($screens_path . '/' . $file_id, 0777) == TRUE)
                                @chmod($screens_path . '/' . $file_id, 0777);
                            $handle->file_new_name_body = $file_id;
                            $handle->allowed = array(
                                'image/jpeg',
                                'image/gif',
                                'image/png'
                            );
                            $handle->file_max_size = 1024 * Vars::$SYSTEM_SET['filesize'];
                            $handle->file_overwrite = TRUE;
                            if ($set_down['screen_resize']) {
                                $handle->image_resize = TRUE;
                                $handle->image_x = 240;
                                $handle->image_ratio_y = TRUE;
                            }
                            $handle->process($screens_path . '/' . $file_id . '/');
                            if ($handle->processed) {
                                echo '<div class="gmenu">' . __('upload_screen_ok') . '</div>';
                            } else
                                echo '<div class="rmenu">' . __('upload_screen_no') . ': ' . $handle->error . '</div>';
                        } else
                            echo '<div class="rmenu">' . __('upload_screen_no') . '</div>';
                        if (!$set_down['mod'] || Vars::$USER_RIGHTS > 6 || Vars::$USER_RIGHTS == 4) {
                            echo '<div class="menu"><a href="' . $url . '?act=view&amp;id=' . $file_id . '">' . __('continue') . '</a></div>';
                            $dirid = Vars::$ID;
                            $sql = '';
                            $i = 0;
                            while ($dirid != '0' && $dirid != "") {
                                $res_down = DB::PDO()->query("SELECT `refid` FROM `cms_download_category` WHERE `id` = '$dirid' LIMIT 1")->fetch();
                                if ($i)
                                    $sql .= ' OR ';
                                $sql .= '`id` = \'' . $dirid . '\'';
                                $dirid = $res_down['refid'];
                                ++$i;
                            }
                            DB::PDO()->exec("UPDATE `cms_download_category` SET `total` = (`total`+1) WHERE $sql");
                        }
                        echo '<div class="phdr"><a href="' . $url . '?act=down_file&amp;id=' . Vars::$ID . '">' . __('upload_file_more') . '</a> | <a href="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a></div>';
                    } else
                        echo '<div class="rmenu">' . __('upload_file_no') . '<br /><a href="' . $url . '?act=down_file&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a></div>';
                }
            } else
                echo '<div class="rmenu">' . __('upload_file_no') . '<br /><a href="' . $url . '?act=down_file&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a></div>';
        } else {
            echo '<div class="phdr"><b>' . __('download_upload_file') . ': ' . Validate::checkout($res['rus_name']) . '</b></div>' .
                '<div class="list1"><form action="' . $url . '?act=down_file&amp;id=' . Vars::$ID . '" method="post" enctype="multipart/form-data">' .
                __('select_file') . '<span class="red">*</span>:<br /><input type="file" name="fail"/><br />' .
                __('save_name_file') . ':<br /><input type="text" name="new_file"/><br />' .
                __('screen_file') . ':<br /><input type="file" name="screen"/><br />' .
                __('name_file') . ' (мах. 200):<br /><input type="text" name="text"/><br />' .
                __('link_file') . ' (мах. 200)<span class="red">*</span>:<br /><input type="text" name="name_link" value="' . __('download_file') . '"/><br />' .
                __('dir_desc') . ' (max. 500)<br /><textarea name="opis"></textarea><br />' .
                '<input type="submit" name="submit" value="' . __('upload') . '"/></form></div>' .
                '<div class="phdr"><small>' . __('file_size_faq') . ' ' . Vars::$SYSTEM_SET['filesize'] . 'kb<br />' .
                __('extensions') . ': ' . implode(', ', $al_ext) . ($set_down['screen_resize'] ? '<br />' . __('add_screen_faq') : '') . '</small></div>' .
                '<p><a href="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a></p>';
        }
    } else
        echo functions::displayError(__('access_forbidden '), '<a href="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a>');
} else {
    echo Functions::displayError(__('not_found_dir'), '<a href="' . $url . '">' . __('download_title') . '</a>');
    exit;
}