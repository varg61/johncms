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
$tpl->title = lng('mail') . ' | ' . lng('archive');
$total = mysql_result(mysql_query("SELECT COUNT(*)
 FROM `cms_contacts`
 WHERE `archive`='1'
 AND `banned`='0' 
 AND `user_id`='" . Vars::$USER_ID . "'
 AND `cms_contacts`.`delete`='0';"), 0);
$tpl->total = $total;
if ($total) {
    if (isset($_POST['delete'])) {
        if (!empty($_POST['delch']) && is_array($_POST['delch'])) {
            Mail::mailSelectContacts($_POST['delch'], 'delete');
        }
        Header('Location: ' . Vars::$MODULE_URI . '?act=archive');
        exit;
    }
    $query = mysql_query("SELECT *
	FROM `cms_contacts`
	LEFT JOIN `users`
	ON `cms_contacts`.`contact_id`=`users`.`id`
	WHERE `cms_contacts`.`user_id`='" . Vars::$USER_ID . "'
	AND `cms_contacts`.`archive`='1'
    AND `cms_contacts`.`banned`='0'
	AND `cms_contacts`.`delete`='0'
	ORDER BY `cms_contacts`.`time` DESC" . Vars::db_pagination());
    $array = array();
    $i = 1;
    while ($row = mysql_fetch_assoc($query))
    {
        $array[] = array(
            'id'        => $row['id'],
            'icon'      => Functions::getImage('usr_' . ($row['sex'] == 'm' ? 'm' : 'w') . '.png', ''),
            'list'      => (($i % 2) ? 'list1' : 'list2'),
            'nickname'  => $row['nickname'],
            'count_in'  => $row['count_in'],
            'count_out' => $row['count_out'],
            'count_new' => Mail::countNew($row['contact_id']),
            'url'       => (Vars::$MODULE_URI . '?act=messages&amp;id=' . $row['id']),
            'online'    => (time() > $row['last_visit'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>'));
        ++$i;
    }
    $tpl->display_pagination = Functions::displayPagination(Vars::$MODULE_URI . '?act=archive&amp;',
        Vars::$START, $total, Vars::$USER_SET['page_size']);
    $tpl->query = $array;
    $tpl->contacts = $tpl->includeTpl('contacts');
} else
{
    $tpl->contacts = '<div class="rmenu">' . lng('no_archive') . '!</div>';
}
$tpl->contents = $tpl->includeTpl('archive');
