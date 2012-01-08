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

$textl = $lng_profile['profile_edit'];
require_once('../includes/head.php');
require_once('../includes/lib/class.upload.php');
if ($user['rights'] > Vars::$USER_RIGHTS) {
    // Если не хватает прав, выводим ошибку
    echo display_error($lng_profile['error_rights']);
    require_once('../includes/end.php');
    exit;
}
switch (Vars::$MOD) {
    case 'avatar':
        /*
        -----------------------------------------------------------------
        Выгружаем аватар
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '"><b>' . Vars::$LNG['profile'] . '</b></a> | ' . $lng_profile['upload_avatar'] . '</div>';
        if (isset($_POST['submit'])) {
            $handle = new upload($_FILES['imagefile']);
            if ($handle->uploaded) {
                // Обрабатываем фото
                $handle->file_new_name_body = $user['user_id'];
                //$handle->mime_check = false;
                $handle->allowed = array(
                    'image/jpeg',
                    'image/gif',
                    'image/png'
                );
                $handle->file_max_size = 1024 * Vars::$SYSTEM_SET['flsz'];
                $handle->file_overwrite = true;
                $handle->image_resize = true;
                $handle->image_x = 32;
                $handle->image_y = 32;
                $handle->image_convert = 'png';
                $handle->process('../files/users/avatar/');
                if ($handle->processed) {
                    echo '<div class="gmenu"><p>' . $lng_profile['avatar_uploaded'] . '<br />' .
                         '<a href="profile.php?act=edit&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['continue'] . '</a></p></div>' .
                         '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '">' . Vars::$LNG['profile'] . '</a></div>';
                } else {
                    echo Functions::displayError($handle->error);
                }
                $handle->clean();
            }
        } else {
            echo '<form enctype="multipart/form-data" method="post" action="profile.php?act=images&amp;mod=avatar&amp;user=' . $user['user_id'] . '">' .
                 '<div class="menu"><p>' . $lng_profile['select_image'] . ':<br />' .
                 '<input type="file" name="imagefile" value="" />' .
                 '<input type="hidden" name="MAX_FILE_SIZE" value="' . (1024 * Vars::$SYSTEM_SET['flsz']) . '" /></p>' .
                 '<p><input type="submit" name="submit" value="' . $lng_profile['upload'] . '" />' .
                 '</p></div></form>' .
                 '<div class="phdr"><small>' . $lng_profile['select_image_help'] . ' ' . Vars::$SYSTEM_SET['flsz'] . ' kb.<br />' . $lng_profile['select_image_help_2'] . '<br />' . $lng_profile['select_image_help_3'] . $lng_profile['select_image_help_4']
                 . '</small></div>';
        }
        break;

    case 'up_photo':
        echo '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '"><b>' . Vars::$LNG['profile'] . '</b></a> | ' . $lng_profile['upload_photo'] . '</div>';
        if (isset($_POST['submit'])) {
            $handle = new upload($_FILES['imagefile']);
            if ($handle->uploaded) {
                // Обрабатываем фото
                $handle->file_new_name_body = $user['user_id'];
                //$handle->mime_check = false;
                $handle->allowed = array(
                    'image/jpeg',
                    'image/gif',
                    'image/png'
                );
                $handle->file_max_size = 1024 * Vars::$SYSTEM_SET['flsz'];
                $handle->file_overwrite = true;
                $handle->image_resize = true;
                $handle->image_x = 320;
                $handle->image_ratio_y = true;
                $handle->image_convert = 'jpg';
                $handle->process('../files/users/photo/');
                if ($handle->processed) {
                    // Обрабатываем превьюшку
                    $handle->file_new_name_body = $user['user_id'] . '_small';
                    $handle->file_overwrite = true;
                    $handle->image_resize = true;
                    $handle->image_x = 100;
                    $handle->image_ratio_y = true;
                    $handle->image_convert = 'jpg';
                    $handle->process('../files/users/photo/');
                    if ($handle->processed) {
                        echo '<div class="gmenu"><p>' . $lng_profile['photo_uploaded'] . '<br /><a href="profile.php?act=edit&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['continue'] . '</a></p></div>';
                        echo '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '">' . Vars::$LNG['profile'] . '</a></div>';
                    } else {
                        echo Functions::displayError($handle->error);
                    }
                } else {
                    echo Functions::displayError($handle->error);
                }
                $handle->clean();
            }
        } else {
            echo '<form enctype="multipart/form-data" method="post" action="profile.php?act=images&amp;mod=up_photo&amp;user=' . $user['user_id'] . '"><div class="menu"><p>' . $lng_profile['select_image'] . ':<br />' .
                 '<input type="file" name="imagefile" value="" />' .
                 '<input type="hidden" name="MAX_FILE_SIZE" value="' . (1024 * Vars::$SYSTEM_SET['flsz']) . '" /></p>' .
                 '<p><input type="submit" name="submit" value="' . $lng_profile['upload'] . '" /></p>' .
                 '</div></form>' .
                 '<div class="phdr"><small>' . $lng_profile['select_image_help'] . ' ' . Vars::$SYSTEM_SET['flsz'] . 'kb.<br />' . $lng_profile['select_image_help_5'] . '<br />' . $lng_profile['select_image_help_3'] . '</small></div>';
        }
        break;
}