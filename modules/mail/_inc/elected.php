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

//Заголовок
$tpl->title = __('mail') . ' | ' . __('elected');
if (Vars::$ID) {
    if (Vars::$ID == Vars::$USER_ID) {
        $tpl->contents = Functions::displayError(__('error_request'), '<a href="' . $backLink . '">' . __('contacts') . '</a>');
    } else {
        $q = mysql_query("SELECT `nickname` FROM `users` WHERE `id`='" . Vars::$ID . "' LIMIT 1");
        if (mysql_num_rows($q)) {
            $total = mysql_result(mysql_query("SELECT COUNT(*)
			FROM `cms_mail_messages`
			WHERE ((`user_id`='" . Vars::$USER_ID . "'
			AND `contact_id`='" . Vars::$ID . "')
			OR (`contact_id`='" . Vars::$USER_ID . "'
			AND `user_id`='" . Vars::$ID . "'))
			AND (`elected_in`='" . Vars::$USER_ID . "'
			OR `elected_out`='" . Vars::$USER_ID . "') AND `delete`!='" . Vars::$USER_ID . "'"), 0);
            if ($total) {
                //Формируем список избранных сообщений определенного контакта
                $query = mysql_query("SELECT `cms_mail_messages`.*, `cms_mail_messages`.`id` as `mid`, `users`.*
				FROM `cms_mail_messages`
				LEFT JOIN `users` 
				ON `cms_mail_messages`.`user_id`=`users`.`id` 
				WHERE ((`cms_mail_messages`.`user_id`='" . Vars::$USER_ID . "'
				AND `cms_mail_messages`.`contact_id`='" . Vars::$ID . "')
				OR (`cms_mail_messages`.`contact_id`='" . Vars::$USER_ID . "'
				AND `cms_mail_messages`.`user_id`='" . Vars::$ID . "'))
				AND (`cms_mail_messages`.`elected_in`='" . Vars::$USER_ID . "'
				OR `cms_mail_messages`.`elected_out`='" . Vars::$USER_ID . "')
				AND `delete`!='" . Vars::$USER_ID . "'
				ORDER BY `cms_mail_messages`.`time` DESC" . Vars::db_pagination());
                $array = array();

                $i = 1;
                while ($row = mysql_fetch_assoc($query)) {
                    $text = Validate::checkout($row['text'], 1, 1);
                    if (Vars::$USER_SET['smileys'])
                        $text = Functions::smilies($text, $row['rights'] >= 1 ? 1 : 0);
                    $array[] = array(
                        'id'        => $row['id'],
                        'mid'       => $row['mid'],
                        'icon'      => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' : 'w') .
                            '.png', '', 'align="middle"'),
                        'list'      => (($i % 2) ? 'list1' : 'list2'),
                        'nickname'  => $row['nickname'],
                        'file'      => $row['filename'] ? '<a href="' . $backLink . '?act=load&amp;id=' .
                            $row['mid'] . '">' . $row['filename'] . '</a> (' . UploadMail::formatsize($row['filesize']) .
                            ')(' . $row['filecount'] . ')' : '',
                        'time'      => Functions::displayDate($row['time']),
                        'text'      => $text,
                        'urlDelete' => $backLink . '?act=messages&amp;mod=delete&amp;id=' . $row['mid'],
                        'url'       => ($backLink . '?act=messages&amp;id=' . $row['id']),
                        'online'    => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                            '<span class="green"> [ON]</span>'),
                        'elected'   => (($row['elected_in'] != Vars::$USER_ID && $row['elected_out'] !=
                            Vars::$USER_ID) ? TRUE : FALSE),
                        'selectBar' => '[<span class="red">х</span> <a href="' . $backLink .
                            '?act=delete&amp;id=' . $row['mid'] . '">' . __('delete') . '</a>]');
                    ++$i;
                }

                $tpl->query = $array;
                $tpl->titleTest = '<div class="phdr"><strong>' . __('elected') . '</strong></div>';
                $tpl->urlTest = '<p><a href="' . Vars::$URI . '">' . __('contacts') .
                    '</a></p>';
                $tpl->total = $total;
                //Навигация
                $tpl->display_pagination = Functions::displayPagination($backLink . '?act=elected&amp;id=' .
                    Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']);
                //Подключаем шаблон модуля list.php
                $tpl->contents = $tpl->includeTpl('list');
            } else {
                Header('Location: ' . $backLink . '?act=elected');
                exit;
            }
        } else {
            //Если пользователь не существует, показываем ошибку
            $tpl->contents = Functions::displayError(__('user_does_not_exist'), '<a href="' .
                $backLink . '">' . __('contacts') . '</a>');
        }
    }
} else {
    $total = mysql_result(mysql_query("SELECT COUNT(*)
	FROM (SELECT DISTINCT `cms_mail_contacts`.`contact_id` 
	FROM `cms_mail_contacts` 
	LEFT JOIN `cms_mail_messages` 
	ON `cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`user_id` 
	OR `cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`contact_id` 
	WHERE ((`cms_mail_contacts`.`contact_id`!='" . Vars::$USER_ID . "' 
	AND (`cms_mail_messages`.`contact_id`!='" . Vars::$USER_ID . "' 
	OR `cms_mail_messages`.`user_id`!='" . Vars::$USER_ID . "') 
	AND (`cms_mail_messages`.`elected_out`='" . Vars::$USER_ID . "' 
	OR `cms_mail_messages`.`elected_in`='" . Vars::$USER_ID . "') 
	AND (`cms_mail_messages`.`delete_out`!='" . Vars::$USER_ID . "' 
	OR `cms_mail_messages`.`delete_in`!='" . Vars::$USER_ID . "')) 
	AND (`cms_mail_contacts`.`delete`='0' 
	AND `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "')) 
	AND `cms_mail_messages`.`delete`!='" . Vars::$USER_ID . "') a"), 0);
    $tpl->total = $total;
    if ($total) {
        //Удаляем сообщения
        if (isset($_POST['delete']) && ValidMail::checkCSRF() === TRUE) {
            if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
                Mail::mailSelectContacts($_POST['delch'], 'elected');
            }
            Header('Location: ' . $backLink . '?act=elected');
            exit;
        }
        //Формируем список избранных сообщений по контактам
        $query = mysql_query("SELECT `users`.`id`, `users`.`nickname`,  `users`.`sex`,  `users`.`last_visit`,
		`cms_mail_contacts`.`contact_id`, `cms_mail_contacts`.`user_id`, COUNT(*) as `count`
		FROM `cms_mail_contacts`
		LEFT JOIN `cms_mail_messages`
		ON `cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`user_id`
		OR `cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`contact_id`
		LEFT JOIN `users`
		ON `cms_mail_contacts`.`contact_id`=`users`.`id`
		WHERE ((`cms_mail_contacts`.`contact_id`!='" . Vars::$USER_ID . "' 
		AND (`cms_mail_messages`.`contact_id`!='" . Vars::$USER_ID . "' 
		OR `cms_mail_messages`.`user_id`!='" . Vars::$USER_ID . "') 
		AND (`cms_mail_messages`.`elected_out`='" . Vars::$USER_ID . "' 
		OR `cms_mail_messages`.`elected_in`='" . Vars::$USER_ID . "') 
		AND (`cms_mail_messages`.`delete_out`!='" . Vars::$USER_ID . "' 
		OR `cms_mail_messages`.`delete_in`!='" . Vars::$USER_ID . "')) 
		AND (`cms_mail_contacts`.`delete`='0' 
		AND `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "')) 
		AND `cms_mail_messages`.`delete`!='" . Vars::$USER_ID . "'
		GROUP BY `cms_mail_contacts`.`contact_id`
		ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination());
        $array = array();
        $i = 1;
        while ($row = mysql_fetch_assoc($query)) {
            $array[] = array(
                'id'        => $row['id'],
                'icon'      => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' : 'w') . '.png',
                    '', 'align="middle"'),
                'list'      => (($i % 2) ? 'list1' : 'list2'),
                'nickname'  => $row['nickname'],
                'count'     => $row['count'],
                'count_new' => '',
                'url'       => ($backLink . '?act=elected&amp;id=' . $row['id']),
                'online'    => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                    '<span class="green"> [ON]</span>'));
            ++$i;
        }
        //Навигация
        $tpl->display_pagination = Functions::displayPagination($backLink . '?act=elected&amp;',
            Vars::$START, $total, Vars::$USER_SET['page_size']);
        $tpl->query = $array;
        $tpl->token = mt_rand(100, 10000);
        $_SESSION['token_status'] = $tpl->token;
        //Подключаем шаблон модуля contacts.php
        $tpl->contacts = $tpl->includeTpl('contacts');
    }
    //Подключаем шаблон модуля elected.php
    $tpl->contents = $tpl->includeTpl('elected');
}
