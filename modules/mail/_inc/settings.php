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
    Header( 'Location: ' . Vars::$HOME_URL . '404' );
    exit;
}
//Заголовок
$tpl->title = __( 'mail' ) . ' | ' . __( 'settings' );
if(($settings = Vars::getUserData('settings_mail')) === false) 
	$settings['access'] = 0;

$tpl->save = '';
if(isset($_POST['submit'])) 
{
	$settings['access'] = isset($_POST['access']) && $_POST['access'] >= 0 && $_POST['access'] <= 3 ? intval($_POST['access']) : 0;
	Vars::setUserData('settings_mail', $settings);
	$tpl->save = '<div class="rmenu">Настройки сохранены</div>';
}
$tpl->access = $settings['access'];
$tpl->contents = $tpl->includeTpl( 'settings' );