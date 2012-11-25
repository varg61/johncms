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
        $result = mysql_query("SELECT `id`, `nickname` FROM `users` WHERE `id`='" . Vars::$ID . "'");
        if (mysql_num_rows($result)) {
            $user = mysql_fetch_assoc($result);
            $tpl->id = $user['id'];
            $tpl->nickname = $user['nickname'];
            //Получаем список друзей
            $tpl->total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_contacts`
    		LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id`
    		WHERE `cms_mail_contacts`.`user_id`='" . Vars::$ID . "' AND `cms_mail_contacts`.`access`='2' AND `cms_mail_contacts`.`friends`='1' AND `cms_mail_contacts`.`banned`!='1'
    		"), 0);
            if ($tpl->total) {
                $query = mysql_query("SELECT `users`.`id`, `users`.`nickname`, `users`.`last_visit`, `users`.`sex` FROM `cms_mail_contacts`
                LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id`
                WHERE `cms_mail_contacts`.`user_id`='" . Vars::$ID . "' AND `cms_mail_contacts`.`access`='2' AND `cms_mail_contacts`.`friends`='1' AND `cms_mail_contacts`.`banned`!='1' ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination());

                $array = array();
                $i = 1;
                while ($row = mysql_fetch_assoc($query)) {
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
                $tpl->display_pagination = Functions::displayPagination(Vars::$MODULE_URI . '?id=' . Vars::$ID . '&amp;', Vars::$START, $tpl->total, Vars::$USER_SET['page_size']);
            }
            $tpl->contents = $tpl->includeTpl('list');
        } else {
            $tpl->contents = Functions::displayError(__('user_does_not_exist'));
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
                    'icon'     => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' :
                        'w') . '.png', '', 'align="middle"'),
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