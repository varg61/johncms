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
    Header('Location: ' . Vars::$HOME_URL . '/404');
    exit;
}

$backLink = Router::getUrl(2);

$tpl->title = __('mail') . ' | ' . __('inmess');
if (Vars::$MOD == 'cleaning') {
    if (isset($_POST['submit']) && ValidMail::checkCSRF() === TRUE) {
        $cl = isset($_POST['cl']) ? (int)$_POST['cl'] : '';
        switch ($cl) {
            case 1:
                mysql_query("UPDATE `cms_mail_messages` SET
				`delete_in`='" . Vars::$USER_ID . "' WHERE `contact_id`='" . Vars::$USER_ID . "' AND `time`<='" . (time() - 604800) . "'");
                break;

            case 2:
                mysql_query("UPDATE `cms_mail_messages` SET
				`delete_in`='" . Vars::$USER_ID . "' WHERE `contact_id`='" . Vars::$USER_ID . "'");
                break;

            default:
                mysql_query("UPDATE `cms_mail_messages` SET
				`delete_in`='" . Vars::$USER_ID . "' WHERE `contact_id`='" . Vars::$USER_ID . "' AND `time`<='" . (time() - 2592000) . "'");
        }
        Header('Location: ' . $backLink . '?act=inmess');
        exit;
    }
    $tpl->urlSelect = $backLink . '?act=inmess&amp;mod=cleaning';
    $tpl->submit = __('clear');
    $tpl->phdr = __('cleaning');
    $tpl->token = mt_rand(100, 10000);
    $_SESSION['token_status'] = $tpl->token;
    $tpl->contents = $tpl->includeTpl('time');
} else if (Vars::$MOD == 'delete_read') {
    if (isset($_POST['submit']) && ValidMail::checkCSRF() === TRUE) {
        mysql_query("UPDATE `cms_mail_messages` SET
		`delete_in`='" . Vars::$USER_ID . "' WHERE `contact_id`='" . Vars::$USER_ID . "' AND `read`='1'");
        Header('Location: ' . $backLink . '?act=inmess');
        exit;
    }
    $tpl->urlSelect = $backLink . '?act=inmess&amp;mod=delete_read';
    $tpl->select = __('confirm_delete_read');
    $tpl->submit = __('delete');
    $tpl->phdr = __('delete_read');
    $tpl->token = mt_rand(100, 10000);
    $_SESSION['token_status'] = $tpl->token;
    $tpl->contents = $tpl->includeTpl('select');
} else {
    $total = mysql_result(mysql_query("SELECT COUNT(*)
	FROM `cms_mail_messages`
	WHERE `contact_id`='" . Vars::$USER_ID . "' AND `delete_in`!='" . Vars::$USER_ID . "' AND `delete_out`!='" . Vars::$USER_ID . "'"), 0);
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
                    $query = mysql_query("SELECT *
					FROM `cms_mail_messages` 
					WHERE (`user_id`='" . Vars::$USER_ID . "' 
					OR `contact_id`='" . Vars::$USER_ID . "') AND `id` IN (" . $id . ")");
                    while ($row = mysql_fetch_assoc($query)) {
                        if ($row['contact_id'] == Vars::$USER_ID) {
                            mysql_query("UPDATE `cms_mail_messages` SET
							`delete_in`='" . Vars::$USER_ID . "' WHERE `id`='" . $row['id'] . "'");
                        }
                        if ($row['user_id'] == Vars::$USER_ID) {
                            mysql_query("UPDATE `cms_mail_messages` SET
							`delete_out`='" . Vars::$USER_ID . "' WHERE `id`='" . $row['id'] . "'");
                        }
                    }
                }
            }
            Header('Location: ' . $backLink . '?act=inmess');
            exit;
        }
        $query = mysql_query("SELECT `cms_mail_messages`.*, `cms_mail_messages`.`id` as `mid`, `users`.`nickname`, `users`.`last_visit` FROM `cms_mail_messages`
		LEFT JOIN `users` ON
		`cms_mail_messages`.`user_id`=`users`.`id`
		WHERE `cms_mail_messages`.`contact_id`='" . Vars::$USER_ID . "' 
		AND `cms_mail_messages`.`delete_in`!='" . Vars::$USER_ID . "' 
		AND `cms_mail_messages`.`delete_out`!='" . Vars::$USER_ID . "' 
		ORDER BY `cms_mail_messages`.`time` DESC" . Vars::db_pagination());
        $array = array();
        $i = 1;
        while ($row = mysql_fetch_assoc($query)) {
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
            '?act=inmess&amp;', Vars::$START, $total, Vars::
        $USER_SET['page_size']);
    }

    //Подключаем шаблон inout.php
    $tpl->pref_in = __('pref_in');
    $tpl->tit = __('inmess');
    $tpl->pages_type = 'inmess';
    $tpl->token = mt_rand(100, 10000);
    $_SESSION['token_status'] = $tpl->token;
    $tpl->mess_err = __('inmess_not');

    $tpl->link = $backLink;
    $tpl->contents = $tpl->includeTpl('inout');
}