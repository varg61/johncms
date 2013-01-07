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
//Закрываем прямой доступ к файлу
defined('_IN_JOHNCMS_MAIL') or die('Error: restricted access');
//Закрываем доступ гостям
if (!Vars::$USER_ID) {
    Header('Location: ' . Vars::$HOME_URL . '/404');
    exit;
}

$backLink = Router::getUrl(2);

if (Vars::$ID) {
    $req = mysql_query("SELECT * FROM `cms_mail_messages` WHERE (`user_id`='" . Vars::$USER_ID . "' OR `contact_id`='" .
        Vars::$USER_ID . "') AND `id` = '" . Vars::$ID . "' AND `filename` != '' AND `delete`!='" . Vars::
    $USER_ID . "' LIMIT 1");
    //Проверяем существование файла в базе
    if (mysql_num_rows($req) == 0) {
        //Выводим ошибку
        $tpl->contents = Functions::displayError(__('file_does_not_exist'), '<a href="' . $backLink . '">' . __('contacts') . '</a>');
    }
    $res = mysql_fetch_assoc($req);
    //Проверяем существование файла на сервере
    if (file_exists(FILEPATH . 'users/pm/' . $res['filename'])) {
        if (empty($_SESSION['file_' . Vars::$ID])) {
            mysql_query("UPDATE `cms_mail_messages` SET `filecount` = `filecount`+1 WHERE `id` = '" . Vars::
            $ID . "' AND `user_id`!='" . Vars::$USER_ID . "' LIMIT 1");
            $_SESSION['file_' . Vars::$ID] = 1;
        }
        //Загрузка файла
        Header('Location: ' . Vars::$HOME_URL . '/files/users/pm/' . $res['filename']);
        exit;
    } else {
        //Если файл не существует, показываем сообщение об ошибке
        $tpl->contents = Functions::displayError(__('file_does_not_exist'), '<a href="' . $backLink . '">' . __('contacts') . '</a>');
    }
} else {
    //Если файл не выбран, показываем сообщение об ошибке
    $tpl->contents = Functions::displayError(__('file_does_not_exist'), '<a href="' . $backLink . '">' . __('contacts') . '</a>');
}
