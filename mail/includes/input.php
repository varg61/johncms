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

$total = mysql_result(mysql_query("SELECT COUNT(*)
	  FROM (SELECT DISTINCT `cms_mail`.`user_id`
	  FROM `cms_mail`
	  LEFT JOIN `cms_contact`
	  ON `cms_mail`.`user_id`=`cms_contact`.`from_id`
	  AND `cms_contact`.`user_id`='$user_id'
	  WHERE `cms_mail`.`from_id`='$user_id'
	  AND `cms_mail`.`sys`='0'
	  AND `cms_mail`.`delete`!='$user_id'
	  AND `cms_contact`.`ban`!='1') `tmp`"), 0);

if ($total) {
    $req = mysql_query("SELECT `users`.*, MAX(`cms_mail`.`time`) AS `time`
		FROM `cms_mail`
		LEFT JOIN `users`
		ON `cms_mail`.`user_id`=`users`.`id`
		LEFT JOIN `cms_contact`
		ON `cms_mail`.`user_id`=`cms_contact`.`from_id`
		WHERE `cms_mail`.`from_id`='$user_id'
		AND `cms_contact`.`user_id`='$user_id'
		AND `cms_mail`.`sys`='0'
		AND `cms_mail`.`delete`!='$user_id'
		AND `cms_contact`.`ban`!='1'
		GROUP BY `cms_mail`.`user_id`
		ORDER BY MAX(`cms_mail`.`time`) DESC LIMIT " . $start . "," . $kmess);

    $i = 1;
    while (($row = mysql_fetch_assoc($req)) !== FALSE) {
        $count_message = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail`
            WHERE `user_id`='{$row['id']}'
            AND `from_id`='$user_id'
            AND `delete`!='$user_id'
            AND `sys`!='1'
        "), 0);

        $last_msg = mysql_fetch_assoc(mysql_query("SELECT * FROM `cms_mail` WHERE `from_id`='$user_id' AND `user_id` = '{$row['id']}' AND `delete` != '$user_id' ORDER BY `id` DESC LIMIT 1"));
        if (mb_strlen($last_msg['text']) > 500) {
            $text = mb_substr($last_msg['text'], 0, 500);
            $text = functions::checkout($text, 1, 1);
            if ($set_user['smileys']) {
                $text = functions::smileys($text, $res['rights'] ? 1 : 0);
            }
            $text = bbcode::notags($text);
            $text .= '...<a href="index.php?act=write&amp;id=' . $row['id'] . '">' . $lng['continue'] . ' &gt;&gt;</a>';
        } else {
            // Или, обрабатываем тэги и выводим весь текст
            $text = functions::checkout($last_msg['text'], 1, 1);
            if ($set_user['smileys'])
                $text = functions::smileys($text, $res['rights'] ? 1 : 0);
        }

        $arg = array(
            'header' => '(' . $count_message . ') <a href="index.php?act=ignor&amp;id=' . $row['id'] . '&amp;add">[i]</a>&#160;<span class="red"><a href="index.php?act=deluser&amp;id=' . $row['id'] . '">[x]</a></span>',
            'body'   => '<div class="sub" style="font-size: small"><span class="gray">(' . functions::display_date($last_msg['time']) . ')</span><br/>' . $text . '</div>',
            'sub'    => '<p><form action="index.php?act=write&amp;id=' . $row['id'] . '" method="post"><input type="submit" value="' . $lng_mail['correspondence'] . '"/></form></p>',
            'iphide' => 1
        );

        if (!$last_msg['read']) {
            echo '<div class="gmenu">';
        } else {
            echo $i % 2 ? '<div class="list1">' : '<div class="list2">';
        }
        echo functions::display_user($row, $arg);
        echo '</div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
}

echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
if ($total > $kmess) {
    echo '<div class="topmenu">' . functions::display_pagination('index.php?act=input&amp;', $start, $total, $kmess) . '</div>' .
        '<p><form action="index.php" method="get">
                <input type="hidden" name="act" value="input"/>
                <input type="text" name="page" size="2"/>
                <input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
}

echo '<p><a href="../users/profile.php?act=office">' . $lng['personal'] . '</a></p>';