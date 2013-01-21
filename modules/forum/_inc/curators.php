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

if (Vars::$USER_RIGHTS >= 7) {
    $url = Router::getUri(2);
    $req = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 't'");
    if (!$req->rowCount() || Vars::$USER_RIGHTS < 7) {
        echo Functions::displayError(__('error_topic_deleted'));
        exit;
    }
    $topic = $req->fetch();
    $req = DB::PDO()->query("SELECT `forum`.*, `users`.`id`
        FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
        WHERE `forum`.`refid` = " . Vars::$ID . " AND `users`.`rights` < 6 AND `users`.`rights` != 3 GROUP BY `forum`.`from` ORDER BY `forum`.`from`");
    $total = $req->rowCount();
    $res = $req->fetch();
    echo '<div class="phdr"><a href="' . $url . '?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '"><b>' . __('forum') . '</b></a> | ' . __('curators') . '</div>' .
        '<div class="bmenu">' . $res['text'] . '</div>';
    $curators = array();
    $users = !empty($topic['curators']) ? unserialize($topic['curators']) : array();
    if (isset($_POST['submit'])) {
        $users = isset($_POST['users']) ? $_POST['users'] : array();
        if (!is_array($users)) $users = array();
    }
    if ($total > 0) {
        echo '<form action="' . $url . '?act=curators&amp;id=' . Vars::$ID . '&amp;start=' . Vars::$START . '" method="post">';
        $i = 0;
        while ($res = $req->fetch()) {
            $checked = array_key_exists($res['user_id'], $users) ? TRUE : FALSE;
            if ($checked) $curators[$res['user_id']] = $res['from'];
            echo ($i++ % 2 ? '<div class="list2">' : '<div class="list1">') .
                '<input type="checkbox" name="users[' . $res['user_id'] . ']" value="' . $res['from'] . '"' . ($checked ? ' checked="checked"' : '') . '/>&#160;' .
                '<a href="../users/profile.php?user=' . $res['user_id'] . '">' . $res['from'] . '</a></div>';
        }
        echo '<div class="gmenu"><input type="submit" value="' . __('assign') . '" name="submit" /></div></form>';
        if (isset($_POST['submit'])) {
            $STH = DB::PDO()->prepare('
                UPDATE `forum` SET
                `curators` = ?
                WHERE `id` = ' . Vars::$ID
            );
            $STH->execute(array(serialize($curators)));
            $STH = NULL;
        }

    } else
        echo Functions::displayError(__('list_empty'));
    echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>' .
        '<p><a href="' . $url . '?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '">' . __('back') . '</a></p>';
}