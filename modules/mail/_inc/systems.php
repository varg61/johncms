<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined ( '_IN_JOHNCMS' ) or die( 'Error: restricted access' );
//Закрываем прямой доступ к файлу
defined ( '_IN_JOHNCMS_MAIL' ) or die( 'Error: restricted access' );
//Закрываем доступ гостям
if ( !Vars::$USER_ID )
{
    Header( 'Location: ' . Vars::$HOME_URL . '/404' );
    exit;
}
//Заголовок
$tpl->title = lng( 'mail' ) . ' | ' . lng( 'system' );
//Удаляем системное сообщение
if ( Vars::$MOD == 'delete' )
{
    $total = mysql_result( mysql_query( "SELECT COUNT(*) FROM `cms_mail_messages` WHERE `id`='" . Vars::$ID .
        "' AND `contact_id`='" . Vars::$USER_ID . "' AND `sys`='1'" ), 0 );
    if ( $total )
    {
        if ( isset( $_POST['submit'] ) && ValidMail::checkCSRF() === true )
        {
            mysql_query( "DELETE FROM `cms_mail_messages` WHERE `id`='" . Vars::$ID . "' AND `contact_id`='" .
                Vars::$USER_ID . "' AND `sys`='1'" );
            Header( 'Location: ' . Vars::$MODULE_URI . '?act=systems' );
            exit;
        } else
        {
            $tpl->select = lng( 'confirm_removing' );
            $tpl->submit = lng( 'delete' );
            $tpl->phdr = lng( 'removing_message' );
            $tpl->urlBack = Vars::$MODULE_URI . '?act=systems';
            $tpl->token = mt_rand(100, 10000);
			$_SESSION['token_status'] = $tpl->token;
			$tpl->contents = $tpl->includeTpl( 'select' );
        }
    } else
    {
        $tpl->contents = Functions::displayError( lng( 'page_does_not_exist' ), '<a href="' .
            Vars::$MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
    }
} else
    //Очищаем системные сообщения
	if ( Vars::$MOD == 'clear' )
    {
        if ( isset( $_POST['submit'] ) && ValidMail::checkCSRF() === true )
        {
            mysql_query( "DELETE FROM `cms_mail_messages` WHERE `contact_id`='" . Vars::$USER_ID . "' AND `sys`='1'" );
            Header( 'Location: ' . Vars::$MODULE_URI . '?act=systems' );
            exit;
        } else
        {
            $tpl->select = lng( 'confirm_clear_systems' );
            $tpl->submit = lng( 'clear' );
            $tpl->phdr = lng( 'clear_systems' );
            $tpl->urlBack = Vars::$MODULE_URI . '?act=systems';
			$tpl->token = mt_rand(100, 10000);
			$_SESSION['token_status'] = $tpl->token;
            $tpl->contents = $tpl->includeTpl( 'select' );
        }
    } else
    {
        //Считаем количество системных сообщений
		$total = mysql_result( mysql_query( "SELECT COUNT(*) 
		FROM `cms_mail_messages` 
		WHERE `contact_id`='" . Vars::$USER_ID . "'
		AND `sys`='1'" ), 0 );
        $tpl->total = $total;
        if ( $total )
        {
            //Формируем список системных сообщений
			$query = mysql_query( "SELECT * 
			FROM `cms_mail_messages` 
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
            //Навигация
			$tpl->display_pagination = Functions::displayPagination( Vars::$MODULE_URI . '?act=systems&amp;',
                Vars::$START, $total, Vars::$USER_SET['page_size'] );
        } else
        {
            //Сообщений нет
			$tpl->error = '<div class="rmenu">' . lng( 'no_messages' ) . '</div>';
        }
		//Подключаем шаблон модуля systems.php
        $tpl->contents = $tpl->includeTpl( 'systems' );
    }
