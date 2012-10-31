<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */
//TODO: Доработать английский язык

defined( '_IN_JOHNCMS' ) or die( 'Error: restricted access' );
define ('_IN_JOHNCMS_MAIL', 1);

//Закрываем доступ гостям
if ( !Vars::$USER_ID ) {
	Header( 'Location: ' . Vars::$HOME_URL . '/404' );
    exit;
}
define( 'MAILDIR', 'mail' ); //Папка с модулем
define( 'MAILPATH', MODPATH . MAILDIR . DIRECTORY_SEPARATOR );//Абсолютный путь до модуля

//Подключаем шаблонизатор
$tpl = Template::getInstance();

//Проверяем и подключаем нужные файлы модуля
$connect = array(
'add',
'basket',
'delete',
'edit',
'elected',
'files',
'inmess',
'load',
'messages',
'new',
'outmess',
'read',
'restore',
'send',
'settings' );
if ( Vars::$ACT && ( $key = array_search( Vars::$ACT, $connect ) ) !== false && file_exists( MAILPATH .
    '_inc' . DIRECTORY_SEPARATOR . $connect[$key] . '.php' ) ) {
    require ( MAILPATH . '_inc' . DIRECTORY_SEPARATOR . $connect[$key] . '.php' );
} else {
    $tpl->newmess = Functions::mailCount('new'); //Счетчик 
	$tpl->title = lng( 'mail' );
    $tpl->contents = $tpl->includeTpl( '_index' );
}