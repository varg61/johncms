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
if ($user['id'] != Vars::$USER_ID
    && Vars::$USER_RIGHTS != 9
    && (Vars::$USER_RIGHTS < 7 || $user['rights'] >= Vars::$USER_RIGHTS)
) {
    echo Functions::displayError(lng('error_rights'));
    exit;
}

$tpl = Template::getInstance();
$tpl->user = $user;
$tpl->setUsers = isset(Vars::$SYSTEM_SET['users']) && !empty(Vars::$SYSTEM_SET['users'])
    ? unserialize(Vars::$SYSTEM_SET['users'])
    : Vars::$USER_SYS;

if (is_file(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.gif')) {
    $tpl->avatar = true;
}
if (is_file(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '_small.jpg')) {
    $tpl->photo = true;
}

$menu[] = '<a href="' . Vars::$URI . '?act=nick&amp;user=' . $user['id'] . '">' . lng('change_nick') . '</a>';
if ($tpl->setUsers['change_status']
    || Vars::$USER_RIGHTS == 9
    || (Vars::$USER_RIGHTS == 7 && $user['rights'] < 7)
) {
    $menu[] = '<a href="' . Vars::$URI . '?act=status&amp;user=' . $user['id'] . '">' . lng('change_status') . '</a>';
}
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
        if (isset($_POST['submit'])
            && isset($_POST['token'])
            && isset($_SESSION['token_profile'])
            && $_POST['token'] == $_SESSION['token_profile']
        ) {
            @unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.gif');
            header('Location: ' . Vars::$URI . '?user=' . $user['id']);
            exit;
        } else {
            $tpl->token = mt_rand(100, 10000);
            $_SESSION['token_profile'] = $tpl->token;
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
        if ($tpl->setUsers['upload_animation'] || Vars::$USER_RIGHTS >= 7) {
            if (isset($_POST['submit'])
                && isset($_POST['token'])
                && isset($_SESSION['token_profile'])
                && $_POST['token'] == $_SESSION['token_profile']
            ) {
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
                $tpl->token = mt_rand(100, 10000);
                $_SESSION['token_profile'] = $tpl->token;
                $tpl->contents = $tpl->includeTpl('upload_animation');
            }
        } else {
            echo Functions::displayError(lng('access_forbidden'));
        }
        break;

    case 'upload_avatar':
        /*
        -----------------------------------------------------------------
        Выгрузка аватара
        -----------------------------------------------------------------
        */
        if ($tpl->setUsers['upload_avatars'] || Vars::$USER_RIGHTS >= 7) {
            if (isset($_POST['submit'])
                && isset($_POST['token'])
                && isset($_SESSION['token_profile'])
                && $_POST['token'] == $_SESSION['token_profile']
            ) {
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
                $tpl->token = mt_rand(100, 10000);
                $_SESSION['token_profile'] = $tpl->token;
                $tpl->contents = $tpl->includeTpl('upload_avatar');
            }
        } else {
            echo Functions::displayError(lng('access_forbidden'));
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
        if ($tpl->setUsers['change_status'] || Vars::$USER_RIGHTS >= 7) {
            $tpl->status = $user['status'];
            if (isset($_POST['submit'])
                && isset($_POST['status'])
                && isset($_POST['token'])
                && isset($_SESSION['token_profile'])
                && $_POST['token'] == $_SESSION['token_profile']
            ) {
                $tpl->status = trim($_POST['status']);
                if (mb_strlen($tpl->status) < 51) {
                    mysql_query("UPDATE `users` SET `status` = '" . mysql_real_escape_string($tpl->status) . "' WHERE `id` = " . $user['id']);
                    header('Location: ' . Vars::$HOME_URL . '/profile/edit?user=' . $user['id']);
                    exit;
                }
                $tpl->error = lng('error_status_lenght');
            }
            $tpl->token = mt_rand(100, 10000);
            $_SESSION['token_profile'] = $tpl->token;
            $tpl->contents = $tpl->includeTpl('change_status');
        } else {
            echo Functions::displayError(lng('access_forbidden'));
        }
        break;

    case'avatar':
        /*
        -----------------------------------------------------------------
        Меню смены аватара
        -----------------------------------------------------------------
        */
        $tpl->contents = $tpl->includeTpl('avatar');
        break;

    default:
        /*
        -----------------------------------------------------------------
        Редактирование анкеты
        -----------------------------------------------------------------
        */
        if (isset($_POST['submit'])
            && isset($_POST['token'])
            && isset($_SESSION['token_profile'])
            && $_POST['token'] == $_SESSION['token_profile']
        ) {
            $error = array();

            // Принимаем данные о половой принадлежности
            if (Vars::$USER_SYS['change_sex']) {
                $user['sex'] = isset($_POST['sex']) && trim($_POST['sex']) == 'w' ? 'w' : 'm';
            }

            // Принимаем и обрабатываем Имя
            if (isset($_POST['imname'])) {
                $user['imname'] = mb_substr(trim($_POST['imname']), 0, 50);
            }

            // Принимаем и обрабатываем дату рожденья
            if (isset($_POST['day'])
                && isset($_POST['month'])
                && isset($_POST['year'])
            ) {
                $day = intval($_POST['day']);
                $month = intval($_POST['month']);
                $year = intval($_POST['year']);
                if (!checkdate($month, $day, $year)) {
                    //TODO: Дописать обработку даты
                    //echo'ERROR: date';
                    //exit;
                }
            }

            // Принимаем и обрабатываем данные о месте проживания
            if (isset($_POST['live'])) {
                $user['live'] = mb_substr(trim($_POST['live']), 0, 100);
            }

            // Принимаем и обрабатываем дополнительную информацию "о себе"
            if (isset($_POST['about'])) {
                $user['about'] = mb_substr(trim($_POST['about']), 0, 5000);
            }

            // Принимаем и обрабатываем номер телефона
            if (isset($_POST['tel'])) {
                $user['tel'] = mb_substr(trim($_POST['tel']), 0, 100);
            }

            // Принимаем и обрабатываем URL сайта
            if (isset($_POST['siteurl'])) {
                $user['siteurl'] = mb_substr(trim($_POST['siteurl']), 0, 100);
            }

            // Принимаем и обрабатываем e-mail
            if (isset($_POST['email'])) {
                $user['email'] = mb_substr(trim($_POST['email']), 0, 50);
                $email_valid = Validate::email($user['email'], 1, 1);
                if ($email_valid) {
                    $user['mailvis'] = isset($_POST['mailvis']) ? 1 : 0;
                } else {
                    $tpl->email_error = Validate::$error;
                }
            }

            // Принимаем и обрабатываем ICQ
            if (isset($_POST['icq']) && (empty($_POST['icq']) || intval($_POST['icq']) > 10000)) {
                $user['icq'] = intval($_POST['icq']);
            }

            // Принимаем и обрабатываем Skype
            if (isset($_POST['skype'])) {
                $user['skype'] = mb_substr(trim($_POST['skype']), 0, 50);
            }

            if (isset($tpl->email_error)) {
                $tpl->error = 1;
            }

            $tpl->user = $user;
            mysql_query("UPDATE `users` SET
                `sex` = '" . $user['sex'] . "',
                `imname` = '" . mysql_real_escape_string($user['imname']) . "',
                `live` = '" . mysql_real_escape_string($user['live']) . "',
                `about` = '" . mysql_real_escape_string($user['about']) . "',
                `tel` = '" . mysql_real_escape_string($user['tel']) . "',
                `siteurl` = '" . mysql_real_escape_string($user['siteurl']) . "',
                " . (isset($email_valid) && $email_valid ? "`email` = '" . mysql_real_escape_string($user['email']) . "'," : "") . "
                `mailvis` = " . $user['mailvis'] . ",
                `icq` = " . $user['icq'] . ",
                `skype` = '" . mysql_real_escape_string($user['skype']) . "'
                WHERE `id` = " . $user['id']
            ) or exit(mysql_error());
            $tpl->save = 1;
        }
        $tpl->token = mt_rand(100, 10000);
        $_SESSION['token_profile'] = $tpl->token;
        $tpl->contents = $tpl->includeTpl('profile_edit');
}
