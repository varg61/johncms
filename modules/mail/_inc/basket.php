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
defined('_IN_JOHNCMS_MAIL') or die('Error: restricted access');
if (!Vars::$USER_ID) {
    Header('Location: ' . Vars::$HOME_URL . '/404.php');
    exit;
}

$tpl->title = lng('mail') . ' | ' . lng('basket');
if (Vars::$ID) {
    if (Vars::$ID == Vars::$USER_ID) {
        $tpl->contents = Functions::displayError(lng('error_request') . '!', '<a href="' . Vars::
        $MODULE_URI . '">' . lng('contacts') . '</a>');
    } else
    {
        $q = mysql_query("SELECT `nickname` FROM `users` WHERE `id`='" . Vars::$ID . "' LIMIT 1");
        if (mysql_num_rows($q)) {
            $total = mysql_result(mysql_query("SELECT COUNT(*)
			FROM `cms_messages`
			WHERE ((`user_id`='" . Vars::$USER_ID . "'
			AND `contact_id`='" . Vars::$ID . "')
			OR (`contact_id`='" . Vars::$USER_ID . "'
			AND `user_id`='" . Vars::$ID . "'))
			AND (`delete_in`='" . Vars::$USER_ID . "'
			OR `delete_out`='" . Vars::$USER_ID . "') AND `delete`!='" . Vars::$USER_ID . "'"), 0);
            if ($total) {
                $query = mysql_query("SELECT `cms_messages`.*, `cms_messages`.`id` as `mid`, `users`.*
				FROM `cms_messages`
				LEFT JOIN `users` 
				ON `cms_messages`.`user_id`=`users`.`id` 
				WHERE ((`cms_messages`.`user_id`='" . Vars::$USER_ID . "'
				AND `cms_messages`.`contact_id`='" . Vars::$ID . "')
				OR (`cms_messages`.`contact_id`='" . Vars::$USER_ID . "'
				AND `cms_messages`.`user_id`='" . Vars::$ID . "'))
				AND (`cms_messages`.`delete_in`='" . Vars::$USER_ID . "'
				OR `cms_messages`.`delete_out`='" . Vars::$USER_ID . "')
				AND `delete`!='" . Vars::$USER_ID . "'
				ORDER BY `cms_messages`.`time` DESC" . Vars::db_pagination());
                $array = array();

                $i = 1;
                while ($row = mysql_fetch_assoc($query))
                {
                    $text = Validate::filterString($row['text'], 1, 1);
                    if (Vars::$USER_SET['smileys'])
                        $text = Functions::smileys($text, $row['rights'] >= 1 ? 1 : 0);
                    $array[] = array(
                        'id'        => $row['id'],
                        'mid'       => $row['mid'],
                        'icon'      => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' : 'w') . '.png', '', 'align="middle"'),
                        'list'      => (($i % 2) ? 'list1' : 'list2'),
                        'nickname'  => $row['nickname'],
                        'file'      => $row['filename'] ? '<a href="' . Vars::$MODULE_URI . '?act=load&amp;id=' . $row['mid'] . '">' . $row['filename'] . '</a> (' . Mail::formatsize($row['filesize']) . ')(' . $row['filecount'] . ')' : '',
                        'time'      => Functions::displayDate($row['time']),
                        'text'      => $text,
                        'urlDelete' => Vars::$MODULE_URI . '?act=messages&amp;mod=delete&amp;id=' . $row['mid'],
                        'url'       => (Vars::$MODULE_URI . '?act=messages&amp;id=' . $row['id']),
                        'online'    => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>'),
                        'elected'   => (($row['elected_in'] != Vars::$USER_ID && $row['elected_out'] != Vars::$USER_ID) ? true : false),
                        'selectBar' => '[<span class="green">&laquo;</span> <a href="' . Vars::$MODULE_URI .
                            '?act=restore&amp;id=' . $row['mid'] . '">' . lng('rest') . '</a>] [<span class="red">х</span> <a href="' .
                            Vars::$MODULE_URI . '?act=delete&amp;id=' . $row['mid'] . '">' . lng('delete') .
                            '</a>]');
                    ++$i;
                }

                $tpl->query = $array;
                $tpl->titleTest = '<div class="phdr"><h3>' . lng('deleted_message') . '</h3></div>';
                $tpl->urlTest = '<div class="menu"><a href="' . Vars::$URI . '">' . lng('contacts') .
                    '</a></div>';
                $tpl->total = $total;
                $tpl->display_pagination = Functions::displayPagination(Vars::$MODULE_URI . '?act=basket&amp;id=' .
                    Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']);
                $tpl->contents = $tpl->includeTpl('mail/list');
            } else
            {
                Header('Location: ' . Vars::$MODULE_URI . '?act=basket');
                exit;
            }
        } else
        {
            $tpl->contents = Functions::displayError(lng('user_does_not_exist') . '!', '<a href="' .
                Vars::$MODULE_URI . '">' . lng('contacts') . '</a>');
        }
    }
} else
{
    $total = mysql_result(mysql_query("SELECT COUNT(*)
	 FROM (SELECT
	 DISTINCT `cms_contacts`.`contact_id`
	 FROM `cms_contacts`
	 LEFT JOIN `cms_messages`
	 ON `cms_contacts`.`contact_id`=`cms_messages`.`user_id`
	 OR `cms_contacts`.`contact_id`=`cms_messages`.`contact_id`
	 WHERE ((`cms_contacts`.`contact_id`!='" . Vars::$USER_ID . "'
	 AND (`cms_messages`.`contact_id`!='" . Vars::$USER_ID . "'
	 OR `cms_messages`.`user_id`!='" . Vars::$USER_ID . "')
	 AND (`cms_messages`.`delete_out`='" . Vars::$USER_ID . "'
	 OR `cms_messages`.`delete_in`='" . Vars::$USER_ID . "'))
	 OR (`cms_contacts`.`delete`='1'
	 AND `cms_contacts`.`user_id`='" . Vars::$USER_ID . "'))
	 AND `cms_messages`.`delete`!='" . Vars::$USER_ID . "') a"), 0);
    $tpl->total = $total;
    if ($total) {
        if (isset($_POST['restore'])) {
            if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
                Mail::mailSelectContacts($_POST['delch'], 'restore');
            }
            Header('Location: ' . Vars::$MODULE_URI . '?act=basket');
            exit;
        }
        if (isset($_POST['delete'])) {
            if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
                Mail::mailSelectContacts($_POST['delch'], 'drop');
            }
            Header('Location: ' . Vars::$MODULE_URI . '?act=basket');
            exit;
        }
        if (isset($_POST['clear'])) {
            Mail::mailSelectContacts(array(), 'clear');
            Header('Location: ' . Vars::$MODULE_URI . '?act=basket');
            exit;
        }
        $query = mysql_query("SELECT * FROM `cms_contacts`
		LEFT JOIN `cms_messages`
		ON `cms_contacts`.`contact_id`=`cms_messages`.`user_id`
		OR `cms_contacts`.`contact_id`=`cms_messages`.`contact_id`
		LEFT JOIN `users`
		ON `cms_contacts`.`contact_id`=`users`.`id`
		WHERE ((`cms_contacts`.`contact_id`!='" . Vars::$USER_ID . "'
		AND (`cms_messages`.`contact_id`!='" . Vars::$USER_ID . "'
		OR `cms_messages`.`user_id`!='" . Vars::$USER_ID . "')
		AND (`cms_messages`.`delete_out`='" . Vars::$USER_ID . "'
		OR `cms_messages`.`delete_in`='" . Vars::$USER_ID . "'))
		OR (`cms_contacts`.`delete`='1' AND `cms_contacts`.`user_id`='" . Vars::$USER_ID . "'))
		AND `cms_messages`.`delete`!='" . Vars::$USER_ID . "'
		GROUP BY `cms_contacts`.`contact_id`
		ORDER BY `cms_contacts`.`time` DESC" . Vars::db_pagination());
        $array = array();
        $i = 1;
        while ($row = mysql_fetch_assoc($query))
        {
            $array[] = array(
                'id'        => $row['id'],
                'icon'      => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' : 'w') . '.png', '', 'align="middle"'),
                'list'      => (($i % 2) ? 'list1' : 'list2'),
                'nickname'  => $row['nickname'],
                'count_in'  => mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_messages` WHERE `user_id`='{$row['id']}' AND `delete_in`='" . Vars::$USER_ID . "' AND `delete`!='" . Vars::$USER_ID . "'"), 0),
                'count_out' => mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_messages` WHERE `contact_id`='{$row['id']}' AND `delete_out`='" . Vars::$USER_ID . "' AND `delete`!='" . Vars::$USER_ID . "'"), 0),
                'count_new' => '',
                'url'       => (Vars::$MODULE_URI . '?act=basket&amp;id=' . $row['id']),
                'online'    => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' :
                    '<span class="green"> [ON]</span>'));
            ++$i;
        }
        $tpl->display_pagination = Functions::displayPagination(Vars::$MODULE_URI . '?act=basket&amp;',
            Vars::$START, $total, Vars::$USER_SET['page_size']);
        $tpl->query = $array;
        $tpl->contacts = $tpl->includeTpl('contacts');
    } else
    {
        $tpl->contacts = '<div class="rmenu">' . lng('empty_basket') . '!</div>';
    }
    $tpl->contents = $tpl->includeTpl('basket');
}
