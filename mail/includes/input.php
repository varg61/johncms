<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

$textl = $lng['mail'];
require_once('../incfiles/head.php');

echo '<div class="phdr"><b>' . $lng_mail['input_messages'] . '</b></div>';
$count = mysql_result(mysql_query("
SELECT COUNT(*)
FROM `cms_mail`
LEFT JOIN `cms_contact`
ON `cms_mail`.`user_id`=`cms_contact`.`from_id`
AND `cms_contact`.`user_id`='$user_id'
WHERE `cms_mail`.`from_id`='$user_id'
AND `cms_mail`.`sys`='0' AND `cms_mail`.`delete`!='$user_id'
AND `cms_contact`.`ban`!='1' AND `cms_mail`.`spam`!='1'"), 0);

if ($count) {
    $req = mysql_query("SELECT `users`.*, `cms_mail`.`text`, `cms_mail`.`time`
		FROM `cms_mail`
		LEFT JOIN `users` ON `cms_mail`.`user_id`=`users`.`id`
		LEFT JOIN `cms_contact` ON `cms_mail`.`user_id`=`cms_contact`.`from_id`
		WHERE `cms_mail`.`from_id`='$user_id'
		AND `cms_contact`.`user_id`='$user_id'
		AND `cms_mail`.`sys`='0'
		AND `cms_mail`.`spam`!='1'
		AND `cms_mail`.`delete`!='$user_id'
		AND `cms_contact`.`ban`!='1'
		ORDER BY `cms_mail`.`id` DESC
		LIMIT " . $start . "," . $kmess);
    $i = 1;
    while ($row = mysql_fetch_assoc($req)) {
        echo $i % 2 ? '<div class="list1">' : '<div class="list2">';
        $subtext = '<a href="index.php?act=write&amp;id=' . $row['id'] . '">' . $lng_mail['correspondence'] . '</a> | <a href="index.php?act=deluser&amp;id=' . $row['id'] . '">' . $lng['delete'] . '</a> | <a href="index.php?act=ignor&amp;id=' . $row['id'] . '&amp;add">' . $lng_mail['ban_contact'] . '</a>';

        $arg = array(
            'header' => '<span class="gray">(' . functions::display_date($row['time']) . ')</span>',
            'body'   => $row['text'],
            'sub'    => $subtext,
            'iphide' => 1
        );

        core::$user_set['avatar'] = 0;
        echo functions::display_user($row, $arg);
        echo '</div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
}

echo '<div class="phdr">' . $lng['total'] . ': ' . $count . '</div>';
if ($count > $kmess) {
    echo '<div class="topmenu">' . functions::display_pagination('index.php?act=input&amp;', $start, $count, $kmess) . '</div>' .
        '<p><form action="index.php" method="get">
            <input type="hidden" name="act" value="input"/>
            <input type="text" name="page" size="2"/>
            <input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
}

echo '<p><a href="../users/profile.php?act=office">' . $lng['personal'] . '</a></p>';