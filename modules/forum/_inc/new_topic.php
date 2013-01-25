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

// Закрываем доступ для определенных ситуаций
if (!Vars::$ID || isset(Vars::$USER_BAN['1']) || isset(Vars::$USER_BAN['11']) || (!Vars::$USER_RIGHTS && Vars::$SYSTEM_SET['mod_forum'] == 3)) {
    echo Functions::displayError(__('access_forbidden'));
    exit;
}

$url = Router::getUri(2);
$settings = Forum::settings();

// Вспомогательная Функция обработки ссылок форума
function forum_link($m)
{
    if (!isset($m[3])) {
        return '[url=' . $m[1] . ']' . $m[2] . '[/url]';
    } else {
        $p = parse_url($m[3]);
        if ('http://' . $p['host'] . $p['path'] . '?id=' == Vars::$HOME_URL . 'forum/?id=') {
            $thid = abs(intval(preg_replace('/(.*?)id=/si', '', $m[3])));
            $req = DB::PDO()->query("SELECT `text` FROM `forum` WHERE `id`= '$thid' AND `type` = 't' AND `close` != '1'");
            if ($req->rowCount()) {
                $res = $req->fetch();
                $name = strtr($res['text'], array(
                    '&quot;' => '',
                    '&amp;'  => '',
                    '&lt;'   => '',
                    '&gt;'   => '',
                    '&#039;' => '',
                    '['      => '',
                    ']'      => ''
                ));
                if (mb_strlen($name) > 40)
                    $name = mb_substr($name, 0, 40) . '...';

                return '[url=' . $m[3] . ']' . $name . '[/url]';
            } else {
                return $m[3];
            }
        } else
            return $m[3];
    }
}

