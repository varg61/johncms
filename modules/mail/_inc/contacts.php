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
$tpl->title = lng( 'contacts' );
//Считаем количество контактов
$total = mysql_result( mysql_query( "SELECT COUNT(*)
FROM `cms_mail_contacts`
WHERE `user_id`='" . Vars::$USER_ID . "' AND `delete`='0' AND `banned`='0' AND `archive`='0'" ), 0 );
$tpl->total = $total;
if ( $total )
{
    //Перемещаем контакты в корзину
	if ( isset( $_POST['delete'] ) && ValidMail::checkCSRF() === true )
	{
		if ( !empty( $_POST['delch'] ) && is_array( $_POST['delch'] ) )
		{
			Mail::mailSelectContacts( $_POST['delch'], 'delete' );
		}
		Header( 'Location: ' . Vars::$MODULE_URI . '?act=contacts' );
		exit;
	} //Перемещаем контакты в архив
	else if ( isset( $_POST['archive'] ) && ValidMail::checkCSRF() === true )
	{
		if ( !empty( $_POST['delch'] ) && is_array( $_POST['delch'] ) )
		{
			Mail::mailSelectContacts( $_POST['delch'], 'archive' );
		}
		Header( 'Location: ' . Vars::$MODULE_URI . '?act=contacts' );
		exit;
	}
	//Формируем список контактов
	$query = mysql_query( "SELECT * 
	FROM `cms_mail_contacts` 
	LEFT JOIN `users` 
	ON `cms_mail_contacts`.`contact_id`=`users`.`id` 
	WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "'
	AND `cms_mail_contacts`.`archive`='0'
	AND `cms_mail_contacts`.`banned`='0'
	AND `cms_mail_contacts`.`delete`='0'
	AND `users`.`id` IS NOT NULL
	ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination() );
	$array = array();
	$i = 1;
	while ( $row = mysql_fetch_assoc( $query ) )
	{
		$array[] = array(
		'id' => $row['id'],
		'icon' => Functions::getImage( 'usr_' . ( $row['sex'] == 'm' ? 'm' : 'w' ) . '.png',
		'', 'style="margin: 0 0 -3px 0;"' ),
		'list' => ( ( $i % 2 ) ? 'list1' : 'list2' ),
		'nickname' => $row['nickname'],
		'count_in' => $row['count_in'],
		'count_out' => $row['count_out'],
		'count_new' => Mail::countNew( $row['contact_id'] ),
		'url' => ( Vars::$MODULE_URI . '?act=messages&amp;id=' . $row['id'] ),
		'online' => ( time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
		'<span class="green"> [ON]</span>' ) );
		++$i;
	}
	//Навигация
	$tpl->display_pagination = Functions::displayPagination( Vars::$MODULE_URI . '?', Vars::$START,
	$total, Vars::$USER_SET['page_size'] );
	$tpl->query = $array;
}

$tpl->token = mt_rand(100, 10000);
$_SESSION['token_status'] = $tpl->token;
$tpl->friends = Functions::friendsCount();
$tpl->archive = Mail::counter( 'archive' ); //Счетчик архива
$tpl->banned = Mail::counter( 'banned' );   //Счетчик заблокированных
//Подключаем шаблон модуля archive.php
$tpl->contents = $tpl->includeTpl( 'contact_mod' );