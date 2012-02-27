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
Получаем данные пользователя
-----------------------------------------------------------------
*/
if (Vars::$USER_ID) {
    if (($user = Vars::getUser()) === false) {
        echo Functions::displayError(Vars::$LNG['user_does_not_exist']);
        exit;
    }
} else {
    echo Functions::displayError(Vars::$LNG['access_guest_forbidden']);
    exit;
}

/*
-----------------------------------------------------------------
Проверяем права доступа для редактирования Профиля
-----------------------------------------------------------------
*/
if ($user['id'] != Vars::$USER_ID && (Vars::$USER_RIGHTS < 7 || $user['rights'] > Vars::$USER_RIGHTS)) {
    echo Functions::displayError(Vars::$LNG['error_rights']);
    exit;
}

$tpl = Template::getInstance();
$tpl->lng = Vars::loadLanguage(1);
$tpl->user = $user;

if (is_file(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.gif')) {
    $tpl->avatar = true;
}
if (is_file(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '_small.jpg')) {
    $tpl->photo = true;
}

$menu[] = '<a href="' . Vars::$URI . '?act=nick&amp;user=' . $user['id'] . '">' . $tpl->lng['change_nick'] . '</a>';
$menu[] = '<a href="' . Vars::$URI . '?act=status&amp;user=' . $user['id'] . '">' . $tpl->lng['change_status'] . '</a>';
$menu[] = '<a href="' . Vars::$URI . '?act=avatar&amp;user=' . $user['id'] . '">' . $tpl->lng['change_avatar'] . '</a>';
$arg['sub'] = '<p><b>' . $tpl->lng['change'] . '</b>: ' . Functions::displayMenu($menu) . '</p>';
$tpl->userarg = $arg;

switch (Vars::$ACT) {
    case'delete_avatar':
        // Удаляем аватар
        if (isset($_POST['submit'])) {
            @unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.gif');
            header('Location: ' . Vars::$URI . '?user=' . $user['id']);
        } else {
            $tpl->contents = $tpl->includeTpl('delete_avatar');
        }
        break;

    case'delete_photo':
        // Удаляем фото
        if (isset($_POST['submit'])) {
            @unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '.jpg');
            @unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '_small.jpg');
            header('Location: ' . Vars::$URI . '?user=' . $user['id']);
        } else {
            $tpl->contents = $tpl->includeTpl('delete_photo');
        }
        break;

    case 'upload_avatar':
        /*
        -----------------------------------------------------------------
        Выгружаем аватар
        -----------------------------------------------------------------
        */
        if (isset($_POST['submit'])) {
            $handle = new upload($_FILES['imagefile']);
            if ($handle->uploaded) {
                // Обрабатываем фото
                $handle->file_new_name_body = $user['id'];
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
                $handle->image_convert = 'gif';
                $handle->process(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR);
                if ($handle->processed) {
                    echo '<div class="gmenu"><p>' . $tpl->lng['avatar_uploaded'] . '<br />' .
                        '<a href="' . Vars::$URI . '?user=' . $user['id'] . '">' . Vars::$LNG['continue'] . '</a></p></div>';
                } else {
                    echo Functions::displayError($handle->error);
                }
                $handle->clean();
            }
        } else {
            $tpl->contents = $tpl->includeTpl('upload_avatar');
        }
        break;

    case 'upload_photo':
        echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '"><b>' . Vars::$LNG['profile'] . '</b></a> | ' . $tpl->lng['upload_photo'] . '</div>';
        if (isset($_POST['submit'])) {
            $handle = new upload($_FILES['imagefile']);
            if ($handle->uploaded) {
                // Обрабатываем фото
                $handle->file_new_name_body = $user['id'];
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
                    $handle->file_new_name_body = $user['id'] . '_small';
                    $handle->file_overwrite = true;
                    $handle->image_resize = true;
                    $handle->image_x = 100;
                    $handle->image_ratio_y = true;
                    $handle->image_convert = 'jpg';
                    $handle->process('../files/users/photo/');
                    if ($handle->processed) {
                        echo '<div class="gmenu"><p>' . $tpl->lng['photo_uploaded'] . '<br /><a href="profile.php?act=edit&amp;user=' . $user['id'] . '">' . Vars::$LNG['continue'] . '</a></p></div>';
                        echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '">' . Vars::$LNG['profile'] . '</a></div>';
                    } else {
                        echo Functions::displayError($handle->error);
                    }
                } else {
                    echo Functions::displayError($handle->error);
                }
                $handle->clean();
            }
        } else {
            echo '<form enctype="multipart/form-data" method="post" action="profile.php?act=images&amp;mod=up_photo&amp;user=' . $user['id'] . '"><div class="menu"><p>' . $tpl->lng['select_image'] . ':<br />' .
                '<input type="file" name="imagefile" value="" />' .
                '<input type="hidden" name="MAX_FILE_SIZE" value="' . (1024 * Vars::$SYSTEM_SET['flsz']) . '" /></p>' .
                '<p><input type="submit" name="submit" value="' . $tpl->lng['upload'] . '" /></p>' .
                '</div></form>' .
                '<div class="phdr"><small>' . $tpl->lng['select_image_help'] . ' ' . Vars::$SYSTEM_SET['flsz'] . 'kb.<br />' . $tpl->lng['select_image_help_5'] . '<br />' . $tpl->lng['select_image_help_3'] . '</small></div>';
        }
        break;

    case 'administration':
        $tpl->contents = $tpl->includeTpl('profile_edit_adm');
        break;

    case 'nick':
        $tpl->contents = $tpl->includeTpl('change_nickname');
        break;

    case 'status':
        $tpl->contents = $tpl->includeTpl('change_status');
        break;

    case'avatar':
        $tpl->contents = $tpl->includeTpl('change_avatar');
        break;

    default:
        // Редактируем анкету
        $tpl->contents = $tpl->includeTpl('profile_edit');
}
