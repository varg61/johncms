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
$req = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . Vars::$ID . "' AND `type` = 1 LIMIT 1");
$res = mysql_fetch_assoc($req);
if (mysql_num_rows($req) && is_dir($res['dir'] . '/' . $res['name'])) {
    if (($res['field'] && Vars::$USER_ID) || (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6)) {
        $al_ext = $res['field'] ? explode(', ', $res['text']) :  $defaultExt;
        if (isset($_POST['submit'])) {
            $load_cat = $res['dir'] . '/' . $res['name'];
            $do_file = false;
            if ($_FILES['fail']['size'] > 0) {
                $do_file = true;
                $fname = strtolower($_FILES['fail']['name']);
                $fsize = $_FILES['fail']['size'];
            }
            if ($do_file) {
                $error = array ();
                $new_file = isset($_POST['new_file']) ? trim($_POST['new_file']) : null;
                $name = isset($_POST['text']) ? trim($_POST['text']) : null;
                $name_link = isset($_POST['name_link']) ? mysql_real_escape_string(Validate::filterString(mb_substr($_POST['name_link'], 0, 200))) : null;
                $text = isset($_POST['opis']) ? mysql_real_escape_string(trim($_POST['opis'])) : null;
                $ext = explode(".", $fname);
                if (!empty($new_file)) {
                    $fname = strtolower($new_file . '.' . $ext[1]);
                    $ext = explode(".", $fname);
                }
                if (empty($name))
                    $name = $fname;
                if (empty($name_link))
                    $error[] = lng('error_empty_fields');
                if ($fsize > 1024 * Vars::$SYSTEM_SET['flsz'])
                    $error[] = lng('error_file_size') . ' ' . Vars::$SYSTEM_SET['flsz'] . 'kb.';
                if (!in_array($ext[(count($ext)-1)], $al_ext))
                    $error[] = lng('error_file_ext') .  ': ' . implode(', ', $al_ext);
                if (strlen($fname) > 100)
                    $error[] = lng('error_file_name_size ');
                if (preg_match("/[^\da-z_\-.]+/", $fname))
                    $error[] = lng('error_file_symbols');
				if ($error) {
                    $error[] = '<a href="' . Vars::$URI . '?act=down_file&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a>';
                    echo functions::displayError($error);
                } else {
                    if (file_exists("$load_cat/$fname"))
                        $fname = time() . $fname;
                    if ((move_uploaded_file($_FILES["fail"]["tmp_name"], "$load_cat/$fname")) == true) {
                        echo '<div class="phdr"><b>' . lng('download_upload_file') . ': ' . Validate::filterString($res['rus_name']) . '</b></div>';
                        @chmod("$fname", 0777);
                        @chmod("$load_cat/$fname", 0777);
                        echo '<div class="gmenu">' . lng('upload_file_ok');
                        if ($set_down['mod'] && (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS!= 4)) {
                            echo lng('upload_file_mod');
                            $type = 3;
                        } else $type = 2;
                        echo '</div>';
                        $fname = mysql_real_escape_string($fname);
                        $name = mysql_real_escape_string(mb_substr($name, 0, 200));
                        mysql_query("INSERT INTO `cms_download_files` SET
                            `refid`='" . Vars::$ID . "',
                            `dir`='$load_cat',
                            `time`='" . time() . "',
                            `name`='$fname',
                            `text` = '$name_link',
                            `rus_name`='$name',
                            `type` = '$type',
                            `user_id`='" . Vars::$USER_ID . "',
                            `about` = '$text',
                            `desc` = ''
                        ") or die('77: ' . mysql_error());
                        $file_id = mysql_insert_id();
						$handle = new upload($_FILES['screen']);
                        if ($handle->uploaded) {
                            if (mkdir($screens_path . '/' . $file_id, 0777) == true)
                                @chmod($screens_path . '/' . $file_id, 0777);
                            $handle->file_new_name_body = $file_id;
                            $handle->allowed = array (
                                'image/jpeg',
                                'image/gif',
                                'image/png'
                            );
                            $handle->file_max_size = 1024 * Vars::$SYSTEM_SET['flsz'];
                            $handle->file_overwrite = true;
                            if ($set_down['screen_resize']) {
                                $handle->image_resize = true;
                                $handle->image_x = 240;
                                $handle->image_ratio_y = true;
                            }
                            $handle->process($screens_path . '/' . $file_id . '/');
                            if ($handle->processed) {
                                echo '<div class="gmenu">' . lng('upload_screen_ok') . '</div>';
                            } else
                                echo '<div class="rmenu">' . lng('upload_screen_no') . ': ' . $handle->error . '</div>';
                        } else
                            echo '<div class="rmenu">' . lng('upload_screen_no') . '</div>';
						if (!$set_down['mod'] || Vars::$USER_RIGHTS > 6 || Vars::$USER_RIGHTS == 4) {
                            echo '<div class="menu"><a href="' . Vars::$URI . '?act=view&amp;id=' . $file_id . '">' . lng('continue') . '</a></div>';
                            $dirid = Vars::$ID;
                            $sql = '';
                            $i = 0;
                            while ($dirid != '0' && $dirid != "") {
                                $res_down = mysql_fetch_assoc(mysql_query("SELECT `refid` FROM `cms_download_files` WHERE `type` = 1 AND `id` = '$dirid' LIMIT 1"));
                                if ($i)
                                    $sql .= ' OR ';
                                $sql .= '`id` = \'' . $dirid . '\'';
                                $dirid = $res_down['refid'];
                                ++$i;
                            }
                            mysql_query("UPDATE `cms_download_files` SET `total` = (`total`+1) WHERE $sql");
                            mysql_query("OPTIMIZE TABLE `cms_download_files`");
                        }
                        echo '<div class="phdr"><a href="' . Vars::$URI. '?act=down_file&amp;id=' . Vars::$ID. '">' . lng('upload_file_more') . '</a> | <a href="' . Vars::$URI. '?id=' . Vars::$ID. '">' . lng('back') . '</a></div>';
                    } else
                        echo '<div class="rmenu">' . lng('upload_file_no') . '<br /><a href="' . Vars::$URI. '?act=down_file&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a></div>';
                }
            } else
                echo '<div class="rmenu">' . lng('upload_file_no') . '<br /><a href="' . Vars::$URI. '?act=down_file&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a></div>';
        } else {
			 echo '<div class="phdr"><b>' . lng('download_upload_file') . ': ' . Validate::filterString($res['rus_name']) . '</b></div>' .
             '<div class="list1"><form action="' . Vars::$URI  . '?act=down_file&amp;id=' . Vars::$ID  . '" method="post" enctype="multipart/form-data">' .
             lng('select_file') . '<span class="red">*</span>:<br /><input type="file" name="fail"/><br />' .
             lng('save_name_file') . ':<br /><input type="text" name="new_file"/><br />' .
             lng('screen_file') . ':<br /><input type="file" name="screen"/><br />' .
             lng('name_file') . ' (мах. 200):<br /><input type="text" name="text"/><br />' .
             lng('link_file') . ' (мах. 200)<span class="red">*</span>:<br /><input type="text" name="name_link" value="' . lng('download_file') . '"/><br />' .
             lng('dir_desc') . ' (max. 500)<br /><textarea name="opis"></textarea><br />' .
             '<input type="submit" name="submit" value="' . lng('upload') . '"/></form></div>' .
             '<div class="phdr"><small>' . lng('file_size_faq') . ' ' . Vars::$SYSTEM_SET['flsz'] . 'kb<br />' .
             lng('extensions') . ': ' . implode(', ', $al_ext) . ($set_down['screen_resize'] ? '<br />' . lng('add_screen_faq')  : '') . '</small></div>' .
             '<p><a href="' . Vars::$URI  . '?id=' . Vars::$ID  . '">' . lng('back') . '</a></p>';
        }
    } else
        echo functions::displayError(lng('access_forbidden '), '<a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . lng('back') . '</a>');
} else  {
    echo Functions::displayError(lng('not_found_dir'), '<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
    exit;
}