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
//Заголовок
$tpl->title = lng( 'mail' ) . ' | ' . lng( 'write_message' );
if(Vars::$ID) {
	$q = mysql_query("SELECT * FROM `cms_mail_messages` WHERE (`user_id`='" . Vars::$USER_ID . "' OR `contact_id`='" . Vars::$USER_ID . "') AND `id`='" . Vars::$ID . "'");
	if($num = mysql_num_rows($q)) {
		$result = mysql_fetch_assoc($q);
		
		$id = isset( $_POST['contact_id'] ) ? trim( $_POST['contact_id'] ) : '';
		$add_message['login'] = isset( $_POST['login'] ) ? trim( $_POST['login'] ) : '';
		$add_message['text'] = isset( $_POST['text'] ) ? trim( $_POST['text'] ) : '';
		$addmail = new ValidMail($add_message, ($id ? $id: false));
		
		$tpl->text = Validate::checkout($result['text']);
		if($addmail->validateForm() === false) {
			if($error = $addmail->error_log) {
				//Передаем переменные в шаблон
				$tpl->login = Validate::checkout($add_message['login']);
				$tpl->text = Validate::checkout($add_message['text']);
				//Выводим на экран ошибку
				$tpl->mail_error = Functions::displayError( $error );
			}
		}
		
		$tpl->count_contact = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_contacts` WHERE `user_id`='" . Vars::$USER_ID . "' AND `delete`!='1' AND `archive`!='1' AND `banned`!='1'"), 0);
		if($tpl->count_contact) {
			$query = mysql_query("SELECT `users`.`id`, `users`.`nickname` FROM `cms_mail_contacts` LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id` WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "' AND `cms_mail_contacts`.`delete`!='1' AND `cms_mail_contacts`.`archive`!='1' AND `cms_mail_contacts`.`banned`!='1' ORDER BY `cms_mail_contacts`.`time` DESC LIMIT 0,20");
			$array = array();
			while($row = mysql_fetch_assoc($query)) {
				$array[] = array(
				'id' => $row['id'],
				'nickname' => $row['nickname']);
			}
			$tpl->query = $array;
		}
		
		$tpl->url = Vars::$MODULE_URI . '?act=send&amp;id=' . Vars::$ID;
		$tpl->maxsize = 1024 * Vars::$SYSTEM_SET['filesize'];
		$tpl->size = Vars::$SYSTEM_SET['filesize'];
		$tpl->token = mt_rand(100, 10000);
		$_SESSION['token_status'] = $tpl->token;
		$tpl->contents = $tpl->includeTpl( 'add' );
	} else {
		$tpl->contacts = Functions::displayError( lng('page_does_not_exist'), '<a href="' . Vars::$MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
	}
} else {
	$tpl->contacts = Functions::displayError( lng('message_no_select'), '<a href="' . Vars::$MODULE_URI . '">' . lng( 'mail' ) . '</a>' );
}