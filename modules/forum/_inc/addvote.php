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
if (Vars::$USER_RIGHTS == 3 || Vars::$USER_RIGHTS >= 6) {
    $vote_name = isset($_POST['vote_name']) ? mb_substr(trim($_POST['vote_name']), 0, 50) : '';
    $vote_count = isset($_POST['vote_count']) ? abs(intval($_POST['vote_count'])) : 2;
    if ($vote_count > 20) $vote_count = 20;
    else if ($vote_count < 2) $vote_count = 2;
    $topic = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='t' AND `id` = " . Vars::$ID . " AND `edit` != '1'"), 0);
    $topic_vote = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type`='1' AND `topic` = " . Vars::$ID), 0);
    if ($topic_vote != 0 || $topic == 0) {
        echo Functions::displayError(__('error_wrong_data'), '<a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">' . __('back') . '</a>');
        exit;
    }
    if (isset($_POST['submit'])) {
        if (!empty($vote_name) && !empty($_POST[0]) && !empty($_POST[1])) {
            mysql_query("INSERT INTO `cms_forum_vote` SET
                `name`='" . mysql_real_escape_string($vote_name) . "',
                `time`='" . time() . "',
                `type` = '1',
                `topic` = " . Vars::$ID
            ) or die(mysql_error());
            mysql_query("UPDATE `forum` SET  `realid` = '1'  WHERE `id` = " . Vars::$ID);
            for ($vote = 0; $vote < $vote_count; $vote++) {
                $text = mb_substr(trim($_POST[$vote]), 0, 30);
                if (empty($text)) continue;
                mysql_query("INSERT INTO `cms_forum_vote` SET
                    `name`='" . mysql_real_escape_string($text) . "',
                    `type` = '2',
                    `topic` = " . Vars::$ID
                );
            }
            echo __('voting_added') . '<br /><a href="?id=' . Vars::$ID . '">' . __('continue') . '</a>';
        } else
            echo __('error_empty_fields') . '<br /><a href="?act=addvote&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>';
    } else {
        echo '<form action="' . Vars::$URI . '?act=addvote&amp;id=' . Vars::$ID . '" method="post">' .
             '<br />' . __('voting') . ':<br/>' .
             '<input type="text" size="20" maxlength="150" name="vote_name" value="' . Validate::checkout($vote_name) . '"/><br/>';
        if (isset($_POST['plus'])) ++$vote_count;
        elseif (isset($_POST['minus'])) --$vote_count;
        for ($i = 0; $i < $vote_count; $i++) {
            $answer[$i] = isset($_POST[$i]) ? Validate::checkout($_POST[$i]) : '';
            echo __('answer') . ' ' . ($i + 1) . '(max. 50): <br/><input type="text" name="' . $i . '" value="' . $answer[$i] . '"/><br/>';
        }
        echo '<input type="hidden" name="vote_count" value="' . $vote_count . '"/>';
        echo ($vote_count < 20) ? '<br/><input type="submit" name="plus" value="' . __('add_answer') . '"/>' : '';
        echo $vote_count > 2 ? '<input type="submit" name="minus" value="' . __('delete_last') . '"/><br/>' : '<br/>';
        echo '<p><input type="submit" name="submit" value="' . __('save') . '"/></p></form>';
        echo '<a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . __('back') . '</a>';
    }
} else {
    header('location: ../404.php');
}