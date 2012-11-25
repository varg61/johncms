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

$topic_vote = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type` = '1' AND `topic` = " . Vars::$ID), 0);
if ($topic_vote == 0) {
    echo Functions::displayError(__('error_wrong_data'));
    exit;
} else {
    $topic_vote = mysql_fetch_array(mysql_query("SELECT `name`, `time`, `count` FROM `cms_forum_vote` WHERE `type` = '1' AND `topic` = " . Vars::$ID . " LIMIT 1"));
    echo '<div  class="phdr">' . __('voting_users') . ' &laquo;<b>' . htmlentities($topic_vote['name'], ENT_QUOTES, 'UTF-8') . '</b>&raquo;</div>';
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote_users` WHERE `topic`=" . Vars::$ID), 0);
    $req = mysql_query("SELECT `cms_forum_vote_users`.*, `users`.`rights`, `users`.`last_visit`, `users`.`nickname`, `users`.`sex`, `users`.`status`, `users`.`join_date`, `users`.`id`
    FROM `cms_forum_vote_users` LEFT JOIN `users` ON `cms_forum_vote_users`.`user` = `users`.`id`
    WHERE `cms_forum_vote_users`.`topic`=" . Vars::$ID . " ORDER BY `cms_forum_vote_users`.`id` DESC " . Vars::db_pagination()) or die(mysql_error());
    for($i = 0; $res = mysql_fetch_array($req); ++$i){
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        echo Functions::displayUser($res, array('iphide' => 1));
        echo '</div>';
    }
    if ($total == 0)
        echo '<div class="menu">' . __('voting_users_empty') . '</div>';
    echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
    if ($total > Vars::$USER_SET['page_size']) {
        echo'<p>' . Functions::displayPagination(Vars::$URI . '?act=users&amp;id=' . Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>' .
            '<p><form action="' . Vars::$URI . '?act=users&amp;id=' . Vars::$ID . '" method="post">' .
            '<input type="text" name="page" size="2"/>' .
            '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
    }
    echo '<p><a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . __('to_topic') . '</a></p>';
}