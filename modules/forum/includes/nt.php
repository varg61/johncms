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

/*
-----------------------------------------------------------------
Закрываем доступ для определенных ситуаций
-----------------------------------------------------------------
*/
if (!Vars::$ID || !Vars::$USER_ID || isset(Vars::$USER_BAN['1']) || isset(Vars::$USER_BAN['11']) || (!Vars::$USER_RIGHTS && Vars::$SYSTEM_SET['mod_forum'] == 3)) {
    echo Functions::displayError(Vars::$LNG['access_forbidden']);
    exit;
}

/*
-----------------------------------------------------------------
Вспомогательная Функция обработки ссылок форума
-----------------------------------------------------------------
*/
function forum_link($m)
{
    if (!isset($m[3])) {
        return '[url=' . $m[1] . ']' . $m[2] . '[/url]';
    } else {
        $p = parse_url($m[3]);
        if ('http://' . $p['host'] . $p['path'] . '?id=' == Vars::$SYSTEM_SET['homeurl'] . '/forum/index.php?id=') {
            $thid = abs(intval(preg_replace('/(.*?)id=/si', '', $m[3])));
            $req = mysql_query("SELECT `text` FROM `forum` WHERE `id`= '$thid' AND `type` = 't' AND `close` != '1'");
            if (mysql_num_rows($req) > 0) {
                $res = mysql_fetch_array($req);
                $name = strtr($res['text'], array(
                    '&quot;' => '',
                    '&amp;' => '',
                    '&lt;' => '',
                    '&gt;' => '',
                    '&#039;' => '',
                    '[' => '',
                    ']' => ''
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
    echo Functions::displayError(Vars::$LNG['error_flood'] . ' ' . $flood . Vars::$LNG['sec'] . ', <a href="index.php?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '">' . Vars::$LNG['back'] . '</a>');
    exit;
}
$req_r = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 'r' LIMIT 1");
if (!mysql_num_rows($req_r)) {
    echo Functions::displayError(Vars::$LNG['error_wrong_data']);
    exit;
}
$th = isset($_POST['th']) ? Validate::filterString(mb_substr(trim($_POST['th']), 0, 100)) : '';
$msg = isset($_POST['msg']) ? trim($_POST['msg']) : '';
if (isset($_POST['msgtrans'])) {
    $th = Functions::translit($th);
    $msg = Functions::translit($msg);
}
$msg = preg_replace_callback('~\\[url=(http://.+?)\\](.+?)\\[/url\\]|(http://(www.)?[0-9a-zA-Z\.-]+\.[0-9a-zA-Z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'forum_link', $msg);
if (isset($_POST['submit'])) {
    $error = array();
    if (empty($th))
        $error[] = $lng_forum['error_topic_name'];
    if (mb_strlen($th) < 2)
        $error[] = $lng_forum['error_topic_name_lenght'];
    if (empty($msg))
        $error[] = Vars::$LNG['error_empty_message'];
    if (mb_strlen($msg) < 4)
        $error[] = Vars::$LNG['error_message_short'];
    if (!$error) {
        $msg = preg_replace_callback('~\\[url=(http://.+?)\\](.+?)\\[/url\\]|(http://(www.)?[0-9a-zA-Z\.-]+\.[0-9a-zA-Z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'forum_link', $msg);
        // Прверяем, есть ли уже такая тема в текущем разделе?
        if (mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `refid` = " . Vars::$ID . " AND `text` = '" . mysql_real_escape_string($th) . "'"), 0) > 0)
            $error[] = $lng_forum['error_topic_exists'];
        // Проверяем, не повторяется ли сообщение?
        $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = " . Vars::$USER_ID . " AND `type` = 'm' ORDER BY `time` DESC");
        if (mysql_num_rows($req) > 0) {
            $res = mysql_fetch_array($req);
            if ($msg == $res['text'])
                $error[] = Vars::$LNG['error_message_exists'];
        }
    }
    if (!$error) {
        // Добавляем тему
        mysql_query("INSERT INTO `forum` SET
            `refid` = " . Vars::$ID . ",
            `type` = 't',
            `time` = '" . time() . "',
            `user_id` = " . Vars::$USER_ID . ",
            `from` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
            `text` = '" . mysql_real_escape_string($th) . "'
        ");
        $rid = mysql_insert_id();
        // Добавляем текст поста
        mysql_query("INSERT INTO `forum` SET
            `refid` = '$rid',
            `type` = 'm',
            `time` = '" . time() . "',
            `user_id` = " . Vars::$USER_ID . ",
            `from` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
            `ip` = '" . Vars::$IP . "',
            `ip_via_proxy` = '" . Vars::$IP_VIA_PROXY . "',
            `soft` = '" . mysql_real_escape_string(Vars::$USER_AGENT) . "',
            `text` = '" . mysql_real_escape_string($msg) . "'
        ");
        $postid = mysql_insert_id();
        // Записываем счетчик постов юзера
        //TODO: Разобраться со счетчиком!
        $fpst = $datauser['postforum'] + 1;
        mysql_query("UPDATE `users` SET
            `postforum` = '$fpst',
            `lastpost` = '" . time() . "'
            WHERE `id` = " . Vars::$USER_ID . "
        ");
        // Ставим метку о прочтении
        mysql_query("INSERT INTO `cms_forum_rdm` SET
            `topic_id`='$rid',
            `user_id`=" . Vars::$USER_ID . ",
            `time`='" . time() . "'
        ");
        if ($_POST['addfiles'] == 1)
            header("Location: index.php?id=$postid&act=addfile");
        else
            header("Location: index.php?id=$rid");
    } else {
        // Выводим сообщение об ошибке
        echo Functions::displayError($error, '<a href="index.php?act=nt&amp;id=' . Vars::$ID . '">' . Vars::$LNG['repeat'] . '</a>');
        exit;
    }
} else {
    $res_r = mysql_fetch_assoc($req_r);
    $req_c = mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $res_r['refid'] . "'");
    $res_c = mysql_fetch_assoc($req_c);
    if ($datauser['postforum'] == 0) {
        if (!isset($_GET['yes'])) {
            $lng_faq = Vars::loadLanguage('faq');
            echo '<p>' . $lng_faq['forum_rules_text'] . '</p>';
            echo '<p><a href="index.php?act=nt&amp;id=' . Vars::$ID . '&amp;yes">' . $lng_forum['agree'] . '</a> | <a href="index.php?id=' . Vars::$ID . '">' . $lng_forum['not_agree'] . '</a></p>';
            exit;
        }
    }
    $msg_pre = Validate::filterString($msg, 1, 1);
    if (Vars::$USER_SET['smileys'])
        $msg_pre = Functions::smileys($msg_pre, Vars::$USER_RIGHTS ? 1 : 0);
    $msg_pre = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $msg_pre);
    echo '<div class="phdr"><a href="index.php?id=' . Vars::$ID . '"><b>' . Vars::$LNG['forum'] . '</b></a> | ' . $lng_forum['new_topic'] . '</div>';
    if ($msg && $th && !isset($_POST['submit']))
        //TODO: Разобраться с $datauser
        echo '<div class="list1">' . Functions::getImage('forum_normal.png') . '&#160;<span style="font-weight: bold">' . $th . '</span></div>' .
             '<div class="list2">' . Functions::displayUser($datauser, array('iphide' => 1, 'header' => '<span class="gray">(' . Functions::displayDate(time()) . ')</span>', 'body' => $msg_pre)) . '</div>';
    echo '<form name="form" action="index.php?act=nt&amp;id=' . Vars::$ID . '" method="post">' .
         '<div class="gmenu">' .
         '<p><h3>' . Vars::$LNG['section'] . '</h3>' .
         '<a href="index.php?id=' . $res_c['id'] . '">' . $res_c['text'] . '</a> | <a href="index.php?id=' . $res_r['id'] . '">' . $res_r['text'] . '</a></p>' .
         '<p><h3>' . $lng_forum['new_topic_name'] . '</h3>' .
         '<input type="text" size="20" maxlength="100" name="th" value="' . $th . '"/></p>' .
         '<p><h3>' . $lng_forum['post'] . '</h3>';
    if (!Vars::$IS_MOBILE)
        echo '</p><p>' . TextParser::autoBB('form', 'msg');
    echo '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg">' . (isset($_POST['msg']) ? Validate::filterString($_POST['msg']) : '') . '</textarea></p>' .
         '<p><input type="checkbox" name="addfiles" value="1" ' . (isset($_POST['addfiles']) ? 'checked="checked" ' : '') . '/> ' . $lng_forum['add_file'];
    if (Vars::$USER_SET['translit'])
        echo '<br /><input type="checkbox" name="msgtrans" value="1" ' . (isset($_POST['msgtrans']) ? 'checked="checked" ' : '') . '/> ' . Vars::$LNG['translit'];
    echo '</p><p><input type="submit" name="submit" value="' . Vars::$LNG['save'] . '" style="width: 107px; cursor: pointer;"/> ' .
         ($set_forum['preview'] ? '<input type="submit" value="' . Vars::$LNG['preview'] . '" style="width: 107px; cursor: pointer;"/>' : '') .
         '</p></div></form>' .
         '<div class="phdr"><a href="../pages/faq.php?act=trans">' . Vars::$LNG['translit'] . '</a> | ' .
         '<a href="../pages/faq.php?act=smileys">' . Vars::$LNG['smileys'] . '</a></div>' .
         '<p><a href="index.php?id=' . Vars::$ID . '">' . Vars::$LNG['back'] . '</a></p>';
}