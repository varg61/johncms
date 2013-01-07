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

//Заголовок
$tpl->title = __('mail') . ' | ' . __('basket');
if (Vars::$ID) {
    if (isset($_POST['delete_mess']) && is_array($_POST['delch']) && ValidMail::checkCSRF() === TRUE) {
        $delch = array_map('intval', $_POST['delch']);
        $delch = implode(',', $delch);

        $q = mysql_query("SELECT * FROM `cms_mail_messages` WHERE (`user_id`='" . Vars::$USER_ID . "' OR `contact_id`='" . Vars::$USER_ID . "') AND `id` IN (" . $delch . ")");
        $delete = array();
        $update = array();
        while ($row = mysql_fetch_assoc($q)) {
            if (!empty($row['delete']) && $row['delete'] != Vars::$USER_ID) {
                $delete[] = $row['id'];
                if ($row['filename'])
                    @unlink(FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'pm' . DIRECTORY_SEPARATOR . $row['filename']);
            } else {
                $update[] = $row['id'];
            }
        }

        if ($delete) {
            $delete = implode(',', $delete);
            mysql_query("DELETE FROM `cms_mail_messages`
			WHERE `id` IN (" . $delete . ")");
        }
        if ($update) {
            $update = implode(',', $update);
            mysql_query("UPDATE `cms_mail_messages` SET
			`delete`='" . Vars::$USER_ID . "' 
			WHERE `id` IN (" . $update . ")");
        }
        unset($delete, $update);

        Header('Location: ' . $backLink . '?act=basket&id=' . Vars::$ID);
        exit;
    } else {
        if (Vars::$ID == Vars::$USER_ID) {
            $tpl->contents = Functions::displayError(__('error_request'), '<a href="' . $backLink . '">' . __('contacts') . '</a>');
        } else {
            $q = mysql_query("SELECT `nickname` FROM `users` WHERE `id`='" . Vars::$ID . "' LIMIT 1");
            if (mysql_num_rows($q)) {
                $total = mysql_result(mysql_query("SELECT COUNT(*)
				FROM `cms_mail_messages`
				WHERE ((`user_id`='" . Vars::$USER_ID . "'
				AND `contact_id`='" . Vars::$ID . "')
				OR (`contact_id`='" . Vars::$USER_ID . "'
				AND `user_id`='" . Vars::$ID . "'))
				AND (`delete_in`='" . Vars::$USER_ID . "'
				OR `delete_out`='" . Vars::$USER_ID . "') AND `delete`!='" . Vars::$USER_ID . "'"), 0);
                if ($total) {
                    //Формируем список удаленных сообщений определенного контакта
                    $query = mysql_query("SELECT `cms_mail_messages`.*, `cms_mail_messages`.`id` as `mid`, `users`.*
					FROM `cms_mail_messages`
					LEFT JOIN `users` 
					ON `cms_mail_messages`.`user_id`=`users`.`id` 
					WHERE ((`cms_mail_messages`.`user_id`='" . Vars::$USER_ID . "'
					AND `cms_mail_messages`.`contact_id`='" . Vars::$ID . "')
					OR (`cms_mail_messages`.`contact_id`='" . Vars::$USER_ID . "'
					AND `cms_mail_messages`.`user_id`='" . Vars::$ID . "'))
					AND (`cms_mail_messages`.`delete_in`='" . Vars::$USER_ID . "'
					OR `cms_mail_messages`.`delete_out`='" . Vars::$USER_ID . "')
					AND `delete`!='" . Vars::$USER_ID . "'
					ORDER BY `cms_mail_messages`.`time` DESC" . Vars::db_pagination());
                    $array = array();

                    $i = 1;
                    while ($row = mysql_fetch_assoc($query)) {
                        $text = Validate::checkout($row['text'], 1, 1);
                        if (Vars::$USER_SET['smileys'])
                            $text = Functions::smilies($text, $row['rights'] >= 1 ? 1 : 0);
                        $array[] = array(
                            'id'        => $row['id'],
                            'mid'       => $row['mid'],
                            'icon'      => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' : 'w') .
                                '.png', '', 'align="middle"'),
                            'list'      => (($i % 2) ? 'list1' : 'list2'),
                            'nickname'  => $row['nickname'],
                            'file'      => $row['filename'] ? '<a href="' . $backLink . '?act=load&amp;id=' .
                                $row['mid'] . '">' . $row['filename'] . '</a> (' . UploadMail::formatsize($row['filesize']) .
                                ')(' . $row['filecount'] . ')' : '',
                            'time'      => Functions::displayDate($row['time']),
                            'text'      => $text,
                            'urlDelete' => $backLink . '?act=messages&amp;mod=delete&amp;id=' . $row['mid'],
                            'url'       => ($backLink . '?act=messages&amp;id=' . $row['id']),
                            'online'    => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                                '<span class="green"> [ON]</span>'),
                            'elected'   => (($row['elected_in'] != Vars::$USER_ID && $row['elected_out'] !=
                                Vars::$USER_ID) ? TRUE : FALSE),
                            'selectBar' => '[<span class="green">&laquo;</span> <a href="' . $backLink .
                                '?act=restore&amp;id=' . $row['mid'] . '">' . __('rest') . '</a>] [<span class="red">х</span> <a href="' .
                                $backLink . '?act=delete&amp;id=' . $row['mid'] . '">' . __('delete') .
                                '</a>]');
                        ++$i;
                    }

                    $tpl->query = $array;
                    $tpl->titleTest = '<div class="phdr">' . __('deleted_message') . '</div>';
                    $tpl->urlTest = '<p><a href="' . Router::getUrl(2) . '">' . __('mail') .
                        '</a></p>';
                    $tpl->total = $total;
                    //Навигация
                    $tpl->display_pagination = Functions::displayPagination($backLink . '?act=basket&amp;id=' .
                        Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']);
                    $tpl->token = mt_rand(100, 10000);
                    $_SESSION['token_status'] = $tpl->token;
                    $tpl->url_type = $backLink . '?act=basket&amp;id=' . Vars::$ID;
                    //Подключаем шаблон модуля list.php
                    $tpl->contents = $tpl->includeTpl('list');
                } else {
                    Header('Location: ' . $backLink . '?act=basket');
                    exit;
                }
            } else {
                //Если пользователь не существует, показываем ошибку
                $tpl->contents = Functions::displayError(__('user_does_not_exist'), '<a href="' .
                    $backLink . '">' . __('mail') . '</a>');
            }
        }
    }
} else {
    $total = Mail::counter('delete');
    $tpl->total = $total;
    if ($total) {
        //Восстанавливаем сообщения
        if (isset($_POST['restore']) && ValidMail::checkCSRF() === TRUE) {
            if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
                $id = array_map('intval', $_POST['delch']);
                $id = implode(',', $id);
                if (!empty($id)) {
                    mysql_query("UPDATE `cms_mail_messages` SET
					`delete_in`='0'
					WHERE `user_id` IN (" . $id . ") AND `contact_id`='" . Vars::$USER_ID . "' AND `delete_in`='" . Vars::$USER_ID . "'");
                    mysql_query("UPDATE `cms_mail_messages` SET
					`delete_out`='0'
					WHERE `contact_id` IN (" . $id . ") AND `user_id`='" . Vars::$USER_ID . "' AND `delete_out`='" . Vars::$USER_ID . "'");
                    mysql_query("UPDATE `cms_mail_contacts` SET
					`delete`='0'
					WHERE `user_id`='" . Vars::$USER_ID . "' 
					AND `contact_id` IN (" . $id . ")");
                }
            }
            Header('Location: ' . $backLink . '?act=basket');
            exit;
        }
        //Удаляем сообщения
        if (isset($_POST['delete']) && ValidMail::checkCSRF() === TRUE) {
            if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
                $id = array_map('intval', $_POST['delch']);
                $id = implode(',', $id);
                if (!empty($id)) {
                    $query = mysql_query("SELECT *
					FROM `cms_mail_messages` 
					WHERE ((`delete_out`='" . Vars::$USER_ID . "' AND `user_id`='" . Vars::$USER_ID . "' AND `contact_id` IN (" . $id . "))
					OR (`delete_in`='" . Vars::$USER_ID . "' AND `contact_id`='" . Vars::$USER_ID . "' AND `user_id` IN (" . $id . ")))
					AND `delete`!='" . Vars::$USER_ID . "'");
                    $update = array();
                    $delete = array();
                    while ($row = mysql_fetch_assoc($query)) {
                        if ($row['delete'] && $row['delete'] != Vars::$USER_ID)
                            $delete[] = $row['id'];
                        else
                            $update[] = $row['id'];
                    }
                    if ($delete) {
                        $delete = implode(',', $delete);
                        $q = mysql_query("SELECT * FROM `cms_mail_messages`
						WHERE `id` IN (" . $delete . ") AND `filename`!=''");
                        while ($res = mysql_fetch_assoc($q)) {
                            if (file_exists(FILEPATH . 'users/pm/' . $res['filename']) !== FALSE)
                                @unlink(FILEPATH . 'users/pm/' . $res['filename']);
                        }
                        mysql_query("DELETE FROM `cms_mail_messages`
						WHERE `id` IN (" . $delete . ")");
                    }
                    if ($update) {
                        $update = implode(',', $update);
                        mysql_query("UPDATE `cms_mail_messages` SET
						`delete`='" . Vars::$USER_ID . "' 
						WHERE `id` IN (" . $update . ")");
                    }
                    unset($delete, $update);
                }
            }
            Header('Location: ' . $backLink . '?act=basket');
            exit;
        }
        //Очищаем корзину
        if (isset($_POST['clear']) && ValidMail::checkCSRF() === TRUE) {
            $query = mysql_query("SELECT *
			FROM `cms_mail_messages` 
			WHERE `delete_in`='" . Vars::$USER_ID . "' 
			OR `delete_out`='" . Vars::$USER_ID . "'");
            $update = array();
            $delete = array();
            while ($row = mysql_fetch_assoc($query)) {
                if (!empty($row['delete']) && $row['delete'] != Vars::$USER_ID) {
                    $delete[] = $row['id'];
                }
                $update[] = $row['id'];
            }

            if ($delete) {
                $del = implode(',', $delete);
                $qq1 = mysql_query("SELECT `filename`
				FROM `cms_mail_messages` 
				WHERE `filename`!='' 
				AND `id` IN (" . $del . ")");
                while ($r1 = mysql_fetch_assoc($qq1)) {
                    @unlink(FILEPATH . 'users/pm/' . $r1['filename']);
                }
                mysql_query("DELETE FROM `cms_mail_messages`
				WHERE `id` IN (" . $del . ")");
            }

            if ($update) {
                $id = implode(',', $update);
                mysql_query("UPDATE `cms_mail_messages` SET `delete`='" . Vars::$USER_ID . "'
				WHERE `id` IN (" . $id . ")");
            }
            Header('Location: ' . $backLink . '?act=basket');
            exit;
        }
        //Формируем список удаленных сообщений по контактам
        $query = mysql_query("SELECT `users`.`id`, `users`.`nickname`,  `users`.`sex`,  `users`.`last_visit`, 
		`cms_mail_contacts`.`contact_id`, `cms_mail_contacts`.`user_id`, COUNT(*) as `count`
		FROM `cms_mail_contacts`
		LEFT JOIN `users`
		ON `cms_mail_contacts`.`contact_id`=`users`.`id`
		LEFT JOIN `cms_mail_messages`
		ON (`cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`contact_id`
		OR `cms_mail_contacts`.`contact_id`=`cms_mail_messages`.`user_id`)
		AND `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "'
		WHERE ((`cms_mail_messages`.`user_id`='" . Vars::$USER_ID . "'
		OR `cms_mail_messages`.`contact_id`='" . Vars::$USER_ID . "')
		AND (`cms_mail_messages`.`delete_out`='" . Vars::$USER_ID . "' 
		OR `cms_mail_messages`.`delete_in`='" . Vars::$USER_ID . "' 
		OR (`cms_mail_contacts`.`delete`='1' 
		AND `cms_mail_contacts`.`user_id`='" . Vars::$USER_ID . "')))
		AND `cms_mail_messages`.`delete`!='" . Vars::$USER_ID . "'
		GROUP BY `cms_mail_contacts`.`contact_id`
		ORDER BY `cms_mail_contacts`.`time` DESC" . Vars::db_pagination());

        $array = array();
        $i = 1;
        while ($row = mysql_fetch_assoc($query)) {
            $array[] = array(
                'id'        => $row['id'],
                'icon'      => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' : 'w') . '.png',
                    '', 'align="middle"'),
                'list'      => (($i % 2) ? 'list1' : 'list2'),
                'nickname'  => $row['nickname'],
                'count'     => $row['count'],
                'count_new' => '',
                'url'       => ($backLink . '?act=basket&amp;id=' . $row['id']),
                'online'    => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                    '<span class="green"> [ON]</span>'));
            ++$i;
        }
        //Навигация
        $tpl->display_pagination = Functions::displayPagination($backLink . '?act=basket&amp;',
            Vars::$START, $total, Vars::$USER_SET['page_size']);
        $tpl->query = $array;
        unset($array);
        $tpl->token = mt_rand(100, 10000);
        $_SESSION['token_status'] = $tpl->token;
        //Подключаем шаблон модуля contacts.php
        $tpl->contacts = $tpl->includeTpl('contacts');
    }
    //Подключаем шаблон модуля basket.php
    $tpl->contents = $tpl->includeTpl('basket');
}
