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

$add_message['login'] = isset( $_POST['login'] ) ? trim( $_POST['login'] ) : '';
$add_message['text'] = isset( $_POST['text'] ) ? trim( $_POST['text'] ) : '';
$addmail = new ValidMail($add_message);
if($addmail->validateForm() === false) {
	//Передаем переменные в шаблон
	$tpl->login = Validate::filterString($add_message['login']);
	$tpl->text = Validate::filterString($add_message['text']);
	//Выводим на экран ошибку
	$tpl->mail_error = Functions::displayError( $addmail->error_log );
}
$tpl->maxsize = 1024 * Vars::$SYSTEM_SET['flsz'];
$tpl->size = Vars::$SYSTEM_SET['flsz'];
$tpl->token = mt_rand(100, 10000);
$_SESSION['token_status'] = $tpl->token;
$tpl->contents = $tpl->includeTpl( 'add' );