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
$tpl->title = Vars::$LNG['mail'] . ' | ' . Vars::$LNG['files'];
$total = mysql_result( mysql_query( "SELECT COUNT(*) FROM `cms_messages` WHERE `filename`!='' AND (`user_id`='" .
    Vars::$USER_ID . "' OR `contact_id`='" . Vars::$USER_ID . "') AND `delete_in`!='" . Vars::$USER_ID .
    "' AND `delete_out`!='" . Vars::$USER_ID . "' AND `delete`!='" . Vars::$USER_ID . "'" ), 0 );
if ( $total )
{
    $array = array();
    $query = mysql_query( "SELECT * FROM `cms_messages` WHERE `filename`!='' AND (`user_id`='" . Vars::
        $USER_ID . "' OR `contact_id`='" . Vars::$USER_ID . "') AND `delete_in`!='" . Vars::$USER_ID .
        "' AND `delete_out`!='" . Vars::$USER_ID . "' AND `delete`!='" . Vars::$USER_ID . "' ORDER BY `time` DESC" .
        Vars::db_pagination() );
    $i = 1;
    while ( $row = mysql_fetch_assoc( $query ) )
    {
        $array[] = array(
            'id' => $row['id'],
            'list' => ( ( $i % 2 ) ? 'list1' : 'list2' ),
            'filename' => $row['filename'],
            'icon' => Functions::getImage( Mail::fileicon( $row['filename'] ), '', 'style="margin: 0 0 -3px 0;"' ),
            'filesize' => Mail::formatsize( $row['filesize'] ),
            'filecount' => $row['filecount'] );
        ++$i;
    }
    $tpl->display_pagination = Functions::displayPagination( Vars::$MODULE_URI . '?act=files&amp;', Vars::
        $START, $total, Vars::$USER_SET['page_size'] );
    $tpl->query = $array;
    $tpl->total = $total;
}
$tpl->contents = $tpl->includeTpl( 'files' );
