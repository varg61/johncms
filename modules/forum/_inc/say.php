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
    echo Functions::displayError(lng('access_forbidden'));
    exit;
}

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
        if ('http://' . $p['host'] . $p['path'] . '?id=' == Vars::$HOME_URL . '/forum?id=') {
            $thid = abs(intval(preg_replace('/(.*?)id=/si', '', $m[3])));
            $req = mysql_query("SELECT `text` FROM `forum` WHERE `id`= '$thid' AND `type` = 't' AND `close` != '1'");
            if (mysql_num_rows($req) > 0) {
                $res = mysql_fetch_array($req);
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
    echo Functions::displayError(lng('error_flood') . ' ' . $flood . lng('sec'), '<a href="?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '">' . lng('back') . '</a>');
    exit;
}

$agn1 = strtok(Vars::$USER_AGENT, ' ');
$type = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID);
$type1 = mysql_fetch_assoc($type);
switch ($type1['type']) {
    case 't':
        /*
        -----------------------------------------------------------------
        Добавление простого сообщения
        -----------------------------------------------------------------
        */
        if (($type1['edit'] == 1 || $type1['close'] == 1) && Vars::$USER_RIGHTS < 7) {
            // Проверка, закрыта ли тема
            echo Functions::displayError(lng('error_topic_closed'), '<a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . lng('back') . '</a>');
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
                echo Functions::displayError(lng('error_message_short'), '<a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . lng('back') . '</a>');
                exit;
            }
            // Проверяем, не повторяется ли сообщение?
            $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = " . Vars::$USER_ID . " AND `type` = 'm' ORDER BY `time` DESC");
            if (mysql_num_rows($req) > 0) {
                $res = mysql_fetch_array($req);
                if ($msg == $res['text']) {
                    echo Functions::displayError(lng('error_message_exists'), '<a href="?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '">' . lng('back') . '</a>');
                    exit;
                }
            }
            // Удаляем фильтр, если он был
            if (isset($_SESSION['fsort_id']) && $_SESSION['fsort_id'] == Vars::$ID) {
                unset($_SESSION['fsort_id']);
                unset($_SESSION['fsort_users']);
            }
            // Добавляем сообщение в базу
            mysql_query("INSERT INTO `forum` SET
                `refid` = " . Vars::$ID . ",
                `type` = 'm' ,
                `time` = '" . time() . "',
                `user_id` = " . Vars::$USER_ID . ",
                `from` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                `ip` = '" . Vars::$IP . "',
                `ip_via_proxy` = '" . Vars::$IP_VIA_PROXY . "',
                `soft` = '" . mysql_real_escape_string($agn1) . "',
                `text` = '" . mysql_real_escape_string($msg) . "',
                `edit` = '',
                `curators` = ''
            ") or die(mysql_error());
            $fadd = mysql_insert_id();
            // Обновляем время топика
            mysql_query("UPDATE `forum` SET `time` = '" . time() . "' WHERE `id` = " . Vars::$ID);
            // Обновляем статистику юзера
            mysql_query("UPDATE `users` SET
                `count_forum` = '" . ++Vars::$USER_DATA['count_forum'] . "',
                `lastpost` = '" . time() . "'
                WHERE `id` = " . Vars::$USER_ID
            );
            // Вычисляем, на какую страницу попадает добавляемый пост
            $page = $set_forum['upfp'] ? 1 : ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = " . Vars::$ID . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close` != '1'")), 0) / Vars::$USER_SET['page_size']);
            if (isset($_POST['addfiles']))
                header("Location: " . Vars::$URI . "?id=$fadd&act=addfile");
            else
                header("Location: " . Vars::$URI . "?id=" . Vars::$ID . "&page=$page");
        } else {
            if (!Vars::$USER_DATA['count_forum']) {
                if (!isset($_GET['yes'])) {
                    echo'<p>' . lng('forum_rules_text') . '</p>' .
                        '<p><a href="' . Vars::$URI . '?act=say&amp;id=' . Vars::$ID . '&amp;yes">' . lng('agree') . '</a> | ' .
                        '<a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . lng('not_agree') . '</a></p>';
                    exit;
                }
            }
            $msg_pre = Validate::filterString($msg, 1, 1);
            if (Vars::$USER_SET['smileys'])
                $msg_pre = Functions::smileys($msg_pre, Vars::$USER_RIGHTS ? 1 : 0);
            $msg_pre = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $msg_pre);
            echo '<div class="phdr"><b>' . lng('topic') . ':</b> ' . $type1['text'] . '</div>';
            //TODO: Разобраться с $datauser
            if ($msg && !isset($_POST['submit']))
                echo '<div class="list1">' . Functions::displayUser(Vars::$USER_DATA, array('iphide' => 1,
                                                                                            'header' => '<span class="gray">(' . Functions::displayDate(time()) . ')</span>',
                                                                                            'body'   => $msg_pre)) . '</div>';
            echo '<form name="form" action="' . Vars::$URI . '?act=say&amp;id=' . Vars::$ID . '&amp;start=' . Vars::$START . '" method="post"><div class="gmenu">' .
                '<p><h3>' . lng('post') . '</h3>';
            if (!Vars::$IS_MOBILE)
                echo '</p><p>' . TextParser::autoBB('form', 'msg');
            echo '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg">' . (empty($_POST['msg']) ? '' : Validate::filterString($msg)) . '</textarea></p>' .
                '<p><input type="checkbox" name="addfiles" value="1" ' . (isset($_POST['addfiles']) ? 'checked="checked" ' : '') . '/> ' . lng('add_file');
            if (Vars::$USER_SET['translit'])
                echo '<br /><input type="checkbox" name="msgtrans" value="1" ' . (isset($_POST['msgtrans']) ? 'checked="checked" ' : '') . '/> ' . lng('translit');
            echo '</p><p><input type="submit" name="submit" value="' . lng('sent') . '" style="width: 107px; cursor: pointer;"/> ' .
                ($set_forum['preview'] ? '<input type="submit" value="' . lng('preview') . '" style="width: 107px; cursor: pointer;"/>' : '') .
                '</p></div></form>';
        }
        echo '<div class="phdr"><a href="../pages/faq.php?act=trans">' . lng('translit') . '</a> | ' .
            '<a href="../pages/faq.php?act=smileys">' . lng('smileys') . '</a></div>' .
            '<p><a href="?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '">' . lng('back') . '</a></p>';
        break;

    case 'm':
        /*
        -----------------------------------------------------------------
        Добавление сообщения с цитированием поста
        -----------------------------------------------------------------
        */
        $th = $type1['refid'];
        $th2 = mysql_query("SELECT * FROM `forum` WHERE `id` = '$th'");
        $th1 = mysql_fetch_array($th2);
        if (($th1['edit'] == 1 || $th1['close'] == 1) && Vars::$USER_RIGHTS < 7) {
            echo Functions::displayError(lng('error_topic_closed'), '<a href="' . Vars::$URI . '?id=' . $th1['id'] . '">' . lng('back') . '</a>');
            exit;
        }
        if ($type1['user_id'] == Vars::$USER_ID) {
            echo Functions::displayError('Нельзя отвечать на свое же сообщение', '<a href="' . Vars::$URI . '?id=' . $th1['id'] . '">' . lng('back') . '</a>');
            exit;
        }
        $shift = (Vars::$SYSTEM_SET['timeshift'] + Vars::$USER_SET['timeshift']) * 3600;
        $vr = date("d.m.Y / H:i", $type1['time'] + $shift);
        $msg = isset($_POST['msg']) ? trim($_POST['msg']) : '';
        $txt = isset($_POST['txt']) ? intval($_POST['txt']) : FALSE;
        if (isset($_POST['msgtrans']))
            $msg = Functions::translit($msg);
        $to = $type1['from'];
        if (!empty($_POST['citata'])) {
            // Если была цитата, форматируем ее и обрабатываем
            $citata = isset($_POST['citata']) ? trim($_POST['citata']) : '';
            $citata = TextParser::noTags($citata);
            $citata = preg_replace('#\[c\](.*?)\[/c\]#si', '', $citata);
            $citata = mb_substr($citata, 0, 200);
            $tp = date("d.m.Y/H:i", $type1['time']);
            $msg = '[c]' . $to . ' (' . $tp . ")\r\n" . $citata . '[/c]' . $msg;
        } elseif (isset($_POST['txt'])) {
            // Если был ответ, обрабатываем реплику
            switch ($txt) {
                case 2:
                    $repl = $type1['from'] . ', ' . lng('reply_1') . ', ';
                    break;

                case 3:
                    $repl = $type1['from'] . ', ' . lng('reply_2') . ' ([url=' . Vars::$HOME_URL . '/forum?act=post&id=' . $type1['id'] . ']' . $vr . '[/url]) ' . lng('reply_3') . ', ';
                    break;

                case 4:
                    $repl = $type1['from'] . ', ' . lng('reply_4') . ' ';
                    break;

                default :
                    $repl = $type1['from'] . ', ';
            }
            $msg = $repl . ' ' . $msg;
        }
        //Обрабатываем ссылки
        $msg = preg_replace_callback('~\\[url=(http://.+?)\\](.+?)\\[/url\\]|(http://(www.)?[0-9a-zA-Z\.-]+\.[0-9a-zA-Z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'forum_link', $msg);
        if (isset($_POST['submit'])) {
            if (empty($_POST['msg'])) {
                echo Functions::displayError(lng('error_empty_message'), '<a href="' . Vars::$URI . '?act=say&amp;id=' . $th . (isset($_GET['cyt']) ? '&amp;cyt' : '') . '">' . lng('repeat') . '</a>');
                exit;
            }
            // Проверяем на минимальную длину
            if (mb_strlen($msg) < 4) {
                echo Functions::displayError(lng('error_message_short'), '<a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . lng('back') . '</a>');
                exit;
            }
            // Проверяем, не повторяется ли сообщение?
            $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = " . Vars::$USER_ID . " AND `type` = 'm' ORDER BY `time` DESC LIMIT 1");
            if (mysql_num_rows($req) > 0) {
                $res = mysql_fetch_array($req);
                if ($msg == $res['text']) {
                    echo Functions::displayError(lng('error_message_exists'), '<a href="' . Vars::$URI . '?id=' . $th . '&amp;start=' . Vars::$START . '">' . lng('back') . '</a>');
                    exit;
                }
            }
            // Удаляем фильтр, если он был
            if (isset($_SESSION['fsort_id']) && $_SESSION['fsort_id'] == $th) {
                unset($_SESSION['fsort_id']);
                unset($_SESSION['fsort_users']);
            }
            // Добавляем сообщение в базу
            mysql_query("INSERT INTO `forum` SET
                `refid` = '$th',
                `type` = 'm',
                `time` = '" . time() . "',
                `user_id` = " . Vars::$USER_ID . ",
                `from` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                `ip` = '" . Vars::$IP . "',
                `ip_via_proxy` = '" . Vars::$IP_VIA_PROXY . "',
                `soft` = '" . mysql_real_escape_string($agn1) . "',
                `text` = '" . mysql_real_escape_string($msg) . "',
                `edit` = '',
                `curators` = ''
            ");
            $fadd = mysql_insert_id();
            // Обновляем время топика
            mysql_query("UPDATE `forum`
                SET `time` = '" . time() . "'
                WHERE `id` = '$th'
            ");
            // Обновляем статистику юзера
            //TODO: Разобраться со счетчиком!
            mysql_query("UPDATE `users` SET
                `count_forum`='" . ++Vars::$USER_DATA['count_forum'] . "',
                `lastpost` = '" . time() . "'
                WHERE `id` = " . Vars::$USER_ID
            );
            // Вычисляем, на какую страницу попадает добавляемый пост
            $page = $set_forum['upfp'] ? 1 : ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '$th'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close` != '1'")), 0) / Vars::$USER_SET['page_size']);
            $addfiles = intval($_POST['addfiles']);
            if ($addfiles == 1) {
                header("Location: " . Vars::$URI . "?id=$fadd&act=addfile");
            } else {
                header("Location: " . Vars::$URI . "?id=$th&page=$page");
            }
        } else {
            $qt = " $type1[text]";
            if ((Vars::$USER_DATA['count_forum'] == "" || Vars::$USER_DATA['count_forum'] == 0)) {
                if (!isset($_GET['yes'])) {
                    echo '<p>' . lng('forum_rules_text') . '</p>';
                    echo '<p><a href="' . Vars::$URI . '?act=say&amp;id=' . Vars::$ID . '&amp;yes&amp;cyt">' . lng('agree') . '</a> | <a href="' . Vars::$URI . '?id=' . $type1['refid'] . '">' . lng('not_agree') . '</a></p>';
                    exit;
                }
            }
            $msg_pre = Validate::filterString($msg, 1, 1);
            if (Vars::$USER_SET['smileys'])
                $msg_pre = Functions::smileys($msg_pre, Vars::$USER_RIGHTS ? 1 : 0);
            $msg_pre = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $msg_pre);
            echo '<div class="phdr"><b>' . lng('topic') . ':</b> ' . $th1['text'] . '</div>';
            $qt = str_replace("<br/>", "\r\n", $qt);
            $qt = trim(preg_replace('#\[c\](.*?)\[/c\]#si', '', $qt));
            $qt = Validate::filterString($qt, 0, 2);
            //TODO: Разобраться с $datauser
            if (!empty($_POST['msg']) && !isset($_POST['submit']))
                echo '<div class="list1">' . Functions::displayUser(Vars::$USER_DATA, array('iphide' => 1,
                                                                                            'header' => '<span class="gray">(' . Functions::displayDate(time()) . ')</span>',
                                                                                            'body'   => $msg_pre)) . '</div>';
            echo '<form name="form" action="?act=say&amp;id=' . Vars::$ID . '&amp;start=' . Vars::$START . (isset($_GET['cyt']) ? '&amp;cyt' : '') . '" method="post"><div class="gmenu">';
            if (isset($_GET['cyt'])) {
                // Форма с цитатой
                echo '<p><b>' . $type1['from'] . '</b> <span class="gray">(' . date("d.m.Y/H:i", $type1['time']) . ')</span></p>' .
                    '<p><h3>' . lng('cytate') . '</h3>' .
                    '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="citata">' . (empty($_POST['citata']) ? $qt : Validate::filterString($_POST['citata'])) . '</textarea>' .
                    '<br /><small>' . lng('cytate_help') . '</small></p>';
            } else {
                // Форма с репликой
                echo '<p><h3>' . lng('reference') . '</h3>' .
                    '<input type="radio" value="0" ' . (!$txt ? 'checked="checked"' : '') . ' name="txt" />&#160;<b>' . $type1['from'] . '</b>,<br />' .
                    '<input type="radio" value="2" ' . ($txt == 2 ? 'checked="checked"' : '') . ' name="txt" />&#160;<b>' . $type1['from'] . '</b>, ' . lng('reply_1') . ',<br />' .
                    '<input type="radio" value="3" ' . ($txt == 3 ? 'checked="checked"'
                    : '') . ' name="txt" />&#160;<b>' . $type1['from'] . '</b>, ' . lng('reply_2') . ' (<a href="' . Vars::$URI . '?act=post&amp;id=' . $type1['id'] . '">' . $vr . '</a>) ' . lng('reply_3') . ',<br />' .
                    '<input type="radio" value="4" ' . ($txt == 4 ? 'checked="checked"' : '') . ' name="txt" />&#160;<b>' . $type1['from'] . '</b>, ' . lng('reply_4') . '</p>';
            }
            echo '<p><h3>' . lng('post') . '</h3>';
            if (!Vars::$IS_MOBILE)
                echo '</p><p>' . TextParser::autoBB('form', 'msg');
            echo '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg">' . (empty($_POST['msg']) ? '' : Validate::filterString($_POST['msg'])) . '</textarea></p>' .
                '<p><input type="checkbox" name="addfiles" value="1" ' . (isset($_POST['addfiles']) ? 'checked="checked" ' : '') . '/> ' . lng('add_file');
            if (Vars::$USER_SET['translit'])
                echo '<br /><input type="checkbox" name="msgtrans" value="1" ' . (isset($_POST['msgtrans']) ? 'checked="checked" ' : '') . '/> ' . lng('translit');
            echo '</p><p><input type="submit" name="submit" value="' . lng('sent') . '" style="width: 107px; cursor: pointer;"/> ' .
                ($set_forum['preview'] ? '<input type="submit" value="' . lng('preview') . '" style="width: 107px; cursor: pointer;"/>' : '') .
                '</p></div></form>';
        }
        echo '<div class="phdr"><a href="../pages/faq.php?act=trans">' . lng('translit') . '</a> | ' .
            '<a href="../pages/faq.php?act=smileys">' . lng('smileys') . '</a></div>' .
            '<p><a href="?id=' . $type1['refid'] . '&amp;start=' . Vars::$START . '">' . lng('back') . '</a></p>';
        break;

    default:
        echo Functions::displayError(lng('error_topic_deleted'), '<a href="' . Vars::$URI . '">' . lng('to_forum') . '</a>');
}