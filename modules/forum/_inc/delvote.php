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
    $topic_vote = DB::PDO()->query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type`='1' AND `topic` = " . Vars::$ID)->fetchColumn();
    if ($topic_vote == 0) {
        echo Functions::displayError(__('error_wrong_data'));
        exit;
    }
    if (isset($_GET['yes'])) {
        DB::PDO()->exec("DELETE FROM `cms_forum_vote` WHERE `topic` = " . Vars::$ID);
        DB::PDO()->exec("DELETE FROM `cms_forum_vote_users` WHERE `topic` = " . Vars::$ID);
        DB::PDO()->exec("UPDATE `forum` SET  `realid` = '0'  WHERE `id` = " . Vars::$ID);
        echo __('voting_deleted') . '<br /><a href="' . $_SESSION['prd'] . '">' . __('continue') . '</a>';
    } else {
        echo '<p>' . __('voting_delete_warning') . '</p>';
        echo '<p><a href="?act=delvote&amp;id=' . Vars::$ID . '&amp;yes">' . __('delete') . '</a><br />';
        echo '<a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">' . __('cancel') . '</a></p>';
        $_SESSION['prd'] = htmlspecialchars(getenv("HTTP_REFERER"));
    }
} else {
    header('location: ../404.php');
}