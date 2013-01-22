<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
//Закрываем прямой доступ к файлу
defined('_IN_JOHNCMS_FRIENDS') or die('Error: restricted access');
//Закрываем доступ гостям
if (!Vars::$USER_ID) {
    Header('Location: ' . Vars::$HOME_URL . '404');
    exit;
}
//Получаем список друзей
$tpl->total = DB::PDO()->query("SELECT COUNT(*) FROM `cms_mail_contacts`
LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id`
WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "'
AND `cms_mail_contacts`.`access`='2'
AND `cms_mail_contacts`.`friends`='1'
AND `cms_mail_contacts`.`banned`!='1'
AND `users`.`last_visit` > " . (time() - 300) . "")->fetchColumn();
if ($tpl->total) {
    $query = DB::PDO()->query("SELECT `users`.* FROM `cms_mail_contacts`
	LEFT JOIN `users` ON `cms_mail_contacts`.`contact_id`=`users`.`id`
	WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "' AND `cms_mail_contacts`.`access`='2' AND `cms_mail_contacts`.`friends`='1' AND `cms_mail_contacts`.`banned`!='1' AND `users`.`last_visit` > " . (time() - 300) . " ORDER BY `users`.`last_visit` DESC" . Vars::db_pagination());

    $array = array();
    $i = 1;
    while ($row = $query->fetch()) {
        $array[] = array(
            'id'       => $row['id'],
            'list'     => (($i % 2) ? 'list1' : 'list2'),
            'nickname' => $row['nickname'],
            'icon'     => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' :
                'w') . '.png', '', 'align="middle"'),
        );
        ++$i;
    }
    $tpl->query = $array;
    $tpl->display_pagination = Functions::displayPagination(Router::getUri(2) . '?', Vars::$START, $tpl->total, Vars::$USER_SET['page_size']);
}
$tpl->contents = $tpl->includeTpl('online');