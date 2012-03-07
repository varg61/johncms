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
define ('_IN_JOHNCMS_MAIL', 1);

if (!Vars::$USER_ID) {
    Header('Location: ' . Vars::$HOME_URL . '/404');
    exit;
}

define('MAILDIR', 'mail'); //Папка с модулем
define('MAILPATH', MODPATH . MAILDIR . DIRECTORY_SEPARATOR);

if (isset($_SESSION['ref']))
    unset($_SESSION['ref']);

//Подключаем шаблонизатор
$tpl = Template::getInstance();
require (MAILPATH . '_inc/class.mail.php');

//Заголовок
$tpl->title = lng('mail');

//Проверяем и подключаем нужные файлы модуля
$connect = Mail::mailConnect();
if (Vars::$ACT && ($key = array_search(Vars::$ACT, $connect)) !== false && file_exists(MAILPATH . '_inc' . DIRECTORY_SEPARATOR . $connect[$key] . '.php')) {
    require (MAILPATH . '_inc' . DIRECTORY_SEPARATOR . $connect[$key] . '.php');
} else {
    $total = mysql_result(mysql_query("SELECT COUNT(*)
	FROM `cms_contacts`
	WHERE `user_id`='" . Vars::$USER_ID . "' AND `delete`='0' AND `banned`='0' AND `archive`='0'"), 0);
    $tpl->contacts = '';
    $tpl->total = $total;
    if ($total) {
        if (isset($_POST['delete'])) {
            if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
                Mail::mailSelectContacts($_POST['delch'], 'delete');
            }
            Header('Location: ' . Vars::$MODULE_URI);
            exit;
        } else
            if (isset($_POST['archive'])) {
                if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
                    Mail::mailSelectContacts($_POST['delch'], 'archive');
                }
                Header('Location: ' . Vars::$MODULE_URI);
                exit;
            }
        $query = mysql_query("SELECT *
		    FROM `cms_contacts`
		    LEFT JOIN `users`
		    ON `cms_contacts`.`contact_id`=`users`.`id`
		    WHERE `cms_contacts`.`user_id`='" . Vars::$USER_ID . "'
		    AND `cms_contacts`.`archive`='0'
		    AND `cms_contacts`.`banned`='0'
		    AND `cms_contacts`.`delete`='0'
		    AND `users`.`id` IS NOT NULL
		    ORDER BY `cms_contacts`.`time` DESC" . Vars::db_pagination()
        );
        $array = array();
        $i = 1;
        while ($row = mysql_fetch_assoc($query))
        {
            $array[] = array(
                'id' => $row['id'],
                'icon' => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' : 'w') . '.png', '', 'style="margin: 0 0 -3px 0;"'),
                'list' => (($i % 2) ? 'list1' : 'list2'),
                'nickname' => $row['nickname'],
                'count_in' => $row['count_in'],
                'count_out' => $row['count_out'],
                'count_new' => Mail::countNew($row['contact_id']),
                'url' => (Vars::$MODULE_URI . '?act=messages&amp;id=' . $row['id']),
                'online' => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>'));
            ++$i;
        }
        $tpl->display_pagination = Functions::displayPagination(Vars::$MODULE_URI . '?', Vars::$START,
            $total, Vars::$USER_SET['page_size']);
        $tpl->query = $array;
    }

    //Счетчики
    $tpl->systems = Mail::counter('systems');
    $tpl->elected = Mail::counter('elected');
    $tpl->archive = Mail::counter('archive');
    $tpl->delete = Mail::counter('delete');
    $tpl->banned = Mail::counter('banned');
    $tpl->files = Mail::counter('files');

    //Подключаем шаблон модуля
    $tpl->contents = $tpl->includeTpl('_index');
}
