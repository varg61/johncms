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
			//Входящие
            case 'inmess':
                return mysql_result( mysql_query( "SELECT COUNT(*)
				FROM `cms_mail_messages`
				WHERE `contact_id`='" . Vars::$USER_ID . "' 

				AND `delete_in`!='" . Vars::$USER_ID . "' 
				AND `delete_out`!='" . Vars::$USER_ID . "'" ), 0 );
			//Исходящие
            case 'outmess':
                return mysql_result( mysql_query( "SELECT COUNT(*)
				FROM `cms_mail_messages`
				WHERE `user_id`='" . Vars::$USER_ID . "' 

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
        }
    }
}