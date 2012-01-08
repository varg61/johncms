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

require_once('../includes/head.php');
$topic_vote = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type` = '1' AND `topic` = " . Vars::$ID), 0);
if ($topic_vote == 0) {
    echo Functions::displayError(Vars::$LNG['error_wrong_data']);
    require_once('../includes/end.php');
    exit;
} else {
    $topic_vote = mysql_fetch_array(mysql_query("SELECT `name`, `time`, `count` FROM `cms_forum_vote` WHERE `type` = '1' AND `topic` = " . Vars::$ID . " LIMIT 1"));
    echo '<div  class="phdr">' . $lng_forum['voting_users'] . ' &laquo;<b>' . htmlentities($topic_vote['name'], ENT_QUOTES, 'UTF-8') . '</b>&raquo;</div>';
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote_users` WHERE `topic`=" . Vars::$ID), 0);
    $req = mysql_query("SELECT `cms_forum_vote_users`.*, `users`.`rights`, `users`.`lastdate`, `users`.`name`, `users`.`sex`, `users`.`status`, `users`.`datereg`, `users`.`id`
    FROM `cms_forum_vote_users` LEFT JOIN `users` ON `cms_forum_vote_users`.`user` = `users`.`id`
    WHERE `cms_forum_vote_users`.`topic`=" . Vars::$ID . " ORDER BY `time` DESC LIMIT " . Vars::db_pagination());
    $i = 0;
    while ($res = mysql_fetch_array($req)) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        echo Functions::displayUser($res, array('iphide' => 1));
        echo '</div>';
        ++$i;
    }
    if ($total == 0)
        echo '<div class="menu">' . $lng_forum['voting_users_empty'] . '</div>';
    echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<p>' . Functions::displayPagination('index.php?act=users&amp;id=' . Vars::$ID . '&amp;', $start, $total, Vars::$USER_SET['page_size']) . '</p>' .
             '<p><form action="index.php?act=users&amp;id=' . Vars::$ID . '" method="post">' .
             '<input type="text" name="page" size="2"/>' .
             '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/></form></p>';
    }
    echo '<p><a href="index.php?id=' . Vars::$ID . '">' . $lng_forum['to_topic'] . '</a></p>';
}

require_once('../includes/end.php');