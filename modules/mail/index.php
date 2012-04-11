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
define ('_IN_JOHNCMS_MAIL', 1);

//Закрываем доступ гостям
if ( !Vars::$USER_ID )
{
	Header( 'Location: ' . Vars::$HOME_URL . '/404' );
    exit;
}

define( 'MAILDIR', 'mail' ); //Папка с модулем
define( 'MAILPATH', MODPATH . MAILDIR . DIRECTORY_SEPARATOR );//Абсолютный путь до модуля

if ( isset( $_SESSION['ref'] ) )
    unset( $_SESSION['ref'] );

//Подключаем шаблонизатор
$tpl = Template::getInstance();

//Проверяем и подключаем нужные файлы модуля
$connect = Mail::mailConnect();
if ( Vars::$ACT && ( $key = array_search( Vars::$ACT, $connect ) ) !== false && file_exists( MAILPATH .
    '_inc' . DIRECTORY_SEPARATOR . $connect[$key] . '.php' ) )
{
    require ( MAILPATH . '_inc' . DIRECTORY_SEPARATOR . $connect[$key] . '.php' );
} else
{
    $new = Functions::mailCount('new');
	//Заголовок
	$tpl->title = lng( 'mail' );
	
	if(($settings = Vars::getUserData('settings_mail')) === false) 
		$settings['access'] = 0;
	
	$arr = array(lng('all'), lng('contact_friends'), lng('only_friends'));
	
	$tpl->receive_mail = $arr[$settings['access']]; //Информер "От кого принимать почту"
	
    $tpl->systems = Mail::counter( 'systems' );   //Счетчик системных сообщений
    $tpl->elected = Mail::counter( 'elected' );   //Счетчик избранных

    $tpl->delete  = Mail::counter( 'delete' );    //Счетчик удаленных
	
    $tpl->inmess  = Mail::counter( 'inmess' );    //Счетчик удаленных
	$tpl->outmess = Mail::counter( 'outmess' );   //Счетчик удаленных
	
	$tpl->newmess = $new ? '+' . $new : '';       //Счетчик новых
	
    $tpl->files   = Mail::counter( 'files' );     //Счетчик файлов
    
	//Подключаем шаблон модуля
    $tpl->contents = $tpl->includeTpl( '_index' );
}