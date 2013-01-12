<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

global $img, $tpl, $user;

/*
-----------------------------------------------------------------
Редактировать картинку
-----------------------------------------------------------------
*/
if ($img && $user['id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 6) {
    $req = mysql_query("SELECT * FROM `cms_album_files` WHERE `id` = '$img' AND `user_id` = '" . $user['id'] . "'");
    if (mysql_num_rows($req)) {
        $tpl->res = mysql_fetch_assoc($req);
        $tpl->album = $tpl->res['album_id'];
        $tpl->tmb_name = $tpl->res['tmb_name'];
        $tpl->description = $tpl->res['description'];
        echo '<div class="phdr"><a href="' . Router::getUri(3) . '?act=show&amp;al=' . $tpl->album . '&amp;user=' . $user['id'] . '"><b>' . __('photo_album') . '</b></a> | ' . __('image_edit') . '</div>';
        if (isset($_POST['submit'])) {
            $sql = '';
            $rotate = isset($_POST['rotate']) ? intval($_POST['rotate']) : 0;
            $brightness = isset($_POST['brightness']) ? intval($_POST['brightness']) : 0;
            $contrast = isset($_POST['contrast']) ? intval($_POST['contrast']) : 0;
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $tpl->description = mb_substr($description, 0, 500);
            if ($rotate == 1 || $rotate == 2 || ($brightness > 0 && $brightness < 5) || ($contrast > 0 && $contrast < 5)) {
                $path = ALBUMPATH . $user['id'] . DIRECTORY_SEPARATOR;
                $handle = new upload($path . $tpl->res['img_name']);
                // Обрабатываем основное изображение
                $handle->file_new_name_body = 'img_' . time();
                if ($rotate == 1 || $rotate == 2)
                    $handle->image_rotate = ($rotate == 2 ? 90 : 270);
                if ($brightness > 0 && $brightness < 5) {
                    switch ($brightness) {
                        case 1:
                            $handle->image_brightness = -40;
                            break;
                        case 2:
                            $handle->image_brightness = -20;
                            break;
                        case 3:
                            $handle->image_brightness = 20;
                            break;
                        case 4:
                            $handle->image_brightness = 40;
                            break;
                    }
                }
                if ($contrast > 0 && $contrast < 5) {
                    switch ($contrast) {
                        case 1:
                            $handle->image_contrast = -50;
                            break;
                        case 2:
                            $handle->image_contrast = -25;
                            break;
                        case 3:
                            $handle->image_contrast = 25;
                            break;
                        case 4:
                            $handle->image_contrast = 50;
                            break;
                    }
                }
                $handle->process($path);
                $img_name = $handle->file_dst_name;
                if ($handle->processed) {
                    // Обрабатываем превьюшку
                    $handle->file_new_name_body = 'tmb_' . time();
                    if ($rotate == 1 || $rotate == 2)
                        $handle->image_rotate = ($rotate == 2 ? 90 : 270);
                    if ($brightness > 0 && $brightness < 5) {
                        switch ($brightness) {
                            case 1:
                                $handle->image_brightness = -40;
                                break;
                            case 2:
                                $handle->image_brightness = -20;
                                break;
                            case 3:
                                $handle->image_brightness = 20;
                                break;
                            case 4:
                                $handle->image_brightness = 40;
                                break;
                        }
                    }
                    if ($contrast > 0 && $contrast < 5) {
                        switch ($contrast) {
                            case 1:
                                $handle->image_contrast = -50;
                                break;
                            case 2:
                                $handle->image_contrast = -25;
                                break;
                            case 3:
                                $handle->image_contrast = 25;
                                break;
                            case 4:
                                $handle->image_contrast = 50;
                                break;
                        }
                    }
                    $handle->image_resize = TRUE;
                    $handle->image_x = 80;
                    $handle->image_y = 80;
                    $handle->image_ratio_no_zoom_in = TRUE;
                    $handle->process($path);
                    $tmb_name = $handle->file_dst_name;
                    $tpl->tmb_name = $tmb_name;
                }
                $handle->clean();
                @unlink(ALBUMPATH . $user['id'] . DIRECTORY_SEPARATOR . $tpl->res['img_name']);
                @unlink(ALBUMPATH . $user['id'] . DIRECTORY_SEPARATOR . $tpl->res['tmb_name']);
                $sql = "`img_name` = '" . mysql_real_escape_string($img_name) . "', `tmb_name` = '" . mysql_real_escape_string($tmb_name) . "',";
            }
            mysql_query("UPDATE `cms_album_files` SET $sql
                `description` = '" . mysql_real_escape_string($tpl->description) . "'
                WHERE `id` = '$img'
            ");
            $tpl->save = 1;
        }
        $tpl->link = Router::getUri(3);
        $tpl->contents = $tpl->includeTpl('image_edit');
    } else {
        echo Functions::displayError(__('error_wrong_data'));
    }
}