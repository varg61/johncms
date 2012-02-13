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
    public static function counter( $var = null )
    {
        switch ( $var )
        {
            case 'elected':
                return mysql_result( mysql_query( "SELECT COUNT(*)
                 FROM (SELECT DISTINCT `cms_contacts`.`contact_id` 
                 FROM `cms_contacts` 
                 LEFT JOIN `cms_messages` 
                 ON `cms_contacts`.`contact_id`=`cms_messages`.`user_id` 
                 OR `cms_contacts`.`contact_id`=`cms_messages`.`contact_id` 
                 WHERE ((`cms_contacts`.`contact_id`!='" . parent::$USER_ID . "' 
                 AND (`cms_messages`.`contact_id`!='" . parent::$USER_ID . "' 
                 OR `cms_messages`.`user_id`!='" . parent::$USER_ID . "') 
                 AND (`cms_messages`.`elected_out`='" . parent::$USER_ID . "' 
                 OR `cms_messages`.`elected_in`='" . parent::$USER_ID . "') 
                 AND (`cms_messages`.`delete_out`!='" . parent::$USER_ID . "' 
                 OR `cms_messages`.`delete_in`!='" . parent::$USER_ID . "')) 
                 AND (`cms_contacts`.`delete`='0' 
                 AND `cms_contacts`.`user_id`='" . parent::$USER_ID . "')) 
                 AND `cms_messages`.`delete`!='" . parent::$USER_ID . "' 
                 AND `cms_messages`.`sys`='0') a" ), 0 );
            case 'archive':
                return mysql_result( mysql_query( "SELECT COUNT(*) 
                FROM `cms_contacts` 
                WHERE `archive`='1' 
                AND `banned`='0' 
                AND `user_id`='" . parent::$USER_ID . "' 
                AND `cms_contacts`.`delete`='0'" ), 0 );
            case 'delete':
                return mysql_result( mysql_query( "SELECT COUNT(*) 
                FROM (SELECT DISTINCT `cms_contacts`.`contact_id` 
                FROM `cms_contacts` 
                LEFT JOIN `cms_messages` 
                ON `cms_contacts`.`contact_id`=`cms_messages`.`user_id` 
                OR `cms_contacts`.`contact_id`=`cms_messages`.`contact_id` 
                WHERE ((`cms_contacts`.`contact_id`!='" . parent::$USER_ID . "' 
                AND (`cms_messages`.`contact_id`!='" . parent::$USER_ID . "' 
                OR `cms_messages`.`user_id`!='" . parent::$USER_ID . "') 
                AND (`cms_messages`.`delete_out`='" . parent::$USER_ID . "' 
                OR `cms_messages`.`delete_in`='" . parent::$USER_ID . "')) 
                OR (`cms_contacts`.`delete`='1' 
                AND `cms_contacts`.`user_id`='" . parent::$USER_ID . "')) 
                AND `cms_messages`.`delete`!='" . parent::$USER_ID . "' 
                AND `cms_messages`.`sys`='0') a" ), 0 );
            case 'systems':
                return mysql_result( mysql_query( "SELECT COUNT(*) 
     			FROM `cms_messages` 
     			WHERE `contact_id`='" . parent::$USER_ID . "'  AND `sys`='1'" ), 0 );
            case 'banned':
                return mysql_result( mysql_query( "SELECT COUNT(*) 
                FROM `cms_contacts` 
                WHERE `banned`='1' 
                AND `user_id`='" . parent::$USER_ID . "' 
                AND `cms_contacts`.`delete`='0'" ), 0 );
            case 'new':
                $new = mysql_result( mysql_query( "SELECT COUNT(*) 
     			FROM `cms_messages` 
                LEFT JOIN `cms_contacts` 
     			ON `cms_messages`.`user_id`=`cms_contacts`.`contact_id` 
 			    AND `cms_contacts`.`user_id`='" . parent::$USER_ID . "' 
     			WHERE `cms_messages`.`contact_id`='" . parent::$USER_ID . "' 
                AND `cms_messages`.`sys`='0' 
     			AND `cms_messages`.`read`='0' 
       	        AND (`cms_messages`.`delete_in`!='" . parent::$USER_ID . "' 
     			AND `cms_messages`.`delete_out`!='" . parent::$USER_ID . "')
     			AND `cms_messages`.`delete`!='" . parent::$USER_ID . "' 
     			AND `cms_contacts`.`banned`!='1'" ), 0 );
                return $new ? '<div class="rmenu"><a href="' . parent::$HOME_URL . '/mail?act=new">Почта</a> (+' .
                    $new . ')</div>' : '';
            case 'newsys':
                return 0;
            case 'files':
                return mysql_result( mysql_query( "SELECT COUNT(*) 
                FROM `cms_messages` 
                WHERE `filename`!='' 
                AND (`user_id`='" . parent::$USER_ID . "' 
                OR `contact_id`='" . parent::$USER_ID . "') 
                AND `delete_in`!='" . parent::$USER_ID . "' 
                AND `delete_out`!='" . parent::$USER_ID . "' 
                AND `delete`!='" . parent::$USER_ID . "'" ), 0 );
            default:
                return mysql_result( mysql_query( "SELECT COUNT(*) 
                FROM `cms_messages` 
                WHERE (`user_id` = '" . parent::$USER_ID . "' 
                OR `contact_id` = '" . parent::$USER_ID . "') 
                AND (`delete_out`!='" . parent::$USER_ID . "' 
                AND `delete_in`!='" . parent::$USER_ID . "') 
                AND `delete`!='" . parent::$USER_ID . "'" ), 0 );
        }
    }

    public static function mailSelectContacts( array $array, $param )
    {
        $id = implode( ',', $array );
        switch ( $param )
        {
            case 'archive':
                if ( !empty( $id ) )
                {
                    mysql_query( "UPDATE `cms_contacts` 
                    SET	`archive`='1' 
                    WHERE `user_id`='" . parent::$USER_ID . "' 
                    AND `contact_id` IN (" . $id . ") AND `archive`='0'" );
                }
                break;
            case 'delete':
                $mass = array();
                $mass_contact = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_contacts` 
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
                    FROM `cms_messages` 
                    WHERE `user_id`='" . parent::$USER_ID . "'
                    AND `contact_id` IN (" . $sms . ")" );
                    while ( $rows1 = mysql_fetch_assoc( $query1 ) )
                    {
                        $out[] = $rows1['id'];
                    }
                    $out_str = implode( ',', $out );
                    if ( !empty( $out_str ) )
                    {
                        mysql_query( "UPDATE `cms_messages`
                         SET `delete_out`='" . parent::$USER_ID . "' 
                         WHERE `id` IN (" . $out_str . ")" );
                    }
                    $in = array();
                    $query2 = mysql_query( "SELECT * 
                    FROM `cms_messages` 
                    WHERE `contact_id`='" . parent::$USER_ID . "' 
                    AND `user_id` IN (" . $sms . ")" );
                    while ( $rows2 = mysql_fetch_assoc( $query2 ) )
                    {
                        $in[] = $rows2['id'];
                    }
                    $in_str = implode( ',', $in );
                    if ( !empty( $in_str ) )
                    {
                        mysql_query( "UPDATE `cms_messages` SET 
                        `delete_in`='" . parent::$USER_ID . "' 
                        WHERE `id` IN (" . $in_str . ")" );
                    }
                    $query3 = mysql_query( "SELECT * 
                    FROM `cms_contacts` 
                    WHERE `user_id`='" . parent::$USER_ID . "' 
                    AND `contact_id` IN (" . $id . ")" );
                    while ( $rows3 = mysql_fetch_assoc( $query3 ) )
                    {
                        $count_mess_in = mysql_result( mysql_query( "SELECT COUNT(*) 
                        FROM `cms_messages` 
                        WHERE `contact_id`='" . parent::$USER_ID . "' 
                        AND `user_id`='{$rows3['user_id']}' 
                        AND `delete`!='" . parent::$USER_ID . "'" ), 0 );
                        $count_mess_out = mysql_result( mysql_query( "SELECT COUNT(*) 
                        FROM `cms_messages` WHERE `user_id`='" . parent::$USER_ID . "'
                        AND `contact_id`='{$rows3['user_id']}' 
                        AND `delete`!='" . parent::$USER_ID . "'" ), 0 );
                        mysql_query( "UPDATE `cms_contacts` SET
							`count_in`='$count_mess_in',
							`count_out`='$count_mess_out',
							`delete`='1' 
                            WHERE `user_id`='" . parent::$USER_ID . "' 
                            AND `contact_id`='{$rows3['contact_id']}'" );
                    }

                }
                break;
            case 'banned':
                $mass = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_contacts` 
                WHERE `user_id`='" . parent::$USER_ID . "' 
                AND `contact_id` IN (" . $id . ")" );
                while ( $rows = mysql_fetch_assoc( $query ) )
                {
                    $mass[] = $rows['id'];
                }
                if ( !empty( $mass ) )
                {
                    $exp = implode( ',', $mass );
                    mysql_query( "UPDATE `cms_contacts` SET
					`banned`='1' 
                    WHERE `user_id`='" . parent::$USER_ID . "' 
                    AND `id` IN (" . $exp . ")" );
                }
                break;
            case 'unban':
                $mass = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_contacts` 
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
                    mysql_query( "UPDATE `cms_contacts` SET
					`banned`='0' 
                    WHERE `user_id`='" . parent::$USER_ID . "' 
                    AND `id` IN (" . $exp . ")" );
                }
                break;

            case 'restore':
                $mass = array();
                $mass_contact = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_contacts` 
                WHERE `user_id`='" . parent::$USER_ID . "' 
                AND `contact_id` IN (" . $id . ")" );
                while ( $rows = mysql_fetch_assoc( $query ) )
                {
                    $mass[] = $rows['id'];
                    $mass_contact[] = $rows['contact_id'];
                    $count_mess_in = mysql_result( mysql_query( "SELECT COUNT(*) 
                    FROM `cms_messages` 
                    WHERE `contact_id`='" . parent::$USER_ID . "' 
                    AND `user_id`='{$rows['contact_id']}' 
                    AND `delete`!='" . parent::$USER_ID . "'" ), 0 );
                    $count_mess_out = mysql_result( mysql_query( "SELECT COUNT(*) 
                    FROM `cms_messages` 
                    WHERE `user_id`='" . parent::$USER_ID . "' 
                    AND `contact_id`='{$rows['contact_id']}' 
                    AND `delete`!='" . parent::$USER_ID . "'" ), 0 );
                    mysql_query( "UPDATE `cms_contacts` SET
					`count_in`='$count_mess_in',
					`count_out`='$count_mess_out',
					`delete`='0'
                     WHERE `user_id`='" . parent::$USER_ID . "'
                     AND `contact_id`='{$rows['contact_id']}'" );
                }
                if ( !empty( $mass ) )
                {
                    $sms = implode( ',', $mass_contact );
                    $out = array();
                    $count_in = 0;
                    $count_out = 0;
                    $query1 = mysql_query( "SELECT * 
                    FROM `cms_messages` 
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
                        mysql_query( "UPDATE `cms_messages` SET
						`delete_out`='0'
                        WHERE `id` IN (" . $out_str . ")" );
                    }
                    $in = array();
                    $query2 = mysql_query( "SELECT * 
                    FROM `cms_messages` 
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
                        mysql_query( "UPDATE `cms_messages` SET
						`delete_in`='0' 
                        WHERE `id` IN (" . $in_str . ")" );
                    }
                }
                break;
            case 'elected':
                $mass = array();
                $mass_contact = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_contacts` 
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
                    FROM `cms_messages` 
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
                        mysql_query( "UPDATE `cms_messages` SET
						`elected_out`='0' 
                        WHERE `id` IN (" . $out_str . ")" );
                    }
                    $in = array();
                    $query2 = mysql_query( "SELECT * 
                    FROM `cms_messages` 
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
                        mysql_query( "UPDATE `cms_messages` SET
						`elected_in`='0' 
                        WHERE `id` IN (" . $in_str . ")" );
                    }
                }
                break;
            case 'drop':
                $mass = array();
                $mass_contact = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_contacts` 
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
                    FROM `cms_messages` 
                    WHERE `delete_out`='" . parent::$USER_ID . "' 
                    AND `user_id`='" . parent::$USER_ID . "' 
                    AND `contact_id` IN (" . $sms . ")" );
                    while ( $rows1 = mysql_fetch_assoc( $query1 ) )
                    {
                        $out[] = $rows1['id'];
                    }
                    $out_str = implode( ',', $out );
                    if ( !empty( $out_str ) )
                    {
                        $query4 = mysql_query( "SELECT * 
                        FROM `cms_messages` 
                        WHERE `id` IN (" . $out_str . ")" );
                        $deleted_out = array();
                        $delete_out = array();
                        while ( $rows4 = mysql_fetch_assoc( $query4 ) )
                        {
                            if ( $rows4['delete'] && $rows4['delete'] != parent::$USER_ID )
                            {
                                $deleted_out[] = $rows4['id'];
                            } else
                                if ( !$rows4['delete'] || $rows4['delete'] == parent::$USER_ID )
                                {
                                    $delete_out[] = $rows4['id'];
                                }
                        }
                        $delete_out = implode( ',', $delete_out );
                        if ( !empty( $delete_out ) )
                        {
                            mysql_query( "UPDATE `cms_messages` SET
							`delete`='" . parent::$USER_ID . "' 
                            WHERE `id` IN (" . $delete_out . ")" );
                        }
                        $deleted_out = implode( ',', $deleted_out );
                        if ( !empty( $deleted_out ) )
                        {
                            $qq = mysql_query( "SELECT `filename` 
                            FROM `cms_messages` 
                            WHERE `filename`!='' 
                            AND `id` IN (" . $deleted_out . ")" );
                            while ( $r = mysql_fetch_assoc( $qq ) )
                            {
                                @unlink( ROOTPATH . 'files/' . MAILDIR . '/' . $r['filename'] );
                            }
                            mysql_query( "DELETE FROM `cms_messages` 
                            WHERE `id` IN (" . $deleted_out . ")" );
                        }
                    }
                    $in = array();
                    $query2 = mysql_query( "SELECT * 
                    FROM `cms_messages` 
                    WHERE `delete_in`='" . parent::$USER_ID . "' 
                    AND `contact_id`='" . parent::$USER_ID . "' 
                    AND `user_id` IN (" . $sms . ")" );
                    while ( $rows2 = mysql_fetch_assoc( $query2 ) )
                    {
                        $in[] = $rows2['id'];
                    }
                    $in_str = implode( ',', $in );
                    if ( !empty( $in_str ) )
                    {
                        $query5 = mysql_query( "SELECT * 
                        FROM `cms_messages` 
                        WHERE `id` IN (" . $in_str . ")" );
                        $deleted_in = array();
                        $delete_in = array();
                        while ( $rows5 = mysql_fetch_assoc( $query5 ) )
                        {
                            if ( $rows5['delete'] && $rows5['delete'] != parent::$USER_ID )
                            {
                                $deleted_in[] = $rows5['id'];
                            } else
                                if ( !$rows5['delete'] || $rows5['delete'] == parent::$USER_ID )
                                {
                                    $delete_in[] = $rows5['id'];
                                }
                        }
                        $delete_in = implode( ',', $delete_in );
                        if ( !empty( $delete_out ) )
                        {
                            mysql_query( "UPDATE `cms_messages` SET
							`delete`='" . parent::$USER_ID . "' 
                            WHERE `id` IN (" . $delete_in . ")" );
                        }
                        $deleted_in = implode( ',', $deleted_in );
                        if ( !empty( $deleted_in ) )
                        {
                            $qq1 = mysql_query( "SELECT `filename` 
                            FROM `cms_messages` 
                            WHERE `filename`!='' AND `id` IN (" . $deleted_in . ")" );
                            while ( $r1 = mysql_fetch_assoc( $qq1 ) )
                            {
                                @unlink( ROOTPATH . 'files/' . MAILDIR . '/' . $r1['filename'] );
                            }
                            mysql_query( "DELETE FROM `cms_messages` 
                            WHERE `id` IN (" . $deleted_in . ")" );
                        }
                    }
                    $query3 = mysql_query( "SELECT * FROM `cms_contacts` 
                    WHERE `user_id`='" . parent::$USER_ID . "' 
                    AND `contact_id` IN (" . $id . ")" );
                    while ( $rows3 = mysql_fetch_assoc( $query3 ) )
                    {
                        $count_mess_in = mysql_result( mysql_query( "SELECT COUNT(*) 
                        FROM `cms_messages` WHERE `contact_id`='" . parent::$USER_ID . "' 
                        AND `user_id`='{$rows3['contact_id']}' 
                        AND `delete`!=?;" ), 0 );
                        $count_mess_out = mysql_result( mysql_query( "SELECT COUNT(*) 
                        FROM `cms_messages` WHERE `user_id`='" . parent::$USER_ID . "' 
                        AND `contact_id`='{$rows3['contact_id']}' 
                        AND `delete`!='" . parent::$USER_ID . "';" ), 0 );
                        mysql_query( "UPDATE `cms_contacts` SET
						`count_in`='$count_mess_in',
						`count_out`='$count_mess_out' 
                        WHERE `user_id`='" . parent::$USER_ID . "' 
                        AND `contact_id`='{$rows3['contact_id']}'" );
                    }
                }
                break;

            case 'clear':

                $query = mysql_query( "SELECT * 
                FROM `cms_messages` 
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
                    FROM `cms_messages` 
                    WHERE `filename`!='' 
                    AND `id` IN (" . $del . ")" );
                    while ( $r1 = mysql_fetch_assoc( $qq1 ) )
                    {
                        @unlink( ROOTPATH . 'files/' . MAILDIR . '/' . $r1['filename'] );
                    }
                    mysql_query( "DELETE FROM `cms_messages` 
                    WHERE `id` IN (" . $del . ")" );
                }

                if ( $update )
                {
                    $id = implode( ',', $update );
                    mysql_query( "UPDATE `cms_messages` SET `delete`='" . parent::$USER_ID . "' 
                    WHERE `id` IN (" . $id . ")" );
                }

                break;

            case 'read':
                $mass = array();
                $mass_contact = array();
                $query = mysql_query( "SELECT * 
                FROM `cms_contacts` 
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
                    FROM `cms_messages` 
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
                        mysql_query( "UPDATE `cms_messages` SET
						`read`='1' 
                        WHERE `id` IN (" . $in_str . ")" );
                    }
                }
                break;
        }
    }

    public static function mailConnect()
    {
        return array(
            'add',
            'archive',
            'banned',
            'basket',
            'delete',
            'elected',
            'elected',
            'files',
            'load',
            'messages',
            'new',
            'restore',
            'search',
            'select',
            'systems' );
    }

    public static function checkContact( $id = null )
    {
        if ( $id == null )
            return false;
        $count = mysql_result( mysql_query( "SELECT COUNT(*) 
        FROM `cms_contacts` 
        WHERE `user_id`='$id' 
        AND `contact_id`='" . parent::$USER_ID . "'" ), 0 );
        $total = mysql_result( mysql_query( "SELECT COUNT(*) 
        FROM `cms_contacts` 
        WHERE `user_id`='" . parent::$USER_ID . "' 
        AND `contact_id`='$id'" ), 0 );
        if ( $total )
        {
            mysql_query( "UPDATE `cms_contacts` SET
			`time`='" . time() . "' 
            WHERE `user_id`='" . parent::$USER_ID . "' 
            AND `contact_id`='$id'" );
            if ( $count )
            {
                mysql_query( "UPDATE `cms_contacts` SET
				`time`='" . time() . "' 
                WHERE `user_id`='$id' 
                AND `contact_id`='" . parent::$USER_ID . "'" );
            } else
            {
                mysql_query( "INSERT INTO `cms_contacts` SET
				`user_id`='$id',
				`contact_id`='" . parent::$USER_ID . "',
				`time`='" . time() . "'" );
            }
        } else
        {
            mysql_query( "INSERT INTO `cms_contacts` SET
			`user_id`='" . parent::$USER_ID . "',
			`contact_id`='$id',
			`time`='" . time() . "'" );
            if ( $count )
            {
                mysql_query( "UPDATE `cms_contacts` SET
				`time`='" . time() . "' 
                WHERE `user_id`='$id' 
                AND `contact_id`='" . parent::$USER_ID . "'" );
            } else
            {
                mysql_query( "INSERT INTO `cms_contacts` SET
				`user_id`='$id',
				`contact_id`='" . parent::$USER_ID . "',
				`time`='" . time() . "'" );
            }
        }
    }

    public static function countPlus( $id = null )
    {
        if ( $id == null )
            return false;
        mysql_query( "UPDATE `cms_contacts` SET
		`count_out`=`count_out`+1,
        `time`='" . time() . "', 
        `archive`='0', 
        `delete`='0' 
        WHERE `user_id`='" . parent::$USER_ID . "' 
        AND `contact_id`='$id'" );
        mysql_query( "UPDATE `cms_contacts` SET
        `count_in`=`count_in`+1, 
        `time`='" . time() . "', 
        `archive`='0', 
        `delete`='0' 
        WHERE `user_id`='$id' 
        AND `contact_id`='" . parent::$USER_ID . "'" );
    }

    public static function countNew( $id = null )
    {
        if ( $id == null )
            return false;
        $new = mysql_result( mysql_query( "SELECT COUNT(*) 
			FROM `cms_messages` 
			LEFT JOIN `cms_contacts` 
			ON `cms_messages`.`user_id`=`cms_contacts`.`contact_id` 
			AND `cms_contacts`.`user_id`='" . parent::$USER_ID . "' 
			WHERE `cms_messages`.`user_id`='" . $id . "' 
			AND `cms_messages`.`contact_id`='" . parent::$USER_ID . "' 
			AND `cms_messages`.`sys`='0' 
			AND `cms_messages`.`read`='0' 
			AND (`cms_messages`.`delete_in`!='" . parent::$USER_ID . "' 
			AND `cms_messages`.`delete_out`!='" . parent::$USER_ID . "')
			AND `cms_messages`.`delete`!='" . parent::$USER_ID . "' 
			AND `cms_contacts`.`banned`!='1'" ), 0 );
        if ( $new )
            return '+' . $new;
        else
            return false;
    }

    public static function formatsize( $var = 0 )
    {
        if ( $var >= 1073741824 )
            $var = round( $var / 1073741824 * 100 ) / 100 . ' Gb';
        elseif ( $var >= 1048576 )
            $var = round( $var / 1048576 * 100 ) / 100 . ' Mb';
        elseif ( $var >= 1024 )
            $var = round( $var / 1024 * 100 ) / 100 . ' Kb';
        else
            $var = $var . ' b';
        return $var;
    }

    public static function ignor( $id = null )
    {
        if ( $id == null )
            return false;
        $query = mysql_query( "SELECT * FROM `cms_contacts` 
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

    public static function fileicon( $file = null )
    {
        if ( $file == null )
            return false;
        $ext = pathinfo( $file, PATHINFO_EXTENSION );
        switch ( $ext )
        {
            case 'zip':
            case 'rar':
            case '7z':
            case 'tar':
            case 'gz':
                return 'filetype_6.png';

            case 'mp3':
            case 'amr':
                return 'filetype_8.png';

            case 'txt':
            case 'pdf':
            case 'doc':
            case 'rtf':
            case 'djvu':
            case 'xls':
                return 'filetype_4.png';

            case 'jar':
            case 'jad':
                return 'filetype_2.png';

            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'bmp':
                return 'filetype_5.png';

            case 'sis':
            case 'sisx':
                return 'filetype_3.png';

            case '3gp':
            case 'avi':
            case 'flv':
            case 'mpeg':
            case 'mp4':
                return 'filetype_7.png';

            case 'exe':
            case 'msi':
                return 'filetype_1.png';

            default:
                return 'filetype_9.png';
        }
    }
}
