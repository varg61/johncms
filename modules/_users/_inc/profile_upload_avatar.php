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
Выгрузка обычного аватара
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