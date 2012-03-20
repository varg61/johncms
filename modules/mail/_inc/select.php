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
if ( Vars::$ID )
{
    $q = mysql_query( "SELECT * FROM `users` WHERE `id`='" . Vars::$ID . "' LIMIT 1" );
    if ( mysql_num_rows( $q ) )
    {
        switch ( Vars::$MOD )
        {
            //Добаляем или удаляем пользователя в контакт
			case 'contact':
                if ( Mail::ignor( Vars::$ID ) )
                {
                    $tpl->contents = Functions::displayError( lng( 'ignor_no_select' ), '<a href="' .
                        Vars::$MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
                } else
                {
                    $cont = mysql_query( "SELECT * FROM `cms_mail_contacts` WHERE `user_id`='" . Vars::$USER_ID .
                        "' AND `contact_id`='" . Vars::$ID . "' LIMIT 1" );
                    $result = mysql_fetch_assoc( $cont );
                    if ( $result && $result['delete'] == 0 )
                    {
                        if ( isset( $_POST['submit'] ) )
                        {
                            Mail::mailSelectContacts( array( Vars::$ID ), 'delete' );
                            Header( 'Location: ' . Vars::$HOME_URL . '/profile?user=' . Vars::
                                $ID );
                            exit;
                        }
                        $tpl->urlBack = Vars::$HOME_URL . '/profile?user=' . Vars::$ID;
                        $tpl->urlSelect = Vars::$MODULE_URI . '?act=select&amp;mod=contact&amp;id=' .
                            Vars::$ID;
                        $tpl->select = lng( 'confirm_delete_contact' );
                        $tpl->submit = lng( 'delete' );
                        $tpl->phdr = lng( 'delete_contact' );

                    } else
                    {

                        if ( isset( $_POST['submit'] ) )
                        {
                            if ( $result['delete'] == 1 )
                            {
                                Mail::mailSelectContacts( array( Vars::$ID ), 'restore' );
                            } else
                            {
                                mysql_query( "INSERT INTO `cms_mail_contacts` SET
								`user_id`='" . Vars::$USER_ID . "',
								`contact_id`='" . Vars::$ID . "',
								`time`='" . time() . "'" );
                            }
                            Header( 'Location: ' . Vars::$HOME_URL . '/profile?user=' . Vars::
                                $ID );
                            exit;
                        }
                        $tpl->urlBack = Vars::$HOME_URL . '/profile?user=' . Vars::$ID;
                        $tpl->urlSelect = Vars::$MODULE_URI . '?act=select&amp;mod=contact&amp;id=' .
                            Vars::$ID;
                        $tpl->select = lng( 'confirm_add_contact' );
                        $tpl->submit = lng( 'add' );
                        $tpl->phdr = lng( 'add_contact' );

                    }
                    $tpl->contents = $tpl->includeTpl( 'select' );
                }
                break;
			//Добавляем или удаляем пользователя из контактов
            case 'banned':
                $ban = mysql_query( "SELECT * FROM `cms_mail_contacts` WHERE `user_id`='" . Vars::$USER_ID .
                    "' AND `contact_id`='" . Vars::$ID . "' LIMIT 1" );
                $result = mysql_fetch_assoc( $ban );
                if ( $result && $result['banned'] == 1 )
                {
                    if ( isset( $_POST['submit'] ) )
                    {
                        Mail::mailSelectContacts( array( Vars::$ID ), 'unban' );
                        Header( 'Location: ' . Vars::$HOME_URL . '/profile?user=' . Vars::
                            $ID );
                        exit;
                    }
                    $tpl->urlBack = Vars::$HOME_URL . '/profile?user=' . Vars::$ID;
                    $tpl->urlSelect = Vars::$MODULE_URI . '?act=select&amp;mod=banned&amp;id=' . Vars::
                        $ID;
                    $tpl->select = lng( 'confirm_unban_contact' );
                    $tpl->submit = lng( 'unban' );
                    $tpl->phdr = lng( 'unban_contact' );
                    $tpl->contents = $tpl->includeTpl( 'select' );
                } else
                {
                    $user = mysql_fetch_assoc( $q );
                    //Администрацию нельзя добавлять в игнор
					if ( $user['rights'] )
                    {
                        $tpl->contents = Functions::displayError( lng( 'admin_user' ), '<a href="' .
                            Vars::$MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
                    } else
                    {
                        if ( isset( $_POST['submit'] ) )
                        {
                            if ( $result )
                            {
                                Mail::mailSelectContacts( array( Vars::$ID ), 'banned' );
                            } else
                            {
                                mysql_query( "INSERT INTO `cms_mail_contacts` SET
								`user_id`='" . Vars::$USER_ID . "',
								`contact_id`='" . Vars::$ID . "',
								`time`='" . time() . "',
								`banned`='1'" );
								
                            }
							Header( 'Location: ' . Vars::$HOME_URL . '/profile?user=' .
                                    Vars::$ID );
							exit;
                        }
                        $tpl->urlBack = Vars::$HOME_URL . '/profile?user=' . Vars::$ID;
                        $tpl->urlSelect = Vars::$MODULE_URI . '?act=select&amp;mod=banned&amp;id=' .
                            Vars::$ID;
                        $tpl->select = lng( 'confirm_ban_contact' );
                        $tpl->submit = lng( 'ban' );
                        $tpl->phdr = lng( 'ban_contact' );
                        $tpl->contents = $tpl->includeTpl( 'select' );
                    }
                }

                break;

            default:
                //Ошибка запроса
				$tpl->contents = Functions::displayError( lng( 'error_request' ), '<a href="' .
                    Vars::$MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
        }
    } else
    {
        //Пользователя не существует
		$tpl->contents = Functions::displayError( lng( 'user_does_not_exist' ), '<a href="' .
            Vars::$MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
    }
} else
{
    //Контакт не выбран
	$tpl->contents = Functions::displayError( lng( 'contact_no_select' ), '<a href="' . Vars::
        $MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
}
