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
defined('_IN_JOHNCMS_CONTACTS') or die('Error: restricted access');
//Закрываем доступ гостям
if (!Vars::$USER_ID) {
    Header('Location: ' . Vars::$HOME_URL . '404');
    exit;
}
//Заголовок
$tpl->title = __('mail') . ' | ' . __('banned');
//Считаем количество контактов в игноре
$total = DB::PDO()->query("SELECT COUNT(*)
 FROM `cms_mail_contacts`
 WHERE `banned`='1'
 AND `user_id`='" . Vars::$USER_ID . "'
 AND `cms_mail_contacts`.`delete`='0';")->fetchColumn();
$tpl->total = $total;
if ($total) {
    //Удаляем контакты из игнора
    if (isset($_POST['unban']) && isset($_POST['token']) && isset($_SESSION['token_status']) &&
        $_POST['token'] == $_SESSION['token_status']
    ) {
        if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
            $id = array_map('intval', $_POST['delch']);
            $id = implode(',', $id);
            if (!empty($id)) {
                $mass = array();
                $query = DB::PDO()->query("SELECT *
                FROM `cms_mail_contacts` 
                WHERE `user_id`='" . Vars::$USER_ID . "' 
                AND `contact_id` IN (" . $id . ") 
                AND `banned`='1'");
                while ($rows = $query->fetch()) {
                    $mass[] = $rows['id'];
                }
                if (!empty($mass)) {
                    $exp = implode(',', $mass);
                    DB::PDO()->exec("UPDATE `cms_mail_contacts` SET
					`banned`='0' 
                    WHERE `user_id`='" . Vars::$USER_ID . "' 
                    AND `id` IN (" . $exp . ")");
                }
            }
        }
        Header('Location: ' . Router::getUri(2) . '?act=banned');
        exit;
    }


    $query = DB::PDO()->query("SELECT `users`.`id`, `users`.`nickname`,  `users`.`sex`,  `users`.`last_visit`
	FROM `cms_mail_contacts`
	LEFT JOIN `users`
	ON `cms_mail_contacts`.`contact_id`=`users`.`id`
	WHERE `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "' 
	AND `cms_mail_contacts`.`delete`='0' 
	AND `cms_mail_contacts`.`banned`='1'
	ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination());

    $array = array();
    $i = 0;
    while ($row = $query->fetch()) {
        $array[] = array(
            'id'        => $row['id'],
            'icon'      => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' : 'w') . '.png',
                '', 'style="margin: 0 0 -3px 0;"'),
            'list'      => (($i % 2) ? 'list1' : 'list2'),
            'nickname'  => $row['nickname'],
            'count'     => DB::PDO()->query("SELECT COUNT(*) FROM `cms_mail_messages` WHERE ((`user_id`=" . Vars::$USER_ID . " AND `contact_id`=" . $row['id'] . ") OR (`contact_id`=" . Vars::$USER_ID . " AND `contact_id`=" . $row['id'] . ")) AND `delete_in`!=" . Vars::$USER_ID . " AND `delete_out`!=" . Vars::$USER_ID . "")->fetchColumn(),
            'count_new' => countNew($row['id']),
            'url'       => (Vars::$HOME_URL . 'mail/?act=messages&amp;id=' . $row['id']),
            'online'    => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                '<span class="green"> [ON]</span>'));
        ++$i;
    }
    //Навигация
    $tpl->display_pagination = Functions::displayPagination(Router::getUri(2) . '?act=banned&amp;',
        Vars::$START, $total, Vars::$USER_SET['page_size']);
    $tpl->query = $array;
    $tpl->token = mt_rand(100, 10000);
    $_SESSION['token_status'] = $tpl->token;
    //Подключаем шаблон модуля contacts.php
    $tpl->contacts = $tpl->includeTpl('contacts');
} else {
    $tpl->contacts = '<div class="rmenu">' . __('no_banned') . '!</div>';
}
//Подключаем шаблон модуля banned.php
$tpl->contents = $tpl->includeTpl('banned');
