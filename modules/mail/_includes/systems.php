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
if ( Vars::$MOD == 'delete' )
{
    $total = mysql_result( mysql_query( "SELECT COUNT(*) FROM `cms_messages` WHERE `id`='" . Vars::$ID .
        "' AND `contact_id`='" . Vars::$USER_ID . "' AND `sys`='1'" ), 0 );
    if ( $total )
    {
        if ( isset( $_POST['submit'] ) )
        {
            mysql_query( "DELETE FROM `cms_messages` WHERE `id`='" . Vars::$ID . "' AND `contact_id`='" .
                Vars::$USER_ID . "' AND `sys`='1'" );
            Header( 'Location: ' . Vars::$MODULE_URI . '?act=systems' );
            exit;
        } else
        {
            $tpl->select = $lng_mail['confirm_removing'];
            $tpl->submit = Vars::$LNG['delete'];
            $tpl->phdr = $lng_mail['removing_message'];
            $tpl->urlBack = Vars::$MODULE_URI . '?act=systems';
            $tpl->contents = $tpl->includeTpl( 'mail/select' );
        }
    } else
    {
        $tpl->contents = Functions::displayError( $lng_mail['page_does_not_exist'] . '!', '<a href="' .
            Vars::$MODULE_URI . '">' . Vars::$LNG['mail'] . '</a>' );
    }
} else
    if ( Vars::$MOD == 'clear' )
    {
        if ( isset( $_POST['submit'] ) )
        {
            mysql_query( "DELETE FROM `cms_messages` WHERE `contact_id`='" . Vars::$USER_ID . "' AND `sys`='1'" );
            Header( 'Location: ' . Vars::$MODULE_URI . '?act=systems' );
            exit;
        } else
        {
            $tpl->select = $lng_mail['confirm_clear_systems'];
            $tpl->submit = Vars::$LNG['clear'];
            $tpl->phdr = $lng_mail['clear_systems'];
            $tpl->urlBack = Vars::$MODULE_URI . '?act=systems';
            $tpl->contents = $tpl->includeTpl( 'mail/select' );
        }
    } else
    {
        $total = mysql_result( mysql_query( "SELECT COUNT(*) 
	FROM `cms_messages` 
	WHERE `contact_id`='" . Vars::$USER_ID . "'
	AND `sys`='1'" ), 0 );
        $tpl->total = $total;
        if ( $total )
        {
            $query = mysql_query( "SELECT * 
		FROM `cms_messages` 
		WHERE `contact_id`='" . Vars::$USER_ID . "'
		AND `sys`='1'
		ORDER BY `time` DESC" . Vars::db_pagination() );
            $array = array();
            $i = 1;
            while ( $row = mysql_fetch_assoc( $query ) )
            {
                $text = Validate::filterString( $row['text'], 1, 1 );
                if ( Vars::$USER_SET['smileys'] )
                    $text = Functions::smileys( $text );
                $array[] = array(
                    'id' => $row['id'],
                    'list' => ( $i % 2 ? 'list1' : 'list2' ),
                    'theme' => Validate::filterString( $row['theme'] ),
                    'text' => $text,
                    'time' => Functions::displayDate( $row['time'] ) );
                ++$i;
            }
            $tpl->query = $array;
            $tpl->display_pagination = Functions::displayPagination( Vars::$MODULE_URI . '?act=systems&amp;',
                Vars::$START, $total, Vars::$USER_SET['page_size'] );
        } else
        {
            $tpl->error = '<div class="rmenu">' . $lng_mail['no_messages'] . '</div>';
        }
        $tpl->contents = $tpl->includeTpl( 'mail/systems' );
    }
