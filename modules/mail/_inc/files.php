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
    Header('Location: ' . Vars::$HOME_URL . '404');
    exit;
}
//Заголовок
$tpl->title = __('mail') . ' | ' . __('files');
//Считаем количество файлов
$tpl->total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_messages` WHERE `filename`!='' AND (`user_id`='" .
    Vars::$USER_ID . "' OR `contact_id`='" . Vars::$USER_ID . "') AND `delete_in`!='" . Vars::$USER_ID .
    "' AND `delete_out`!='" . Vars::$USER_ID . "' AND `delete`!='" . Vars::$USER_ID . "'"), 0);
if ($tpl->total) {
    //Формируем список файлов
    $array = array();
    $query = mysql_query("SELECT * FROM `cms_mail_messages` WHERE `filename`!='' AND (`user_id`='" . Vars::
    $USER_ID . "' OR `contact_id`='" . Vars::$USER_ID . "') AND `delete_in`!='" . Vars::$USER_ID .
        "' AND `delete_out`!='" . Vars::$USER_ID . "' AND `delete`!='" . Vars::$USER_ID . "' ORDER BY `time` DESC" .
        Vars::db_pagination());
    $i = 1;
    while ($row = mysql_fetch_assoc($query)) {
        $array[] = array(
            'id'        => $row['id'],
            'list'      => (($i % 2) ? 'list1' : 'list2'),
            'filename'  => $row['filename'],
            'icon'      => Functions::getImage(UploadMail::fileicon($row['filename']), '', 'style="margin: 0 0 -3px 0;"'),
            'filesize'  => UploadMail::formatsize($row['filesize']),
            'filecount' => $row['filecount']);
        ++$i;
    }
    //Навигация
    $tpl->display_pagination = Functions::displayPagination(Router::getUrl(2) . '?act=files&amp;', Vars::
    $START, $tpl->total, Vars::$USER_SET['page_size']);
    $tpl->query = $array;
    unset($array);
}
//Подключаем шаблон модуля files.php
$tpl->contents = $tpl->includeTpl('files');