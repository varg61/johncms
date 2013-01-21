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
$url = Router::getUri(2);

if (!Vars::$ID) {
    echo "";
    exit;
}

//TODO: Переделать на новый антиспам
// Проверка на спам
$old = (Vars::$USER_RIGHTS > 0) ? 5 : 60;
if (Vars::$USER_DATA['lastpost'] > (time() - $old)) {
    echo '<p>' . __('error_flood') . ' ' . $old . ' ' . __('sec') . '<br/><br/><a href ="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a></p>';
    exit;
}

$ms = DB::PDO()->query("select * from `lib` where `id` = " . Vars::$ID)->fetch();
if (Vars::$ID != 0 && $ms['type'] != "cat") {
    echo "";
    exit;
}
if ($ms['ip'] == 0) {
    if ((Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) || ($ms['soft'] == 1 && !empty($_SESSION['uid']))) {
        if (isset($_POST['submit'])) {
            if (empty($_POST['name'])) {
                echo __('error_empty_title') . "<br/><a href='" . $url . "?act=write&amp;id=" . Vars::$ID . "'>" . __('repeat') . "</a><br/>";
                exit;
            }
            if (empty($_POST['text'])) {
                echo __('error_empty_text') . "<br/><a href='" . $url . "?act=write&amp;id=" . Vars::$ID . "'>" . __('repeat') . "</a><br/>";
                exit;
            }
            $text = trim($_POST['text']);
            if (!empty($_POST['anons'])) {
                $anons = mb_substr(trim($_POST['anons']), 0, 100);
            } else {
                $anons = mb_substr($text, 0, 100);
            }
            if (Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) {
                $md = 1;
            } else {
                $md = 0;
            }

            $STH = $STH = DB::PDO()->prepare('
                INSERT INTO `lib` SET
                `refid`    = :refid,
                `time`     = :time,
                `type`     = "bk",
                `name`     = :name,
                `announce` = :announce,
                `text`     = :text,
                `avtor`    = :avtor,
                `ip`       = :ip,
                `soft`     = :soft,
                `moder`    = :moder
            ');

            $STH->bindValue(':refid', Vars::$ID);
            $STH->bindValue(':time', time());
            $STH->bindValue(':name', mb_substr(trim($_POST['name']), 0, 100));
            $STH->bindValue(':announce', $anons);
            $STH->bindValue(':text', $text);
            $STH->bindValue(':avtor', Vars::$USER_NICKNAME);
            $STH->bindValue(':ip', Vars::$IP);
            $STH->bindValue(':soft', Vars::$USER_AGENT);
            $STH->bindValue(':moder', $md);
            $STH->execute();
            $cid = DB::PDO()->lastInsertId();
            $STH = NULL;

            if ($md == 1) {
                echo '<p>' . __('article_added') . '</p>';
            } else {
                echo '<p>' . __('article_added') . '<br/>' . __('article_added_thanks') . '</p>';
            }
            DB::PDO()->exec("UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = " . Vars::$USER_ID);
            echo '<p><a href="' . $url . '?id=' . $cid . '">' . __('to_article') . '</a></p>';
        } else {
            echo '<h3>' . __('write_article') . '</h3><form action="' . $url . '?act=write&amp;id=' . Vars::$ID . '" method="post">';
            echo '<p>' . __('title') . ' (max. 100):<br/><input type="text" name="name"/></p>';
            echo '<p>' . __('announce') . ' (max. 100):<br/><input type="text" name="anons"/></p>';
            echo '<p>' . __('text') . ':<br/><textarea name="text" rows="' . Vars::$USER_SET['field_h'] . '"></textarea></p>';
            echo '<p><input type="submit" name="submit" value="' . __('save') . '"/></p>';
            echo '</form><p><a href ="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a></p>';
        }
    } else {
        header("location: " . $url);
    }
}
echo "<a href='" . $url . "'>" . __('to_library') . "</a><br/>";