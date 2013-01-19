<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_USERS') or die('Error: restricted access');
$uri = Router::getUri(4);
//TODO: Добавить проверку на разрешение выгрузки аватаров
$tpl = Template::getInstance();
$form = new Form($uri);

$form
    ->fieldsetStart(__('upload_avatar'))

    ->add('file', 'image', array(
    'label'       => __('select_image'),
    'description' => __('select_avatar_help')))

    ->add('file', 'animation', array(
    'label'       => __('animation'),
    'description' => __('select_animation_help')))

    ->fieldsetStart()

    ->add('submit', 'submit', array(
    'value' => __('save'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Router::getUri(3) . 'option/">' . __('back') . '</a>');

$tpl->form = $form->display();

if($form->isSubmitted){

}

$tpl->contents = $tpl->includeTpl('upload_avatar');

/*
if (isset($_POST['submit'])
    && isset($_POST['form_token'])
    && isset($_SESSION['form_token'])
    && $_POST['form_token'] == $_SESSION['form_token']
) {
    if ($_FILES['image']['size'] > 0) {
        $handle = new upload($_FILES['image']);
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
                $tpl->hbar = __('upload_avatar');
                $tpl->message = __('avatar_uploaded');
                $tpl->continue = Router::getUrl(3) . '?act=settings&amp;user=' . $tpl->user['id'];
                $tpl->contents = $tpl->includeTpl('message', 1);
            } else {
                echo Functions::displayError($handle->error);
            }
            $handle->clean();
        }
    } elseif ($_FILES['animation']['size'] > 0) {
        // Проверка на допустимый вес файла
        if ($_FILES['animation']['size'] > 10240) {
            $error[] = __('error_avatar_filesize');
        }

        $param = getimagesize($_FILES['animation']['tmp_name']);

        // Проверка на допустимый тип файла
        if ($param == FALSE || $param['mime'] != 'image/gif') {
            $error[] = __('error_avatar_filetype');
        }

        // Проверка на допустимый размер изображения
        if ($param != FALSE && ($param[0] != 32 || $param[1] != 32)) {
            $error[] = __('error_avatar_size');
        }

        if (empty($error)) {
            if ((move_uploaded_file($_FILES['animation']['tmp_name'],
                FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $tpl->user['id'] . '.gif')) == TRUE
            ) {
                $tpl->hbar = __('upload_animation');
                $tpl->message = __('avatar_uploaded');
                $tpl->continue = Router::getUrl(3) . '?act=settings&amp;user=' . $tpl->user['id'];
                $tpl->contents = $tpl->includeTpl('message', 1);
            } else {
                $error[] = __('error_avatar_upload');
            }
        } else {
            echo Functions::displayError($error, '<a href="' . Router::getUrl(3) . '?act=avatar_upload&amp;user=' . $tpl->user['id'] . '">' . __('back') . '</a>');
        }
    } else {
        // Если не выбран файл
        $error[] = __('error_file_not_selected');
    }
} else {
    $tpl->form_token = mt_rand(100, 10000);
    $_SESSION['form_token'] = $tpl->form_token;
    $tpl->contents = $tpl->includeTpl('upload_avatar');
}
*/