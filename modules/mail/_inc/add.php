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
    Header( 'Location: ' . Vars::$HOME_URL . '/404.php' );
    exit;
}

$tpl->mail_error = '';
if ( isset( $_POST['submit'] ) )
{
    $login = isset( $_POST['login'] ) ? trim( $_POST['login'] ) : '';
    $text = isset( $_POST['text'] ) ? trim( $_POST['text'] ) : '';
    $error = array();
    if ( Vars::$USER_BAN['1'] || Vars::$USER_BAN['3'] )
        $error[] = $lng_mail['error_banned'];
    if ( empty( $error ) )
    {
        if ( empty( $login ) )
            $error[] = $lng_mail['error_banned'] . '!';
        else
            if ( mb_strlen( $login ) < 2 || mb_strlen( $login ) > 20 )
                $error[] = $lng_mail['error_login'] . '!';
        if ( empty( $text ) )
            $error[] = $lng_mail['empty_message'] . '!';
        else
            if ( mb_strlen( $text ) < 2 || mb_strlen( $text ) > 5000 )
                $error[] = $lng_mail['error_message'] . '!';

        $flood = Functions::antiFlood();
        if ( $flood )
            $error = Vars::$LNG['error_flood'] . '&#160;' . $flood . '&#160;' . Vars::$LNG['seconds'];
        if ( empty( $error ) )
        {
            $q = mysql_query( "SELECT * FROM `users` WHERE `nickname`='" . mysql_real_escape_string( $login ) .
                "' LIMIT 1;" );
            if ( mysql_num_rows( $q ) )
            {
                $data = mysql_fetch_assoc( $q );
                Vars::$ID = $data['id'];
                //if ( Vars::$ID == Vars::$USER_ID )
                //    $error[] = $lng_mail['error_my_message'] . '!';
            } else
                $error[] = $lng_mail['user_does_not_exist'] . '!';
        }
		
		if( empty($error) ) {
			$query = mysql_query( "SELECT * FROM `cms_contacts` 
			WHERE `user_id`='" . Vars::$ID . "' 
			AND `contact_id`='" . Vars::$USER_ID . "' 
			AND `banned`='1' LIMIT 1" );
			if ( mysql_num_rows( $query ) )
			{
				$error[] = 'Пользователь добавил вас в игнор';
			}
		}
		
        $filename = '';
        $filesize = 0;

        if ( empty( $error ) )
        {
            require ( MODPATH . MAILDIR . '/_includes/class.upload.php' );
            $handle = new Upload( $_FILES );
            $handle->DIR = ROOTPATH . 'files/' . MAILDIR;
            $handle->MAX_FILE_SIZE = ( 1024 * 1024 ) * 2;
            $handle->PREFIX_FILE = true;
            if ( $handle->upload() == true )
            {
                $filename = $handle->FILE_UPLOAD;
                $filesize = $handle->INFO['size'];
            } else
            {
                if ( $handle->errors() )
                    $error[] = $handle->errors();
            }
        }

        if ( empty( $error ) )
            Mail::checkContact( Vars::$ID );
    }
    if ( empty( $error ) )
    {
        mysql_query( "INSERT INTO `cms_messages` SET
		`user_id`='" . Vars::$USER_ID . "',
		`contact_id`='" . Vars::$ID . "',
		`text`='" . mysql_real_escape_string( $text ) . "',
		`time`='" . time() . "',
		`filename`='$filename',
		`filesize`='$filesize'" );
        Mail::countPlus( Vars::$ID );

        // Фиксируем время последнего поста (антиспам)
        mysql_query( "UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = " . Vars::$USER_ID );

        Header( 'Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . Vars::$ID );
        exit;
    } else
    {
		$tpl->login = Validate::filterString(trim($_POST['login']));
		$tpl->text = Validate::filterString(trim($_POST['text']));
		$tpl->mail_error = Functions::displayError( $error );
    }
}

$tpl->contents = $tpl->includeTpl( 'add' );
