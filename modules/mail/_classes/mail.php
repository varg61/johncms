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

class Mail extends Vars
{
	/*
    -----------------------------------------------------------------
    Счетчики
    -----------------------------------------------------------------
    */
	public static function counter( $var = null )
    {
        switch ( $var )
        {
            //Счетчик избранных
			case 'elected':
                return mysql_result( mysql_query( "SELECT COUNT(*)
                 FROM (SELECT DISTINCT `cms_mail_contacts`.`contact_id` 
                 FROM `cms_mail_contacts` 
                 LEFT JOIN `cms_mail_messages` 
                 ON `cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`user_id` 
                 OR `cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`contact_id` 
                 WHERE ((`cms_mail_contacts`.`contact_id`!='" . parent::$USER_ID . "' 
                 AND (`cms_mail_messages`.`contact_id`!='" . parent::$USER_ID . "' 
                 OR `cms_mail_messages`.`user_id`!='" . parent::$USER_ID . "') 
                 AND (`cms_mail_messages`.`elected_out`='" . parent::$USER_ID . "' 
                 OR `cms_mail_messages`.`elected_in`='" . parent::$USER_ID . "') 
                 AND (`cms_mail_messages`.`delete_out`!='" . parent::$USER_ID . "' 
                 OR `cms_mail_messages`.`delete_in`!='" . parent::$USER_ID . "')) 
                 AND (`cms_mail_contacts`.`delete`='0' 
                 AND `cms_mail_contacts`.`user_id`='" . parent::$USER_ID . "')) 
                 AND `cms_mail_messages`.`delete`!='" . parent::$USER_ID . "') a" ), 0 );
			//Счетчик архива
            case 'archive':
				return mysql_result( mysql_query( "SELECT COUNT(*)
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
			//Счетчик удаленных
            case 'delete':
                return mysql_result(mysql_query("SELECT COUNT(*)
				FROM (SELECT DISTINCT `cms_mail_contacts`.`contact_id`
				FROM `cms_mail_contacts`
				LEFT JOIN `cms_mail_messages`
				ON (`cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`contact_id`
				OR `cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`user_id`)
				AND `cms_mail_contacts`.`user_id`='" . parent::$USER_ID . "'
				WHERE ((`cms_mail_messages`.`user_id`='" . parent::$USER_ID . "'
				OR `cms_mail_messages`.`contact_id`='" . parent::$USER_ID . "')
				AND (`cms_mail_messages`.`delete_out`='" . parent::$USER_ID . "' 
				OR `cms_mail_messages`.`delete_in`='" . parent::$USER_ID . "' OR (`cms_mail_contacts`.`delete`='1' AND `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "')))
				AND `cms_mail_messages`.`delete`!='" . parent::$USER_ID . "') a"), 0);
			//Игнор
            case 'banned':
                return mysql_result( mysql_query( "SELECT COUNT(*) 
                FROM `cms_mail_contacts` 
                WHERE `banned`='1' 
                AND `user_id`='" . parent::$USER_ID . "' 
                AND `cms_mail_contacts`.`delete`='0'" ), 0 );
			//Входящие
            case 'inmess':
                return mysql_result( mysql_query( "SELECT COUNT(*)
				FROM `cms_mail_messages`
				WHERE `contact_id`='" . Vars::$USER_ID . "' 
				AND `sys`='0' 
				AND `delete_in`!='" . Vars::$USER_ID . "' 
				AND `delete_out`!='" . Vars::$USER_ID . "'" ), 0 );
			//Исходящие
            case 'outmess':
                return mysql_result( mysql_query( "SELECT COUNT(*)
				FROM `cms_mail_messages`
				WHERE `user_id`='" . Vars::$USER_ID . "' 
				AND `sys`='0' 
				AND `delete_in`!='" . Vars::$USER_ID . "' 
				AND `delete_out`!='" . Vars::$USER_ID . "'" ), 0 );
            //Файлы
			case 'files':
                return mysql_result( mysql_query( "SELECT COUNT(*) 
                FROM `cms_mail_messages` 
                WHERE `filename`!='' 
                AND (`user_id`='" . parent::$USER_ID . "' 
                OR `contact_id`='" . parent::$USER_ID . "') 
                AND `delete_in`!='" . parent::$USER_ID . "' 
                AND `delete_out`!='" . parent::$USER_ID . "' 
                AND `delete`!='" . parent::$USER_ID . "'" ), 0 );
            default:
				return false;
        }
    }
	
	/*
    -----------------------------------------------------------------
    Действия с контактами и сообщениями
    -----------------------------------------------------------------
    */
    public static function mailSelectContacts( array $array, $param )
    {
        $id = implode( ',', $array );
        switch ( $param )
        {
            //Добавляем в архив
			case 'archive':
                if ( !empty( $id ) )
                {
                    mysql_query( "UPDATE `cms_mail_contacts` 
                    SET	`archive`='1' 
                    WHERE `user_id`='" . parent::$USER_ID . "' 
                    AND `contact_id` IN (" . $id . ") AND `archive`='0'" );
                }
                break;
			//Удаляем
            case 'delete':
                $mass = array();
                $mass_contact = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_mail_contacts` 
                WHERE `user_id`='" . parent::$USER_ID . "' 
                AND `contact_id` IN (" . $id . ")" );
                while ( $rows = mysql_fetch_assoc( $query ) )
                {
                    $mass[] = $rows['id'];
                    $mass_contact[] = $rows['contact_id'];
                }
                if ( !empty( $mass ) )
                {
                    $exp = implode( ',', $mass );
                    $sms = implode( ',', $mass_contact );
                    $out = array();
                    $count_in = 0;
                    $count_out = 0;
                    $query1 = mysql_query( "SELECT * 
                    FROM `cms_mail_messages` 
                    WHERE `user_id`='" . parent::$USER_ID . "'
                    AND `contact_id` IN (" . $sms . ")" );
                    while ( $rows1 = mysql_fetch_assoc( $query1 ) )
                    {
                        $out[] = $rows1['id'];
                    }
                    $out_str = implode( ',', $out );
                    if ( !empty( $out_str ) )
                    {
                        mysql_query( "UPDATE `cms_mail_messages`
                         SET `delete_out`='" . parent::$USER_ID . "' 
                         WHERE `id` IN (" . $out_str . ")" );
                    }
                    $in = array();
                    $query2 = mysql_query( "SELECT * 
                    FROM `cms_mail_messages` 
                    WHERE `contact_id`='" . parent::$USER_ID . "' 
                    AND `user_id` IN (" . $sms . ")" );
                    while ( $rows2 = mysql_fetch_assoc( $query2 ) )
                    {
                        $in[] = $rows2['id'];
                    }
                    $in_str = implode( ',', $in );
                    if ( !empty( $in_str ) )
                    {
                        mysql_query( "UPDATE `cms_mail_messages` SET 
                        `delete_in`='" . parent::$USER_ID . "' 
                        WHERE `id` IN (" . $in_str . ")" );
                    }
                }
                break;
			//Добавляем в игнор
            case 'banned':
                $mass = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_mail_contacts` 
                WHERE `user_id`='" . parent::$USER_ID . "' 
                AND `contact_id` IN (" . $id . ")" );
                while ( $rows = mysql_fetch_assoc( $query ) )
                {
                    $mass[] = $rows['id'];
                }
                if ( !empty( $mass ) )
                {
                    $exp = implode( ',', $mass );
                    mysql_query( "UPDATE `cms_mail_contacts` SET
					`banned`='1' 
                    WHERE `user_id`='" . parent::$USER_ID . "' 
                    AND `id` IN (" . $exp . ")" );
                }
                break;
			//Удаляем из игнора
            case 'unban':
                $mass = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_mail_contacts` 
                WHERE `user_id`='" . parent::$USER_ID . "' 
                AND `contact_id` IN (" . $id . ") 
                AND `banned`='1'" );
                while ( $rows = mysql_fetch_assoc( $query ) )
                {
                    $mass[] = $rows['id'];
                }
                if ( !empty( $mass ) )
                {
                    $exp = implode( ',', $mass );
                    mysql_query( "UPDATE `cms_mail_contacts` SET
					`banned`='0' 
                    WHERE `user_id`='" . parent::$USER_ID . "' 
                    AND `id` IN (" . $exp . ")" );
                }
                break;
			//Восстановление сообщений
            case 'restore':
                $mass = array();
                $mass_contact = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_mail_contacts` 
                WHERE `user_id`='" . parent::$USER_ID . "' 
                AND `contact_id` IN (" . $id . ")" );
                while ( $rows = mysql_fetch_assoc( $query ) )
                {
                    $mass[] = $rows['id'];
                    $mass_contact[] = $rows['contact_id'];
                }
                if ( !empty( $mass ) )
                {
                    $sms = implode( ',', $mass_contact );
                    $out = array();
                    $count_in = 0;
                    $count_out = 0;
                    $query1 = mysql_query( "SELECT * 
                    FROM `cms_mail_messages` 
                    WHERE `user_id`='" . parent::$USER_ID . "' 
                    AND `contact_id` IN (" . $sms . ") 
                    AND `delete_out`='" . parent::$USER_ID . "' 
                    AND `delete`!='" . parent::$USER_ID . "'" );
                    while ( $rows1 = mysql_fetch_assoc( $query1 ) )
                    {
                        $out[] = $rows1['id'];
                    }
                    $out_str = implode( ',', $out );
                    if ( !empty( $out_str ) )
                    {
                        mysql_query( "UPDATE `cms_mail_messages` SET
						`delete_out`='0'
                        WHERE `id` IN (" . $out_str . ")" );
                    }
                    $in = array();
                    $query2 = mysql_query( "SELECT * 
                    FROM `cms_mail_messages` 
                    WHERE `contact_id`='" . parent::$USER_ID . "' 
                    AND `user_id` IN (" . $sms . ") 
                    AND `delete_in`='" . parent::$USER_ID . "' 
                    AND `delete`!='" . parent::$USER_ID . "'" );
                    while ( $rows2 = mysql_fetch_assoc( $query2 ) )
                    {
                        $in[] = $rows2['id'];
                    }
                    $in_str = implode( ',', $in );
                    if ( !empty( $in_str ) )
                    {
                        mysql_query( "UPDATE `cms_mail_messages` SET
						`delete_in`='0' 
                        WHERE `id` IN (" . $in_str . ")" );
                    }
                }
                break;
			//Добавление в избрвнное
            case 'elected':
                $mass = array();
                $mass_contact = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_mail_contacts` 
                WHERE `user_id`='" . parent::$USER_ID . "' 
                AND `contact_id` IN (" . $id . ")" );
                while ( $rows = mysql_fetch_assoc( $query ) )
                {
                    $mass[] = $rows['id'];
                    $mass_contact[] = $rows['contact_id'];
                }
                if ( !empty( $mass ) )
                {
                    $sms = implode( ',', $mass_contact );
                    $out = array();
                    $count_in = 0;
                    $count_out = 0;
                    $query1 = mysql_query( "SELECT * 
                    FROM `cms_mail_messages` 
                    WHERE `user_id`='" . parent::$USER_ID . "' 
                    AND `contact_id` IN (" . $sms . ") 
                    AND `elected_out`='" . parent::$USER_ID . "' 
                    AND `delete`!='" . parent::$USER_ID . "'" );
                    while ( $rows1 = mysql_fetch_assoc( $query1 ) )
                    {
                        $out[] = $rows1['id'];
                    }
                    $out_str = implode( ',', $out );
                    if ( !empty( $out_str ) )
                    {
                        mysql_query( "UPDATE `cms_mail_messages` SET
						`elected_out`='0' 
                        WHERE `id` IN (" . $out_str . ")" );
                    }
                    $in = array();
                    $query2 = mysql_query( "SELECT * 
                    FROM `cms_mail_messages` 
                    WHERE `contact_id`='" . parent::$USER_ID . "' 
                    AND `user_id` IN (" . $sms . ") 
                    AND `elected_in`='" . parent::$USER_ID . "' 
                    AND `delete`!='" . parent::$USER_ID . "'" );
                    while ( $rows2 = mysql_fetch_assoc( $query2 ) )
                    {
                        $in[] = $rows2['id'];
                    }
                    $in_str = implode( ',', $in );
                    if ( !empty( $in_str ) )
                    {
                        mysql_query( "UPDATE `cms_mail_messages` SET
						`elected_in`='0' 
                        WHERE `id` IN (" . $in_str . ")" );
                    }
                }
                break;
			//Полное удаление 
            case 'drop':
                $query = mysql_query( "SELECT * 
				FROM `cms_mail_messages` 
				WHERE ((`delete_out`='" . parent::$USER_ID . "' AND `user_id`='" . parent::$USER_ID . "' AND `contact_id` IN (" . $id . "))
				OR (`delete_in`='" . parent::$USER_ID . "' AND `contact_id`='" . parent::$USER_ID . "' AND `user_id` IN (" . $id . ")))
				AND `delete`!='" . parent::$USER_ID . "'" );
				$update = array();
				$delete = array();
				while($row = mysql_fetch_assoc($query)) {
					if($row['delete'] && $row['delete'] != parent::$USER_ID) {
						$delete[] = $row['id'];
					} else {
						$update[] = $row['id'];
					}
				}
				if($delete) {
					$delete = implode(',', $delete);
					echo 'Удаляем ' . $delete . '<br />';
					$q = mysql_query( "SELECT * FROM `cms_mail_messages`
					WHERE `id` IN (" . $delete . ") AND `filename`!=''");
					while($res = mysql_fetch_assoc($q)) {
						if(file_exists(FILEPATH . 'users/pm/' . $res['filename']) !== false) {
							@unlink( FILEPATH . 'users/pm/' . $res['filename'] );
						}
					}
					mysql_query( "DELETE FROM `cms_mail_messages` 
					WHERE `id` IN (" . $delete . ")" );
				}
				if($update) {
					$update = implode(',', $update);
					mysql_query( "UPDATE `cms_mail_messages` SET
					`delete`='" . parent::$USER_ID . "' 
					WHERE `id` IN (" . $update . ")" );
				}
				unset($delete, $update);
                break;
			//Полная очистка сообщений
            case 'clear':
                $query = mysql_query( "SELECT * 
                FROM `cms_mail_messages` 
                WHERE `delete_in`='" . parent::$USER_ID . "' 
                OR `delete_out`='" . parent::$USER_ID . "'" );
                $update = array();
                $delete = array();
                while ( $row = mysql_fetch_assoc( $query ) )
                {
                    if ( !empty( $row['delete'] ) && $row['delete'] != parent::$USER_ID )
                    {
                        $delete[] = $row['id'];
                    }
                    $update[] = $row['id'];
                }

                if ( $delete )
                {
                    $del = implode( ',', $delete );
                    $qq1 = mysql_query( "SELECT `filename` 
                    FROM `cms_mail_messages` 
                    WHERE `filename`!='' 
                    AND `id` IN (" . $del . ")" );
                    while ( $r1 = mysql_fetch_assoc( $qq1 ) )
                    {
                        @unlink( FILEPATH . 'users/pm/' . $r1['filename'] );
                    }
                    mysql_query( "DELETE FROM `cms_mail_messages` 
                    WHERE `id` IN (" . $del . ")" );
                }

                if ( $update )
                {
                    $id = implode( ',', $update );
                    mysql_query( "UPDATE `cms_mail_messages` SET `delete`='" . parent::$USER_ID . "' 
                    WHERE `id` IN (" . $id . ")" );
                }
                break;
			//Отмечаем сообщения как прочитанные
            case 'read':
                $mass = array();
                $mass_contact = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_mail_contacts` 
                WHERE `user_id`='" . parent::$USER_ID . "' 
                AND `contact_id` IN (" . $id . ")" );
                while ( $rows = mysql_fetch_assoc( $query ) )
                {
                    $mass[] = $rows['id'];
                    $mass_contact[] = $rows['contact_id'];
                }
                if ( !empty( $mass ) )
                {
                    $sms = implode( ',', $mass_contact );
                    $out = array();
                    $count_in = 0;
                    $count_out = 0;

                    $query1 = mysql_query( "SELECT * 
                    FROM `cms_mail_messages` 
                    WHERE `user_id` IN (" . $sms . ") 
                    AND `contact_id`='" . parent::$USER_ID . "' 
                    AND `read`='0' AND `delete`!='" . parent::$USER_ID . "'" );
                    while ( $rows1 = mysql_fetch_assoc( $query1 ) )
                    {
                        $out[] = $rows1['id'];
                    }

                    if ( !empty( $out ) )
                    {
                        $in_str = implode( ',', $out );
                        mysql_query( "UPDATE `cms_mail_messages` SET
						`read`='1' 
                        WHERE `id` IN (" . $in_str . ")" );
                    }
                }
                break;
        }
    }
	
	/*
    -----------------------------------------------------------------
    Подключаем файлы
    -----------------------------------------------------------------
    */
    public static function mailConnect()
    {
        return array(
            'add',
            'archive',
            'banned',
            'basket',
			'contacts',
            'delete',
            'elected',
            'elected',
            'files',
			'inmess',
            'load',
            'messages',
            'new',
			'outmess',
			'read',
            'restore',
            'search',
            'select',
			'settings',
            'systems' );
    }
	
	/*
    -----------------------------------------------------------------
    Информер новых сообщений в списке контактов
    -----------------------------------------------------------------
    */
    public static function countNew( $id = null )
    {
        if ( $id == null )
            return false;
        $new = mysql_result( mysql_query( "SELECT COUNT(*) 
			FROM `cms_mail_messages` 
			LEFT JOIN `cms_mail_contacts` 
			ON `cms_mail_messages`.`user_id`=`cms_mail_contacts`.`contact_id` 
			AND `cms_mail_contacts`.`user_id`='" . parent::$USER_ID . "' 
			WHERE `cms_mail_messages`.`user_id`='" . $id . "' 
			AND `cms_mail_messages`.`contact_id`='" . parent::$USER_ID . "' 

			AND `cms_mail_messages`.`read`='0' 
			AND (`cms_mail_messages`.`delete_in`!='" . parent::$USER_ID . "' 
			AND `cms_mail_messages`.`delete_out`!='" . parent::$USER_ID . "')
			AND `cms_mail_messages`.`delete`!='" . parent::$USER_ID . "' 
			AND `cms_mail_contacts`.`banned`!='1'" ), 0 );
        if ( $new )
            return '+' . $new;
        else
            return false;
    }
	
	/*
    -----------------------------------------------------------------
    Проверка пользователя на игнор
    -----------------------------------------------------------------
    */
	public static function ignor( $id = null )
    {
		if ( $id == null )
            return false;
        $query = mysql_query( "SELECT * FROM `cms_mail_contacts` 
        WHERE `user_id`='" . parent::$USER_ID . "' 
        AND `contact_id`='" . $id . "' 
        AND `banned`='1' LIMIT 1" );
		if ( mysql_num_rows( $query ) )
        {
            return true;
        } else
        {
            return false;
        }
    }
}