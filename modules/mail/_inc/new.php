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

$total = mysql_result( mysql_query( "SELECT COUNT(*) FROM (SELECT DISTINCT `user_id` FROM `cms_messages` WHERE `contact_id`='" .
    Vars::$USER_ID . "' AND `cms_messages`.`read`='0' AND `cms_messages`.`sys`='0' AND `cms_messages`.`delete_in`!='" .
    Vars::$USER_ID . "' AND `cms_messages`.`delete_out`!='" . Vars::$USER_ID . "') a;" ), 0 );
if ( $total == 1 )
{
    //Если все новые сообщения от одного итого же чела показываем сразу переписку
    $max = mysql_result( mysql_query( "SELECT `user_id`, count(*) FROM `cms_messages` WHERE `contact_id`='" .
        Vars::$USER_ID . "' AND `read`='0' AND `sys`='0' GROUP BY `user_id`;" ), 0 );
    Header( 'Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $max );
    exit();
}
if ( $total )
{
    if ( isset( $_POST['addnew'] ) )
    {
        if ( !empty( $_POST['delch'] ) && is_array( $_POST['delch'] ) )
        {
            Mail::mailSelectContacts( $_POST['delch'], 'read' );
        }
        Header( 'Location: ' . Vars::$MODULE_URI . '?act=new' );
        exit;
    }

    $query = mysql_query( "SELECT * FROM `cms_messages`
	LEFT JOIN `cms_contacts` ON `cms_messages`.`user_id`=`cms_contacts`.`user_id`
	LEFT JOIN `users` ON `cms_contacts`.`user_id`=`users`.`id`
	WHERE `cms_messages`.`contact_id`='" . Vars::$USER_ID . "'
	AND `cms_messages`.`read`='0'
	AND `cms_messages`.`delete_in`!='" . Vars::$USER_ID . "'
	AND `cms_messages`.`delete_out`!='" . Vars::$USER_ID . "'
	GROUP BY `cms_messages`.`user_id`
	ORDER BY `cms_contacts`.`time` DESC" . Vars::db_pagination() );
    $array = array();
    $i = 1;
    while ( $row = mysql_fetch_assoc( $query ) )
    {
        $array[] = array(
            'id' => $row['id'],
            'icon' => Functions::getImage( 'usr_' . ( $row['sex'] == 'm' ? 'm' : 'w' ) . '.png', '' ),
            'list' => ( ( $i % 2 ) ? 'list1' : 'list2' ),
            'nickname' => $row['nickname'],
            'count_in' => $row['count_out'],
            'count_out' => $row['count_in'],
            'count_new' => Mail::countNew( $row['id'] ),
            'url' => ( Vars::$MODULE_URI . '?act=messages&amp;id=' . $row['id'] ),
            'online' => ( time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>' ) );
        ++$i;
    }
    $tpl->total = $total;
    $tpl->display_pagination = Functions::displayPagination( Vars::$MODULE_URI . '?act=new&amp;', Vars::
        $START, $total, Vars::$USER_SET['page_size'] );
    $tpl->query = $array;
    $tpl->contacts = $tpl->includeTpl( 'contacts' );
} else
{
    $tpl->contacts = '<div class="rmenu">' . lng('no_messages') . '!</div>';
}
$tpl->contents = $tpl->includeTpl( 'new' );