// Проверка на флуд
$flood = Functions::antiFlood();
if ($flood) {
    echo Functions::displayError(__('error_flood') . ' ' . $flood . __('sec') . ', <a href="' . $url . '?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '">' . __('back') . '</a>');
    exit;
}
$req_r = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 'r' LIMIT 1");
if (!$req_r->rowCount()) {
    echo Functions::displayError(__('error_wrong_data'));
    exit;
}
$th = isset($_POST['th']) ? Validate::checkout(mb_substr(trim($_POST['th']), 0, 100)) : '';
$msg = isset($_POST['msg']) ? trim($_POST['msg']) : '';
$msg = preg_replace_callback('~\\[url=(http://.+?)\\](.+?)\\[/url\\]|(http://(www.)?[0-9a-zA-Z\.-]+\.[0-9a-zA-Z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'forum_link', $msg);
if (isset($_POST['submit'])) {
    $error = array();
    if (empty($th))
        $error[] = __('error_topic_name');
    if (mb_strlen($th) < 2)
        $error[] = __('error_topic_name_lenght');
    if (empty($msg))
        $error[] = __('error_empty_message');
    if (mb_strlen($msg) < 4)
        $error[] = __('error_message_short');
    if (!$error) {
        $msg = preg_replace_callback('~\\[url=(http://.+?)\\](.+?)\\[/url\\]|(http://(www.)?[0-9a-zA-Z\.-]+\.[0-9a-zA-Z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'forum_link', $msg);

        // Прверяем, есть ли уже такая тема в текущем разделе?
        $STH = DB::PDO()->prepare('
            SELECT COUNT(*) FROM `forum`
            WHERE `type` = ?
            AND `refid` = ?
            AND `text` = ?
        ');

        $STH->execute(array('t', Vars::$ID, $th));
        if ($STH->fetchColumn()) {
            $error[] = __('error_topic_exists');
        }
        $STH = NULL;

        // Проверяем, не повторяется ли сообщение?
        $req = DB::PDO()->query("SELECT * FROM `forum` WHERE `user_id` = " . Vars::$USER_ID . " AND `type` = 'm' ORDER BY `time` DESC");
        if ($req->rowCount()) {
            $res = $req->fetch();
            if ($msg == $res['text'])
                $error[] = __('error_message_exists');
        }
    }
    if (!$error) {
        // Добавляем тему
        $STH = DB::PDO()->prepare('
            INSERT INTO `forum`
            (`refid`, `type`, `time`, `user_id`, `from`, `text`, `soft`, `edit`, `curators`)
            VALUES (?, "t", ?, ?, ?, ?, "", "", "")
        ');

        $STH->execute(array(
            Vars::$ID,
            time(),
            Vars::$USER_ID,
            Vars::$USER_NICKNAME,
            $th
        ));
        $rid = DB::PDO()->lastInsertId();
        $STH = NULL;

        // Добавляем текст поста
        $STH = DB::PDO()->prepare('
            INSERT INTO `forum`
            (`refid`, `type`, `time`, `user_id`, `from`, `ip`, `ip_via_proxy`, `soft`, `text`, `edit`, `curators`)
            VALUES (?, "m", ?, ?, ?, ?, ?, ?, ?, "", "")
        ');

        $STH->execute(array(
            $rid,
            time(),
            Vars::$USER_ID,
            Vars::$USER_NICKNAME,
            Vars::$IP,
            Vars::$IP_VIA_PROXY,
            Vars::$USER_AGENT,
            $msg
        ));
        $postid = DB::PDO()->lastInsertId();

        // Записываем счетчик постов юзера
        DB::PDO()->exec("UPDATE `users` SET
            `count_forum` = '" . ++Vars::$USER_DATA['count_forum'] . "',
            `lastpost` = '" . time() . "'
            WHERE `id` = " . Vars::$USER_ID . "
        ");

        // Ставим метку о прочтении
        DB::PDO()->exec("INSERT INTO `cms_forum_rdm` SET
            `topic_id` = '$rid',
            `user_id` = " . Vars::$USER_ID . ",
            `time` = '" . time() . "'
        ");

        if (isset($_POST['addfiles'])) {
            header('Location: ' . $url . '?id=' . $postid . '&act=addfile');
        } else {
            header('Location: ' . $url . '?id=' . $rid);
        }
    } else {
        // Выводим сообщение об ошибке
        echo Functions::displayError($error, '<a href="' . $url . '?act=nt&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
        exit;
    }
} else {
    $res_r = $req_r->fetch();
    $res_c = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = '" . $res_r['refid'] . "'")->fetch();
    if (!Vars::$USER_DATA['count_forum']) {
        if (!isset($_GET['yes'])) {
            echo '<p>' . __('forum_rules_text') . '</p>';
            echo '<p><a href="' . $url . 'new_topic/?id=' . Vars::$ID . '&amp;yes">' . __('agree') . '</a> | <a href="' . $url . '?id=' . Vars::$ID . '">' . __('not_agree') . '</a></p>';
            exit;
        }
    }
    $msg_pre = Validate::checkout($msg, 1, 1);
    if (Vars::$USER_SET['smilies'])
        $msg_pre = Functions::smilies($msg_pre, Vars::$USER_RIGHTS ? 1 : 0);
    $msg_pre = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $msg_pre);
    echo '<div class="phdr"><a href="' . $url . '?id=' . Vars::$ID . '"><b>' . __('forum') . '</b></a> | ' . __('new_topic') . '</div>';
    if ($msg && $th && !isset($_POST['submit']))
        echo '<div class="list1">' . Functions::getIcon('forum_normal.png') . '&#160;<span style="font-weight: bold">' . $th . '</span></div>' .
            '<div class="list2">' . Functions::displayUser(Vars::$USER_DATA, array('iphide' => 1,
                                                                                   'header' => '<span class="gray">(' . Functions::displayDate(time()) . ')</span>',
                                                                                   'body'   => $msg_pre)) . '</div>';
    echo'<form name="form" action="' . $url . 'new_topic/?id=' . Vars::$ID . '" method="post">' .
        '<div class="gmenu">' .
        '<p><h3>' . __('section') . '</h3>' .
        '<a href="' . $url . '?id=' . $res_c['id'] . '">' . $res_c['text'] . '</a> | <a href="' . $url . '?id=' . $res_r['id'] . '">' . $res_r['text'] . '</a></p>' .
        '<p><h3>' . __('new_topic_name') . '</h3>' .
        '<input type="text" size="20" maxlength="100" name="th" value="' . $th . '"/></p>' .
        '<p><h3>' . __('post') . '</h3>';
    if (!Vars::$IS_MOBILE)
        echo '</p><p>' . TextParser::autoBB('form', 'msg');
    echo '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg">' . (isset($_POST['msg']) ? Validate::checkout($_POST['msg']) : '') . '</textarea></p>' .
        '<p><input type="checkbox" name="addfiles" value="1" ' . (isset($_POST['addfiles']) ? 'checked="checked" ' : '') . '/> ' . __('add_file');
    echo'</p><p><input type="submit" name="submit" value="' . __('save') . '" style="width: 107px; cursor: pointer;"/> ' .
        ($settings['preview'] ? '<input type="submit" value="' . __('preview') . '" style="width: 107px; cursor: pointer;"/>' : '') .
        '</p></div></form>' .
        '<div class="phdr"><a href="../pages/faq.php?act=trans">' . __('translit') . '</a> | ' .
        '<a href="../pages/faq.php?act=smilies">' . __('smilies') . '</a></div>' .
        '<p><a href="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a></p>';
}