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

$add_message['text'] = isset($_POST['text']) ? trim($_POST['text']) : '';
$addmail = new ValidMail($add_message, Vars::$ID);

if ($addmail->request() !== TRUE && empty(Vars::$MOD)) {
    $tpl->contents = Functions::displayError($addmail->error_request, '<a href="' . Vars::
    $HOME_URL . '/contacts">' . lng('contacts') . '</a>');
} else {
    //Очистка сообщений
    if (Vars::$ID && isset($_POST['delete_mess']) && is_array($_POST['delch']) && ValidMail::checkCSRF() === TRUE) {
        $delch = array_map('intval', $_POST['delch']);
        $delch = implode(',', $delch);
        mysql_query("UPDATE `cms_mail_messages` SET
		`delete_in`='" . Vars::$USER_ID . "' WHERE `user_id`='" . Vars::$ID . "' AND `contact_id`='" . Vars::$USER_ID . "' AND `id` IN (" . $delch . ")");
        mysql_query("UPDATE `cms_mail_messages` SET
		`delete_out`='" . Vars::$USER_ID . "' WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='" . Vars::$ID . "' AND `id` IN (" . $delch . ")");
        Header('Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . Vars::$ID);
        exit;
    } else if (Vars::$ID && Vars::$MOD == 'cleaning') {
        if (isset($_POST['submit']) && ValidMail::checkCSRF() === TRUE) {
            $cl = isset($_POST['cl']) ? (int)$_POST['cl'] : '';
            switch ($cl) {
                case 1:
                    mysql_query("UPDATE `cms_mail_messages` SET
					`delete_in`='" . Vars::$USER_ID . "' WHERE `user_id`='" . Vars::$ID . "' AND `contact_id`='" . Vars::$USER_ID . "' AND `time`<='" . (time() - 604800) . "'");
                    mysql_query("UPDATE `cms_mail_messages` SET
					`delete_out`='" . Vars::$USER_ID . "' WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='" . Vars::$ID . "' AND `time`<='" . (time() - 604800) . "'");
                    break;

                case 2:
                    mysql_query("UPDATE `cms_mail_messages` SET
					`delete_in`='" . Vars::$USER_ID . "' WHERE `user_id`='" . Vars::$ID . "' AND `contact_id`='" . Vars::$USER_ID . "'");
                    mysql_query("UPDATE `cms_mail_messages` SET
					`delete_out`='" . Vars::$USER_ID . "' WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='" . Vars::$ID . "'");
                    break;

                default:
                    mysql_query("UPDATE `cms_mail_messages` SET
					`delete_in`='" . Vars::$USER_ID . "' WHERE `user_id`='" . Vars::$ID . "' AND `contact_id`='" . Vars::$USER_ID . "' AND `time`<='" . (time() - 2592000) . "'");
                    mysql_query("UPDATE `cms_mail_messages` SET
					`delete_out`='" . Vars::$USER_ID . "' WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='" . Vars::$ID . "' AND `time`<='" . (time() - 2592000) . "'");
            }
            Header('Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . Vars::$ID);
            exit;
        }
        $tpl->urlSelect = Vars::$MODULE_URI . '?act=messages&amp;mod=cleaning&amp;id=' . Vars::$ID;
        $tpl->submit = lng('clear');
        $tpl->phdr = lng('cleaning');
        $tpl->token = mt_rand(100, 10000);
        $_SESSION['token_status'] = $tpl->token;
        $tpl->contents = $tpl->includeTpl('time');
    } else if (Vars::$ID && Vars::$MOD == 'delete') //Удаляем собщение
    {
        $q = mysql_query("SELECT * FROM `cms_mail_messages` WHERE (`user_id`='" . Vars::$USER_ID . "' OR `contact_id`='" .
            Vars::$USER_ID . "') AND `id`='" . Vars::$ID . "'");
        if (mysql_num_rows($q)) {
            $data = mysql_fetch_assoc($q);
            if (isset($_POST['submit']) && ValidMail::checkCSRF() === TRUE) {
                if ($data['user_id'] == Vars::$USER_ID) {
                    if ($data['delete_out'] != Vars::$USER_ID) {
                        mysql_query("UPDATE `cms_mail_messages` SET
						`delete_out`='" . Vars::$USER_ID . "' WHERE `id`='" . Vars::$ID . "'");
                    }
                }
                if ($data['contact_id'] == Vars::$USER_ID) {
                    if ($data['delete_in'] != Vars::$USER_ID) {
                        mysql_query("UPDATE `cms_mail_messages` SET
						`delete_in`='" . Vars::$USER_ID . "' WHERE `id`='" . Vars::$ID . "'");
                    }
                }
                if ($data['user_id'] == Vars::$USER_ID) {
                    Header('Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $data['contact_id']);
                    exit;
                } else {
                    Header('Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $data['user_id']);
                    exit;
                }
            } else {
                if ($data['user_id'] == Vars::$USER_ID)
                    $tpl->urlBack = Vars::$MODULE_URI . '?act=messages&id=' . $data['contact_id'];
                else
                    $tpl->urlBack = Vars::$MODULE_URI . '?act=messages&id=' . $data['user_id'];

                $tpl->urlSelect = Vars::$MODULE_URI . '?act=messages&amp;mod=delete&amp;id=' . Vars::
                $ID;
                $tpl->select = lng('confirm_removing');
                $tpl->submit = lng('delete');
                $tpl->phdr = lng('removing_message');
                $tpl->token = mt_rand(100, 10000);
                $_SESSION['token_status'] = $tpl->token;
                $tpl->contents = $tpl->includeTpl('select');
            }
        } else {
            $tpl->contents = lng('page_does_not_exist');
        }
    } else {
        //Добавляем сообщение в избранные
        if (Vars::$ID && Vars::$MOD == 'elected') {
            $q = mysql_query("SELECT * FROM `cms_mail_messages` WHERE (`user_id`='" . Vars::$USER_ID . "' OR `contact_id`='" .
                Vars::$USER_ID . "') AND `id`='" . Vars::$ID . "'");
            if (mysql_num_rows($q)) {
                $data = mysql_fetch_assoc($q);
                if ($data['user_id'] == Vars::$USER_ID) {
                    if ($data['elected_out'] != Vars::$USER_ID) {
                        mysql_query("UPDATE `cms_mail_messages` SET
						`elected_out`='" . Vars::$USER_ID . "' WHERE `id`='" . Vars::$ID . "'");
                    }
                }
                if ($data['contact_id'] == Vars::$USER_ID) {
                    if ($data['elected_in'] != Vars::$USER_ID) {
                        mysql_query("UPDATE `cms_mail_messages` SET
						`elected_in`='" . Vars::$USER_ID . "' WHERE `id`='" . Vars::$ID . "'");
                    }
                }
                if ($data['user_id'] == Vars::$USER_ID) {
                    Header('Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $data['contact_id']);
                    exit;
                } else {
                    Header('Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $data['user_id']);
                    exit;
                }
            } else {
                $tpl->contents = lng('page_does_not_exist');
            }
        } else {
            if ($addmail->validateForm() !== TRUE) {
                $tpl->text = Validate::checkout($add_message['text']);
                //Выводим на экран ошибку
                $tpl->error_add = Functions::displayError($addmail->error_log);
            }
            //$tpl->error = $addmail->error_test;
            //Считаем количество сообщений
            $tpl->login = $addmail->nickname;
            $tpl->ignor = Functions::checkIgnor(Vars::$ID) === TRUE ? '<div class="rmenu">' . lng('user_banned') . '</div>' : '';
            $tpl->maxsize = 1024 * Vars::$SYSTEM_SET['filesize'];
            $tpl->size = Vars::$SYSTEM_SET['filesize'];
            $tpl->token = mt_rand(100, 10000);
            $_SESSION['token_status'] = $tpl->token;

            $total = mysql_result(mysql_query("SELECT COUNT(*)
			FROM `cms_mail_messages`
			WHERE ((`user_id`='" . Vars::$USER_ID . "'
			AND `contact_id`='" . Vars::$ID . "')
			OR (`contact_id`='" . Vars::$USER_ID . "'
			AND `user_id`='" . Vars::$ID . "'))
			AND `delete_in`!='" . Vars::$USER_ID . "'
			AND `delete_out`!='" . Vars::$USER_ID . "'"), 0);
            if ($total) {
                //Формируем список сообщений
                $query = mysql_query("SELECT `cms_mail_messages`.*, `cms_mail_messages`.`id` as `mid`, `users`.*
				FROM `cms_mail_messages`
				LEFT JOIN `users` 
				ON `cms_mail_messages`.`user_id`=`users`.`id` 
				WHERE ((`cms_mail_messages`.`user_id`='" . Vars::$USER_ID . "'
				AND `cms_mail_messages`.`contact_id`='" . Vars::$ID . "')
				OR (`cms_mail_messages`.`contact_id`='" . Vars::$USER_ID . "'
				AND `cms_mail_messages`.`user_id`='" . Vars::$ID . "'))
				AND `cms_mail_messages`.`delete_in`!='" . Vars::$USER_ID . "'
				AND `cms_mail_messages`.`delete_out`!='" . Vars::$USER_ID . "'
				ORDER BY `cms_mail_messages`.`time` DESC" . Vars::db_pagination());

                $array = array();
                $i = 1;
                $mass_read = array();
                while ($row = mysql_fetch_assoc($query)) {
                    if ($row['read'] == 0 && $row['contact_id'] == Vars::$USER_ID)
                        $mass_read[] = $row['mid'];
                    $text = Validate::checkout($row['text'], 1, 1);
                    if (Vars::$USER_SET['smileys'])
                        $text = Functions::smileys($text, $row['rights'] >= 1 ? 1 : 0);
                    $array[] = array(
                        'id'        => $row['id'],
                        'mid'       => $row['mid'],
                        'icon'      => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' :
                            'w') . '.png', '', 'align="middle"'),
                        'list'      => (($i % 2) ? 'list1' : 'list2'),
                        'nickname'  => $row['nickname'],
                        'file'      => $row['filename'] ? Functions::getImage(UploadMail::fileicon($row['filename']),
                            '', 'style="margin: 0 0 -4px 0;"') . '&#160;<a href="' . Vars::
                        $MODULE_URI . '?act=load&amp;id=' . $row['mid'] . '">' . $row['filename'] .
                            '</a> (' . UploadMail::formatsize($row['filesize']) . ')(' . $row['filecount'] .
                            ')' : '',
                        'time'      => Functions::displayDate($row['time']),
                        'text'      => $text,
                        'read'      => $row['read'],
                        'user_id'   => $row['user_id'],
                        'url'       => (Vars::$MODULE_URI . '?act=messages&amp;id=' . $row['id']),
                        'online'    => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                            '<span class="green"> [ON]</span>'),
                        'elected'   => (($row['elected_in'] != Vars::$USER_ID && $row['elected_out'] !=
                            Vars::$USER_ID) ? TRUE : FALSE),
                        'selectBar' => ('[<span class="red">х</span>&#160;<a href="' . Vars::
                        $MODULE_URI . '?act=messages&amp;mod=delete&amp;id=' . $row['mid'] .
                            '">' . lng('delete') . '</a>] ' . (($row['elected_in'] !=
                            Vars::$USER_ID && $row['elected_out'] != Vars::$USER_ID) ? '[<a href="' .
                            Vars::$MODULE_URI . '?act=messages&amp;mod=elected&amp;id=' . $row['mid'] .
                            '">' . lng('in_elected') . '</a>]' : '')));
                    ++$i;
                }
                //Ставим метку о прочтении
                if ($mass_read) {
                    $result = implode(',', $mass_read);
                    mysql_query("UPDATE `cms_mail_messages` SET `read`='1' WHERE `contact_id`='" .
                        Vars::$USER_ID . "' AND `id` IN (" . $result . ")");
                }
                $tpl->query = $array;
                $tpl->total = $total;
                //Навигация
                $tpl->display_pagination = Functions::displayPagination(Vars::$MODULE_URI .
                    '?act=messages&amp;id=' . Vars::$ID . '&amp;', Vars::$START, $total, Vars::
                $USER_SET['page_size']);
                $tpl->url_type = Vars::$MODULE_URI . '?act=messages&amp;id=' . Vars::$ID;
                //Подключаем шаблон list.php
                $tpl->list = $tpl->includeTpl('list');
            } else {
                $tpl->list = '<div class="rmenu">' . lng('no_messages') . '</div>';
            }
            $tpl->contents = $tpl->includeTpl('messages');
        }
    }
}