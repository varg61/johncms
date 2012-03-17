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
        echo Functions::displayError(lng('user_does_not_exist'));
        exit;
    }
} else {
    echo Functions::displayError(lng('access_guest_forbidden'));
    exit;
}

/*
-----------------------------------------------------------------
Проверяем права доступа для редактирования Профиля
-----------------------------------------------------------------
*/
if ($user['id'] != Vars::$USER_ID && (Vars::$USER_RIGHTS < 7 || $user['rights'] > Vars::$USER_RIGHTS)) {
    echo Functions::displayError(lng('error_rights'));
    exit;
}

$tpl = Template::getInstance();
$tpl->user = $user;

if (is_file(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.gif')) {
    $tpl->avatar = true;
}
if (is_file(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '_small.jpg')) {
    $tpl->photo = true;
}

$menu[] = '<a href="' . Vars::$URI . '?act=nick&amp;user=' . $user['id'] . '">' . lng('change_nick') . '</a>';
$menu[] = '<a href="' . Vars::$URI . '?act=status&amp;user=' . $user['id'] . '">' . lng('change_status') . '</a>';
$menu[] = '<a href="' . Vars::$URI . '?act=avatar&amp;user=' . $user['id'] . '">' . lng('change_avatar') . '</a>';
$arg['sub'] = '<p><b>' . lng('change') . '</b>: ' . Functions::displayMenu($menu) . '</p>';
$tpl->userarg = $arg;

switch (Vars::$ACT) {
    case'delete_avatar':
        /*
        -----------------------------------------------------------------
        Удаление аватара
        -----------------------------------------------------------------
        */
        if (isset($_POST['submit'])) {
            @unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.gif');
            header('Location: ' . Vars::$URI . '?user=' . $user['id']);
        } else {
            $tpl->contents = $tpl->includeTpl('delete_avatar');
        }
        break;

    case'delete_photo':
        /*
        -----------------------------------------------------------------
        Удаление фото
        -----------------------------------------------------------------
        */
        if (isset($_POST['submit'])) {
            @unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '.jpg');
            @unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '_small.jpg');
            header('Location: ' . Vars::$URI . '?user=' . $user['id']);
        } else {
            $tpl->contents = $tpl->includeTpl('delete_photo');
        }
        break;

    case 'upload_animation':
        /*
        -----------------------------------------------------------------
        Выгрузка анимированного аватара
        -----------------------------------------------------------------
        */
        if (isset($_POST['submit'])) {
            $error = array();
            if ($_FILES['imagefile']['size'] > 0) {
                // Проверка на допустимый вес файла
                if ($_FILES['imagefile']['size'] > 10240) {
                    $error[] = lng('error_avatar_filesize');
                }

                $param = getimagesize($_FILES['imagefile']['tmp_name']);

                // Проверка на допустимый тип файла
                if ($param == false || $param['mime'] != 'image/gif') {
                    $error[] = lng('error_avatar_filetype');
                }

                // Проверка на допустимый размер изображения
                if ($param != false && ($param[0] != 32 || $param[1] != 32)) {
                    $error[] = lng('error_avatar_size');
                }
            } else {
                // Если не выбран файл
                $error[] = lng('error_file_not_selected');
            }

            if (empty($error)) {
                if ((move_uploaded_file($_FILES["imagefile"]["tmp_name"],
                    ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.gif')) == true
                ) {
                    echo'<div class="gmenu">' .
                        '<p>' . lng('avatar_uploaded') . '<br/>' .
                        '<a href="' . Vars::$URI . '">' . lng('continue') . '</a></p>' .
                        '</div>';
                } else {
                    $error[] = lng('error_avatar_upload');
                }
            } else {
                echo Functions::displayError($error, '<a href="' . Vars::$URI . '?act=upload_animation">' . lng('back') . '</a>');
            }
        } else {
            $tpl->contents = $tpl->includeTpl('upload_animation');
        }
        break;

    case 'upload_avatar':
        /*
        -----------------------------------------------------------------
        Выгрузка аватара
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
                $handle->file_max_size = 102400;
                $handle->file_overwrite = true;
                $handle->image_resize = true;
                $handle->image_x = 32;
                $handle->image_y = 32;
                $handle->image_convert = 'gif';
                $handle->process(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR);
                if ($handle->processed) {
                    echo '<div class="gmenu"><p>' . lng('avatar_uploaded') . '<br />' .
                        '<a href="' . Vars::$URI . '?user=' . $user['id'] . '">' . lng('continue') . '</a></p></div>';
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
        /*
        -----------------------------------------------------------------
        Выгрузка фотографии
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '"><b>' . lng('profile') . '</b></a> | ' . lng('upload_photo') . '</div>';
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
                        echo '<div class="gmenu"><p>' . lng('photo_uploaded') . '<br /><a href="profile.php?act=edit&amp;user=' . $user['id'] . '">' . lng('continue') . '</a></p></div>';
                        echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '">' . lng('profile') . '</a></div>';
                    } else {
                        echo Functions::displayError($handle->error);
                    }
                } else {
                    echo Functions::displayError($handle->error);
                }
                $handle->clean();
            }
        } else {
            echo '<form enctype="multipart/form-data" method="post" action="profile.php?act=images&amp;mod=up_photo&amp;user=' . $user['id'] . '"><div class="menu"><p>' . lng('select_image') . ':<br />' .
                '<input type="file" name="imagefile" value="" />' .
                '<input type="hidden" name="MAX_FILE_SIZE" value="' . (1024 * Vars::$SYSTEM_SET['flsz']) . '" /></p>' .
                '<p><input type="submit" name="submit" value="' . lng('upload') . '" /></p>' .
                '</div></form>' .
                '<div class="phdr"><small>' . lng('select_image_help') . ' ' . Vars::$SYSTEM_SET['flsz'] . 'kb.<br />' . lng('select_image_help_5') . '<br />' . lng('select_image_help_3') . '</small></div>';
        }
        break;

    case 'administration':
        /*
        -----------------------------------------------------------------
        Административные функции
        -----------------------------------------------------------------
        */
        $tpl->contents = $tpl->includeTpl('profile_edit_adm');
        break;

    case 'nick':
        /*
        -----------------------------------------------------------------
        Смена ника
        -----------------------------------------------------------------
        */
        $tpl->contents = $tpl->includeTpl('change_nickname');
        break;

    case 'status':
        /*
        -----------------------------------------------------------------
        Смена статуса
        -----------------------------------------------------------------
        */
        $tpl->status = $user['status'];
        if (isset($_POST['submit']) && isset($_POST['status']) && !empty($_POST['status'])) {
            $tpl->status = trim($_POST['status']);
            if (mb_strlen($tpl->status) > 2 && mb_strlen($tpl->status) < 51) {
                mysql_query("UPDATE `users` SET `status` = '" . mysql_real_escape_string($tpl->status) . "'");
                header('Location: ' . Vars::$HOME_URL . '/profile/edit?user=' . $user['id']);
                exit;
            }
            $tpl->error = lng('error_status_lenght');
        }
        $tpl->contents = $tpl->includeTpl('change_status');
        break;

    case'avatar':
        /*
        -----------------------------------------------------------------
        Сменв аватара
        -----------------------------------------------------------------
        */
        $tpl->contents = $tpl->includeTpl('change_avatar');
        break;

    default:
        /*
        -----------------------------------------------------------------
        Редактирование анкеты
        -----------------------------------------------------------------
        */
        $tpl->contents = $tpl->includeTpl('profile_edit');
}
