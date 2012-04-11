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
    //Проверяем выбранное сообщение
	$q = mysql_query( "SELECT * FROM `cms_mail_messages` WHERE (`user_id`='" . Vars::$USER_ID . "' OR `contact_id`='" .
        Vars::$USER_ID . "') AND `id`='" . Vars::$ID . "' AND (`delete_in`='" . Vars::$USER_ID . "' OR `delete_out`='" .
        Vars::$USER_ID . "' OR `elected_in`='" . Vars::$USER_ID . "' OR `elected_out`='" . Vars::$USER_ID .
        "')  AND `delete`!='" . Vars::$USER_ID . "'" );
    if ( mysql_num_rows( $q ) )
    {
        //Удаление отдельных сообщений
		$data = mysql_fetch_assoc( $q );
        if ( $data['elected_in'] == Vars::$USER_ID || $data['elected_out'] == Vars::$USER_ID )
        {
            if ( isset( $_POST['submit'] ) && ValidMail::checkCSRF() === true )
            {
                if ( $data['user_id'] == Vars::$USER_ID )
                {
                    if ( $data['elected_out'] == Vars::$USER_ID )
                    {
                        mysql_query( "UPDATE `cms_mail_messages` SET
						`elected_out`='0' WHERE `id`='" . Vars::$ID . "'" );
						
                    }
                }
                if ( $data['contact_id'] == Vars::$USER_ID )
                {
                    if ( $data['elected_in'] == Vars::$USER_ID )
                    {
                        mysql_query( "UPDATE `cms_mail_messages` SET
						`elected_in`='0' WHERE `id`='" . Vars::$ID . "'" );
                    }
                }
                if ( $data['user_id'] == Vars::$USER_ID )
                {
                    Header( 'Location: ' . Vars::$MODULE_URI . '?act=elected&id=' . $data['contact_id'] );
                    exit;
                } else
                {
                    Header( 'Location: ' . Vars::$MODULE_URI . '?act=elected&id=' . $data['user_id'] );
                    exit;
                }
            }
            $tpl->urlSelect = Vars::$MODULE_URI . '?act=delete&amp;id=' . Vars::$ID;
            $tpl->select = lng( 'confirm_removing' );
            $tpl->submit = lng( 'delete' );
            $tpl->phdr = lng( 'removing_message' );
            $tpl->urlBack = Vars::$MODULE_URI . '?act=elected';
        } else
        {
            if ( isset( $_POST['submit'] ) && ValidMail::checkCSRF() === true )
            {
                if ( $data['delete'] )
                {
                    if ( $data['filename'] )
                        @unlink( ROOTPATH . 'files/' . MAILDIR . '/' . $data['filename'] );
                    mysql_query( "DELETE FROM `cms_mail_messages` WHERE `id`='" . Vars::$ID . "'" );
                } else
                {
                    mysql_query( "UPDATE `cms_mail_messages` SET
					`delete`='" . Vars::$USER_ID . "' WHERE `id`='" . Vars::$ID . "'" );
                }
                if ( $data['user_id'] == Vars::$USER_ID )
                {
                    Header( 'Location: ' . Vars::$MODULE_URI . '?act=basket&id=' . $data['contact_id'] );
                    exit;
                } else
                {
                    Header( 'Location: ' . Vars::$MODULE_URI . '?act=basket&id=' . $data['user_id'] );
                    exit;
                }
            }
            $tpl->urlSelect = Vars::$MODULE_URI . '?act=delete&amp;id=' . Vars::$ID;
            $tpl->select = lng( 'confirm_removing' );
            $tpl->submit = lng( 'delete' );
            $tpl->phdr = lng( 'removing_message' );
            $tpl->urlBack = Vars::$MODULE_URI . '?act=basket';
        }
		$tpl->token = mt_rand(100, 10000);
		$_SESSION['token_status'] = $tpl->token;
		//Подключаем шаблон модуля select.php
        $tpl->contents = $tpl->includeTpl( 'select' );
    } else
    {
       //Сообщение об ошибке
	   $tpl->contents = '<div class="rmenu">' . lng( 'page_does_not_exist' ) . '</div>';
    }
} else
{
    $tpl->contents = Functions::displayError( lng( 'message_no_select' ), '<a href="' . Vars::
        $MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
}
