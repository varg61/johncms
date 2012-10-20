<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_PROFILE') or die('Error: restricted access');

global $tpl;

/*
-----------------------------------------------------------------
Проверяем права доступа для редактирования Профиля
-----------------------------------------------------------------
*/
if ($tpl->user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS < 7) {
    exit(Functions::displayError(lng('access_forbidden')));
}
if ($tpl->user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS != 9 && $tpl->user['rights'] >= Vars::$USER_RIGHTS) {
    exit(Functions::displayError(lng('error_rights')));
}

$error = array();

if (is_file(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $tpl->user['id'] . '.gif')) {
    $tpl->avatar = TRUE;
}

if (is_file(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $tpl->user['id'] . '_small.jpg')) {
    $tpl->photo = TRUE;
}

switch (Vars::$MOD) {
    case'delete_avatar':
        /*
        -----------------------------------------------------------------
        Удаление аватара
        -----------------------------------------------------------------
        */
        if (isset($_POST['submit'])
            && isset($_POST['form_token'])
            && isset($_SESSION['form_token'])
            && $_POST['form_token'] == $_SESSION['form_token']
        ) {
            @unlink(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $tpl->user['id'] . '.gif');
            $tpl->message = lng('avatar_deleted');
            $tpl->continue = Vars::$URI . '?act=edit&amp;mod=avatar&amp;user=' . $tpl->user['id'];
            $tpl->contents = $tpl->includeTpl('message', 1);
        } else {
            $tpl->form_token = mt_rand(100, 10000);
            $_SESSION['form_token'] = $tpl->form_token;
            $tpl->contents = $tpl->includeTpl('profile_delete_avatar');
        }
        break;

    case'delete_photo':
        /*
        -----------------------------------------------------------------
        Удаление фото
        -----------------------------------------------------------------
        */
        if (isset($_POST['submit'])) {
            @unlink(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $tpl->user['id'] . '.jpg');
            @unlink(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $tpl->user['id'] . '_small.jpg');
            header('Location: ' . Vars::$URI . '?act=edit&user=' . $tpl->user['id']);
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
                && isset($_POST['form_token'])
                && isset($_SESSION['form_token'])
                && $_POST['form_token'] == $_SESSION['form_token']
            ) {
                if ($_FILES['imagefile']['size'] > 0) {
                    // Проверка на допустимый вес файла
                    if ($_FILES['imagefile']['size'] > 10240) {
                        $error[] = lng('error_avatar_filesize');
                    }

                    $param = getimagesize($_FILES['imagefile']['tmp_name']);

                    // Проверка на допустимый тип файла
                    if ($param == FALSE || $param['mime'] != 'image/gif') {
                        $error[] = lng('error_avatar_filetype');
                    }

                    // Проверка на допустимый размер изображения
                    if ($param != FALSE && ($param[0] != 32 || $param[1] != 32)) {
                        $error[] = lng('error_avatar_size');
                    }
                } else {
                    // Если не выбран файл
                    $error[] = lng('error_file_not_selected');
                }

                if (empty($error)) {
                    if ((move_uploaded_file($_FILES["imagefile"]["tmp_name"],
                        FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $tpl->user['id'] . '.gif')) == TRUE
                    ) {
                        $tpl->message = lng('avatar_uploaded');
                        $tpl->continue = Vars::$URI . '?act=edit&amp;mod=avatar';
                        $tpl->contents = $tpl->includeTpl('message', 1);
                    } else {
                        $error[] = lng('error_avatar_upload');
                    }
                } else {
                    echo Functions::displayError($error, '<a href="' . Vars::$URI . '?act=edit&amp;mod=upload_animation&amp;user=' . $tpl->user['id'] . '">' . lng('back') . '</a>');
                }
            } else {
                $tpl->form_token = mt_rand(100, 10000);
                $_SESSION['form_token'] = $tpl->form_token;
                $tpl->contents = $tpl->includeTpl('profile_upload_animation');
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
                && isset($_POST['form_token'])
                && isset($_SESSION['form_token'])
                && $_POST['form_token'] == $_SESSION['form_token']
            ) {
                $handle = new upload($_FILES['imagefile']);
                if ($handle->uploaded) {
                    // Обрабатываем фото
                    $handle->file_new_name_body = $tpl->user['id'];
                    $handle->allowed = array(
                        'image/jpeg',
                        'image/gif',
                        'image/png'
                    );
                    $handle->file_max_size = 102400;
                    $handle->file_overwrite = TRUE;
                    $handle->image_resize = TRUE;
                    $handle->image_x = 32;
                    $handle->image_y = 32;
                    $handle->image_convert = 'gif';
                    $handle->process(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR);
                    if ($handle->processed) {
                        $tpl->message = lng('avatar_uploaded');
                        $tpl->continue = Vars::$MODULE_URI . '?act=edit&amp;mod=avatar';
                        $tpl->contents = $tpl->includeTpl('message', 1);
                    } else {
                        echo Functions::displayError($handle->error);
                    }
                    $handle->clean();
                }
            } else {
                $tpl->form_token = mt_rand(100, 10000);
                $_SESSION['form_token'] = $tpl->form_token;
                $tpl->contents = $tpl->includeTpl('profile_upload_avatar');
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
        if (isset($_POST['submit'])
            && isset($_POST['form_token'])
            && isset($_SESSION['form_token'])
            && $_POST['form_token'] == $_SESSION['form_token']
        ) {
            $handle = new upload($_FILES['imagefile']);
            if ($handle->uploaded) {
                // Обрабатываем фото
                $handle->file_new_name_body = $tpl->user['id'];
                //$handle->mime_check = false;
                $handle->allowed = array(
                    'image/jpeg',
                    'image/gif',
                    'image/png'
                );
                $handle->file_max_size = 1024 * Vars::$SYSTEM_SET['flsz'];
                $handle->file_overwrite = TRUE;
                $handle->image_resize = TRUE;
                $handle->image_x = 320;
                $handle->image_ratio_y = TRUE;
                $handle->image_convert = 'jpg';
                $handle->process(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR);
                if ($handle->processed) {
                    // Обрабатываем превьюшку
                    $handle->file_new_name_body = $tpl->user['id'] . '_small';
                    $handle->file_overwrite = TRUE;
                    $handle->image_resize = TRUE;
                    $handle->image_x = 100;
                    $handle->image_ratio_y = TRUE;
                    $handle->image_convert = 'jpg';
                    $handle->process(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR);
                    if ($handle->processed) {
                        echo'<div class="gmenu"><p>' . lng('photo_uploaded') . '<br />' .
                            '<a href="' . Vars::$URI . '?act=edit&amp;user=' . $tpl->user['id'] . '">' . lng('continue') . '</a></p></div>';
                    } else {
                        echo Functions::displayError($handle->error);
                    }
                } else {
                    echo Functions::displayError($handle->error);
                }
                $handle->clean();
            }
        } else {
            $tpl->form_token = mt_rand(100, 10000);
            $_SESSION['form_token'] = $tpl->form_token;
            $tpl->contents = $tpl->includeTpl('upload_photo');
        }
        break;

    case 'administration':
        /*
        -----------------------------------------------------------------
        Административные функции
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 7) {
            if (isset($_POST['submit'])
                && isset($_POST['rights'])
                && isset($_POST['password'])
                && isset($_POST['form_token'])
                && isset($_SESSION['form_token'])
                && $_POST['form_token'] == $_SESSION['form_token']
                && $_POST['rights'] != $tpl->user['rights']
                && $_POST['rights'] >= 0
                && $_POST['rights'] != 8
                && $_POST['rights'] <= 9
            ) {
                $rights = intval($_POST['rights']);
                $password = trim($_POST['password']);
                if (Validate::password($password) === TRUE
                    && crypt($password, Vars::$USER_DATA['password']) === Vars::$USER_DATA['password']
                    && (Vars::$USER_RIGHTS == 9 || (Vars::$USER_RIGHTS == 7 && $rights < 7))
                ) {
                    // Если пароль совпадает, обрабатываем форму
                    mysql_query("UPDATE `users` SET `rights` = '$rights' WHERE `id` = " . $tpl->user['id']);
                    $tpl->user['rights'] = $rights;
                    $tpl->save = 1;
                    if ($tpl->user['id'] == Vars::$USER_ID) {
                        header('Location: ' . Vars::$URI . '?act=edit');
                        exit;
                    }
                } else {
                    $error['password'] = lng('error_wrong_password');
                }
            }

            $tpl->error = $error;
            $tpl->form_token = mt_rand(100, 10000);
            $_SESSION['form_token'] = $tpl->form_token;
            $tpl->contents = $tpl->includeTpl('profile_edit_adm');
        } else {
            echo Functions::displayError(lng('access_forbidden'));
        }
        break;

    case 'nickname':
        /*
        -----------------------------------------------------------------
        Смена ника
        -----------------------------------------------------------------
        */
        if ($tpl->setUsers['change_nickname'] || Vars::$USER_RIGHTS >= 7) {
            if ($tpl->user['change_time'] < time() - (Vars::$USER_SYS['change_period'] * 86400)
                || Vars::$USER_RIGHTS >= 7
            ) {
                $tpl->nickname = isset($_POST['nickname']) ? trim($_POST['nickname']) : $tpl->user['nickname'];
                if ((isset($_POST['check_login']) || isset($_POST['submit']))
                    && isset($_POST['password'])
                    && isset($_POST['form_token'])
                    && isset($_SESSION['form_token'])
                    && $_POST['form_token'] == $_SESSION['form_token']
                    && $tpl->nickname != $tpl->user['nickname']
                    && Validate::nickname($tpl->nickname, TRUE)
                    && Validate::nicknameAvailability($tpl->nickname, TRUE)
                ) {
                    if (isset($_POST['submit'])) {
                        $password = trim($_POST['password']);
                        if (Validate::password($password) === TRUE
                            && crypt($password, Vars::$USER_DATA['password']) === Vars::$USER_DATA['password']
                        ) {
                            mysql_query("UPDATE `users` SET
                                    `nickname` = '" . mysql_real_escape_string($tpl->nickname) . "',
                                    `change_time` = " . time() . "
                                    WHERE `id` = " . $tpl->user['id']
                            );
                            $tpl->message = lng('change_nickname_confirm');
                            $tpl->continue = Vars::$MODULE_URI . '/profile?act=settings&amp;user=' . $tpl->user['id'];
                            $tpl->contents = $tpl->includeTpl('message', 1);
                            exit;
                        } else {
                            $error['password'] = lng('error_wrong_password');
                        }
                    } else {
                        $tpl->available = 1;
                    }
                }

                $tpl->error = array_merge($error, Validate::$error);
                $tpl->form_token = mt_rand(100, 10000);
                $_SESSION['form_token'] = $tpl->form_token;
                $tpl->contents = $tpl->includeTpl('profile_change_nickname');
            } else {
                $tpl->change_time = $tpl->user['change_time'] + (Vars::$USER_SYS['change_period'] * 86400);
                $tpl->contents = $tpl->includeTpl('profile_change_nickname_wait');
            }
        } else {
            echo Functions::displayError(lng('access_forbidden'));
        }
        break;

    case 'status':
        /*
        -----------------------------------------------------------------
        Смена статуса
        -----------------------------------------------------------------
        */
        $tpl->status = Validate::filterString($tpl->user['status']);
        if ($tpl->setUsers['change_status'] || Vars::$USER_RIGHTS >= 7) {
            if (isset($_POST['submit'])
                && isset($_POST['status'])
                && isset($_POST['form_token'])
                && isset($_SESSION['form_token'])
                && $_POST['form_token'] == $_SESSION['form_token']
                && $_POST['status'] != $tpl->user['status']
            ) {
                $status = trim($_POST['status']);
                $tpl->status = Validate::filterString($status);
                if (mb_strlen($status) < 51) {
                    mysql_query("UPDATE `users` SET `status` = '" . mysql_real_escape_string($status) . "' WHERE `id` = " . $tpl->user['id']);
                    $tpl->user['status'] = $status;
                } else {
                    $tpl->error = lng('error_status_lenght');
                }
            }
            $tpl->form_token = mt_rand(100, 10000);
            $_SESSION['form_token'] = $tpl->form_token;
            $tpl->contents = $tpl->includeTpl('profile_change_status');
        } else {
            echo Functions::displayError(lng('access_forbidden'));
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Редактирование анкеты
        -----------------------------------------------------------------
        */
        $birth = strtotime($tpl->user['birth']);
        if ($birth) {
            $tpl->day = date('d', $birth);
            $tpl->month = date('m', $birth);
            $tpl->year = date('Y', $birth);
        } else {
            $tpl->day = '';
            $tpl->month = '';
            $tpl->year = '';
        }

        if (isset($_POST['submit'])
            && isset($_POST['form_token'])
            && isset($_SESSION['form_token'])
            && $_POST['form_token'] == $_SESSION['form_token']
        ) {
            // Принимаем данные о половой принадлежности
            if (Vars::$USER_SYS['change_sex'] || Vars::$USER_RIGHTS >= 7) {
                $tpl->user['sex'] = isset($_POST['sex']) && $_POST['sex'] == 'w' ? 'w' : 'm';
            }

            // Принимаем и обрабатываем Имя
            if (isset($_POST['imname'])) {
                $tpl->user['imname'] = mb_substr(trim($_POST['imname']), 0, 50);
            }

            // Принимаем и обрабатываем дату рожденья
            if (isset($_POST['day'])
                && isset($_POST['month'])
                && isset($_POST['year'])
            ) {
                if (empty($_POST['day'])
                    && empty($_POST['month'])
                    && empty($_POST['year'])
                ) {
                    // Удаляем дату рожденья
                    $tpl->user['birth'] = '00-00-0000';
                    $tpl->day = '';
                    $tpl->month = '';
                    $tpl->year = '';
                } else {
                    $tpl->day = trim($_POST['day']);
                    $tpl->month = trim($_POST['month']);
                    $tpl->year = trim($_POST['year']);
                    $tpl->user['birth'] = intval($tpl->year) . '-' . intval($tpl->month) . '-' . intval($tpl->day);
                    if (!@checkdate($tpl->month, $tpl->day, $tpl->year)
                        || $tpl->year < 1940
                        || $tpl->year > 2010
                    ) {
                        // Если дата рожденья указана неверно, показываем ошибку
                        $tpl->error['birth'] = lng('error_birth');
                    }
                }
            }

            // Принимаем и обрабатываем данные о месте проживания
            if (isset($_POST['live'])) {
                $tpl->user['live'] = mb_substr(trim($_POST['live']), 0, 100);
            }

            // Принимаем и обрабатываем дополнительную информацию "о себе"
            if (isset($_POST['about'])) {
                $tpl->user['about'] = mb_substr(trim($_POST['about']), 0, 5000);
            }

            // Принимаем и обрабатываем номер телефона
            if (isset($_POST['tel'])) {
                $tpl->user['tel'] = mb_substr(trim($_POST['tel']), 0, 100);
            }

            // Принимаем и обрабатываем URL сайта
            if (isset($_POST['siteurl'])) {
                $tpl->user['siteurl'] = mb_substr(trim($_POST['siteurl']), 0, 100);
            }

            // Принимаем и обрабатываем e-mail
            if (isset($_POST['email'])) {
                $tpl->user['email'] = mb_substr(trim($_POST['email']), 0, 50);
                $email_valid = Validate::email($tpl->user['email'], 1, 1);
                if ($email_valid) {
                    $tpl->user['mailvis'] = isset($_POST['mailvis']) ? 1 : 0;
                } else {
                    $tpl->error['email'] = Validate::$error['email'];
                }
            }

            // Принимаем и обрабатываем ICQ
            if (isset($_POST['icq']) && (empty($_POST['icq']) || intval($_POST['icq']) > 10000)) {
                $tpl->user['icq'] = intval($_POST['icq']);
            }

            // Принимаем и обрабатываем Skype
            if (isset($_POST['skype'])) {
                $tpl->user['skype'] = mb_substr(trim($_POST['skype']), 0, 50);
            }

            if (empty($tpl->error)) {
                mysql_query("UPDATE `users` SET
                `sex` = '" . $tpl->user['sex'] . "',
                `imname` = '" . mysql_real_escape_string($tpl->user['imname']) . "',
                " . (isset($tpl->birth_error) ? '' : "`birth` = '" . $tpl->user['birth'] . "',") . "
                `live` = '" . mysql_real_escape_string($tpl->user['live']) . "',
                `about` = '" . mysql_real_escape_string($tpl->user['about']) . "',
                `tel` = '" . mysql_real_escape_string($tpl->user['tel']) . "',
                `siteurl` = '" . mysql_real_escape_string($tpl->user['siteurl']) . "',
                " . (isset($email_valid) && $email_valid ? "`email` = '" . mysql_real_escape_string($tpl->user['email']) . "'," : "") . "
                `mailvis` = " . $tpl->user['mailvis'] . ",
                `icq` = " . $tpl->user['icq'] . ",
                `skype` = '" . mysql_real_escape_string($tpl->user['skype']) . "'
                WHERE `id` = " . $tpl->user['id']
                ) or exit('MYSQL: ' . mysql_error());
                $tpl->save = 1;
            }
        }

        $tpl->form_token = mt_rand(100, 10000);
        $_SESSION['form_token'] = $tpl->form_token;
        $tpl->contents = $tpl->includeTpl('profile_edit');
}
