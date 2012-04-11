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

$total = mysql_result( mysql_query( "SELECT COUNT(*)
FROM `cms_mail_messages`
WHERE `user_id`='" . Vars::$USER_ID . "' AND `sys`='0' AND `delete_in`!='" . Vars::$USER_ID . "' AND `delete_out`!='" . Vars::$USER_ID . "'" ), 0 );
$tpl->total = $total;
if ( $total )
{
	//Перемещаем контакты в корзину
	if ( isset( $_POST['delete_mess'] ) && ValidMail::checkCSRF() === true )
	{
		if ( !empty( $_POST['delch'] ) && is_array( $_POST['delch'] ) )
		{
			Mail::mailSelectContacts( $_POST['delch'], 'delete_mess' );
		}
		Header( 'Location: ' . Vars::$MODULE_URI . '?act=inmess' );
		exit;
	}
	$query = mysql_query( "SELECT `cms_mail_messages`.*, `users`.`nickname`, `users`.`last_visit` FROM `cms_mail_messages`
	LEFT JOIN `users` ON
	`cms_mail_messages`.`contact_id`=`users`.`id`
	WHERE `cms_mail_messages`.`user_id`='" . Vars::$USER_ID . "' AND `cms_mail_messages`.`sys`='0' AND `cms_mail_messages`.`delete_in`!='" . Vars::$USER_ID . "' AND `cms_mail_messages`.`delete_out`!='" . Vars::$USER_ID . "' ORDER BY `cms_mail_messages`.`time` DESC" . Vars::db_pagination() );
	$array = array();
	$i = 1;
	while ( $row = mysql_fetch_assoc( $query ) )
	{
		$array[] = array(
		'list' => (!$row['read'] ? 'gmenu' : ( ( $i % 2 ) ? 'list1' : 'list2' )),
		'id' => $row['id'], 
		'nickname' => $row['nickname'], 
		'time' => Functions::displayDate( $row['time'] ), 
		'online' => ( time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
			'<span class="green"> [ON]</span>' ),
		'file' => true, 
		);
		++$i;
	}
	$tpl->query = $array;
	
	//Навигация
	$tpl->display_pagination = Functions::displayPagination( Vars::$MODULE_URI .
	'?act=outmes&amp;', Vars::$START, $total, Vars::
	$USER_SET['page_size'] );
}

//Подключаем шаблон inout.php
$tpl->pref_in = lng( 'pref_out' );
$tpl->tit = lng( 'outmess' );

$tpl->token = mt_rand(100, 10000);
$_SESSION['token_status'] = $tpl->token;
$tpl->mess_err = lng( 'outmess_not' );

$tpl->contents = $tpl->includeTpl( 'inout' );