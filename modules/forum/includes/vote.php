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

if (Vars::$USER_ID) {
    $topic = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='t' AND `id` = " . Vars::$ID . " AND `edit` != '1'"), 0);
    $vote = abs(intval($_POST['vote']));
    $topic_vote = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type` = '2' AND `id` = '$vote' AND `topic` = " . Vars::$ID), 0);
    $vote_user = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote_users` WHERE `user` = " . Vars::$USER_ID . " AND `topic` = " . Vars::$ID), 0);
    if ($topic_vote == 0 || $vote_user > 0 || $topic == 0) {
        echo Functions::displayError(Vars::$LNG['error_wrong_data']);
        exit;
    }
    mysql_query("INSERT INTO `cms_forum_vote_users` SET `topic` = " . Vars::$ID . ", `user` = " . Vars::$USER_ID . ", `vote` = '$vote'");
    mysql_query("UPDATE `cms_forum_vote` SET `count` = count + 1 WHERE id = '$vote'");
    mysql_query("UPDATE `cms_forum_vote` SET `count` = count + 1 WHERE topic = " . Vars::$ID . " AND `type` = '1'");
    echo $lng_forum['vote_accepted'] . '<br /><a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">' . Vars::$LNG['back'] . '</a>';
} else {
    echo Functions::displayError(Vars::$LNG['access_guest_forbidden']);
}