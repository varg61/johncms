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
if ( Vars::$ID == Vars::$USER_ID && empty( Vars::$MOD ) )
{
    $tpl->contents = Functions::displayError( $lng_mail['error_request'] . '!', '<a href="' . Vars::
        $MODULE_URI . '">' . Vars::$LNG['contacts'] . '</a>' );
} else
{
    if ( Vars::$ID && Vars::$MOD == 'delete' )
    {
        $q = mysql_query( "SELECT * FROM `cms_messages` WHERE (`user_id`='" . Vars::$USER_ID . "' OR `contact_id`='" .
            Vars::$USER_ID . "') AND `id`='" . Vars::$ID . "'" );
        if ( mysql_num_rows( $q ) )
        {
            $data = mysql_fetch_assoc( $q );
            if ( isset( $_POST['submit'] ) )
            {
                if ( $data['user_id'] == Vars::$USER_ID )
                {
                    if ( $data['delete_out'] != Vars::$USER_ID )
                    {
                        mysql_query( "UPDATE `cms_messages` SET
						`delete_out`='" . Vars::$USER_ID . "' WHERE `id`='" . Vars::$ID . "'" );
                        mysql_query( "UPDATE `cms_contacts` SET
						`count_out`=`count_out`-1 WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='{$data['contact_id']}'" );
                    }
                }
                if ( $data['contact_id'] == Vars::$USER_ID )
                {
                    if ( $data['delete_in'] != Vars::$USER_ID )
                    {
                        mysql_query( "UPDATE `cms_messages` SET
						`delete_in`='" . Vars::$USER_ID . "' WHERE `id`='" . Vars::$ID . "'" );
                        mysql_query( "UPDATE `cms_contacts` SET
						`count_in`=`count_in`-1 WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='{$data['user_id']}'" );
                    }
                }
                if ( $data['user_id'] == Vars::$USER_ID )
                {
                    Header( 'Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $data['contact_id'] );
                    exit;
                } else
                {
                    Header( 'Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $data['user_id'] );
                    exit;
                }
            } else
            {
                if ( $data['user_id'] == Vars::$USER_ID )
                    $tpl->urlBack = Vars::$MODULE_URI . '?act=messages&id=' . $data['contact_id'];
                else
                    $tpl->urlBack = Vars::$MODULE_URI . '?act=messages&id=' . $data['user_id'];

                $tpl->urlSelect = Vars::$MODULE_URI . '?act=messages&amp;mod=delete&amp;id=' . Vars::
                    $ID;
                $tpl->select = $lng_mail['confirm_removing'];
                $tpl->submit = Vars::$LNG['delete'];
                $tpl->phdr = $lng_mail['removing_message'];
                $tpl->contents = $tpl->includeTpl( 'select' );
            }
        } else
        {
            $tpl->contents = $lng_mail['page_does_not_exist'];
        }
    } else {
        if ( Vars::$ID && Vars::$MOD == 'elected' )
        {
            $q = mysql_query( "SELECT * FROM `cms_messages` WHERE (`user_id`='" . Vars::$USER_ID . "' OR `contact_id`='" .
                Vars::$USER_ID . "') AND `id`='" . Vars::$ID . "'" );
            if ( mysql_num_rows( $q ) )
            {
                $data = mysql_fetch_assoc( $q );
                if ( $data['user_id'] == Vars::$USER_ID )
                {
                    if ( $data['elected_out'] != Vars::$USER_ID )
                    {
                        mysql_query( "UPDATE `cms_messages` SET
					`elected_out`='" . Vars::$USER_ID . "' WHERE `id`='" . Vars::$ID . "'" );
                    }
                }
                if ( $data['contact_id'] == Vars::$USER_ID )
                {
                    if ( $data['elected_in'] != Vars::$USER_ID )
                    {
                        mysql_query( "UPDATE `cms_messages` SET
					`elected_in`='" . Vars::$USER_ID . "' WHERE `id`='" . Vars::$ID . "'" );
                    }
                }
                if ( $data['user_id'] == Vars::$USER_ID )
                {
                    Header( 'Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $data['contact_id'] );
                    exit;
                } else
                {
                    Header( 'Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $data['user_id'] );
                    exit;
                }
            } else
            {
                $tpl->contents = $lng_mail['page_does_not_exist'];
            }
        } else
        {
            if ( Vars::$ID )
            {
                $q = mysql_query( "SELECT `nickname` FROM `users` WHERE `id`='" . Vars::$ID . "' LIMIT 1" );
                if ( mysql_num_rows( $q ) )
                {
                    $q = mysql_fetch_assoc( $q );
                    $tpl->login = $q['nickname'];
                    if ( isset( $_POST['submit'] ) )
                    {
                        $text = isset( $_POST['text'] ) ? trim( $_POST['text'] ) : '';
                        $error = array();
                        if ( Vars::$USER_BAN['1'] || Vars::$USER_BAN['3'] )
                            $error[] = $lng_mail['error_banned'];
						
                        if ( empty( $error ) )
                        {
                            if ( empty( $text ) )
                                $error[] = $lng_mail['empty_message'];
                            else
                                if ( mb_strlen( $text ) < 2 || mb_strlen( $text ) > 5000 )
                                    $error[] = $lng_mail['error_message'];

                            $flood = Functions::antiFlood();
                            if ( $flood )
                                $error = Vars::$LNG['error_flood'] . '&#160;' . $flood . '&#160;' . Vars::
                                    $LNG['seconds'];

                            $filename = '';
                            $filesize = 0;

							if( empty($error) ) {
								$query = mysql_query( "SELECT * FROM `cms_contacts` 
								WHERE `user_id`='" . Vars::$ID . "' 
								AND `contact_id`='" . Vars::$USER_ID . "' 
								AND `banned`='1' LIMIT 1" );
								if ( mysql_num_rows( $query ) )
								{
									$error[] = $lng_mail['you_banned'];
								}
							}
							
                            if ( empty( $error ) )
                            {
                                require ( MODPATH . MAILDIR . '/_includes/class.upload.php' );
                                $handle = new Upload( $_FILES );
                                $handle->DIR = ROOTPATH . 'files/' . MAILDIR;
                                $handle->MAX_FILE_SIZE = ( 1024 * 1024 ) * 0.1;
                                $handle->PREFIX_FILE = true;
                                if ( $handle->upload() == true )
                                {
                                    $filename = $handle->FILE_UPLOAD;
                                    $filesize = $handle->INFO['size'];
                                } else
                                {
                                    if ( $errors = $handle->errors() )
                                        $error[] = $errors;
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
                            mysql_query( "UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = " .
                                Vars::$USER_ID );

                            Header( 'Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . Vars::$ID );
                            exit;
                        } else
                        {
							$tpl->text = Validate::filterString(trim($_POST['text']));
							$tpl->error_add = Functions::displayError( $error );
                        }
                    }

                    $total = mysql_result( mysql_query( "SELECT COUNT(*)
					FROM `cms_messages`
					WHERE ((`user_id`='" . Vars::$USER_ID . "'
					AND `contact_id`='" . Vars::$ID . "')
					OR (`contact_id`='" . Vars::$USER_ID . "'
					AND `user_id`='" . Vars::$ID . "'))
					AND `delete_in`!='" . Vars::$USER_ID . "'
					AND `delete_out`!='" . Vars::$USER_ID . "'" ), 0 );
                    if ( $total )
                    {
                        $query = mysql_query( "SELECT `cms_messages`.*, `cms_messages`.`id` as `mid`, `users`.*
						FROM `cms_messages`
						LEFT JOIN `users` 
						ON `cms_messages`.`user_id`=`users`.`id` 
						WHERE ((`cms_messages`.`user_id`='" . Vars::$USER_ID . "'
						AND `cms_messages`.`contact_id`='" . Vars::$ID . "')
						OR (`cms_messages`.`contact_id`='" . Vars::$USER_ID . "'
						AND `cms_messages`.`user_id`='" . Vars::$ID . "'))
						AND `cms_messages`.`delete_in`!='" . Vars::$USER_ID . "'
						AND `cms_messages`.`delete_out`!='" . Vars::$USER_ID . "'
						ORDER BY `cms_messages`.`time` DESC" . Vars::db_pagination() );

                        $array = array();
                        $i = 1;
                        $mass_read = array();
                        while ( $row = mysql_fetch_assoc( $query ) )
                        {
                            if ( $row['read'] == 0 && $row['contact_id'] == Vars::$USER_ID )
                                $mass_read[] = $row['mid'];
                            $text = Validate::filterString( $row['text'], 1, 1 );
                            if ( Vars::$USER_SET['smileys'] )
                                $text = Functions::smileys( $text, $row['rights'] >= 1 ? 1 : 0 );
                            $array[] = array(
                                'id' => $row['id'],
                                'mid' => $row['mid'],
                                'icon' => Functions::getImage( 'usr_' . ( $row['sex'] == 'm' ? 'm' :
                                    'w' ) . '.png', '', 'align="middle"' ),
                                'list' => ( ( $i % 2 ) ? 'list1' : 'list2' ),
                                'nickname' => $row['nickname'],
                                'file' => $row['filename'] ? Functions::getImage( Mail::fileicon( $row['filename'] ),
                                    '', 'style="margin: 0 0 -4px 0;"' ) . '&#160;<a href="' . Vars::
                                    $MODULE_URI . '?act=load&amp;id=' . $row['mid'] . '">' . $row['filename'] .
                                    '</a> (' . Mail::formatsize( $row['filesize'] ) . ')(' . $row['filecount'] .
                                    ')' : '',
                                'time' => Functions::displayDate( $row['time'] ),
                                'text' => $text,
                                'url' => ( Vars::$MODULE_URI . '?act=messages&amp;id=' . $row['id'] ),
                                'online' => ( time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                                    '<span class="green"> [ON]</span>' ),
                                'elected' => ( ( $row['elected_in'] != Vars::$USER_ID && $row['elected_out'] !=
                                    Vars::$USER_ID ) ? true : false ),
                                'selectBar' => ( '[<span class="red">х</span>&#160;<a href="' . Vars::
                                    $MODULE_URI . '?act=messages&amp;mod=delete&amp;id=' . $row['mid'] .
                                    '">' . Vars::$LNG['delete'] . '</a>] ' . ( ( $row['elected_in'] !=
                                    Vars::$USER_ID && $row['elected_out'] != Vars::$USER_ID ) ? '[<a href="' .
                                    Vars::$MODULE_URI . '?act=messages&amp;mod=elected&amp;id=' . $row['mid'] .
                                    '">' . $lng_mail['in_elected'] . '</a>]' : '' ) ) );
                            ++$i;
                        }
                        //Ставим метку о прочтении
                        if ( $mass_read )
                        {
                            $result = implode( ',', $mass_read );
                            mysql_query( "UPDATE `cms_messages` SET `read`='1' WHERE `contact_id`='" .
                                Vars::$USER_ID . "' AND `id` IN (" . $result . ")" );
                        }
                        $tpl->query = $array;
                        $tpl->total = $total;
                        $tpl->display_pagination = Functions::displayPagination( Vars::$MODULE_URI .
                            '?act=messages&amp;id=' . Vars::$ID . '&amp;', Vars::$START, $total, Vars::
                            $USER_SET['page_size'] );
                        $tpl->list = $tpl->includeTpl( 'list' );
                    } else
                    {
                        $tpl->list = '<div class="rmenu">' . $lng_mail['no_messages'] . '</div>';
                    }
					if( Vars::$USER_BAN['1'] || Vars::$USER_BAN['3'] ) {
						$tpl->ignor = '<div class="rmenu">' . $lng_mail['error_banned'] . '</div>';
					} else if( Mail::ignor( Vars::$ID ) ) 
					{
						$tpl->ignor = '<div class="rmenu">' . $lng_mail['user_banned'] . '</div>';
					}
                    $tpl->contents = $tpl->includeTpl( 'messages' );
                } else
                {
                    $tpl->contents = Functions::displayError( $lng_mail['user_does_not_exist'] . '!',
                        '<a href="' . Vars::$MODULE_URI . '">' . Vars::$LNG['contacts'] . '</a>' );
                }
            } else
            {
                $tpl->contents = Functions::displayError( $lng_mail['contact_no_select'] . '!', '<a href="' .
                    Vars::$MODULE_URI . '">' . Vars::$LNG['contacts'] . '</a>' );
            }
        }
	}
}
