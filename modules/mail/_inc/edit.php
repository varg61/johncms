<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
//Закрываем прямой доступ к файлу
defined('_IN_JOHNCMS_MAIL') or die('Error: restricted access');
//Закрываем доступ гостям
if (!Vars::$USER_ID) {
    Header('Location: ' . Vars::$HOME_URL . '/404');
    exit;
}
if (Vars::$ID) {
	$result = mysql_query("SELECT * FROM `cms_mail_messages` WHERE `id` = " . Vars::$ID . " AND `user_id` = " . Vars::$USER_ID);
	if(mysql_num_rows($result)) {
		$req = mysql_fetch_assoc($result);
		if($req['delete_out'] != 1 && $req['delete'] != Vars::$USER_ID) {
			if($req['read'] == 0) {
				$tpl->text = htmlentities($req['text'], ENT_QUOTES, 'UTF-8');
				if($_POST['submit'] && ValidMail::checkCSRF() === TRUE) {
					$text = isset( $_POST['text'] ) ? trim( $_POST['text'] ) : '';
					$error = array();
					if(empty($text))
						$error[] = __('empty_message');
					elseif(mb_strlen($text) < 2) 
						$error[] = __('error_message');
					if (($flood = Functions::antiFlood()) !== FALSE)
						$error = __('error_flood') . ' ' . $flood . '&#160;' . __('seconds');
					if(empty($error)) {
						//Отправляем сообщение
						mysql_query("UPDATE `cms_mail_messages` SET
						`text`='" . mysql_real_escape_string($text) . "'
						WHERE `id` = " . Vars::$ID . " AND `user_id` = " . Vars::$USER_ID) or die(mysql_error());
						mysql_query("UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = " . Vars::$USER_ID);
						Header('Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $req['contact_id']);
						exit;
					} else {
						$tpl->mail_error = Functions::displayError( $error );
						$tpl->text = htmlentities($text, ENT_QUOTES, 'UTF-8');
					}
				}
				$tpl->url = Vars::$MODULE_URI . '?act=edit&amp;id=' . Vars::$ID;
				$tpl->token = mt_rand(100, 10000);
				$_SESSION['token_status'] = $tpl->token;
				$tpl->contents = $tpl->includeTpl( 'edit' );
			} else {
				$tpl->contents = Functions::displayError(__( 'message_ready_read' ), '<a href="' . Vars::$MODULE_URI . '">' . __('mail') . '</a>');
			}
		} else {
			$tpl->contents = Functions::displayError(__( 'page_does_not_exist' ), '<a href="' . Vars::$MODULE_URI . '">' . __('mail') . '</a>');
		}
	} else {
		$tpl->contents = Functions::displayError(__( 'page_does_not_exist' ), '<a href="' . Vars::$MODULE_URI . '">' . __('mail') . '</a>');
	}
} else {
    $tpl->contents = Functions::displayError(__('message_no_select'), '<a href="' . Vars::
    $MODULE_URI . '">' . __('mail') . '</a>');
}