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
defined( '_IN_JOHNCMS_CONTACTS' ) or die( 'Error: restricted access' );
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
                if ( Functions::checkIgnor( Vars::$ID ) )
                {
                    $tpl->contents = Functions::displayError( __( 'ignor_no_select' ), '<a href="' .
                        Vars::$MODULE_URI . '">' . __( 'contacts' ) . '</a>' );
                } else
                {
                    $cont = mysql_query( "SELECT * FROM `cms_mail_contacts` WHERE `user_id`='" . Vars::$USER_ID .
                        "' AND `contact_id`='" . Vars::$ID . "' LIMIT 1" );
                    $result = mysql_fetch_assoc( $cont );
                    if ( $result && $result['delete'] == 0 )
                    {
                        if ( isset( $_POST['submit'] ) && isset($_POST['token']) && isset($_SESSION['token_status']) &&
							$_POST['token'] == $_SESSION['token_status'])
                        {
							$id = implode(',', array( Vars::$ID ));
							if (!empty($id)) {
								$mass = array();
								$mass_contact = array();
								$query = mysql_query("SELECT * 
								FROM `cms_mail_contacts` 
								WHERE `user_id`='" . Vars::$USER_ID . "' 
								AND `contact_id` IN (" . $id . ")");
								while ($rows = mysql_fetch_assoc($query)) {
									$mass[] = $rows['id'];
									$mass_contact[] = $rows['contact_id'];
								}
								if (!empty($mass)) {
									$exp = implode(',', $mass);
									$sms = implode(',', $mass_contact);
									$out = array();
									$query1 = mysql_query("SELECT * 
									FROM `cms_mail_messages` 
									WHERE `user_id`='" . Vars::$USER_ID . "'
									AND `contact_id` IN (" . $sms . ")");
									while ($rows1 = mysql_fetch_assoc($query1)) {
										$out[] = $rows1['id'];
									}
									$out_str = implode(',', $out);
									if (!empty($out_str)) {
										mysql_query("UPDATE `cms_mail_messages`
										 SET `delete_out`='" . Vars::$USER_ID . "' 
										 WHERE `id` IN (" . $out_str . ")");
									}
									$in = array();
									$query2 = mysql_query("SELECT * 
									FROM `cms_mail_messages` 
									WHERE `contact_id`='" . Vars::$USER_ID . "' 
									AND `user_id` IN (" . $sms . ")");
									while ($rows2 = mysql_fetch_assoc($query2)) {
										$in[] = $rows2['id'];
									}
									$in_str = implode(',', $in);
									if (!empty($in_str)) {
										mysql_query("UPDATE `cms_mail_messages` SET 
										`delete_in`='" . Vars::$USER_ID . "' 
										WHERE `id` IN (" . $in_str . ")");
									}
								}
								mysql_query("UPDATE `cms_mail_contacts` SET 
								`delete`='1'
								WHERE `user_id`='" . Vars::$USER_ID . "' 
								AND `contact_id` IN (" . $id . ")");
							}
							
                            Header( 'Location: ' . Vars::$HOME_URL . '/profile?user=' . Vars::
                                $ID );
                            exit;
                        }
                        $tpl->urlBack = Vars::$HOME_URL . '/profile?user=' . Vars::$ID;
                        $tpl->urlSelect = Vars::$MODULE_URI . '?act=select&amp;mod=contact&amp;id=' .
                            Vars::$ID;
                        $tpl->select = __( 'confirm_delete_contact' );
                        $tpl->submit = __( 'delete' );
						$tpl->token = mt_rand(100, 10000);
						$_SESSION['token_status'] = $tpl->token;
                        $tpl->phdr = __( 'delete_contact' );

                    } else
                    {

                        if ( isset( $_POST['submit'] ) && isset($_POST['token']) && isset($_SESSION['token_status']) &&
							$_POST['token'] == $_SESSION['token_status'] )
                        {
                            if ( $result['delete'] == 1 )
                            {
								$id = implode(',', array( Vars::$ID ));
								if (!empty($id)) {
									$mass = array();
									$mass_contact = array();
									$query = mysql_query( "SELECT * 
									FROM `cms_mail_contacts` 
									WHERE `user_id`='" . Vars::$USER_ID . "' 
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
										$query1 = mysql_query( "SELECT * 
										FROM `cms_mail_messages` 
										WHERE `user_id`='" . Vars::$USER_ID . "' 
										AND `contact_id` IN (" . $sms . ") 
										AND `delete_out`='" . Vars::$USER_ID . "' 
										AND `delete`!='" . Vars::$USER_ID . "'" );
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
										WHERE `contact_id`='" . Vars::$USER_ID . "' 
										AND `user_id` IN (" . $sms . ") 
										AND `delete_in`='" . Vars::$USER_ID . "' 
										AND `delete`!='" . Vars::$USER_ID . "'" );
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
										mysql_query("UPDATE `cms_mail_contacts` SET 
										`delete`='0'
										WHERE `user_id`='" . Vars::$USER_ID . "' 
										AND `contact_id` IN (" . $id . ")");
									}
								}
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
                        $tpl->select = __( 'confirm_add_contact' );
                        $tpl->submit = __( 'add' );
                        $tpl->phdr = __( 'add_contact' );

                    }
					$tpl->token = mt_rand(100, 10000);
					$_SESSION['token_status'] = $tpl->token;
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
                    if ( isset( $_POST['submit'] ) && isset($_POST['token']) && isset($_SESSION['token_status']) &&
						$_POST['token'] == $_SESSION['token_status'])
                    {
						mysql_query( "UPDATE `cms_mail_contacts` SET
						`banned`='0' 
						WHERE `user_id`='" . Vars::$USER_ID . "' 
						AND `contact_id`=" . Vars::$ID );
                        Header( 'Location: ' . Vars::$HOME_URL . '/profile?user=' . Vars::$ID );
                        exit;
                    }
                    $tpl->urlBack = Vars::$HOME_URL . '/profile?user=' . Vars::$ID;
                    $tpl->urlSelect = Vars::$MODULE_URI . '?act=select&amp;mod=banned&amp;id=' . Vars::
                        $ID;
                    $tpl->select = __( 'confirm_unban_contact' );
                    $tpl->submit = __( 'unban' );
                    $tpl->phdr = __( 'unban_contact' );
					$tpl->token = mt_rand(100, 10000);
					$_SESSION['token_status'] = $tpl->token;
                    $tpl->contents = $tpl->includeTpl( 'select' );
                } else
                {
                    $user = mysql_fetch_assoc( $q );
                    //Администрацию нельзя добавлять в игнор
					if ( $user['rights'] )
                    {
                        $tpl->contents = Functions::displayError( __( 'admin_user' ), '<a href="' .
                            Vars::$MODULE_URI . '">' . __( 'mail' ) . '</a>' );
                    } else
                    {
                        if ( isset( $_POST['submit'] ) && isset($_POST['token']) && isset($_SESSION['token_status']) &&
							$_POST['token'] == $_SESSION['token_status'])
                        {
                            if ( $result )
                            {
								mysql_query( "UPDATE `cms_mail_contacts` SET
								`banned`='1' 
								WHERE `user_id`='" . Vars::$USER_ID . "' 
								AND `contact_id`=" . Vars::$ID );
                            } else
                            {
                                mysql_query( "INSERT INTO `cms_mail_contacts` SET
								`user_id`='" . Vars::$USER_ID . "',
								`contact_id`='" . Vars::$ID . "',
								`time`='" . time() . "',
								`banned`='1'" );
								
                            }
							Header( 'Location: ' . Vars::$HOME_URL . '/profile?user=' . Vars::$ID );
							exit;
                        }
                        $tpl->urlBack = Vars::$HOME_URL . '/profile?user=' . Vars::$ID;
                        $tpl->urlSelect = Vars::$MODULE_URI . '?act=select&amp;mod=banned&amp;id=' .
                            Vars::$ID;
                        $tpl->select = __( 'confirm_ban_contact' );
                        $tpl->submit = __( 'ban' );
                        $tpl->phdr = __( 'ban_contact' );
						$tpl->token = mt_rand(100, 10000);
						$_SESSION['token_status'] = $tpl->token;
                        $tpl->contents = $tpl->includeTpl( 'select' );
                    }
                }

                break;

            default:
                //Ошибка запроса
				$tpl->contents = Functions::displayError( __( 'error_request' ), '<a href="' .
                    Vars::$MODULE_URI . '">' . __( 'contacts' ) . '</a>' );
        }
    } else
    {
        //Пользователя не существует
		$tpl->contents = Functions::displayError( __( 'user_does_not_exist' ), '<a href="' .
            Vars::$MODULE_URI . '">' . __( 'contacts' ) . '</a>' );
    }
} else
{
    //Контакт не выбран
	$tpl->contents = Functions::displayError( __( 'contact_no_select' ), '<a href="' . Vars::
        $MODULE_URI . '">' . __( 'contacts' ) . '</a>' );
}
