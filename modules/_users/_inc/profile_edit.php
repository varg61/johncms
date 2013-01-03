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

if (is_file(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $tpl->user['id'] . '_small.jpg')) {
    $tpl->photo = TRUE;
}

switch (Vars::$MOD) {
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
                $handle->file_max_size = 1024 * Vars::$SYSTEM_SET['filesize'];
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
                        echo'<div class="gmenu"><p>' . __('photo_uploaded') . '<br />' .
                            '<a href="' . Vars::$URI . '?act=edit&amp;user=' . $tpl->user['id'] . '">' . __('continue') . '</a></p></div>';
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
                            $tpl->message = __('change_nickname_confirm');
                            $tpl->continue = Vars::$MODULE_URI . '/profile?act=settings&amp;user=' . $tpl->user['id'];
                            $tpl->contents = $tpl->includeTpl('message', 1);
                            exit;
                        } else {
                            $error['password'] = __('error_wrong_password');
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
            echo Functions::displayError(__('access_forbidden'));
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Редактирование анкеты
        -----------------------------------------------------------------
        */
        $form = new Form(Vars::$URI . '?act=edit&amp;user=' . $tpl->user['id']);

        $form
            ->add('text', 'imname', array(
            'label'       => __('name'),
            'value'       => $tpl->user['imname'],
            'description' => __('description_name')));

        if (Vars::$USER_SYS['change_sex'] || Vars::$USER_RIGHTS >= 7) {
            $form
                ->add('radio', 'sex', array(
                'label'   => __('sex'),
                'checked' => $tpl->user['sex'],
                'items'   => array(
                    'm' => __('sex_m'),
                    'w' => __('sex_w')
                )));
        }

        $form
            ->add('text', 'day', array(
            'label' => __('birthday'),
            'value' => date("d", strtotime($tpl->user['birth'])),
            'class' => 'mini'))

            ->add('text', 'month', array(
            'value' => date("m", strtotime($tpl->user['birth'])),
            'class' => 'mini'))

            ->add('text', 'year', array(
            'value'       => date("Y", strtotime($tpl->user['birth'])),
            'class'       => 'small',
            'description' => __('description_birth')))

            ->add('text', 'live', array(
            'label'       => __('live'),
            'value'       => $tpl->user['live'],
            'description' => __('description_live')))

            ->add('textarea', 'about', array(
            'label'       => __('about'),
            'value'       => $tpl->user['about'],
            'buttons'     => (Vars::$IS_MOBILE ? FALSE : TRUE),
            'description' => __('description_about')))

            ->add('text', 'tel', array(
            'label'       => __('phone_number'),
            'value'       => $tpl->user['tel'],
            'description' => __('description_phone_number')))

            ->add('text', 'email', array(
            'label' => 'E-mail',
            'value' => $tpl->user['email']))

            ->add('checkbox', 'mailvis', array(
            'label_inline' => __('show_in_profile'),
            'checked'      => $tpl->user['mailvis'],
            'description'  => __('description_email')))

            ->add('text', 'siteurl', array(
            'label'       => __('site'),
            'value'       => $tpl->user['siteurl'],
            'description' => __('description_siteurl')))

            ->add('text', 'skype', array(
            'label'       => 'Skype',
            'value'       => $tpl->user['skype'],
            'description' => __('description_skype')))

            ->add('text', 'icq', array(
            'label'       => 'ICQ',
            'value'       => $tpl->user['icq'],
            'description' => __('description_icq')))

            ->addHtml('<br/>')

            ->add('submit', 'submit', array(
            'value' => __('save'),
            'class' => 'btn btn-primary btn-large'))

            ->addHtml('<a class="btn" href="' . Vars::$URI . '?act=settings&amp;user=' . $tpl->user['id'] . '">' . __('back') . '</a>');

        $tpl->form = $form->display();

        if ($form->isSubmitted) {
            foreach ($form->validOutput as $key => $val) {
                $tpl->user[$key] = $val;
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
                        $tpl->error['birth'] = __('error_birth');
                    }
                }
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

        $tpl->contents = $tpl->includeTpl('profile_edit');
}
