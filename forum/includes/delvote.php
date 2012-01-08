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
    $topic_vote = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type`='1' AND `topic` = " . Vars::$ID), 0);
    require_once('../includes/head.php');
    if ($topic_vote == 0) {
        echo Functions::displayError(Vars::$LNG['error_wrong_data']);
        require_once('../includes/end.php');
        exit;
    }
    if (isset($_GET['yes'])) {
        mysql_query("DELETE FROM `cms_forum_vote` WHERE `topic` = " . Vars::$ID);
        mysql_query("DELETE FROM `cms_forum_vote_users` WHERE `topic` = " . Vars::$ID);
        mysql_query("UPDATE `forum` SET  `realid` = '0'  WHERE `id` = " . Vars::$ID);
        echo $lng_forum['voting_deleted'] . '<br /><a href="' . $_SESSION['prd'] . '">' . Vars::$LNG['continue'] . '</a>';
    } else {
        echo '<p>' . $lng_forum['voting_delete_warning'] . '</p>';
        echo '<p><a href="?act=delvote&amp;id=' . Vars::$ID . '&amp;yes">' . Vars::$LNG['delete'] . '</a><br />';
        echo '<a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">' . Vars::$LNG['cancel'] . '</a></p>';
        $_SESSION['prd'] = htmlspecialchars(getenv("HTTP_REFERER"));
    }
} else {
    header('location: ../index.php?err');
}