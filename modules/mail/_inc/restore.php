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
    //Восстанавливаем сообщения
	$q = mysql_query( "SELECT * FROM `cms_mail_messages` WHERE (`user_id`='" . Vars::$USER_ID . "' OR `contact_id`='" .
        Vars::$USER_ID . "') AND `id`='" . Vars::$ID . "' AND (`delete_in`='" . Vars::$USER_ID . "' OR `delete_out`='" .
        Vars::$USER_ID . "') AND `delete`!='" . Vars::$USER_ID . "'" );
    if ( mysql_num_rows( $q ) )
    {
        $data = mysql_fetch_assoc( $q );
        if ( isset( $_POST['submit'] ) && ValidMail::checkCSRF() === true )
        {
            if ( $data['user_id'] == Vars::$USER_ID )
            {
                if ( $data['delete_out'] == Vars::$USER_ID )
                {
                    mysql_query( "UPDATE `cms_mail_messages` SET
					`delete_out`='0' WHERE `id`='" . Vars::$ID . "'" );
                    mysql_query( "UPDATE `cms_mail_contacts` SET
					`count_out`=`count_out`+1, `delete`='0' WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='{$data['contact_id']}'" );
                }
            }
            if ( $data['contact_id'] == Vars::$USER_ID )
            {
                if ( $data['delete_in'] == Vars::$USER_ID )
                {
                    mysql_query( "UPDATE `cms_mail_messages` SET
					`delete_in`='0' WHERE `id`='" . Vars::$ID . "'" );
                    mysql_query( "UPDATE `cms_mail_contacts` SET
					`count_in`=`count_in`+1, `delete`='0' WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='{$data['user_id']}'" );
                }
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
        $tpl->urlSelect = Vars::$MODULE_URI . '?act=restore&amp;id=' . Vars::$ID;
        $tpl->select = lng( 'confirm_restore' );
        $tpl->submit = lng( 'restore' );
        $tpl->phdr = lng( 'restore_message' );
        $tpl->urlBack = Vars::$MODULE_URI . '?act=basket';
        $tpl->token = mt_rand(100, 10000);
		$_SESSION['token_status'] = $tpl->token;
		$tpl->contents = $tpl->includeTpl( 'select' );
    } else
    {
        //Если собщение не существует, информируем об этом)
		$tpl->contents = '<div class="rmenu">' . lng( 'page_does_not_exist' ) . '</div>';
    }
} else
{
    //Сообщяем об ошибке, если сообщение не выбрано.
	$tpl->contents = Functions::displayError( lng( 'message_no_select' ), '<a href="' . Vars::
        $MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
}
