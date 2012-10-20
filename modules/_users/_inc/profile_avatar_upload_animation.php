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
                $tpl->hbar = lng('upload_animation');
                $tpl->message = lng('avatar_uploaded');
                $tpl->continue = Vars::$URI . '?act=settings&amp;user=' . $tpl->user['id'];
                $tpl->contents = $tpl->includeTpl('message', 1);
            } else {
                $error[] = lng('error_avatar_upload');
            }
        } else {
            echo Functions::displayError($error, '<a href="' . Vars::$URI . '?act=avatar_upload_animation&amp;user=' . $tpl->user['id'] . '">' . lng('back') . '</a>');
        }
    } else {
        $tpl->form_token = mt_rand(100, 10000);
        $_SESSION['form_token'] = $tpl->form_token;
        $tpl->contents = $tpl->includeTpl('profile_avatar_upload_animation');
    }
} else {
    echo Functions::displayError(lng('access_forbidden'));
}