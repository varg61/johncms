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

global $user, $tpl;

/*
-----------------------------------------------------------------
Проверяем права доступа для редактирования Профиля
-----------------------------------------------------------------
*/
if ($user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS < 7) {
    exit(Functions::displayError(lng('access_forbidden')));
}
if ($user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS != 9 && $user['rights'] >= Vars::$USER_RIGHTS) {
    exit(Functions::displayError(lng('error_rights')));
}

$tpl->setUsers = Vars::$USER_SYS;
$error = array();

if (is_file(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.gif')) {
    $tpl->avatar = TRUE;
}

if (is_file(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '_small.jpg')) {
    $tpl->photo = TRUE;
}

// Ссылка на смену Ника
if ($tpl->setUsers['change_nickname']
    || Vars::$USER_RIGHTS >= 7
) {
    $menu[] = '<a href="' . Vars::$URI . '?act=edit&amp;mod=nickname&amp;user=' . $user['id'] . '">' . lng('nickname') . '</a>';
}

// Ссылка на смену статуса
if ($tpl->setUsers['change_status']
    || Vars::$USER_RIGHTS >= 7
) {
    $menu[] = '<a href="' . Vars::$URI . '?act=edit&amp;mod=status&amp;user=' . $user['id'] . '">' . lng('change_status') . '</a>';
}

// Ссылка на смену аватара
$menu[] = '<a href="' . Vars::$URI . '?act=edit&amp;mod=avatar&amp;user=' . $user['id'] . '">' . lng('change_avatar') . '</a>';

$arg['sub'] = '<p><b>' . lng('change') . '</b>: ' . Functions::displayMenu($menu) . '</p>';
$tpl->userarg = $arg;

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
            @unlink(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.gif');
            header('Location: ' . Vars::$URI . '?act=edit&user=' . $user['id']);
            exit;
        } else {
            $tpl->form_token = mt_rand(100, 10000);
            $_SESSION['form_token'] = $tpl->form_token;
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
            @unlink(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '.jpg');
            @unlink(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $user['id'] . '_small.jpg');
            header('Location: ' . Vars::$URI . '?act=edit&user=' . $user['id']);
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
                        FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.gif')) == TRUE
                    ) {
                        echo'<div class="gmenu">' .
                            '<p>' . lng('avatar_uploaded') . '<br/>' .
                            '<a href="' . Vars::$URI . '?act=edit&amp;user=' . $user['id'] . '">' . lng('continue') . '</a></p>' .
                            '</div>';
                    } else {
                        $error[] = lng('error_avatar_upload');
                    }
                } else {
                    echo Functions::displayError($error, '<a href="' . Vars::$URI . '?act=edit&amp;mod=upload_animation&amp;user=' . $user['id'] . '">' . lng('back') . '</a>');
                }
            } else {
                $tpl->form_token = mt_rand(100, 10000);
                $_SESSION['form_token'] = $tpl->form_token;
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
                && isset($_POST['form_token'])
                && isset($_SESSION['form_token'])
                && $_POST['form_token'] == $_SESSION['form_token']
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
                    $handle->file_overwrite = TRUE;
                    $handle->image_resize = TRUE;
                    $handle->image_x = 32;
                    $handle->image_y = 32;
                    $handle->image_convert = 'gif';
                    $handle->process(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR);
                    if ($handle->processed) {
                        echo'<div class="gmenu"><p>' . lng('avatar_uploaded') . '<br />' .
                            '<a href="' . Vars::$URI . '?act=edit&amp;user=' . $user['id'] . '">' . lng('continue') . '</a></p></div>';
                    } else {
                        echo Functions::displayError($handle->error);
                    }
                    $handle->clean();
                }
            } else {
                $tpl->form_token = mt_rand(100, 10000);
                $_SESSION['form_token'] = $tpl->form_token;
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
        if (isset($_POST['submit'])
            && isset($_POST['form_token'])
            && isset($_SESSION['form_token'])
            && $_POST['form_token'] == $_SESSION['form_token']
        ) {
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
                $handle->file_overwrite = TRUE;
                $handle->image_resize = TRUE;
                $handle->image_x = 320;
                $handle->image_ratio_y = TRUE;
                $handle->image_convert = 'jpg';
                $handle->process(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR);
                if ($handle->processed) {
                    // Обрабатываем превьюшку
                    $handle->file_new_name_body = $user['id'] . '_small';
                    $handle->file_overwrite = TRUE;
                    $handle->image_resize = TRUE;
                    $handle->image_x = 100;
                    $handle->image_ratio_y = TRUE;
                    $handle->image_convert = 'jpg';
                    $handle->process(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR);
                    if ($handle->processed) {
                        echo'<div class="gmenu"><p>' . lng('photo_uploaded') . '<br />' .
                            '<a href="' . Vars::$URI . '?act=edit&amp;user=' . $user['id'] . '">' . lng('continue') . '</a></p></div>';
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
                && $_POST['rights'] != $user['rights']
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
                    mysql_query("UPDATE `users` SET `rights` = '$rights' WHERE `id` = " . $user['id']);
                    $user['rights'] = $rights;
                    $tpl->user = $user;
                    $tpl->save = 1;
                    if ($user['id'] == Vars::$USER_ID) {
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
            if ($user['change_time'] < time() - (Vars::$USER_SYS['change_period'] * 86400)
                || Vars::$USER_RIGHTS >= 7
            ) {
                $tpl->nickname = isset($_POST['nickname']) ? trim($_POST['nickname']) : $user['nickname'];
                if ((isset($_POST['check_login']) || isset($_POST['submit']))
                    && isset($_POST['password'])
                    && isset($_POST['form_token'])
                    && isset($_SESSION['form_token'])
                    && $_POST['form_token'] == $_SESSION['form_token']
                    && $tpl->nickname != $user['nickname']
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
                                    WHERE `id` = " . $user['id']
                            );
                            $tpl->contents = $tpl->includeTpl('change_nickname_confirmation');
                            exit;
                        } else {
                            $error['password'] = lng('error_wrong_password');
                        }
                    } else {
                        $tpl->available = 1;
                    }
                }

                $tpl->user = $user;
                $tpl->error = array_merge($error, Validate::$error);
                $tpl->form_token = mt_rand(100, 10000);
                $_SESSION['form_token'] = $tpl->form_token;
                $tpl->contents = $tpl->includeTpl('change_nickname');
            } else {
                $tpl->change_time = $user['change_time'] + (Vars::$USER_SYS['change_period'] * 86400);
                $tpl->contents = $tpl->includeTpl('change_nickname_wait');
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
        if ($tpl->setUsers['change_status'] || Vars::$USER_RIGHTS >= 7) {
            $tpl->status = $user['status'];
            if (isset($_POST['submit'])
                && isset($_POST['status'])
                && isset($_POST['form_token'])
                && isset($_SESSION['form_token'])
                && $_POST['form_token'] == $_SESSION['form_token']
                && $_POST['status'] != $user['status']
            ) {
                $tpl->status = trim($_POST['status']);
                if (mb_strlen($tpl->status) < 51) {
                    mysql_query("UPDATE `users` SET `status` = '" . mysql_real_escape_string($tpl->status) . "' WHERE `id` = " . $user['id']);
                    header('Location: ' . Vars::$URI . '?act=edit&user=' . $user['id']);
                    exit;
                }
                $tpl->error = lng('error_status_lenght');
            }
            $tpl->form_token = mt_rand(100, 10000);
            $_SESSION['form_token'] = $tpl->form_token;
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
        $tpl->contents = $tpl->includeTpl('change_avatar');
        break;

    default:
        /*
        -----------------------------------------------------------------
        Редактирование анкеты
        -----------------------------------------------------------------
        */
        $birth = strtotime($user['birth']);
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
            $error = array();

            // Принимаем данные о половой принадлежности
            if (Vars::$USER_SYS['change_sex'] || Vars::$USER_RIGHTS >= 7) {
                $user['sex'] = isset($_POST['sex']) && $_POST['sex'] == 'w' ? 'w' : 'm';
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
                if (empty($_POST['day'])
                    && empty($_POST['month'])
                    && empty($_POST['year'])
                ) {
                    // Удаляем дату рожденья
                    $user['birth'] = '00-00-0000';
                    $tpl->day = '';
                    $tpl->month = '';
                    $tpl->year = '';
                } else {
                    $tpl->day = trim($_POST['day']);
                    $tpl->month = trim($_POST['month']);
                    $tpl->year = trim($_POST['year']);
                    $user['birth'] = intval($tpl->year) . '-' . intval($tpl->month) . '-' . intval($tpl->day);
                    if (!checkdate($tpl->month, $tpl->day, $tpl->year)
                        || $tpl->year < 1940
                        || $tpl->year > 2010
                    ) {
                        // Если дата рожденья указана неверно, показываем ошибку
                        $tpl->birth_error = lng('error_birth');
                    }
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

            mysql_query("UPDATE `users` SET
                `sex` = '" . $user['sex'] . "',
                `imname` = '" . mysql_real_escape_string($user['imname']) . "',
                " . (isset($tpl->birth_error) ? '' : "`birth` = '" . $user['birth'] . "',") . "
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

        $tpl->user = $user;
        $tpl->form_token = mt_rand(100, 10000);
        $_SESSION['form_token'] = $tpl->form_token;
        $tpl->contents = $tpl->includeTpl('profile_edit');
}
