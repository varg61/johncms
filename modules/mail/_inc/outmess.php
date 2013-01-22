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
defined('_IN_JOHNCMS_MAIL') or die('Error: restricted access');

//Закрываем доступ гостям
if (!Vars::$USER_ID) {
    Header('Location: ' . Vars::$HOME_URL . '404');
    exit;
}

$backLink = Router::getUri(2);

$tpl->title = __('mail') . ' | ' . __('outmess');
if (Vars::$MOD == 'cleaning') {
    if (isset($_POST['submit']) && ValidMail::checkCSRF() === TRUE) {
        $cl = isset($_POST['cl']) ? (int)$_POST['cl'] : '';
        switch ($cl) {
            case 1:
                DB::PDO()->exec("UPDATE `cms_mail_messages` SET
				`delete_out`='" . Vars::$USER_ID . "' WHERE `user_id`='" . Vars::$USER_ID . "' AND `time`<='" . (time() - 604800) . "'");
                break;

            case 2:
                DB::PDO()->exec("UPDATE `cms_mail_messages` SET
				`delete_out`='" . Vars::$USER_ID . "' WHERE `user_id`='" . Vars::$USER_ID . "'");
                break;

            default:
                DB::PDO()->exec("UPDATE `cms_mail_messages` SET
				`delete_out`='" . Vars::$USER_ID . "' WHERE `user_id`='" . Vars::$USER_ID . "' AND `time`<='" . (time() - 2592000) . "'");
        }
        Header('Location: ' . $backLink . '?act=outmess');
        exit;
    }
    $tpl->urlSelect = $backLink . '?act=outmess&amp;mod=cleaning';
    $tpl->submit = __('clear');
    $tpl->phdr = __('cleaning');
    $tpl->token = mt_rand(100, 10000);
    $_SESSION['token_status'] = $tpl->token;
    $tpl->contents = $tpl->includeTpl('time');
} else {
    $total = DB::PDO()->query("SELECT COUNT(*)
	FROM `cms_mail_messages`
	WHERE `user_id`='" . Vars::$USER_ID . "'
	AND `delete_in`!='" . Vars::$USER_ID . "'
	AND `delete_out`!='" . Vars::$USER_ID . "'")->fetchColumn();
    $tpl->total = $total;
    if ($total) {
        //Перемещаем контакты в корзину
        if (isset($_POST['delete_mess']) && ValidMail::checkCSRF() === TRUE) {
            if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
                $id = array_map('intval', $_POST['delch']);
                $id = implode(',', $id);
                if (!empty($id)) {
                    $out = array();
                    $in = array();
                    $query = DB::PDO()->query("SELECT *
					FROM `cms_mail_messages` 
					WHERE (`user_id`='" . Vars::$USER_ID . "' 
					OR `contact_id`='" . Vars::$USER_ID . "') AND `id` IN (" . $id . ")");
                    while ($row = $query->fetch()) {
                        if ($row['contact_id'] == Vars::$USER_ID) {
                            DB::PDO()->exec("UPDATE `cms_mail_messages` SET
							`delete_in`='" . Vars::$USER_ID . "' WHERE `id`='" . $row['id'] . "'");
                        }
                        if ($row['user_id'] == Vars::$USER_ID) {
                            DB::PDO()->exec("UPDATE `cms_mail_messages` SET
							`delete_out`='" . Vars::$USER_ID . "' WHERE `id`='" . $row['id'] . "'");
                        }
                    }
                }
            }
            Header('Location: ' . $backLink . '?act=outmess');
            exit;
        }
        $query = DB::PDO()->query("SELECT `cms_mail_messages`.*, `cms_mail_messages`.`id` as `mid`, `users`.`nickname`, `users`.`last_visit` FROM `cms_mail_messages`
		LEFT JOIN `users` ON
		`cms_mail_messages`.`contact_id`=`users`.`id`
		WHERE `cms_mail_messages`.`user_id`='" . Vars::$USER_ID . "' AND `cms_mail_messages`.`delete_in`!='" . Vars::$USER_ID . "' AND `cms_mail_messages`.`delete_out`!='" . Vars::$USER_ID . "' ORDER BY `cms_mail_messages`.`time` DESC" . Vars::db_pagination());
        $array = array();
        $i = 1;
        while ($row = $query->fetch()) {
            $array[] = array(
                'list'     => (!$row['read'] ? 'gmenu' : (($i % 2) ? 'list1' : 'list2')),
                'id'       => $row['id'],
                'nickname' => $row['nickname'],
                'time'     => Functions::displayDate($row['time']),
                'online'   => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                    '<span class="green"> [ON]</span>'),
                'file'     => $row['filename'] ? TRUE : ''
            );
            ++$i;
        }
        $tpl->query = $array;

        //Навигация
        $tpl->display_pagination = Functions::displayPagination($backLink .
            '?act=outmess&amp;', Vars::$START, $total, Vars::
        $USER_SET['page_size']);
    }

    //Подключаем шаблон inout.php
    $tpl->pref_in = __('pref_out');
    $tpl->tit = __('outmess');
    $tpl->pages_type = 'outmess';
    $tpl->token = mt_rand(100, 10000);
    $_SESSION['token_status'] = $tpl->token;
    $tpl->mess_err = __('outmess_not');

    $tpl->link = $backLink;
    $tpl->contents = $tpl->includeTpl('inout');
}