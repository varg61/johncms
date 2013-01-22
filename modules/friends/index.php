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
    if (Vars::$ID && Vars::$ID != Vars::$USER_ID) {
        $result = DB::PDO()->query("SELECT `id`, `nickname` FROM `users` WHERE `id`='" . Vars::$ID . "'");
        if ($result->rowCount()) {
            $user = $result->fetch();
            $tpl->id = $user['id'];
            $tpl->nickname = $user['nickname'];
            //Получаем список друзей
            $tpl->total = DB::PDO()->query("SELECT COUNT(*) FROM `cms_mail_contacts`
    		LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id`
    		WHERE `cms_mail_contacts`.`user_id`='" . Vars::$ID . "'
    		AND `cms_mail_contacts`.`access`='2'
    		AND `cms_mail_contacts`.`friends`='1'
    		AND `cms_mail_contacts`.`banned`!='1'")->fetchColumn();
            if ($tpl->total) {
                $query = DB::PDO()->query("SELECT `users`.`id`, `users`.`nickname`, `users`.`last_visit`, `users`.`sex` FROM `cms_mail_contacts`
                LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id`
                WHERE `cms_mail_contacts`.`user_id`='" . Vars::$ID . "' AND `cms_mail_contacts`.`access`='2' AND `cms_mail_contacts`.`friends`='1' AND `cms_mail_contacts`.`banned`!='1' ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination());

                $array = array();
                $i = 1;
                while ($row = $query->fetch()) {
                    $array[] = array(
                        'id'       => $row['id'],
                        'list'     => (($i % 2) ? 'list1' : 'list2'),
                        'nickname' => $row['nickname'],
                        'icon'     => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' :
                            'w') . '.png', '', 'align="middle"'),
                        'online'   => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                            '<span class="green"> [ON]</span>'));
                    ++$i;
                }
                $tpl->query = $array;
                $tpl->display_pagination = Functions::displayPagination(Router::getUri(2) . '?id=' . Vars::$ID . '&amp;', Vars::$START, $tpl->total, Vars::$USER_SET['page_size']);
            }
            $tpl->contents = $tpl->includeTpl('list');
        } else {
            $tpl->contents = Functions::displayError(__('user_does_not_exist'));
        }
    } else {
        $tpl->offers = DB::PDO()->query("SELECT COUNT(*) FROM `cms_mail_contacts` WHERE `contact_id`='" . Vars::$USER_ID . "' AND `access`='2' AND `friends`='0' AND `banned`='0'")->fetchColumn();
        $tpl->demands = DB::PDO()->query("SELECT COUNT(*) FROM `cms_mail_contacts` WHERE `user_id`='" . Vars::$USER_ID . "' AND `access`='2' AND `friends`='0' AND `banned`='0'")->fetchColumn();
        //Получаем список друзей
        $tpl->total = DB::PDO()->query("SELECT COUNT(*) FROM `cms_mail_contacts`
		LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id`
		WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "'
		AND `cms_mail_contacts`.`access`='2'
		AND `cms_mail_contacts`.`friends`='1'
		AND `cms_mail_contacts`.`banned`!='1'")->fetchColumn();
        if ($tpl->total) {
            $query = DB::PDO()->query("SELECT `users`.`id`, `users`.`nickname`, `users`.`last_visit`, `users`.`sex` FROM `cms_mail_contacts`
            LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id`
            WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "' AND `cms_mail_contacts`.`access`='2' AND `cms_mail_contacts`.`friends`='1' AND `cms_mail_contacts`.`banned`!='1' ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination());

            $array = array();
            $i = 1;
            while ($row = $query->fetch()) {
                $array[] = array(
                    'id'       => $row['id'],
                    'list'     => (($i % 2) ? 'list1' : 'list2'),
                    'nickname' => $row['nickname'],
                    'icon'     => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' :
                        'w') . '.png', '', 'align="middle"'),
                    'online'   => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                        '<span class="green"> [ON]</span>'));
                ++$i;
            }
            $tpl->query = $array;
            $tpl->display_pagination = Functions::displayPagination(Router::getUri(2) . '?', Vars::$START, $total, Vars::$USER_SET['page_size']);
        }
        $tpl->contents = $tpl->includeTpl('index');
    }
}