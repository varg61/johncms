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

if (!Vars::$ID) {
    echo "";
    exit;
}

//TODO: Переделать на новый антиспам
// Проверка на спам
$old = (Vars::$USER_RIGHTS > 0) ? 5 : 60;
if (Vars::$USER_DATA['lastpost'] > (time() - $old)) {
    echo '<p>' . __('error_flood') . ' ' . $old . ' ' . __('sec') . '<br/><br/><a href ="' . Vars::$URI . '?id=' . Vars::$ID . '">' . __('back') . '</a></p>';
    exit;
}

$typ = mysql_query("select * from `lib` where `id` = " . Vars::$ID);
$ms = mysql_fetch_array($typ);
if (Vars::$ID != 0 && $ms['type'] != "cat") {
    echo "";
    exit;
}
if ($ms['ip'] == 0) {
    if ((Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) || ($ms['soft'] == 1 && !empty($_SESSION['uid']))) {
        if (isset($_POST['submit'])) {
            if (empty($_POST['name'])) {
                echo __('error_empty_title') . "<br/><a href='" . Vars::$URI . "?act=write&amp;id=" . Vars::$ID . "'>" . __('repeat') . "</a><br/>";
                exit;
            }
            if (empty($_POST['text'])) {
                echo __('error_empty_text') . "<br/><a href='" . Vars::$URI . "?act=write&amp;id=" . Vars::$ID . "'>" . __('repeat') . "</a><br/>";
                exit;
            }
            $text = trim($_POST['text']);
            if (!empty($_POST['anons'])) {
                $anons = mb_substr(trim($_POST['anons']), 0, 100);
            } else {
                $anons = mb_substr($text, 0, 100);
            }
            if (Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) {
                $md = 1;
            } else {
                $md = 0;
            }
            mysql_query("INSERT INTO `lib` SET
                `refid` = " . Vars::$ID . ",
                `time` = " . time() . ",
                `type` = 'bk',
                `name` = '" . mysql_real_escape_string(mb_substr(trim($_POST['name']), 0, 100)) . "',
                `announce` = '" . mysql_real_escape_string($anons) . "',
                `text` = '" . mysql_real_escape_string($text) . "',
                `avtor` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                `ip` = " . Vars::$IP . ",
                `soft` = '" . mysql_real_escape_string(Vars::$USER_AGENT) . "',
                `moder` = '$md'
            ");
            $cid = mysql_insert_id();
            if ($md == 1) {
                echo '<p>' . __('article_added') . '</p>';
            } else {
                echo '<p>' . __('article_added') . '<br/>' . __('article_added_thanks') . '</p>';
            }
            //TODO: Доработать!
            //mysql_query("UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = " . Vars::$user_id);
            echo '<p><a href="' . Vars::$URI . '?id=' . $cid . '">' . __('to_article') . '</a></p>';
        } else {
            echo '<h3>' . __('write_article') . '</h3><form action="' . Vars::$URI . '?act=write&amp;id=' . Vars::$ID . '" method="post">';
            echo '<p>' . __('title') . ' (max. 100):<br/><input type="text" name="name"/></p>';
            echo '<p>' . __('announce') . ' (max. 100):<br/><input type="text" name="anons"/></p>';
            echo '<p>' . __('text') . ':<br/><textarea name="text" rows="' . Vars::$USER_SET['field_h'] . '"></textarea></p>';
            echo '<p><input type="submit" name="submit" value="' . __('save') . '"/></p>';
            echo '</form><p><a href ="' . Vars::$URI . '?id=' . Vars::$ID . '">' . __('back') . '</a></p>';
        }
    } else {
        header("location: " . Vars::$URI);
    }
}
echo "<a href='" . Vars::$URI . "'>" . __('to_library') . "</a><br/>";