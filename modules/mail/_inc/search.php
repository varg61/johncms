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
defined( '_IN_JOHNCMS_MAIL' ) or die( 'Error: restricted access' );
if ( !Vars::$USER_ID )
{
    Header( 'Location: ' . Vars::$HOME_URL . '/404' );
    exit;
}
//Заголовок
$tpl->title = lng( 'mail' ) . ' | ' . lng( 'search' );
//Получаем данные из формы поиска
$search = isset( $_REQUEST['q'] ) ? rawurldecode( trim( $_REQUEST['q'] ) ) : '';

//Проверяем длинну запроса
if ( $search && Validate::nickname( $search, 1 ) === true )
{
    //Проверяем валидность введенных данных
	$search_db = strtr( $search, array( '_' => '\\_', '%' => '\\%' ) );
    $tpl->search = Validate::filterString( $search );
    $search_db = '%' . $search_db . '%';
	//Считаем количество найденных контактов
    $total = mysql_result( mysql_query( "SELECT COUNT(*) 
	FROM `cms_mail_contacts`
	LEFT JOIN `users` 
	ON `cms_mail_contacts`.`contact_id`=`users`.`id`
	WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "'
	AND `cms_mail_contacts`.`delete`='0'
	AND `users`.`nickname`
	LIKE '" . $search_db . "'" ), 0 );
    if ( $total )
    {
        //Формируем список контактов
		$query = mysql_query( "SELECT * 
		FROM `cms_mail_contacts`
		LEFT JOIN `users` 
		ON `cms_mail_contacts`.`contact_id`=`users`.`id`
		WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "'
		AND `cms_mail_contacts`.`delete`='0'
		AND `users`.`nickname`
		LIKE '" . $search_db . "'
		ORDER BY `cms_mail_contacts`.`time` DESC 
		" . Vars::db_pagination() );
        $array = array();
        $i = 1;
        
		while ( $row = mysql_fetch_assoc( $query ) )
        {
            $array[] = array(
                'id' => $row['id'],
                'icon' => '',
                'list' => ( ( $i % 2 ) ? 'list1' : 'list2' ),
                'nickname' => preg_replace( '|(' . preg_quote( $search, '/' ) . ')|siu', '<span style="background-color: #FFFF33">$1</span>',
                    $row['nickname'] ),
                'count_in' => $row['count_in'],
                'count_out' => $row['count_out'],
                'count_new' => Mail::countNew( $row['contact_id'] ),
                'url' => ( Vars::$MODULE_URI . '?act=messages&amp;id=' . $row['id'] ),
                'online' => ( time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                    '<span class="green"> [ON]</span>' ) );
            ++$i;
        }

        $tpl->query = $array;
        unset( $array );
    }
    $tpl->total = $total;
    //Навигация
	$tpl->display_pagination = Functions::displayPagination( Vars::$MODULE_URI . '?act=search&amp;q=' .
        rawurlencode( $search ) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size'] );
} else
{
    $tpl->total = 0;
}
//Подключаем шаблон модуля search.php
$tpl->contents = $tpl->includeTpl( 'search' );
