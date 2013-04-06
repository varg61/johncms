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
        $textl = $textl = $lng['mail'];
        require_once('../incfiles/head.php');
        echo functions::display_error($lng['error_user_not_exist']);
        require_once("../incfiles/end.php");
        exit;
    }
    $res = mysql_fetch_assoc($reqs);
    $textl = $lng['mail'];
    require_once('../incfiles/head.php');
    echo '<div class="phdr"><b>' . $lng_mail['sent_messages_for'] . ' ' . $res['name'] . '</b></div>';
    //Отображаем список исходящих сообщений
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE `user_id`='$user_id' AND `from_id`='$id' AND `delete`!='$user_id';"), 0);
    if ($total) {
        if($total > $kmess) echo '<div class="topmenu">' . functions::display_pagination('index.php?act=output&amp;', $start, $total, $kmess) . '</div>';
		$req = mysql_query("SELECT `cms_mail`.*, `cms_mail`.`id` as `mid`, `cms_mail`.`time` as `mtime`, `users`.* FROM `cms_mail` LEFT JOIN `users` ON `cms_mail`.`user_id`=`users`.`id` WHERE `cms_mail`.`user_id`='$user_id' AND `cms_mail`.`from_id`='$id' AND `cms_mail`.`delete`!='$user_id' ORDER BY `cms_mail`.`time` DESC LIMIT " . $start . "," . $kmess);
        for ($i = 0; ($row = mysql_fetch_assoc($req)) !== false; ++$i) {
            echo $i % 2 ? '<div class="list1">' : '<div class="list2">';
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
			'body' => $post,
			'sub' => $subtext
			);
			echo functions::display_user($row, $arg);
			echo '</div>';
        }
    } else {
        echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
    }
    echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
    if ($total > $kmess) {
        echo '<div class="topmenu">' . functions::display_pagination('index.php?act=output&amp;id=' . $id . '&amp;', $start, $total, $kmess) . '</div>';
        echo '<p><form action="index.php" method="get">
			<input type="hidden" name="act" value="output"/>
			<input type="hidden" name="id" value="' . $id . '"/>
			<input type="text" name="page" size="2"/>
			<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
    }
    if (empty($ban['1']) && empty($ban['3']))echo '<div class="menu"><a href="index.php?act=write&amp;id=' . $id . '">' . $lng_mail['send_message'] . '</a></div>';
    echo '<p><a href="index.php?act=output">' . $lng_mail['sent_messages'] . '</a></p>';
} else {
    $textl = $lng['mail'];
    require_once('../incfiles/head.php');
    echo '<div class="phdr"><b>' . $lng_mail['sent_messages'] . '</b></div>';
    //Групируем по контактам
    $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` LEFT JOIN `cms_contact` ON `cms_mail`.`from_id`=`cms_contact`.`from_id` AND `cms_contact`.`user_id`='$user_id' WHERE `cms_mail`.`user_id`='$user_id' AND `cms_mail`.`delete`!='$user_id' AND `cms_mail`.`sys`='0' AND `cms_contact`.`ban`!='1'"), 0);
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM (SELECT DISTINCT `cms_mail`.`from_id` FROM `cms_mail` LEFT JOIN `cms_contact` ON `cms_mail`.`from_id`=`cms_contact`.`from_id` WHERE `cms_mail`.`user_id`='$user_id' AND `cms_mail`.`delete`!='$user_id' AND `cms_mail`.`sys`='0' AND `cms_contact`.`ban`!='1') a;"), 0);
	if ($total) {
		$req = mysql_query("SELECT `users`.* FROM `cms_mail`
			LEFT JOIN `users` ON `cms_mail`.`from_id`=`users`.`id`
			LEFT JOIN `cms_contact` ON `cms_mail`.`from_id`=`cms_contact`.`from_id` AND `cms_contact`.`user_id`='$user_id'
			WHERE `cms_mail`.`user_id`='" . $user_id . "' AND `cms_mail`.`delete`!='$user_id' AND `cms_mail`.`sys`='0' AND `cms_contact`.`ban`!='1' GROUP BY `cms_mail`.`from_id` ORDER BY `cms_mail`.`time` DESC LIMIT " . $start . "," . $kmess
		);
		for ($i = 0; ($row = mysql_fetch_assoc($req)) !== false; ++$i) {
            echo $i % 2 ? '<div class="list1">' : '<div class="list2">';
            $subtext = '<a href="index.php?act=output&amp;id=' . $row['id'] . '">' . $lng_mail['sent'] . '</a> | <a href="index.php?act=write&amp;id=' . $row['id'] . '">' . $lng_mail['correspondence'] . '</a> | <a href="index.php?act=deluser&amp;id=' . $row['id'] . '">' . $lng['delete'] . '</a> | <a href="index.php?act=ignor&amp;id=' . $row['id'] . '&amp;add">' . $lng_mail['ban_contact'] . '</a>';
            $count_message = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE `user_id`='$user_id' AND `from_id`='{$row['id']}' AND `delete`!='$user_id';"), 0);
            $new_count_message = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE `cms_mail`.`user_id`='$user_id' AND `cms_mail`.`from_id`='{$row['id']}' AND `read`='0' AND `delete`!='$user_id';"), 0);
            $arg = array(
			'header' => '(' . $count_message . ($new_count_message ? '/<span class="red">+' . $new_count_message . '</span>' : '') . ')',
			'sub' => $subtext
			);
			echo functions::display_user($row, $arg);
            echo '</div>';
        }
    } else {
        echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
    }

    echo '<div class="phdr">' . $lng['total'] . ': ' . $count . '</div>';
    if ($total > $kmess) {
        echo '<div class="topmenu">' . functions::display_pagination('index.php?act=output&amp;', $start, $total, $kmess) . '</div>';
        echo '<p><form action="index.php" method="get">
			<input type="hidden" name="act" value="output"/>
			<input type="text" name="page" size="2"/>
			<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
    }
}
echo '<p><a href="../users/profile.php?act=office">' . $lng['personal'] . '</a></p>';