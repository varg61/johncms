<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);
$headmod = 'anketa';
require('../incfiles/core.php');
$lng_profile = load_lng('profile');
$textl = $lng_profile['profile_edit'];
require('../incfiles/head.php');
require('../incfiles/lib/class.upload.php');
if (!$user_id) {
    display_error($lng['access_guest_forbidden']);
    require('../incfiles/end.php');
    exit;
}
if ($id && $id != $user_id && $rights >= 7) {
    // Если был запрос на юзера, то получаем его данные
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
    if (mysql_num_rows($req)) {
        $user = mysql_fetch_assoc($req);
        if ($user['rights'] > $datauser['rights']) {
            // Если не хватает прав, выводим ошибку
            echo display_error($lng_profile['error_rights']);
            require('../incfiles/end.php');
            exit;
        }
    } else {
        echo display_error($lng['error_user_not_exist']);
        require('../incfiles/end.php');
        exit;
    }
} else {
    $id = false;
    $user = $datauser;
}
switch ($act) {
    case 'avatar':
        /*
        -----------------------------------------------------------------
        Выгружаем аватар
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="anketa.php?id=' . $user['id'] . '"><b>' . $lng['profile'] . '</b></a> | ' . $lng_profile['upload_avatar'] . '</div>';
        if (isset($_POST['submit'])) {
            $handle = new upload($_FILES['imagefile']);
            if ($handle->uploaded) {
                // Обрабатываем фото
                $handle->file_new_name_body = $user['id'];
                //$handle->mime_check = false;
                $handle->allowed = array (
                    'image/jpeg',
                    'image/gif',
                    'image/png'
                );
                $handle->file_max_size = 1024 * $flsz;
                $handle->file_overwrite = true;
                $handle->image_resize = true;
                $handle->image_x = 32;
                $handle->image_y = 32;
                $handle->image_convert = 'png';
                $handle->process('../files/users/avatar/');
                if ($handle->processed) {
                    echo '<div class="gmenu"><p>' . $lng_profile['avatar_uploaded'] . '<br />' .
                        '<a href="my_data.php?id=' . $user['id'] . '">' . $lng['continue'] . '</a></p></div>' .
                        '<div class="phdr"><a href="anketa.php?id=' . $user['id'] . '">' . $lng['profile'] . '</a></div>';
                } else {
                    echo display_error($handle->error);
                }
                $handle->clean();
            }
        } else {
            echo '<form enctype="multipart/form-data" method="post" action="my_images.php?act=avatar&amp;id=' . $user['id'] . '">' .
                '<div class="menu"><p>' . $lng_profile['select_image'] . ':<br />' .
                '<input type="file" name="imagefile" value="" />' .
                '<input type="hidden" name="MAX_FILE_SIZE" value="' . (1024 * $flsz) . '" /></p>' .
                '<p><input type="submit" name="submit" value="' . $lng_profile['upload'] . '" />' .
                '</p></div></form>' .
                '<div class="phdr"><small>' . $lng_profile['select_image_help'] . ' ' . $flsz . ' kb.<br />' . $lng_profile['select_image_help_2'] . '<br />' . $lng_profile['select_image_help_3'] . $lng_profile['select_image_help_4'] . '</small></div>';
        }
        break;

    case 'up_photo':
        echo '<div class="phdr"><a href="anketa.php?id=' . $user['id'] . '"><b>' . $lng['profile'] . '</b></a> | ' . $lng_profile['upload_photo'] . '</div>';
        if (isset($_POST['submit'])) {
            $handle = new upload($_FILES['imagefile']);
            if ($handle->uploaded) {
                // Обрабатываем фото
                $handle->file_new_name_body = $user['id'];
                //$handle->mime_check = false;
                $handle->allowed = array (
                    'image/jpeg',
                    'image/gif',
                    'image/png'
                );
                $handle->file_max_size = 1024 * $flsz;
                $handle->file_overwrite = true;
                $handle->image_resize = true;
                $handle->image_x = 320;
                $handle->image_ratio_y = true;
                $handle->image_convert = 'jpg';
                $handle->process('../files/users/photo/');
                if ($handle->processed) {
                    // Обрабатываем превьюшку
                    $handle->file_new_name_body = $user['id'] . '_small';
                    $handle->file_overwrite = true;
                    $handle->image_resize = true;
                    $handle->image_x = 100;
                    $handle->image_ratio_y = true;
                    $handle->image_convert = 'jpg';
                    $handle->process('../files/users/photo/');
                    if ($handle->processed) {
                        echo '<div class="gmenu"><p>' . $lng_profile['photo_uploaded'] . '<br /><a href="my_data.php?id=' . $user['id'] . '">' . $lng['continue'] . '</a></p></div>';
                        echo '<div class="phdr"><a href="anketa.php?id=' . $user['id'] . '">' . $lng['profile'] . '</a></div>';
                    } else {
                        echo display_error($handle->error);
                    }
                } else {
                    echo display_error($handle->error);
                }
                $handle->clean();
            }
        } else {
            echo '<form enctype="multipart/form-data" method="post" action="my_images.php?act=up_photo&amp;id=' . $user['id'] . '"><div class="menu"><p>' . $lng_profile['select_image'] . ':<br />' .
                '<input type="file" name="imagefile" value="" />' .
                '<input type="hidden" name="MAX_FILE_SIZE" value="' . (1024 * $flsz) . '" /></p>' .
                '<p><input type="submit" name="submit" value="' . $lng_profile['upload'] . '" /></p>' .
                '</div></form>' .
                '<div class="phdr"><small>' . $lng_profile['select_image_help'] . ' ' . $flsz . 'kb.<br />' . $lng_profile['select_image_help_5'] . '<br />' . $lng_profile['select_image_help_3'] . '</small></div>';
        }
        break;
}

require('../incfiles/end.php');
?>