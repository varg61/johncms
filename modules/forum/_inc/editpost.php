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

if (!Vars::$USER_ID || !Vars::$ID) {
    echo Functions::displayError(lng('error_wrong_data'));
    exit;
}
$req = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 'm' " . (Vars::$USER_RIGHTS >= 7 ? "" : " AND `close` != '1'"));
if (mysql_num_rows($req)) {
    /*
    -----------------------------------------------------------------
    Предварительные проверки
    -----------------------------------------------------------------
    */
    $res = mysql_fetch_assoc($req);
    if (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 3 && Vars::$USER_ID) {
        $topic = mysql_fetch_assoc(mysql_query("SELECT `curators` FROM `forum` WHERE `id` = " . $res['refid']));
        $curators = !empty($topic['curators']) ? unserialize($topic['curators']) : array();
        if (array_key_exists(Vars::$USER_ID, $curators)) Vars::$USER_RIGHTS = 3;
    }
    $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">= " : "<= ") . Vars::$ID . (Vars::$USER_RIGHTS < 7 ? " AND `close` != '1'" : '')), 0) / Vars::$USER_SET['page_size']);
    $posts = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `close` != '1'"), 0);
    $link = Vars::$URI . '?id=' . $res['refid'] . '&amp;page=' . $page;
    $error = false;
    if (Vars::$USER_RIGHTS == 3 || Vars::$USER_RIGHTS >= 6) {
        // Проверка для Администрации
        if ($res['user_id'] != Vars::$USER_ID) {
            $req_u = mysql_query("SELECT * FROM `users` WHERE `id` = '" . $res['user_id'] . "'");
            if (mysql_num_rows($req_u)) {
                $res_u = mysql_fetch_assoc($req_u);
                if ($res_u['rights'] > Vars::$USER_RIGHTS)
                    $error = lng('error_edit_rights') . '<br /><a href="' . $link . '">' . lng('back') . '</a>';
            }
        }
    } else {
        // Проверка для обычных юзеров
        if ($res['user_id'] != Vars::$USER_ID)
            $error = lng('error_edit_another') . '<br /><a href="' . $link . '">' . lng('back') . '</a>';
        if (!$error) {
            $req_m = mysql_query("SELECT * FROM `forum` WHERE `refid` = '" . $res['refid'] . "' ORDER BY `id` DESC LIMIT 1");
            $res_m = mysql_fetch_assoc($req_m);
            if ($res_m['user_id'] != Vars::$USER_ID)
                $error = lng('error_edit_last') . '<br /><a href="' . $link . '">' . lng('back') . '</a>';
            elseif ($res['time'] < time() - 300)
                $error = lng('error_edit_timeout') . '<br /><a href="' . $link . '">' . lng('back') . '</a>';
        }
    }
} else {
    $error = lng('error_post_deleted') . '<br /><a href="' . Vars::$URI . '">' . lng('forum') . '</a>';
}
if (!$error) {
    switch (Vars::$MOD) {
        case 'restore':
            /*
            -----------------------------------------------------------------
            Восстановление удаленного поста
            -----------------------------------------------------------------
            */
            $req_u = mysql_query("SELECT `postforum` FROM `users` WHERE `id` = '" . $res['user_id'] . "'");
            if (mysql_num_rows($req_u)) {
                // Добавляем один балл к счетчику постов юзера
                $res_u = mysql_fetch_assoc($req_u);
                mysql_query("UPDATE `users` SET `postforum` = '" . ($res_u['postforum'] + 1) . "' WHERE `id` = '" . $res['user_id'] . "'");
            }
            mysql_query("UPDATE `forum` SET `close` = '0', `close_who` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "' WHERE `id` = " . Vars::$ID);
            $req_f = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = " . Vars::$ID . " LIMIT 1");
            if (mysql_num_rows($req_f)) {
                mysql_query("UPDATE `cms_forum_files` SET `del` = '0' WHERE `post` = " . Vars::$ID . " LIMIT 1");
            }
            header('Location: ' . $link);
            break;

        case 'delete':
            /*
            -----------------------------------------------------------------
            Удаление поста и прикрепленного файла
            -----------------------------------------------------------------
            */
            if ($res['close'] != 1) {
                $req_u = mysql_query("SELECT `postforum` FROM `users` WHERE `id` = '" . $res['user_id'] . "'");
                if (mysql_num_rows($req_u)) {
                    // Вычитаем один балл из счетчика постов юзера
                    $res_u = mysql_fetch_assoc($req_u);
                    $postforum = $res_u['postforum'] > 0 ? $res_u['postforum'] - 1 : 0;
                    mysql_query("UPDATE `users` SET `postforum` = '" . $postforum . "' WHERE `id` = '" . $res['user_id'] . "'");
                }
            }
            if (Vars::$USER_RIGHTS == 9 && !isset($_GET['hide'])) {
                // Удаление поста (для Супервизоров)
                $req_f = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = " . Vars::$ID . " LIMIT 1");
                if (mysql_num_rows($req_f)) {
                    // Если есть прикрепленный файл, удаляем его
                    $res_f = mysql_fetch_assoc($req_f);
                    unlink('../files/forum/attach/' . $res_f['filename']);
                    mysql_query("DELETE FROM `cms_forum_files` WHERE `post` = " . Vars::$ID . " LIMIT 1");
                }
                // Формируем ссылку на нужную страницу темы
                $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? "> " : "< ") . Vars::$ID), 0) / Vars::$USER_SET['page_size']);
                mysql_query("DELETE FROM `forum` WHERE `id` = " . Vars::$ID);
                if ($posts < 2) {
                    // Пересылка на удаление всей темы
                    header('Location: ' . Vars::$URI . '?act=deltema&id=' . $res['refid']);
                } else {
                    header('Location: ' . Vars::$URI . '?id=' . $res['refid'] . '&page=' . $page);
                }
            } else {
                // Скрытие поста
                $req_f = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = " . Vars::$ID . " LIMIT 1");
                if (mysql_num_rows($req_f)) {
                    // Если есть прикрепленный файл, скрываем его
                    mysql_query("UPDATE `cms_forum_files` SET `del` = '1' WHERE `post` = " . Vars::$ID . " LIMIT 1");
                }
                if ($posts == 1) {
                    // Если это был последний пост темы, то скрываем саму тему
                    $res_l = mysql_fetch_assoc(mysql_query("SELECT `refid` FROM `forum` WHERE `id` = '" . $res['refid'] . "'"));
                    mysql_query("UPDATE `forum` SET `close` = '1', `close_who` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "' WHERE `id` = '" . $res['refid'] . "' AND `type` = 't'");
                    header('Location: ' . Vars::$URI . '?id=' . $res_l['refid']);
                } else {
                    mysql_query("UPDATE `forum` SET `close` = '1', `close_who` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "' WHERE `id` = " . Vars::$ID);
                    header('Location: ' . Vars::$URI . '?id=' . $res['refid'] . '&page=' . $page);
                }
            }
            break;

        case 'del':
            /*
            -----------------------------------------------------------------
            Удаление поста, предварительное напоминание
            -----------------------------------------------------------------
            */
            echo '<div class="phdr"><a href="' . $link . '"><b>' . lng('forum') . '</b></a> | ' . lng('delete_post') . '</div>' .
                 '<div class="rmenu"><p>';
            if ($posts == 1)
                echo lng('delete_last_post_warning') . '<br />';
            echo lng('delete_confirmation') . '</p>' .
                 '<p><a href="' . $link . '">' . lng('cancel') . '</a> | <a href="' . Vars::$URI . '?act=editpost&amp;mod=delete&amp;id=' . Vars::$ID . '">' . lng('delete') . '</a>';
            if (Vars::$USER_RIGHTS == 9)
                echo ' | <a href="' . Vars::$URI . '?act=editpost&amp;mod=delete&amp;hide&amp;id=' . Vars::$ID . '">' . lng('hide') . '</a>';
            echo '</p></div>';
            echo '<div class="phdr"><small>' . lng('delete_post_help') . '</small></div>';
            break;

        default:
            /*
            -----------------------------------------------------------------
            Редактирование поста
            -----------------------------------------------------------------
            */
            $msg = isset($_POST['msg']) ? trim($_POST['msg']) : '';
            if (isset($_POST['msgtrans']))
                $msg = Functions::translit($msg);
            if (isset($_POST['submit'])) {
                if (empty($_POST['msg'])) {
                    echo Functions::displayError(lng('error_empty_message'), '<a href="' . Vars::$URI . '?act=editpost&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a>');
                    exit;
                }
                mysql_query("UPDATE `forum` SET
                    `tedit` = '" . time() . "',
                    `edit` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                    `kedit` = '" . ($res['kedit'] + 1) . "',
                    `text` = '" . mysql_real_escape_string($msg) . "'
                    WHERE `id` = " . Vars::$ID);
                header('Location: ' . Vars::$URI . '?id=' . $res['refid'] . '&page=' . $page);
            } else {
                $msg_pre = Validate::filterString($msg, 1, 1);
                if (Vars::$USER_SET['smileys'])
                    $msg_pre = Functions::smileys($msg_pre, Vars::$USER_RIGHTS ? 1 : 0);
                $msg_pre = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $msg_pre);
                echo '<div class="phdr"><a href="' . $link . '"><b>' . lng('forum') . '</b></a> | ' . lng('edit_message') . '</div>';
                if ($msg && !isset($_POST['submit'])) {
                    $user = mysql_fetch_assoc(mysql_query("SELECT * FROM `users` WHERE `id` = '" . $res['user_id'] . "' LIMIT 1"));
                    echo '<div class="list1">' . Functions::displayUser($user, array('iphide' => 1, 'header' => '<span class="gray">(' . Functions::displayDate($res['time']) . ')</span>', 'body' => $msg_pre)) . '</div>';
                }
                echo '<div class="rmenu"><form name="form" action="?act=editpost&amp;id=' . Vars::$ID . '&amp;start=' . Vars::$START . '" method="post"><p>';
                if (!Vars::$IS_MOBILE)
                    echo TextParser::autoBB('form', 'msg');
                echo '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg">' . (empty($_POST['msg']) ? htmlentities($res['text'], ENT_QUOTES, 'UTF-8') : Validate::filterString($_POST['msg'])) . '</textarea><br/>';
                if (Vars::$USER_SET['translit'])
                    echo '<input type="checkbox" name="msgtrans" value="1" ' . (isset($_POST['msgtrans']) ? 'checked="checked" ' : '') . '/> ' . lng('translit');
                echo '</p><p><input type="submit" name="submit" value="' . lng('save') . '" style="width: 107px; cursor: pointer;"/> ' .
                     ($set_forum['preview'] ? '<input type="submit" value="' . lng('preview') . '" style="width: 107px; cursor: pointer;"/>' : '') .
                     '</p></form></div>' .
                     '<div class="phdr"><a href="../pages/faq.php?act=trans">' . lng('translit') . '</a> | <a href="../pages/faq.php?act=smileys">' . lng('smileys') . '</a></div>' .
                     '<p><a href="' . $link . '">' . lng('back') . '</a></p>';
            }
    }
} else {
    /*
    -----------------------------------------------------------------
    Выводим сообщения об ошибках
    -----------------------------------------------------------------
    */
    echo Functions::displayError($error);
}