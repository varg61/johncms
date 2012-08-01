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
//Закрываем доступ кроме администратора
if ( !Vars::$USER_ID && (Vars::$USER_RIGHTS != 9))
{
    Header( 'Location: ' . Vars::$HOME_URL . '/404' );
    exit;
}
//Заголовок
$tpl->title = lng( 'mail' ) . ' | ' . lng( 'sending' );

$tpl->error = true;
$tpl->post = false;
if(isset($_POST['submit']) && Validmail::checkCSRF() === TRUE) {
	$text = isset($_POST['text']) ? trim($_POST['text']) : '';
	$select = isset($_POST['sending']) && $_POST['sending'] >=0 && $_POST['sending'] <= 3 ? intval($_POST['sending']) : 0;
	$error = array();
	if(empty($text)) {
		$error[] = lng('empty_message');
	} elseif(mb_strlen($text) < 2) {
		$error[] = lng('error_message');
	}
	
	if(empty($error)) {
		$tpl->error = null;
		$tpl->post = true;
	} else {
		$tpl->text = Validate::filterString($text);
		$tpl->mail_error = Functions::displayError($error);
	}
}
$tpl->url = Vars::$MODULE_URI . '?act=sending_out';


$tpl->token = mt_rand(100, 10000);
$_SESSION['token_status'] = $tpl->token;
//Подключаем шаблон модуля sending_out.php
$tpl->contents = $tpl->includeTpl( 'sending_out' );