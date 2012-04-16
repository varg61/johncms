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
WHERE `contact_id`='" . Vars::$USER_ID . "' AND `delete_in`!='" . Vars::$USER_ID . "' AND `delete_out`!='" . Vars::$USER_ID . "'" ), 0 );
$tpl->total = $total;
if ( $total )
{
	//Перемещаем контакты в корзину
	if ( isset( $_POST['delete_mess'] ) && ValidMail::checkCSRF() === true )
	{
		if ( !empty( $_POST['delch'] ) && is_array( $_POST['delch'] ) )
		{
			$id = implode(',', $_POST['delch']);
			if (!empty($id)) {
				$out = array();
				$in = array();
				$query = mysql_query( "SELECT * 
                FROM `cms_mail_messages` 
                WHERE (`user_id`='" . Vars::$USER_ID . "' 
                OR `contact_id`='" . Vars::$USER_ID . "') AND `id` IN (" . $id . ")" );
                while ( $row = mysql_fetch_assoc( $query ) )
                {
					if( $row['contact_id'] == Vars::$USER_ID ) {
						mysql_query( "UPDATE `cms_mail_messages` SET
						`delete_in`='" . Vars::$USER_ID . "' WHERE `id`='" . $row['id'] . "'" );
					}
					if( $row['user_id'] == Vars::$USER_ID ) {
						mysql_query( "UPDATE `cms_mail_messages` SET
						`delete_out`='" . Vars::$USER_ID . "' WHERE `id`='" . $row['id'] . "'" );
					}
                }
			}
		}
		Header( 'Location: ' . Vars::$MODULE_URI . '?act=inmess' );
		exit;
	}
	$query = mysql_query( "SELECT `cms_mail_messages`.*, `cms_mail_messages`.`id` as `mid`, `users`.`nickname`, `users`.`last_visit` FROM `cms_mail_messages`
	LEFT JOIN `users` ON
	`cms_mail_messages`.`user_id`=`users`.`id`
	WHERE `cms_mail_messages`.`contact_id`='" . Vars::$USER_ID . "' 
	AND `cms_mail_messages`.`delete_in`!='" . Vars::$USER_ID . "' 
	AND `cms_mail_messages`.`delete_out`!='" . Vars::$USER_ID . "' 
	ORDER BY `cms_mail_messages`.`time` DESC" . Vars::db_pagination() );
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
		'file' => $row['filename'] ? true : ''
		);
		++$i;
	}
	$tpl->query = $array;
	
	//Навигация
	$tpl->display_pagination = Functions::displayPagination( Vars::$MODULE_URI .
	'?act=inmess&amp;', Vars::$START, $total, Vars::
	$USER_SET['page_size'] );
}

//Подключаем шаблон inout.php
$tpl->pref_in = lng( 'pref_in' );
$tpl->tit = lng( 'inmess' );
$tpl->pages_type = 'inmess';
$tpl->token = mt_rand(100, 10000);
$_SESSION['token_status'] = $tpl->token;
$tpl->mess_err = lng( 'inmess_not' );
$tpl->contents = $tpl->includeTpl( 'inout' );