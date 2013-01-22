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

$backLink = Router::getUri(2);

//Заголовок
$tpl->title = __('mail') . ' | ' . __('new_messages');

//Считаем новые сообщения
$total = DB::PDO()->query("SELECT COUNT(*) FROM (SELECT DISTINCT `user_id` FROM `cms_mail_messages` WHERE `contact_id`='" .
    Vars::$USER_ID . "' AND `cms_mail_messages`.`read`='0' AND `cms_mail_messages`.`delete_in`!='" .
    Vars::$USER_ID . "' AND `cms_mail_messages`.`delete_out`!='" . Vars::$USER_ID . "') a;")->fetchColumn();
if ($total == 1) {
    //Если все новые сообщения от одного итого же чела показываем сразу переписку
    $max = DB::PDO()->query("SELECT `user_id`, count(*) FROM `cms_mail_messages` WHERE `contact_id`='" .
        Vars::$USER_ID . "' AND `read`='0' GROUP BY `user_id`;")->fetchColumn();
    Header('Location: ' . $backLink . '?act=messages&id=' . $max);
    exit();
}
if ($total) {
    //Отмечаем сообщения как прочитанные
    if (isset($_POST['addnew']) && ValidMail::checkCSRF() === TRUE) {
        if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
            $id = array_map('intval', $_POST['delch']);
            $id = implode(',', $id);
            if (!empty($id)) {
                $mass = array();
                $mass_contact = array();
                $query = DB::PDO()->query("SELECT *
                FROM `cms_mail_contacts` 
                WHERE `user_id`='" . Vars::$USER_ID . "' 
                AND `contact_id` IN (" . $id . ")");
                while ($rows = $query->fetch()) {
                    $mass[] = $rows['id'];
                    $mass_contact[] = $rows['contact_id'];
                }
                if (!empty($mass)) {
                    $sms = implode(',', $mass_contact);
                    $out = array();
                    $query1 = DB::PDO()->query("SELECT *
                    FROM `cms_mail_messages` 
                    WHERE `user_id` IN (" . $sms . ") 
                    AND `contact_id`='" . Vars::$USER_ID . "' 
                    AND `read`='0' AND `delete`!='" . Vars::$USER_ID . "'");
                    while ($rows1 = $query1->fetch()) {
                        $out[] = $rows1['id'];
                    }
                    if (!empty($out)) {
                        $in_str = implode(',', $out);
                        DB::PDO()->exec("UPDATE `cms_mail_messages` SET
						`read`='1' 
                        WHERE `id` IN (" . $in_str . ")");
                    }
                }
            }
        }
        Header('Location: ' . $backLink . '?act=new');
        exit;
    }
    //Формируем список новых сообщений по контактам
    $query = DB::PDO()->query("SELECT `users`.`id`, `users`.`nickname`,  `users`.`sex`,  `users`.`last_visit`,
	`cms_mail_contacts`.`contact_id`, `cms_mail_contacts`.`user_id`, COUNT(*) as `count`
	FROM `cms_mail_messages`
	LEFT JOIN `cms_mail_contacts` 
	ON `cms_mail_messages`.`user_id`=`cms_mail_contacts`.`user_id`
	LEFT JOIN `users` 
	ON `cms_mail_contacts`.`user_id`=`users`.`id`
	WHERE `cms_mail_contacts`.`contact_id`='" . Vars::$USER_ID . "'
	AND `cms_mail_messages`.`read`='0'
	AND `cms_mail_messages`.`delete_in`!='" . Vars::$USER_ID . "'
	AND `cms_mail_messages`.`delete_out`!='" . Vars::$USER_ID . "' 
	GROUP BY `cms_mail_messages`.`user_id`
	ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination());
    $array = array();
    $i = 1;
    while ($row = $query->fetch()) {
        $array[] = array(
            'id'        => $row['id'],
            'icon'      => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' : 'w') . '.png', ''),
            'list'      => (($i % 2) ? 'list1' : 'list2'),
            'nickname'  => $row['nickname'],
            'count'     => $row['count'],
            'count_new' => '+' . $row['count'],
            'url'       => ($backLink . '?act=messages&amp;id=' . $row['id']),
            'online'    => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>'));
        ++$i;
    }
    $tpl->total = $total;
    //Навигация
    $tpl->display_pagination = Functions::displayPagination($backLink . '?act=new&amp;', Vars::
    $START, $total, Vars::$USER_SET['page_size']);
    $tpl->query = $array;
    //Подключем шаблон contact.php
    $tpl->token = mt_rand(100, 10000);
    $_SESSION['token_status'] = $tpl->token;
    $tpl->contacts = $tpl->includeTpl('contacts');
}
//Подключем шаблон new.php
$tpl->contents = $tpl->includeTpl('new');
