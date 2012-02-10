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
    $req = mysql_query( "SELECT * FROM `cms_messages` WHERE (`user_id`='" . Vars::$USER_ID . "' OR `contact_id`='" .
        Vars::$USER_ID . "') AND `id` = '" . Vars::$ID . "' AND `filename` != '' AND `delete`!='" . Vars::
        $USER_ID . "' LIMIT 1" );
    if ( mysql_num_rows( $req ) == 0 )
    {
        //Выводим ошибку
        $tpl->contents = Functions::displayError( $lng_mail['file_does_not_exist'], '<a href="' . Vars::
            $MODULE_URI . '">' . Vars::$LNG['contacts'] . '</a>' );
    }
    $res = mysql_fetch_assoc( $req );
    if ( file_exists( ROOTPATH . 'files/' . MAILDIR . '/' . $res['filename'] ) )
    {
        if ( empty( $_SESSION['file_' . Vars::$ID] ) )
        {
            mysql_query( "UPDATE `cms_messages` SET `filecount` = `filecount`+1 WHERE `id` = '" . Vars::
                $ID . "' AND `user_id`!='" . Vars::$USER_ID . "' LIMIT 1" );
            $_SESSION['file_' . Vars::$ID] = 1;
        }
        Header( 'Location: ../files/' . MAILDIR . '/' . $res['filename'] );
        exit;
    } else
    {
        $tpl->contents = Functions::displayError( $lng_mail['file_does_not_exist'], '<a href="' . Vars::
            $MODULE_URI . '">' . Vars::$LNG['contacts'] . '</a>' );
    }
} else
{
    $tpl->contents = Functions::displayError( $lng_mail['file_does_not_exist'], '<a href="' . Vars::
        $MODULE_URI . '">' . Vars::$LNG['contacts'] . '</a>' );
}
