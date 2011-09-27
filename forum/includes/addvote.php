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
if ($rights == 3 || $rights >= 6) {
    $vote_name = isset($_POST['vote_name']) ? mb_substr(trim($_POST['vote_name']), 0, 50) : '';
    $vote_count = isset($_POST['vote_count']) ? abs(intval($_POST['vote_count'])) : 2;
    if ($vote_count > 20) $vote_count = 20;
    else if ($vote_count < 2) $vote_count = 2;
    $topic = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='t' AND `id`='$id' AND `edit` != '1'"), 0);
    $topic_vote = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type`='1' AND `topic`='$id'"), 0);
    require_once('../incfiles/head.php');
    if ($topic_vote != 0 || $topic == 0) {
        echo functions::display_error($lng['error_wrong_data'], '<a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">' . $lng['back'] . '</a>');
        require('../incfiles/end.php');
        exit;
    }
    if (isset($_POST['submit'])) {
        if (!empty($vote_name) && !empty($_POST[0]) && !empty($_POST[1])) {
            mysql_query("INSERT INTO `cms_forum_vote` SET
                `name`='" . mysql_real_escape_string($vote_name) . "',
                `time`='" . time() . "',
                `type` = '1',
                `topic`='$id'
            ");
            mysql_query("UPDATE `forum` SET  `realid` = '1'  WHERE `id` = '$id'");
            for ($vote = 0; $vote < $vote_count; $vote++) {
                $text = mb_substr(trim($_POST[$vote]), 0, 30);
                if (empty($text)) continue;
                mysql_query("INSERT INTO `cms_forum_vote` SET
                    `name`='" . mysql_real_escape_string($text) . "',
                    `type` = '2',
                    `topic`='$id'
                ");
            }
            echo $lng_forum['voting_added'] . '<br /><a href="?id=' . $id . '">' . $lng['continue'] . '</a>';
        } else
            echo $lng['error_empty_fields'] . '<br /><a href="?act=addvote&amp;id=' . $id . '">' . $lng['repeat'] . '</a>';
    } else {
        echo '<form action="index.php?act=addvote&amp;id=' . $id . '" method="post">' .
             '<br />' . $lng_forum['voting'] . ':<br/>' .
             '<input type="text" size="20" maxlength="150" name="vote_name" value="' . functions::checkout($vote_name) . '"/><br/>';
        if (isset($_POST['plus'])) ++$vote_count;
        elseif (isset($_POST['minus'])) --$vote_count;
        for ($i = 0; $i < $vote_count; $i++) {
            $answer[$i] = isset($_POST[$i]) ? functions::checkout($_POST[$i]) : '';
            echo $lng_forum['answer'] . ' ' . ($i + 1) . '(max. 50): <br/><input type="text" name="' . $i . '" value="' . $answer[$i] . '"/><br/>';
        }
        echo '<input type="hidden" name="vote_count" value="' . $vote_count . '"/>';
        echo ($vote_count < 20) ? '<br/><input type="submit" name="plus" value="' . $lng_forum['add_answer'] . '"/>' : '';
        echo $vote_count > 2 ? '<input type="submit" name="minus" value="' . $lng_forum['delete_last'] . '"/><br/>' : '<br/>';
        echo '<p><input type="submit" name="submit" value="' . $lng['save'] . '"/></p></form>';
        echo '<a href="index.php?id=' . $id . '">' . $lng['back'] . '</a>';
    }
} else {
    header('location: ../index.php?err');
}