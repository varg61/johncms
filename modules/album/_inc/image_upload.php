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

/*
-----------------------------------------------------------------
Выгрузка фотографии
-----------------------------------------------------------------
*/
if ($al && $user['id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 7) {
    $req_a = mysql_query("SELECT * FROM `cms_album_cat` WHERE `id` = '$al' AND `user_id` = '" . $user['id'] . "'");
    if (!mysql_num_rows($req_a)) {
        // Если альбома не существует, завершаем скрипт
        echo Functions::displayError(__('error_wrong_data'));
        exit;
    }
    $res_a = mysql_fetch_assoc($req_a);
    echo '<div class="phdr"><a href="' . Vars::$URI . '?act=show&amp;al=' . $al . '&amp;user=' . $user['id'] . '"><b>' . __('photo_album') . '</b></a> | ' . __('upload_photo') . '</div>';
    if (isset($_POST['submit'])) {
        $handle = new upload($_FILES['imagefile']);
        if ($handle->uploaded) {
            // Обрабатываем фото
            $handle->file_new_name_body = 'img_' . time();
            $handle->allowed = array(
                'image/jpeg',
                'image/gif',
                'image/png'
            );
            $handle->file_max_size = 1024 * Vars::$SYSTEM_SET['filesize'];
            $handle->image_resize = TRUE;
            $handle->image_x = 640;
            $handle->image_y = 480;
            $handle->image_ratio_no_zoom_in = TRUE;
            $handle->image_convert = 'jpg';
            // Поставить в зависимость от настроек в Админке
            //$handle->image_text = Vars::$system_set['homeurl'];
            //$handle->image_text_x = 0;
            //$handle->image_text_y = 0;
            //$handle->image_text_font = 3;
            //$handle->image_text_background = '#AAAAAA';
            //$handle->image_text_background_percent = 50;
            //$handle->image_text_padding = 1;
            $handle->process(ALBUMPATH . $user['id'] . DIRECTORY_SEPARATOR);
            $img_name = $handle->file_dst_name;
            if ($handle->processed) {
                // Обрабатываем превьюшку
                $handle->file_new_name_body = 'tmb_' . time();
                $handle->image_resize = TRUE;
                $handle->image_x = 80;
                $handle->image_y = 80;
                $handle->image_ratio_no_zoom_in = TRUE;
                $handle->image_convert = 'jpg';
                $handle->process(ALBUMPATH . $user['id'] . DIRECTORY_SEPARATOR);
                $tmb_name = $handle->file_dst_name;
                if ($handle->processed) {
                    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
                    $description = mb_substr($description, 0, 500);
                    mysql_query("INSERT INTO `cms_album_files` SET
                        `album_id` = '$al',
                        `user_id` = '" . $user['id'] . "',
                        `img_name` = '" . mysql_real_escape_string($img_name) . "',
                        `tmb_name` = '" . mysql_real_escape_string($tmb_name) . "',
                        `description` = '" . mysql_real_escape_string($description) . "',
                        `time` = '" . time() . "',
                        `access` = '" . $res_a['access'] . "'
                    ") or die(mysql_error());
                    echo '<div class="gmenu"><p>' . __('photo_uploaded') . '<br />' .
                         '<a href="' . Vars::$URI . '?act=show&amp;al=' . $al . '&amp;user=' . $user['id'] . '">' . __('continue') . '</a></p></div>' .
                         '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '">' . __('profile') . '</a></div>';
                } else {
                    echo Functions::displayError($handle->error);
                }
            } else {
                echo Functions::displayError($handle->error);
            }
            $handle->clean();
        }
    } else {
        echo '<form enctype="multipart/form-data" method="post" action="' . Vars::$URI . '?act=image_upload&amp;al=' . $al . '&amp;user=' . $user['id'] . '">' .
             '<div class="menu"><p><h3>' . __('select_image') . '</h3>' .
             '<input type="file" name="imagefile" value="" /></p>' .
             '<p><h3>' . __('description') . '</h3>' .
             '<textarea name="description" rows="' . Vars::$USER_SET['field_h'] . '"></textarea><br />' .
             '<small>' . __('not_mandatory_field') . ', max. 500</small></p>' .
             '<input type="hidden" name="MAX_FILE_SIZE" value="' . (1024 * Vars::$SYSTEM_SET['filesize']) . '" />' .
             '<p><input type="submit" name="submit" value="' . __('upload') . '" /></p>' .
             '</div></form>' .
             '<div class="phdr"><small>' . __('select_image_help') . ' ' . Vars::$SYSTEM_SET['filesize'] . 'kb.<br />' . __('select_image_help_5') . '</small></div>' .
             '<p><a href="' . Vars::$URI . '?act=show&amp;al=' . $al . '&amp;user=' . $user['id'] . '">' . __('back') . '</a></p>';
    }
}