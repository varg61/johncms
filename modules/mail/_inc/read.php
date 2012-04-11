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

if(Vars::$ID) {
	$result = mysql_fetch_assoc(mysql_query("SELECT * FROM `cms_mail_messages` WHERE `id`='" . Vars::$ID . "'"));
	if($result) {
		if($result['delete_in'] != Vars::$USER_ID && $result['delete_out'] != Vars::$USER_ID) {
			if($result['user_id'] == Vars::$USER_ID) {
				$id = $result['contact_id'];
				$tpl->pref = lng('pref_out');
				$tpl->back = 'outmess';
			} else {
				$id = $result['user_id'];
				$tpl->pref = lng('pref_in');
				$tpl->back = 'inmess';
			}
			$row = mysql_fetch_assoc(mysql_query("SELECT * FROM `users` WHERE `id`='$id'"));
			$text = Validate::filterString( $result['text'], 1, 1 );
			if ( Vars::$USER_SET['smileys'] )
				$text = Functions::smileys( $text, $result['rights'] >= 1 ? 1 : 0 );
			$tpl->contact_login = $row['nickname'];
			$tpl->user_id = $id;
			$tpl->text = $text;
			$tpl->time_message = Functions::displayDate( $result['time'] );
			//Подключаем шаблон read.php
			$tpl->contents = $tpl->includeTpl( 'read' );
		} else {
			$tpl->contents = Functions::displayError( lng( 'page_does_not_exist' ), '<a href="' . Vars::
			$MODULE_URI . '?act=contacts">' . lng( 'contacts' ) . '</a><br />
			<a href="' . Vars::$MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
		}
	} else {
		$tpl->contents = Functions::displayError( lng( 'page_does_not_exist' ), '<a href="' . Vars::
		$MODULE_URI . '?act=contacts">' . lng( 'contacts' ) . '</a><br />
		<a href="' . Vars::$MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
	}
} else {
	$tpl->contents = Functions::displayError( lng( 'message_no_select' ), '<a href="' . Vars::
	$MODULE_URI . '?act=contacts">' . lng( 'contacts' ) . '</a><br />
	<a href="' . Vars::$MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
}