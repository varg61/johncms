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
$tpl->title = lng( 'mail' ) . ' | ' . lng( 'basket' );
if ( Vars::$ID )
{
    if ( Vars::$ID == Vars::$USER_ID )
    {
        $tpl->contents = Functions::displayError( lng( 'error_request' ), '<a href="' . Vars::
            $MODULE_URI . '">' . lng( 'contacts' ) . '</a>' );
    } else
    {
        
		$q = mysql_query( "SELECT `nickname` FROM `users` WHERE `id`='" . Vars::$ID . "' LIMIT 1" );
        if ( mysql_num_rows( $q ) )
        {
            $total = mysql_result( mysql_query( "SELECT COUNT(*)
			FROM `cms_mail_messages`
			WHERE ((`user_id`='" . Vars::$USER_ID . "'
			AND `contact_id`='" . Vars::$ID . "')
			OR (`contact_id`='" . Vars::$USER_ID . "'
			AND `user_id`='" . Vars::$ID . "'))
			AND (`delete_in`='" . Vars::$USER_ID . "'
			OR `delete_out`='" . Vars::$USER_ID . "') AND `delete`!='" . Vars::$USER_ID . "'" ), 0 );
            if ( $total )
            {
                //Формируем список удаленных сообщений определенного контакта
				$query = mysql_query( "SELECT `cms_mail_messages`.*, `cms_mail_messages`.`id` as `mid`, `users`.*
				FROM `cms_mail_messages`
				LEFT JOIN `users` 
				ON `cms_mail_messages`.`user_id`=`users`.`id` 
				WHERE ((`cms_mail_messages`.`user_id`='" . Vars::$USER_ID . "'
				AND `cms_mail_messages`.`contact_id`='" . Vars::$ID . "')
				OR (`cms_mail_messages`.`contact_id`='" . Vars::$USER_ID . "'
				AND `cms_mail_messages`.`user_id`='" . Vars::$ID . "'))
				AND (`cms_mail_messages`.`delete_in`='" . Vars::$USER_ID . "'
				OR `cms_mail_messages`.`delete_out`='" . Vars::$USER_ID . "')
				AND `delete`!='" . Vars::$USER_ID . "'
				ORDER BY `cms_mail_messages`.`time` DESC" . Vars::db_pagination() );
                $array = array();
				
                $i = 1;
                while ( $row = mysql_fetch_assoc( $query ) )
                {
                    $text = Validate::filterString( $row['text'], 1, 1 );
                    if ( Vars::$USER_SET['smileys'] )
                        $text = Functions::smileys( $text, $row['rights'] >= 1 ? 1 : 0 );
                    $array[] = array(
                        'id' => $row['id'],
                        'mid' => $row['mid'],
                        'icon' => Functions::getImage( 'usr_' . ( $row['sex'] == 'm' ? 'm' : 'w' ) .
                            '.png', '', 'align="middle"' ),
                        'list' => ( ( $i % 2 ) ? 'list1' : 'list2' ),
                        'nickname' => $row['nickname'],
                        'file' => $row['filename'] ? '<a href="' . Vars::$MODULE_URI . '?act=load&amp;id=' .
                            $row['mid'] . '">' . $row['filename'] . '</a> (' . Mail::formatsize( $row['filesize'] ) .
                            ')(' . $row['filecount'] . ')' : '',
                        'time' => Functions::displayDate( $row['time'] ),
                        'text' => $text,
                        'urlDelete' => Vars::$MODULE_URI . '?act=messages&amp;mod=delete&amp;id=' . $row['mid'],
                        'url' => ( Vars::$MODULE_URI . '?act=messages&amp;id=' . $row['id'] ),
                        'online' => ( time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                            '<span class="green"> [ON]</span>' ),
                        'elected' => ( ( $row['elected_in'] != Vars::$USER_ID && $row['elected_out'] !=
                            Vars::$USER_ID ) ? true : false ),
                        'selectBar' => '[<span class="green">&laquo;</span> <a href="' . Vars::$MODULE_URI .
                            '?act=restore&amp;id=' . $row['mid'] . '">' . lng( 'rest' ) . '</a>] [<span class="red">х</span> <a href="' .
                            Vars::$MODULE_URI . '?act=delete&amp;id=' . $row['mid'] . '">' . lng( 'delete' ) .
                            '</a>]' );
                    ++$i;
                }

                $tpl->query = $array;
                $tpl->titleTest = '<div class="phdr"><h3>' . lng( 'deleted_message' ) . '</h3></div>';
                $tpl->urlTest = '<div class="menu"><a href="' . Vars::$URI . '">' . lng( 'contacts' ) .
                    '</a></div>';
                $tpl->total = $total;
                //Навигация
				$tpl->display_pagination = Functions::displayPagination( Vars::$MODULE_URI . '?act=basket&amp;id=' .
                    Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size'] );
                //Подключаем шаблон модуля list.php
				$tpl->contents = $tpl->includeTpl( 'list' );
            } else
            {
                Header( 'Location: ' . Vars::$MODULE_URI . '?act=basket' );
                exit;
            }
        } else
        {
            //Если пользователь не существует, показываем ошибку
			$tpl->contents = Functions::displayError( lng( 'user_does_not_exist' ), '<a href="' .
                Vars::$MODULE_URI . '">' . lng( 'contacts' ) . '</a>' );
        }
    }
} else
{
	$total = Mail::counter( 'delete' );
    $tpl->total = $total;
    if ( $total )
    {
        //Восстанавливаем сообщения
		if ( isset( $_POST['restore'] ) && ValidMail::checkCSRF() === true )
        {
            if ( !empty( $_POST['delch'] ) && is_array( $_POST['delch'] ) )
            {
                Mail::mailSelectContacts( $_POST['delch'], 'restore' );
            }
            Header( 'Location: ' . Vars::$MODULE_URI . '?act=basket' );
            exit;
        }
        //Удаляем сообщения
		if ( isset( $_POST['delete'] ) && ValidMail::checkCSRF() === true )
        {
            if ( !empty( $_POST['delch'] ) && is_array( $_POST['delch'] ) )
            {
                Mail::mailSelectContacts( $_POST['delch'], 'drop' );
            }
            Header( 'Location: ' . Vars::$MODULE_URI . '?act=basket' );
            exit;
        }
		//Очищаем корзину
        if ( isset( $_POST['clear'] ) && ValidMail::checkCSRF() === true )
        {
            Mail::mailSelectContacts( array(), 'clear' );
            Header( 'Location: ' . Vars::$MODULE_URI . '?act=basket' );
            exit;
        }
		//Формируем список удаленных сообщений по контактам
        $query = mysql_query("SELECT `users`.*
		FROM `cms_mail_contacts`
		LEFT JOIN `users`
		ON `cms_mail_contacts`.`contact_id`=`users`.`id`
		LEFT JOIN `cms_mail_messages`
		ON (`cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`contact_id`
		OR `cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`user_id`)
		AND `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "'
		WHERE ((`cms_mail_messages`.`user_id`='" . Vars::$USER_ID . "'
		OR `cms_mail_messages`.`contact_id`='" . Vars::$USER_ID . "')
		AND (`cms_mail_messages`.`delete_out`='" . Vars::$USER_ID . "' 
		OR `cms_mail_messages`.`delete_in`='" . Vars::$USER_ID . "' 
		OR (`cms_mail_contacts`.`delete`='1' 
		AND `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "')))
		AND `cms_mail_messages`.`delete`!='" . Vars::$USER_ID . "'
		AND `cms_mail_messages`.`sys`='0'
		GROUP BY `cms_mail_contacts`.`contact_id`
		ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination() );
		
		$array = array();
        $i = 1;
        while ( $row = mysql_fetch_assoc( $query ) )
        {
            $array[] = array(
                'id' => $row['id'],
                'icon' => Functions::getImage( 'usr_' . ( $row['sex'] == 'm' ? 'm' : 'w' ) . '.png',
                    '', 'align="middle"' ),
                'list' => ( ( $i % 2 ) ? 'list1' : 'list2' ),
                'nickname' => $row['nickname'],
                'count_in' => mysql_result( mysql_query( "SELECT COUNT(*) FROM `cms_mail_messages` WHERE `user_id`='{$row['id']}' AND `delete_in`='" .
                    Vars::$USER_ID . "' AND `delete`!='" . Vars::$USER_ID . "'" ), 0 ),
                'count_out' => mysql_result( mysql_query( "SELECT COUNT(*) FROM `cms_mail_messages` WHERE `contact_id`='{$row['id']}' AND `delete_out`='" .
                    Vars::$USER_ID . "' AND `delete`!='" . Vars::$USER_ID . "'" ), 0 ),
                'count_new' => '',
                'url' => ( Vars::$MODULE_URI . '?act=basket&amp;id=' . $row['id'] ),
                'online' => ( time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                    '<span class="green"> [ON]</span>' ) );
            ++$i;
        }
        //Навигация
		$tpl->display_pagination = Functions::displayPagination( Vars::$MODULE_URI . '?act=basket&amp;',
            Vars::$START, $total, Vars::$USER_SET['page_size'] );
        $tpl->query = $array;
        $tpl->token = mt_rand(100, 10000);
		$_SESSION['token_status'] = $tpl->token;
		//Подключаем шаблон модуля contacts.php
		$tpl->contacts = $tpl->includeTpl( 'contacts' );
    } else
    {
        //Выводим сообщение если корзина пустая
		$tpl->contacts = '<div class="rmenu">' . lng( 'empty_basket' ) . '</div>';
    }
	//Подключаем шаблон модуля basket.php
    $tpl->contents = $tpl->includeTpl( 'basket' );
}
