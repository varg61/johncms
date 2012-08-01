<?php
/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */
//TODO: Доработать под новую систему оповещений
defined('_IN_JOHNCMS') or die('Error: restricted access');
define('_IN_JOHNCMS_CONTACTS', 1);

//Закрываем доступ гостям
if ( !Vars::$USER_ID )
{
    Header( 'Location: ' . Vars::$HOME_URL . '/404' );
    exit;
}

define('CONTACTSDIR', 'contacts'); //Папка с модулем
define('CONTACTSPATH', MODPATH . CONTACTSDIR . DIRECTORY_SEPARATOR); //Абсолютный путь до модуля
//Подключаем шаблонизатор
$tpl = Template::getInstance();

function countNew( $id = null )
{
	if ( $id == null )
		return false;
	$new = mysql_result( mysql_query( "SELECT COUNT(*) 
		FROM `cms_mail_messages` 
		LEFT JOIN `cms_mail_contacts` 
		ON `cms_mail_messages`.`user_id`=`cms_mail_contacts`.`contact_id` 
		AND `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "' 
		WHERE `cms_mail_messages`.`user_id`='" . $id . "' 
		AND `cms_mail_messages`.`contact_id`='" . Vars::$USER_ID . "' 
		AND `cms_mail_messages`.`read`='0' 
		AND `cms_mail_messages`.`delete_in`!='" . Vars::$USER_ID . "' 
		AND `cms_mail_messages`.`delete_out`!='" . Vars::$USER_ID . "'
		AND `cms_mail_messages`.`delete`!='" . Vars::$USER_ID . "' 
		AND `cms_mail_contacts`.`banned`!='1'" ), 0 );
	if ( $new )
		return '+' . $new;
	else
		return false;
}
//Проверяем и подключаем нужные файлы модуля
$connect = array(
'archive',
'banned',
'select',
'search'
);
if ( Vars::$ACT && ( $key = array_search( Vars::$ACT, $connect ) ) !== false && file_exists( CONTACTSPATH .
    '_inc' . DIRECTORY_SEPARATOR . $connect[$key] . '.php' ) )
{
    require ( CONTACTSPATH . '_inc' . DIRECTORY_SEPARATOR . $connect[$key] . '.php' );
} else
{
	//Заголовок
	$tpl->title = lng('contacts');
	//Считаем количество контактов
	$total = Functions::contactsCount();
	$tpl->total = $total;
	if ($total) {
		//Перемещаем контакты и сообщения в корзину
		if (isset($_POST['delete']) && isset($_POST['token']) && isset($_SESSION['token_status']) &&
			$_POST['token'] == $_SESSION['token_status']) {
			if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
				$id = array_map('intval', $_POST['delch']);
				$id = implode(',', $id);
				if (!empty($id)) {
					mysql_query("UPDATE `cms_mail_messages` SET 
					`delete_in`='" . Vars::$USER_ID . "'
					WHERE `user_id` IN (" . $id . ") AND `contact_id`='" . Vars::$USER_ID . "'");
					mysql_query("UPDATE `cms_mail_messages` SET 
					`delete_out`='" . Vars::$USER_ID . "'
					WHERE `contact_id` IN (" . $id . ") AND `user_id`='" . Vars::$USER_ID . "'");
					mysql_query("UPDATE `cms_mail_contacts` SET 
					`delete`='1', 
					`archive`='0'
					WHERE `user_id`='" . Vars::$USER_ID . "' 
					AND `contact_id` IN (" . $id . ")");
				}
			}
			Header('Location: ' . Vars::$MODULE_URI);
			exit;
		} 
		//Перемещаем контакты в архив
		if (isset($_POST['archive']) && isset($_POST['token']) && isset($_SESSION['token_status']) &&
			$_POST['token'] == $_SESSION['token_status']) {
			if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
				$id = array_map('intval', $_POST['delch']);
				$id = implode(',', $id);
				if (!empty($id)) {
					mysql_query("UPDATE `cms_mail_contacts` 
					SET	`archive`='1' 
					WHERE `user_id`='" . Vars::$USER_ID . "' 
					AND `contact_id` IN (" . $id . ") AND `archive`='0'");
				}
			}
			Header('Location: ' . Vars::$MODULE_URI);
			exit;
		}
		//Формируем список контактов
        $query = mysql_query("SELECT `users`.`id`, `users`.`nickname`,  `users`.`sex`,  `users`.`last_visit`, `cms_mail_contacts`.`access`, `cms_mail_contacts`.`friends`, `contacts`.`access` as `acc`, `contacts`.`friends` as `fri`
		FROM `cms_mail_contacts`
		LEFT JOIN `users`
		ON `cms_mail_contacts`.`contact_id`=`users`.`id`
		LEFT JOIN `cms_mail_contacts` as `contacts`
		ON `contacts`.`user_id`=`users`.`id`
		WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "' 
		AND `cms_mail_contacts`.`delete`='0' 
		AND `cms_mail_contacts`.`banned`='0'
		AND `cms_mail_contacts`.`archive`='0'
		GROUP BY `cms_mail_contacts`.`contact_id`
		ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination() );
		$i = 0;
		while($row = mysql_fetch_assoc($query)) {
			if($row['access'] == 2 && $row['friends'] == 1) {
				$icon = Functions::getIcon( 'user' . ( $row['sex'] == 'm' ? '-friends' : '-friends-female' ) . '.png', '', '', 'style="margin: 0 0 -3px 0;"' );
			} else {
				$icon = Functions::getIcon( 'user' . ( $row['sex'] == 'm' ? '' : '-female' ) . '.png', '', '', 'style="margin: 0 0 -3px 0;"' );
			}
			
			if($row['access'] == 2 && $row['friends'] == 1) {
				$access = 1; //Друзья
			} else if($row['access'] == 2 && $row['friends'] == 0) {
				$access = 2; //Отменить заявку
			} else if($row['acc'] == 2 && $row['fri'] == 0) {
				$access = 3; //Подтвердить заявку
			} else {
				$access = 0;
			}
			
			$array[] = array(
			'id' => $row['id'],
			'icon' => $icon,
			'access' => $access,
			'list' => ( ( $i % 2 ) ? 'list1' : 'list2' ),
			'nickname' => $row['nickname'],
			'count' => mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_messages` WHERE ((`user_id`=" . Vars::$USER_ID . " AND `contact_id`=" . $row['id'] . ") OR (`contact_id`=" . Vars::$USER_ID . " AND `user_id`=" . $row['id'] . ")) AND `delete_in`!=" . Vars::$USER_ID . " AND `delete_out`!=" . Vars::$USER_ID . ""), 0),
			'count_new' => countNew( $row['id'] ),
			'url' => ( Vars::$HOME_URL . '/mail?act=messages&amp;id=' . $row['id'] ),
			'online' => ( time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
			'<span class="green"> [ON]</span>' ) );
			++$i;
		}
		//Навигация
		$tpl->display_pagination = Functions::displayPagination(Vars::$MODULE_URI . '?',
			Vars::$START, $total, Vars::$USER_SET['page_size']);
		$tpl->query = $array;
	}

	$tpl->token = mt_rand(100, 10000);
	$_SESSION['token_status'] = $tpl->token;
	
	$tpl->archive = mysql_result(mysql_query("SELECT COUNT(*)
	FROM `cms_mail_contacts`
	WHERE `user_id`='" . Vars::$USER_ID .
	"' AND `delete`='0' AND `banned`='0' AND `archive`='1'"), 0); //Счетчик архива
	
	$tpl->banned = mysql_result( mysql_query( "SELECT COUNT(*) 
	FROM `cms_mail_contacts` 
	WHERE `banned`='1' 
	AND `user_id`='" . Vars::$USER_ID . "' 
	AND `cms_mail_contacts`.`delete`='0'" ), 0 ); //Счетчик заблокированных
	//Подключаем шаблон модуля archive.php
	$tpl->contents = $tpl->includeTpl('index');
}