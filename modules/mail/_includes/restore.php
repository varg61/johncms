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
if ( Vars::$ID )
{
    $q = mysql_query( "SELECT * FROM `cms_messages` WHERE (`user_id`='" . Vars::$USER_ID . "' OR `contact_id`='" .
        Vars::$USER_ID . "') AND `id`='" . Vars::$ID . "' AND (`delete_in`='" . Vars::$USER_ID . "' OR `delete_out`='" .
        Vars::$USER_ID . "') AND `delete`!='" . Vars::$USER_ID . "'" );
    if ( mysql_num_rows( $q ) )
    {
        $data = mysql_fetch_assoc( $q );
        if ( isset( $_POST['submit'] ) )
        {
            if ( $data['user_id'] == Vars::$USER_ID )
            {
                if ( $data['delete_out'] == Vars::$USER_ID )
                {
                    mysql_query( "UPDATE `cms_messages` SET
					`delete_out`='0' WHERE `id`='" . Vars::$ID . "'" );
                    mysql_query( "UPDATE `cms_contacts` SET
					`count_out`=`count_out`+1, `delete`='0' WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='{$data['contact_id']}'" );
                }
            }
            if ( $data['contact_id'] == Vars::$USER_ID )
            {
                if ( $data['delete_in'] == Vars::$USER_ID )
                {
                    mysql_query( "UPDATE `cms_messages` SET
					`delete_in`='0' WHERE `id`='" . Vars::$ID . "'" );
                    mysql_query( "UPDATE `cms_contacts` SET
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
        $tpl->select = $lng_mail['confirm_restore'];
        $tpl->submit = $lng_mail['restore'];
        $tpl->phdr = $lng_mail['restore_message'];
        $tpl->urlBack = Vars::$MODULE_URI . '?act=basket';
        $tpl->contents = $tpl->includeTpl( 'mail/select' );
    } else
    {
        $tpl->contents = '<div class="rmenu">' . $lng_mail['page_does_not_exist'] . '</div>';
    }
} else
{
    $tpl->contents = Functions::displayError( $lng_mail['message_no_select'] . '!', '<a href="' . Vars::
        $MODULE_URI . '">' . Vars::$LNG['mail'] . '</a>' );
}
