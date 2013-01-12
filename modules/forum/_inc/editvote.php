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
    $url = Router::getUri(2);
    $topic_vote = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type`='1' AND `topic`=" . Vars::$ID), 0);
    if ($topic_vote == 0) {
        echo Functions::displayError(__('error_wrong_data'));
        exit;
    }
    if (isset($_GET['delvote']) && !empty($_GET['vote'])) {
        $vote = abs(intval($_GET['vote']));
        $totalvote = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type` = '2' AND `id` = '$vote' AND `topic` = " . Vars::$ID), 0);
        $countvote = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type` = '2' AND `topic` = " . Vars::$ID), 0);
        if ($countvote <= 2)
            header('location: ?act=editvote&id=' . Vars::$ID . '');
        if ($totalvote != 0) {
            if (isset($_GET['yes'])) {
                mysql_query("DELETE FROM `cms_forum_vote` WHERE `id` = '$vote'");
                $countus = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote_users` WHERE `vote` = '$vote' AND `topic` = " . Vars::$ID), 0);
                $topic_vote = mysql_fetch_array(mysql_query("SELECT `count` FROM `cms_forum_vote` WHERE `type` = '1' AND `topic` = " . Vars::$ID . " LIMIT 1"));
                $totalcount = $topic_vote['count'] - $countus;
                mysql_query("UPDATE `cms_forum_vote` SET  `count` = '$totalcount'   WHERE `type` = '1' AND `topic` = " . Vars::$ID);
                mysql_query("DELETE FROM `cms_forum_vote_users` WHERE `vote` = '$vote'");
                header('location: ?act=editvote&id=' . Vars::$ID . '');
            } else {
                echo '<div class="rmenu"><p>' . __('voting_variant_warning') . '<br />' .
                     '<a href="' . $url . '?act=editvote&amp;id=' . Vars::$ID . '&amp;vote=' . $vote . '&amp;delvote&amp;yes">' . __('delete') . '</a><br />' .
                     '<a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">' . __('cancel') . '</a></p></div>';
            }
        } else {
            header('location: ?act=editvote&id=' . Vars::$ID . '');
        }
    } else if (isset($_POST['submit'])) {
        $vote_name = mb_substr(trim($_POST['name_vote']), 0, 50);
        if (!empty($vote_name))
            mysql_query("UPDATE `cms_forum_vote` SET  `name` = '" . mysql_real_escape_string($vote_name) . "'  WHERE `topic` = " . Vars::$ID . " AND `type` = '1'");
        $vote_result = mysql_query("SELECT `id` FROM `cms_forum_vote` WHERE `type`='2' AND `topic` = " . Vars::$ID);
        while ($vote = mysql_fetch_array($vote_result)) {
            if (!empty($_POST[$vote['id'] . 'vote'])) {
                $text = mb_substr(trim($_POST[$vote['id'] . 'vote']), 0, 30);
                mysql_query("UPDATE `cms_forum_vote` SET  `name` = '" . mysql_real_escape_string($text) . "'  WHERE `id` = '" . $vote['id'] . "'");
            }
        }
        $countvote = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type`='2' AND `topic` = " . Vars::$ID), 0);
        for ($vote = $countvote; $vote < 20; $vote++) {
            if (!empty($_POST[$vote])) {
                $text = mb_substr(trim($_POST[$vote]), 0, 30);
                mysql_query("INSERT INTO `cms_forum_vote` SET `name` = '" . mysql_real_escape_string($text) . "',  `type` = '2', `topic` = " . Vars::$ID);
            }
        }
        echo '<div class="gmenu"><p>' . __('voting_changed') . '<br /><a href="' . $url . '?id=' . Vars::$ID . '">' . __('continue') . '</a></p></div>';
    } else {
        /*
        -----------------------------------------------------------------
        Форма редактирования опроса
        -----------------------------------------------------------------
        */
        $countvote = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type` = '2' AND `topic` = " . Vars::$ID), 0);
        $topic_vote = mysql_fetch_array(mysql_query("SELECT `name` FROM `cms_forum_vote` WHERE `type` = '1' AND `topic` = " . Vars::$ID . " LIMIT 1"));
        echo '<div class="phdr"><a href="' . $url . '?id=' . Vars::$ID . '"><b>' . __('forum') . '</b></a> | ' . __('edit_vote') . '</div>' .
             '<form action="' . $url . '?act=editvote&amp;id=' . Vars::$ID . '" method="post">' .
             '<div class="gmenu"><p>' .
             '<b>' . __('voting') . ':</b><br/>' .
             '<input type="text" size="20" maxlength="150" name="name_vote" value="' . htmlentities($topic_vote['name'], ENT_QUOTES, 'UTF-8') . '"/>' .
             '</p></div>' .
             '<div class="menu"><p>';
        $vote_result = mysql_query("SELECT `id`, `name` FROM `cms_forum_vote` WHERE `type` = '2' AND `topic` = " . Vars::$ID);
        while ($vote = mysql_fetch_array($vote_result)) {
            echo __('answer') . ' ' . ($i + 1) . ' (max. 50): <br/>' .
                 '<input type="text" name="' . $vote['id'] . 'vote" value="' . htmlentities($vote['name'], ENT_QUOTES, 'UTF-8') . '"/>';
            if ($countvote > 2)
                echo '&nbsp;<a href="' . $url . '?act=editvote&amp;id=' . Vars::$ID . '&amp;vote=' . $vote['id'] . '&amp;delvote">[x]</a>';
            echo '<br/>';
            ++$i;
        }
        if ($countvote < 20) {
            if (isset($_POST['plus']))
                ++$_POST['count_vote'];
            elseif (isset($_POST['minus']))
                --$_POST['count_vote'];
            if (empty($_POST['count_vote']))
                $_POST['count_vote'] = $countvote;
            elseif ($_POST['count_vote'] > 20)
                $_POST['count_vote'] = 20;
            for ($vote = $i; $vote < $_POST['count_vote']; $vote++) {
                echo 'Ответ ' . ($vote + 1) . '(max. 50): <br/><input type="text" name="' . $vote . '" value="' . Validate::checkout($_POST[$vote]) . '"/><br/>';
            }
            echo '<input type="hidden" name="count_vote" value="' . abs(intval($_POST['count_vote'])) . '"/>' . ($_POST['count_vote'] < 20 ? '<input type="submit" name="plus" value="' . __('add') . '"/>' : '')
                 . ($_POST['count_vote'] - $countvote ? '<input type="submit" name="minus" value="' . __('delete_last') . '"/>' : '');
        }
        echo '</p></div><div class="gmenu">' .
             '<p><input type="submit" name="submit" value="' . __('save') . '"/></p>' .
             '</div></form>' .
             '<div class="phdr"><a href="' . $url . '?id=' . Vars::$ID . '">' . __('cancel') . '</a></div>';
    }
}