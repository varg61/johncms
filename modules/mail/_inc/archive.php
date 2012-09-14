<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined( '_IN_JOHNCMS' ) or die( 'Error: restricted access' );
//Закрываем прямой доступ к файлу
defined( '_IN_JOHNCMS_MAIL' ) or die( 'Error: restricted access' );
//Закрываем доступ гостям
if ( !Vars::$USER_ID )
{
    Header( 'Location: ' . Vars::$HOME_URL . '/404' );
    exit;
}
//Заголовок
$tpl->title = lng( 'mail' ) . ' | ' . lng( 'archive' );
//Считаем количество контактов в архиве
$total = mysql_result( mysql_query( "SELECT COUNT(*)
FROM (SELECT DISTINCT `cms_mail_contacts`.`contact_id` 
FROM `cms_mail_contacts`
LEFT JOIN `cms_mail_messages`
ON (`cms_mail_contacts`.`user_id`=`cms_mail_messages`.`user_id` 
OR `cms_mail_contacts`.`user_id`=`cms_mail_messages`.`contact_id`)
AND (`cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`user_id` 
OR `cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`contact_id`)
LEFT JOIN `users` 
ON `cms_mail_contacts`.`contact_id`=`users`.`id` 
WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "'
AND `cms_mail_contacts`.`archive`='1'
AND `cms_mail_contacts`.`banned`='0'
AND `cms_mail_contacts`.`delete`='0'
AND `cms_mail_messages`.`delete_out`!='" . Vars::$USER_ID . "' 
AND `cms_mail_messages`.`delete_in`!='" . Vars::$USER_ID . "'
AND `users`.`id` IS NOT NULL) a" ), 0 );

$tpl->total = $total;

if ( $total )
{
    //Удаляем контакты из архива
	if ( isset( $_POST['delete'] ) && ValidMail::checkCSRF() === true )
    {
        if ( !empty( $_POST['delch'] ) && is_array( $_POST['delch'] ) )
        {
            Mail::mailSelectContacts( $_POST['delch'], 'delete' );
        }
        Header( 'Location: ' . Vars::$MODULE_URI . '?act=archive' );
        exit;
    }
	//Формируем список контактов в архиве
    $query = mysql_query("SELECT `users`.`id`, `users`.`nickname`,  `users`.`sex`,  `users`.`last_visit`, 
	`cms_mail_contacts`.`contact_id`, `cms_mail_contacts`.`user_id`, COUNT(*) as `count`
	FROM `cms_mail_contacts`
	LEFT JOIN `cms_mail_messages`
	ON (`cms_mail_contacts`.`user_id`=`cms_mail_messages`.`user_id` 
	OR `cms_mail_contacts`.`user_id`=`cms_mail_messages`.`contact_id`)
	AND (`cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`user_id` 
	OR `cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`contact_id`)
	LEFT JOIN `users` 
	ON `cms_mail_contacts`.`contact_id`=`users`.`id` 
	WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "'
	AND `cms_mail_contacts`.`archive`='1'
	AND `cms_mail_contacts`.`banned`='0'
	AND `cms_mail_contacts`.`delete`='0'
	AND `cms_mail_messages`.`delete_out`!='" . Vars::$USER_ID . "' 
	AND `cms_mail_messages`.`delete_in`!='" . Vars::$USER_ID . "'
	AND `users`.`id` IS NOT NULL
	GROUP BY `cms_mail_contacts`.`contact_id`
	ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination() );
    $array = array();
    $i = 1;
    while ( $row = mysql_fetch_assoc( $query ) )
    {
        $array[] = array(
            'id' => $row['id'],
            'icon' => Functions::getImage( 'usr_' . ( $row['sex'] == 'm' ? 'm' : 'w' ) . '.png', '' ),
            'list' => ( ( $i % 2 ) ? 'list1' : 'list2' ),
            'nickname' => $row['nickname'],
            'count' => $row['count'],
            'count_new' => Mail::countNew( $row['contact_id'] ),
            'url' => ( Vars::$MODULE_URI . '?act=messages&amp;id=' . $row['id'] ),
            'online' => ( time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>' ) );
        ++$i;
    }
	//Навигация
    $tpl->display_pagination = Functions::displayPagination( Vars::$MODULE_URI . '?act=archive&amp;',
        Vars::$START, $total, Vars::$USER_SET['page_size'] );
    $tpl->query = $array;
	$tpl->token = mt_rand(100, 10000);
	$_SESSION['token_status'] = $tpl->token;
	//Подключаем шаблон модуля contacts.php
    $tpl->contacts = $tpl->includeTpl( 'contacts' );
} else
{
    //Выводим сообщение если контактов нет
	$tpl->contacts = '<div class="rmenu">' . lng( 'no_archive' ) . '</div>';
}
//Подключаем шаблон модуля archive.php
$tpl->contents = $tpl->includeTpl( 'archive' );