<?php
/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */
 
//Применение функции закрытия анкет, фотоальбомов и прочего
/*
//Простой способ
if(Functions:checkFriend (Vars::$ID, true) == 1) {
	//Есть доступ
} else {
	//Нет доступа
}
//Расширенный способ
$check = Functions:checkFriend (Vars::$ID);
if($check == 1) {
	//Есть доступ
	//Вы друг. Смотрите что хотите :)
} else if($check == 2){
	//Нет доступа
	//Вы не являетесь друзьями, данный пользователь оставил вам заявку на дружбу. Есле вы хотите с ним дружить, то подтвердите ее
} else if($check == 3) {
	//Нет доступа
	//Вы отправили заявку дружить, но она еще не была подверждена. Ожидайте.
} else {
	//Нет доступа
	//Вы не являетесь друзьями.
}
//, где Vars::$ID - ID выбранного пользователя
*/
//TODO: Доработать под новую систему оповещений
//TODO: Доработать английский язык
//TODO: Удалить файл _inc/okfriends.php
defined('_IN_JOHNCMS') or die('Error: restricted access');
define ('_IN_JOHNCMS_FRIENDS', 1);

define('FRIENDSDIR', 'friends'); //Папка с модулем
define('FRIENDSPATH', MODPATH . FRIENDSDIR . DIRECTORY_SEPARATOR); //Абсолютный путь до модуля

//Подключаем шаблонизатор
$tpl = Template::getInstance();

$connect = array(
    'add',
    'cancel',
    'ok',
    'delete',
    'demands',
    'no',
    'offers',
    'online'
);
if (Vars::$ACT && ($key = array_search(Vars::$ACT, $connect)) !== FALSE && file_exists(FRIENDSPATH .
    '_inc' . DIRECTORY_SEPARATOR . $connect[$key] . '.php')
) {
    require (FRIENDSPATH . '_inc' . DIRECTORY_SEPARATOR . $connect[$key] . '.php');
} else {
    if (Vars::$USER && Vars::$USER != Vars::$USER_ID) {
        if (Vars::$USER_ID || Vars::$USER_SYS['view_profiles']) {
			if (($user = Vars::getUser()) === FALSE) {
				$tpl->contents = Functions::displayError(lng('user_does_not_exist'));
			} else {
				$tpl->user = $user;
				
				//Получаем список друзей
				$tpl->total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_contacts`
				LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id`
				WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER . "' AND `cms_mail_contacts`.`access`='2' AND `cms_mail_contacts`.`friends`='1' AND `cms_mail_contacts`.`banned`!='1'
				"), 0);
				if ($tpl->total) {
					$query = mysql_query("SELECT `users`.`id`, `users`.`nickname`, `users`.`last_visit`, `users`.`sex` FROM `cms_mail_contacts`
					LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id`
					WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER . "' AND `cms_mail_contacts`.`access`='2' AND `cms_mail_contacts`.`friends`='1' AND `cms_mail_contacts`.`banned`!='1' ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination());

					$array = array();
					$i = 1;
					while ($row = mysql_fetch_assoc($query)) {
						$array[] = array(
							'id'       => $row['id'],
							'list'     => (($i % 2) ? 'list1' : 'list2'),
							'nickname' => $row['nickname'],
							'icon'     => Functions::getIcon( 'user' . ( $row['sex'] == 'm' ? '' : '-female' ) . '.png', '', '', 'style="margin: 0 0 -3px 0;"' ),
							'online'   => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
								'<span class="green"> [ON]</span>'));
						++$i;
					}
					$tpl->query = $array;
					$tpl->display_pagination = Functions::displayPagination(Vars::$MODULE_URI . '?id=' . Vars::$USER . '&amp;', Vars::$START, $tpl->total, Vars::$USER_SET['page_size']);
				}
				$tpl->contents = $tpl->includeTpl('list');
			}
        } else {
			$tpl->contents = Functions::displayError(lng('access_guest_forbidden'));
		}
    } else {
        $tpl->offers = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_contacts` WHERE `contact_id`='" . Vars::$USER_ID . "' AND `access`='2' AND `friends`='0' AND `banned`='0'"), 0);
        $tpl->demands = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_contacts` WHERE `user_id`='" . Vars::$USER_ID . "' AND `access`='2' AND `friends`='0' AND `banned`='0'"), 0);
        //Получаем список друзей
        $tpl->total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_contacts`
		LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id`
		WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "' AND `cms_mail_contacts`.`access`='2' AND `cms_mail_contacts`.`friends`='1' AND `cms_mail_contacts`.`banned`!='1'
		"), 0);
        if ($tpl->total) {
            $query = mysql_query("SELECT `users`.`id`, `users`.`nickname`, `users`.`last_visit`, `users`.`sex` FROM `cms_mail_contacts`
            LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id`
            WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "' AND `cms_mail_contacts`.`access`='2' AND `cms_mail_contacts`.`friends`='1' AND `cms_mail_contacts`.`banned`!='1' ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination());

            $array = array();
            $i = 1;
            while ($row = mysql_fetch_assoc($query)) {
                $array[] = array(
                    'id'       => $row['id'],
                    'list'     => (($i % 2) ? 'list1' : 'list2'),
                    'nickname' => $row['nickname'],
                    'icon'     => Functions::getIcon( 'user' . ( $row['sex'] == 'm' ? '' : '-female' ) . '.png', '', '', 'style="margin: 0 0 -3px 0;"' ),
                    'online'   => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                        '<span class="green"> [ON]</span>'));
                ++$i;
            }
            $tpl->query = $array;
            $tpl->display_pagination = Functions::displayPagination(Vars::$MODULE_URI . '?', Vars::$START, $total, Vars::$USER_SET['page_size']);
        }
        $tpl->contents = $tpl->includeTpl('index');
    }
}