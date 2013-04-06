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

if ($id) {
    //Проверка существования пользователя
    $reqs = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1;");
    if (mysql_num_rows($reqs) == 0) {
        $textl = $lng['mail'];
        require_once('../incfiles/head.php');
        echo functions::display_error($lng['error_user_not_exist']);
        require_once("../incfiles/end.php");
        exit;
    }
    $res = mysql_fetch_assoc($reqs);
    $out = '';
    $out .= '<div class="phdr"><b>' . $lng_mail['enterring_messages_from'] . ' ' . $res['name'] . '</b></div>';
    //Отображаем список входящих сообщений
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE `user_id`='$id' AND `from_id`='$user_id' AND `delete`!='$user_id' AND `sys`='0' AND `spam`='0';"), 0);
    if ($total) {
        if ($total > $kmess) $out .= '<div class="topmenu">' . functions::display_pagination('index.php?act=input&amp;', $start, $total, $kmess) . '</div>';
        $req = mysql_query("SELECT `cms_mail`.*, `cms_mail`.`id` as `mid`, `cms_mail`.`time` as `mtime`, `users`.* FROM `cms_mail` LEFT JOIN `users` ON `cms_mail`.`user_id`=`users`.`id` WHERE `cms_mail`.`user_id`='$id' AND `cms_mail`.`from_id`='$user_id' AND `cms_mail`.`delete`!='$user_id' AND `cms_mail`.`sys`='0' AND `cms_mail`.`spam`='0' ORDER BY `cms_mail`.`time` DESC LIMIT " . $start . "," . $kmess);
        $mass_read = array();
        for ($i = 0; ($row = mysql_fetch_assoc($req)) !== FALSE; ++$i) {
            $out .= $i % 2 ? '<div class="list1">' : '<div class="list2">';
            if ($row['read'] == 0 && $row['from_id'] == $user_id)
                $mass_read[] = $row['mid'];
            $vrp1 = date("d.m.Y / H:i", $row['mtime'] + $set_user['sdvig'] * 3600);
            $post = $row['text'];
            $post = functions::checkout($post, 1, 1);
            if ($set_user['smileys'])
                $post = functions::smileys($post, $row['rights'] >= 1 ? 1 : 0);
            if ($row['file_name'])
                $post .= '<div class="func">' . $lng_mail['file'] . ': <a href="index.php?act=load&amp;id=' . $row['mid'] . '">' . $row['file_name'] . '</a> (' . formatsize($row['size']) . ')(' . $row['count'] . ')</div>';
            $subtext = '<a href="index.php?act=delete&amp;id=' . $row['mid'] . '">' . $lng['delete'] . '</a>';
            $arg = array(
                'header' => '(' . functions::display_date($row['mtime']) . ')',
                'body'   => $post,
                'sub'    => $subtext
            );
            $out .= functions::display_user($row, $arg);
            $out .= '</div>';
        }
        if ($mass_read) {
            $result = implode(',', $mass_read);
            mysql_query("UPDATE `cms_mail` SET `read`='1' WHERE `from_id`='$user_id' AND `id` IN (" . $result . ")");
        }
    } else {
        $out .= '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
    }
    $out .= '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
    if ($total > $kmess) {
        $out .= '<div class="topmenu">' . functions::display_pagination('index.php?act=input&amp;id=' . $id . '&amp;', $start, $total, $kmess) . '</div>';
        $out .= '<p><form action="index.php" method="get">
			<input type="hidden" name="act" value="input"/>
			<input type="hidden" name="id" value="' . $id . '"/>
			<input type="text" name="page" size="2"/>
			<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
    }
    if (empty($ban['1']) && empty($ban['3'])) $out .= '<div class="menu"><a href="index.php?act=write&amp;id=' . $id . '">' . $lng_mail['send_message'] . '</a></div>';
    $out .= '<div class="menu"><a href="index.php?act=input">' . $lng_mail['input_messages'] . '</a></div>';
} else {
    $out .= '<div class="phdr"><b>' . $lng_mail['input_messages'] . '</b></div>';

    //Групируем по контактам
    //TODO: Разобраться, шозанах? :ахует:
    $count = mysql_result(mysql_query("
	  SELECT COUNT(*)
	  FROM `cms_mail`
	  LEFT JOIN `cms_contact`
	  ON `cms_mail`.`user_id`=`cms_contact`.`from_id`
	  AND `cms_contact`.`user_id`='$user_id'
	  WHERE `cms_mail`.`from_id`='$user_id'
	  AND `cms_mail`.`sys`='0' AND `cms_mail`.`delete`!='$user_id'
	  AND `cms_contact`.`ban`!='1' AND `cms_mail`.`spam`!='1'"), 0);

    $total = mysql_result(mysql_query("SELECT COUNT(*)
	  FROM (SELECT DISTINCT `cms_mail`.`user_id`
	  FROM `cms_mail`
	  LEFT JOIN `cms_contact`
	  ON `cms_mail`.`user_id`=`cms_contact`.`from_id`
	  AND `cms_contact`.`user_id`='$user_id'
	  WHERE `cms_mail`.`from_id`='$user_id'
	  AND `cms_mail`.`sys`='0' AND `cms_mail`.`delete`!='$user_id' AND `cms_contact`.`ban`!='1' AND `cms_mail`.`spam`!='1') a;"), 0);

    if ($total) {
        $req = mysql_query("SELECT `users`.* 
		FROM `cms_mail`
		LEFT JOIN `users` 
		ON `cms_mail`.`user_id`=`users`.`id`
		LEFT JOIN `cms_contact` 
		ON `cms_mail`.`user_id`=`cms_contact`.`from_id` 
		AND `cms_contact`.`user_id`='$user_id' 
		WHERE `cms_mail`.`from_id`='$user_id'
		AND `cms_mail`.`sys`='0'
		AND `cms_mail`.`spam`!='1'
		AND `cms_mail`.`delete`!='$user_id'
		AND `cms_contact`.`ban`!='1'
		GROUP BY `cms_mail`.`user_id`
		ORDER BY `cms_mail`.`time` DESC LIMIT " . $start . "," . $kmess);
        $i = 1;
        while (($row = mysql_fetch_assoc($req)) !== FALSE) {
            $out .= $i % 2 ? '<div class="list1">' : '<div class="list2">';
            $subtext = '<a href="index.php?act=input&amp;id=' . $row['id'] . '">' . $lng_mail['input'] . '</a> | <a href="index.php?act=write&amp;id=' . $row['id'] . '">' . $lng_mail['correspondence'] . '</a> | <a href="index.php?act=deluser&amp;id=' . $row['id'] . '">' . $lng['delete'] . '</a> | <a href="index.php?act=ignor&amp;id=' . $row['id'] . '&amp;add">' . $lng_mail['ban_contact'] . '</a>';
            $count_message = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE `user_id`='{$row['id']}' AND `from_id`='$user_id' AND `delete`!='$user_id' AND `sys`!='1' AND `spam`!='1';"), 0);
            $new_count_message = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE `cms_mail`.`user_id`='{$row['id']}' AND `cms_mail`.`from_id`='$user_id' AND `read`='0' AND `delete`!='$user_id' AND `sys`!='1' AND `spam`!='1';"), 0);
            $arg = array(
                'header' => '(' . $count_message . ($new_count_message ? '/<span class="red">+' . $new_count_message . '</span>' : '') . ')',
                'sub'    => $subtext
            );
            $out .= functions::display_user($row, $arg);

            $out .= '</div>';
            ++$i;
        }
    } else {
        $out .= '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
    }

    $out .= '<div class="phdr">' . $lng['total'] . ': ' . $count . '</div>';
    if ($total > $kmess) {
        $out .= '<div class="topmenu">' . functions::display_pagination('index.php?act=input&amp;', $start, $total, $kmess) . '</div>';
        $out .= '<p><form action="index.php" method="get">
			<input type="hidden" name="act" value="input"/>
			<input type="text" name="page" size="2"/>
			<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
    }
}

$textl = $lng['mail'];
require_once('../incfiles/head.php');
echo $out;
echo '<p><a href="../users/profile.php?act=office">' . $lng['personal'] . '</a></p>';