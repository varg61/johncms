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

if (Vars::$USER_RIGHTS >= 7) {
    $req = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 't'");
    if (!mysql_num_rows($req) || Vars::$USER_RIGHTS < 7) {
        echo Functions::displayError($lng_forum['error_topic_deleted']);
        require_once('../includes/end.php');
        exit;
    }
    $topic = mysql_fetch_assoc($req);
    $req = mysql_query("SELECT `forum`.*, `users`.`id`
        FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
        WHERE `forum`.`refid` = " . Vars::$ID . " AND `users`.`rights` < 6 AND `users`.`rights` != 3 GROUP BY `forum`.`from` ORDER BY `forum`.`from`");
    $total = mysql_num_rows($req);
    $res = mysql_fetch_assoc($req);
    echo '<div class="phdr"><a href="index.php?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '"><b>' . Vars::$LNG['forum'] . '</b></a> | ' . $lng_forum['curators'] . '</div>' .
         '<div class="bmenu">' . $res['text'] . '</div>';
    $curators = array();
    $users = !empty($topic['curators']) ? unserialize($topic['curators']) : array();
    if (isset($_POST['submit'])) {
        $users = isset($_POST['users']) ? $_POST['users'] : array();
        if (!is_array($users)) $users = array();
    }
    if ($total > 0) {
        echo '<form action="index.php?act=curators&amp;id=' . Vars::$ID . '&amp;start=' . Vars::$START . '" method="post">';
        $i = 0;
        while ($res = mysql_fetch_array($req)) {
            $checked = array_key_exists($res['user_id'], $users) ? true : false;
            if ($checked) $curators[$res['user_id']] = $res['from'];
            echo ($i++ % 2 ? '<div class="list2">' : '<div class="list1">') .
                 '<input type="checkbox" name="users[' . $res['user_id'] . ']" value="' . $res['from'] . '"' . ($checked ? ' checked="checked"' : '') . '/>&#160;' .
                 '<a href="../users/profile.php?user=' . $res['user_id'] . '">' . $res['from'] . '</a></div>';
        }
        echo '<div class="gmenu"><input type="submit" value="' . $lng_forum['assign'] . '" name="submit" /></div></form>';
        if (isset($_POST['submit'])) mysql_query("UPDATE `forum` SET `curators`='" . mysql_real_escape_string(serialize($curators)) . "' WHERE `id` = " . Vars::$ID);

    } else
        echo Functions::displayError(Vars::$LNG['list_empty']);
    echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>' .
         '<p><a href="index.php?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '">' . Vars::$LNG['back'] . '</a></p>';
}