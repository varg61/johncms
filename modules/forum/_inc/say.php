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
    echo Functions::displayError(__('access_forbidden'));
    exit;
}

$url = Router::getUri(2);

/*
-----------------------------------------------------------------
Вспомогательная Функция обработки ссылок форума
-----------------------------------------------------------------
*/
function forum_link($m)
{
    global $set;
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
    echo Functions::displayError(__('error_flood') . ' ' . $flood . __('sec'), '<a href="?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '">' . __('back') . '</a>');
    exit;
}

$type = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID)->fetch();
switch ($type['type']) {
    case 't':
        /*
        -----------------------------------------------------------------
        Добавление простого сообщения
        -----------------------------------------------------------------
        */
        if (($type['edit'] == 1 || $type['close'] == 1) && Vars::$USER_RIGHTS < 7) {
            // Проверка, закрыта ли тема
            echo Functions::displayError(__('error_topic_closed'), '<a href="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a>');
            exit;
        }
        $msg = isset($_POST['msg']) ? trim($_POST['msg']) : '';
        if (isset($_POST['msgtrans']))
            $msg = Functions::translit($msg);
        //Обрабатываем ссылки
        $msg = preg_replace_callback('~\\[url=(http://.+?)\\](.+?)\\[/url\\]|(http://(www.)?[0-9a-zA-Z\.-]+\.[0-9a-zA-Z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'forum_link', $msg);
        if (isset($_POST['submit']) && !empty($_POST['msg'])) {
            // Проверяем на минимальную длину
            if (mb_strlen($msg) < 4) {
                echo Functions::displayError(__('error_message_short'), '<a href="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a>');
                exit;
            }
            // Проверяем, не повторяется ли сообщение?
            $req = DB::PDO()->query("SELECT * FROM `forum` WHERE `user_id` = " . Vars::$USER_ID . " AND `type` = 'm' ORDER BY `time` DESC");
            if ($req->rowCount()) {
                $res = $req->fetch();
                if ($msg == $res['text']) {
                    echo Functions::displayError(__('error_message_exists'), '<a href="?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '">' . __('back') . '</a>');
                    exit;
                }
            }
            // Удаляем фильтр, если он был
            if (isset($_SESSION['fsort_id']) && $_SESSION['fsort_id'] == Vars::$ID) {
                unset($_SESSION['fsort_id']);
                unset($_SESSION['fsort_users']);
            }

            // Добавляем сообщение в базу
            $STH = $STH = DB::PDO()->prepare('
                INSERT INTO `forum`
                (refid, type, time, user_id, from, ip, ip_via_proxy, soft, text, edit, curators)
                VALUES (?, "m", ?, ?, ?, ?, ?, ?, ?, "", "")
            ');

            $STH->execute(array(
                Vars::$ID,
                time(),
                Vars::$USER_ID,
                Vars::$USER_NICKNAME,
                Vars::$IP,
                Vars::$IP_VIA_PROXY,
                Vars::$USER_AGENT,
                $msg
            ));
            $fadd = DB::PDO()->lastInsertId();

            // Обновляем время топика
            DB::PDO()->exec("UPDATE `forum` SET `time` = '" . time() . "' WHERE `id` = " . Vars::$ID);

            // Обновляем статистику юзера
            DB::PDO()->exec("UPDATE `users` SET
                `count_forum` = '" . ++Vars::$USER_DATA['count_forum'] . "',
                `lastpost` = '" . time() . "'
                WHERE `id` = " . Vars::$USER_ID
            );

            // Вычисляем, на какую страницу попадает добавляемый пост
            $page = $set_forum['upfp'] ? 1 : ceil(DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = " . Vars::$ID . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close` != '1'"))->fetchColumn() / Vars::$USER_SET['page_size']);
            if (isset($_POST['addfiles']))
                header("Location: " . $url . "?id=$fadd&act=addfile");
            else
                header("Location: " . $url . "?id=" . Vars::$ID . "&page=$page");
        } else {
            if (!Vars::$USER_DATA['count_forum']) {
                if (!isset($_GET['yes'])) {
                    echo'<p>' . __('forum_rules_text') . '</p>' .
                        '<p><a href="' . $url . '?act=say&amp;id=' . Vars::$ID . '&amp;yes">' . __('agree') . '</a> | ' .
                        '<a href="' . $url . '?id=' . Vars::$ID . '">' . __('not_agree') . '</a></p>';
                    exit;
                }
            }
            $msg_pre = Validate::checkout($msg, 1, 1);
            if (Vars::$USER_SET['smilies'])
                $msg_pre = Functions::smilies($msg_pre, Vars::$USER_RIGHTS ? 1 : 0);
            $msg_pre = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $msg_pre);
            echo '<div class="phdr"><b>' . __('topic') . ':</b> ' . $type['text'] . '</div>';
            if ($msg && !isset($_POST['submit']))
                echo '<div class="list1">' . Functions::displayUser(Vars::$USER_DATA, array('iphide' => 1,
                                                                                            'header' => '<span class="gray">(' . Functions::displayDate(time()) . ')</span>',
                                                                                            'body'   => $msg_pre)) . '</div>';
            echo '<form name="form" action="' . $url . '?act=say&amp;id=' . Vars::$ID . '&amp;start=' . Vars::$START . '" method="post"><div class="gmenu">' .
                '<p><h3>' . __('post') . '</h3>';
            if (!Vars::$IS_MOBILE)
                echo '</p><p>' . TextParser::autoBB('form', 'msg');
            echo '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg">' . (empty($_POST['msg']) ? '' : Validate::checkout($msg)) . '</textarea></p>' .
                '<p><input type="checkbox" name="addfiles" value="1" ' . (isset($_POST['addfiles']) ? 'checked="checked" ' : '') . '/> ' . __('add_file');
            if (Vars::$USER_SET['translit'])
                echo '<br /><input type="checkbox" name="msgtrans" value="1" ' . (isset($_POST['msgtrans']) ? 'checked="checked" ' : '') . '/> ' . __('translit');
            echo '</p><p><input type="submit" name="submit" value="' . __('sent') . '" style="width: 107px; cursor: pointer;"/> ' .
                ($set_forum['preview'] ? '<input type="submit" value="' . __('preview') . '" style="width: 107px; cursor: pointer;"/>' : '') .
                '</p></div></form>';
        }
        echo '<div class="phdr"><a href="../pages/faq.php?act=trans">' . __('translit') . '</a> | ' .
            '<a href="../pages/faq.php?act=smilies">' . __('smilies') . '</a></div>' .
            '<p><a href="?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '">' . __('back') . '</a></p>';
        break;

    case 'm':
        /*
        -----------------------------------------------------------------
        Добавление сообщения с цитированием поста
        -----------------------------------------------------------------
        */

        $th = $type1['refid'];
        $th1 = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . $th)->fetch();
        if (($th1['edit'] == 1 || $th1['close'] == 1) && Vars::$USER_RIGHTS < 7) {
            echo Functions::displayError(__('error_topic_closed'), '<a href="' . $url . '?id=' . $th1['id'] . '">' . __('back') . '</a>');
            exit;
        }
        if ($type['user_id'] == Vars::$USER_ID) {
            echo Functions::displayError('Нельзя отвечать на свое же сообщение', '<a href="' . $url . '?id=' . $th1['id'] . '">' . __('back') . '</a>');
            exit;
        }
        $shift = (Vars::$SYSTEM_SET['timeshift'] + Vars::$USER_SET['timeshift']) * 3600;
        $vr = date("d.m.Y / H:i", $type['time'] + $shift);
        $msg = isset($_POST['msg']) ? trim($_POST['msg']) : '';
        $txt = isset($_POST['txt']) ? intval($_POST['txt']) : FALSE;
        if (isset($_POST['msgtrans']))
            $msg = Functions::translit($msg);
        $to = $type['from'];
        if (!empty($_POST['citata'])) {
            // Если была цитата, форматируем ее и обрабатываем
            $citata = isset($_POST['citata']) ? trim($_POST['citata']) : '';
            $citata = TextParser::noTags($citata);
            $citata = preg_replace('#\[c\](.*?)\[/c\]#si', '', $citata);
            $citata = mb_substr($citata, 0, 200);
            $tp = date("d.m.Y/H:i", $type['time']);
            $msg = '[c]' . $to . ' (' . $tp . ")\r\n" . $citata . '[/c]' . $msg;
        } elseif (isset($_POST['txt'])) {
            // Если был ответ, обрабатываем реплику
            switch ($txt) {
                case 2:
                    $repl = $type['from'] . ', ' . __('reply_1') . ', ';
                    break;

                case 3:
                    $repl = $type['from'] . ', ' . __('reply_2') . ' ([url=' . Vars::$HOME_URL . 'forum/?act=post&id=' . $type['id'] . ']' . $vr . '[/url]) ' . __('reply_3') . ', ';
                    break;

                case 4:
                    $repl = $type['from'] . ', ' . __('reply_4') . ' ';
                    break;

                default :
                    $repl = $type['from'] . ', ';
            }
            $msg = $repl . ' ' . $msg;
        }
        //Обрабатываем ссылки
        $msg = preg_replace_callback('~\\[url=(http://.+?)\\](.+?)\\[/url\\]|(http://(www.)?[0-9a-zA-Z\.-]+\.[0-9a-zA-Z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'forum_link', $msg);
        if (isset($_POST['submit'])) {
            if (empty($_POST['msg'])) {
                echo Functions::displayError(__('error_empty_message'), '<a href="' . $url . '?act=say&amp;id=' . $th . (isset($_GET['cyt']) ? '&amp;cyt' : '') . '">' . __('repeat') . '</a>');
                exit;
            }
            // Проверяем на минимальную длину
            if (mb_strlen($msg) < 4) {
                echo Functions::displayError(__('error_message_short'), '<a href="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a>');
                exit;
            }
            // Проверяем, не повторяется ли сообщение?
            $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = " . Vars::$USER_ID . " AND `type` = 'm' ORDER BY `time` DESC LIMIT 1");
            if (mysql_num_rows($req) > 0) {
                $res = mysql_fetch_array($req);
                if ($msg == $res['text']) {
                    echo Functions::displayError(__('error_message_exists'), '<a href="' . $url . '?id=' . $th . '&amp;start=' . Vars::$START . '">' . __('back') . '</a>');
                    exit;
                }
            }
            // Удаляем фильтр, если он был
            if (isset($_SESSION['fsort_id']) && $_SESSION['fsort_id'] == $th) {
                unset($_SESSION['fsort_id']);
                unset($_SESSION['fsort_users']);
            }
            // Добавляем сообщение в базу
            // Добавляем сообщение в базу
            $STH = $STH = DB::PDO()->prepare('
                INSERT INTO `forum`
                (refid, type, time, user_id, from, ip, ip_via_proxy, soft, text, edit, curators)
                VALUES (?, "m", ?, ?, ?, ?, ?, ?, ?, "", "")
            ');

            $STH->execute(array(
                $th,
                time(),
                Vars::$USER_ID,
                Vars::$USER_NICKNAME,
                Vars::$IP,
                Vars::$IP_VIA_PROXY,
                Vars::$USER_AGENT,
                $msg
            ));
            $fadd = DB::PDO()->lastInsertId();

            // Обновляем время топика
            DB::PDO()->exec("UPDATE `forum`
                SET `time` = '" . time() . "'
                WHERE `id` = '$th'
            ");

            // Обновляем статистику юзера
            DB::PDO()->exec("UPDATE `users` SET
                `count_forum` = '" . ++Vars::$USER_DATA['count_forum'] . "',
                `lastpost` = '" . time() . "'
                WHERE `id` = " . Vars::$USER_ID
            );

            // Вычисляем, на какую страницу попадает добавляемый пост
            $page = $set_forum['upfp'] ? 1 : ceil(DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '$th'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close` != '1'"))->fetchColumn() / Vars::$USER_SET['page_size']);
            $addfiles = intval($_POST['addfiles']);
            if ($addfiles == 1) {
                header("Location: " . $url . "?id=$fadd&act=addfile");
            } else {
                header("Location: " . $url . "?id=$th&page=$page");
            }
        } else {
            $qt = " $type[text]";
            if ((Vars::$USER_DATA['count_forum'] == "" || Vars::$USER_DATA['count_forum'] == 0)) {
                if (!isset($_GET['yes'])) {
                    echo '<p>' . __('forum_rules_text') . '</p>';
                    echo '<p><a href="' . $url . '?act=say&amp;id=' . Vars::$ID . '&amp;yes&amp;cyt">' . __('agree') . '</a> | <a href="' . $url . '?id=' . $type['refid'] . '">' . __('not_agree') . '</a></p>';
                    exit;
                }
            }
            $msg_pre = Validate::checkout($msg, 1, 1);
            if (Vars::$USER_SET['smilies'])
                $msg_pre = Functions::smilies($msg_pre, Vars::$USER_RIGHTS ? 1 : 0);
            $msg_pre = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $msg_pre);
            echo '<div class="phdr"><b>' . __('topic') . ':</b> ' . $th1['text'] . '</div>';
            $qt = str_replace("<br/>", "\r\n", $qt);
            $qt = trim(preg_replace('#\[c\](.*?)\[/c\]#si', '', $qt));
            $qt = Validate::checkout($qt, 0, 2);
            if (!empty($_POST['msg']) && !isset($_POST['submit']))
                echo '<div class="list1">' . Functions::displayUser(Vars::$USER_DATA, array('iphide' => 1,
                                                                                            'header' => '<span class="gray">(' . Functions::displayDate(time()) . ')</span>',
                                                                                            'body'   => $msg_pre)) . '</div>';
            echo '<form name="form" action="?act=say&amp;id=' . Vars::$ID . '&amp;start=' . Vars::$START . (isset($_GET['cyt']) ? '&amp;cyt' : '') . '" method="post"><div class="gmenu">';
            if (isset($_GET['cyt'])) {
                // Форма с цитатой
                echo '<p><b>' . $type['from'] . '</b> <span class="gray">(' . date("d.m.Y/H:i", $type['time']) . ')</span></p>' .
                    '<p><h3>' . __('cytate') . '</h3>' .
                    '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="citata">' . (empty($_POST['citata']) ? $qt : Validate::checkout($_POST['citata'])) . '</textarea>' .
                    '<br /><small>' . __('cytate_help') . '</small></p>';
            } else {
                // Форма с репликой
                echo '<p><h3>' . __('reference') . '</h3>' .
                    '<input type="radio" value="0" ' . (!$txt ? 'checked="checked"' : '') . ' name="txt" />&#160;<b>' . $type['from'] . '</b>,<br />' .
                    '<input type="radio" value="2" ' . ($txt == 2 ? 'checked="checked"' : '') . ' name="txt" />&#160;<b>' . $type['from'] . '</b>, ' . __('reply_1') . ',<br />' .
                    '<input type="radio" value="3" ' . ($txt == 3 ? 'checked="checked"'
                    : '') . ' name="txt" />&#160;<b>' . $type['from'] . '</b>, ' . __('reply_2') . ' (<a href="' . $url . '?act=post&amp;id=' . $type['id'] . '">' . $vr . '</a>) ' . __('reply_3') . ',<br />' .
                    '<input type="radio" value="4" ' . ($txt == 4 ? 'checked="checked"' : '') . ' name="txt" />&#160;<b>' . $type['from'] . '</b>, ' . __('reply_4') . '</p>';
            }
            echo '<p><h3>' . __('post') . '</h3>';
            if (!Vars::$IS_MOBILE)
                echo '</p><p>' . TextParser::autoBB('form', 'msg');
            echo '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg">' . (empty($_POST['msg']) ? '' : Validate::checkout($_POST['msg'])) . '</textarea></p>' .
                '<p><input type="checkbox" name="addfiles" value="1" ' . (isset($_POST['addfiles']) ? 'checked="checked" ' : '') . '/> ' . __('add_file');
            if (Vars::$USER_SET['translit'])
                echo '<br /><input type="checkbox" name="msgtrans" value="1" ' . (isset($_POST['msgtrans']) ? 'checked="checked" ' : '') . '/> ' . __('translit');
            echo '</p><p><input type="submit" name="submit" value="' . __('sent') . '" style="width: 107px; cursor: pointer;"/> ' .
                ($set_forum['preview'] ? '<input type="submit" value="' . __('preview') . '" style="width: 107px; cursor: pointer;"/>' : '') .
                '</p></div></form>';
        }
        echo '<div class="phdr"><a href="../pages/faq.php?act=trans">' . __('translit') . '</a> | ' .
            '<a href="../pages/faq.php?act=smilies">' . __('smilies') . '</a></div>' .
            '<p><a href="?id=' . $type['refid'] . '&amp;start=' . Vars::$START . '">' . __('back') . '</a></p>';
        break;

    default:
        echo Functions::displayError(__('error_topic_deleted'), '<a href="' . $url . '">' . __('to_forum') . '</a>');
}