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
    echo Functions::displayError(__('error_wrong_data'));
    exit;
}
$url = Router::getUri(2);

$nick = DB::PDO()->quote(Vars::$USER_NICKNAME);
$req = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 'm' " . (Vars::$USER_RIGHTS >= 7 ? "" : " AND `close` != '1'"));
if ($req->rowCount()) {
    /*
    -----------------------------------------------------------------
    Предварительные проверки
    -----------------------------------------------------------------
    */
    $res = $req->fetch();
    if (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 3 && Vars::$USER_ID) {
        $topic = DB::PDO()->query("SELECT `curators` FROM `forum` WHERE `id` = " . $res['refid'])->fetch();
        $curators = !empty($topic['curators']) ? unserialize($topic['curators']) : array();
        if (array_key_exists(Vars::$USER_ID, $curators)) {
            Vars::$USER_RIGHTS = 3;
        }
    }
    $page = ceil(DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">= " : "<= ") . Vars::$ID . (Vars::$USER_RIGHTS < 7 ? " AND `close` != '1'" : ''))->fetchColumn() / Vars::$USER_SET['page_size']);
    $posts = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `close` != '1'")->fetchColumn();
    $link = $url . '?id=' . $res['refid'] . '&amp;page=' . $page;
    $error = FALSE;
    if (Vars::$USER_RIGHTS == 3 || Vars::$USER_RIGHTS >= 6) {
        // Проверка для Администрации
        if ($res['user_id'] != Vars::$USER_ID) {
            $req_u = DB::PDO()->query("SELECT * FROM `users` WHERE `id` = '" . $res['user_id'] . "'");
            if ($req_u->rowCount()) {
                $res_u = $req_u->fetch();
                if ($res_u['rights'] > Vars::$USER_RIGHTS)
                    $error = __('error_edit_rights') . '<br /><a href="' . $link . '">' . __('back') . '</a>';
            }
        }
    } else {
        // Проверка для обычных юзеров
        if ($res['user_id'] != Vars::$USER_ID)
            $error = __('error_edit_another') . '<br /><a href="' . $link . '">' . __('back') . '</a>';
        if (!$error) {
            $req_m = DB::PDO()->query("SELECT * FROM `forum` WHERE `refid` = '" . $res['refid'] . "' ORDER BY `id` DESC LIMIT 1");
            $res_m = $req_m->fetch();
            if ($res_m['user_id'] != Vars::$USER_ID)
                $error = __('error_edit_last') . '<br /><a href="' . $link . '">' . __('back') . '</a>';
            elseif ($res['time'] < time() - 300)
                $error = __('error_edit_timeout') . '<br /><a href="' . $link . '">' . __('back') . '</a>';
        }
    }
} else {
    $error = __('error_post_deleted') . '<br /><a href="' . $url . '">' . __('forum') . '</a>';
}
if (!$error) {
    switch (Vars::$MOD) {
        case 'restore':
            /*
            -----------------------------------------------------------------
            Восстановление удаленного поста
            -----------------------------------------------------------------
            */
            $req_u = DB::PDO()->query("SELECT `count_forum` FROM `users` WHERE `id` = '" . $res['user_id'] . "'");
            if ($req_u->rowCount()) {
                // Добавляем один балл к счетчику постов юзера
                $res_u = $req_u->fetch();
                DB::PDO()->exec("UPDATE `users` SET `count_forum` = '" . ($res_u['count_forum'] + 1) . "' WHERE `id` = '" . $res['user_id'] . "'");
            }
            DB::PDO()->exec("UPDATE `forum` SET `close` = '0', `close_who` = " . $nick . " WHERE `id` = " . Vars::$ID);
            $req_f = DB::PDO()->query("SELECT * FROM `cms_forum_files` WHERE `post` = " . Vars::$ID . " LIMIT 1");
            if ($req_f->rowCount()) {
                DB::PDO()->exec("UPDATE `cms_forum_files` SET `del` = '0' WHERE `post` = " . Vars::$ID . " LIMIT 1");
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
                $req_u = DB::PDO()->query("SELECT `count_forum` FROM `users` WHERE `id` = '" . $res['user_id'] . "'");
                if ($req_u->rowCount()) {
                    // Вычитаем один балл из счетчика постов юзера
                    $res_u = $req_u->fetch();
                    $postforum = $res_u['count_forum'] > 0 ? $res_u['count_forum'] - 1 : 0;
                    DB::PDO()->exec("UPDATE `users` SET `count_forum` = '" . $postforum . "' WHERE `id` = '" . $res['user_id'] . "'");
                }
            }
            if (Vars::$USER_RIGHTS == 9 && !isset($_GET['hide'])) {
                // Удаление поста (для Супервизоров)
                $req_f = DB::PDO()->query("SELECT * FROM `cms_forum_files` WHERE `post` = " . Vars::$ID . " LIMIT 1");
                if ($req_f->rowCount()) {
                    // Если есть прикрепленный файл, удаляем его
                    $res_f = $req_f->fetch();
                    unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $res_f['filename']);
                    DB::PDO()->exec("DELETE FROM `cms_forum_files` WHERE `post` = " . Vars::$ID . " LIMIT 1");
                }
                // Формируем ссылку на нужную страницу темы
                $page = ceil(DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? "> " : "< ") . Vars::$ID)->fetchColumn() / Vars::$USER_SET['page_size']);
                DB::PDO()->exec("DELETE FROM `forum` WHERE `id` = " . Vars::$ID);
                if ($posts < 2) {
                    // Пересылка на удаление всей темы
                    header('Location: ' . $url . '?act=deltema&id=' . $res['refid']);
                } else {
                    header('Location: ' . $url . '?id=' . $res['refid'] . '&page=' . $page);
                }
            } else {
                // Скрытие поста
                $req_f = DB::PDO()->query("SELECT * FROM `cms_forum_files` WHERE `post` = " . Vars::$ID . " LIMIT 1");
                if ($req_f->rowCount()) {
                    // Если есть прикрепленный файл, скрываем его
                    DB::PDO()->exec("UPDATE `cms_forum_files` SET `del` = '1' WHERE `post` = " . Vars::$ID . " LIMIT 1");
                }
                if ($posts == 1) {
                    // Если это был последний пост темы, то скрываем саму тему
                    $res_l = DB::PDO()->query("SELECT `refid` FROM `forum` WHERE `id` = '" . $res['refid'] . "'")->fetch();
                    DB::PDO()->exec("UPDATE `forum` SET `close` = '1', `close_who` = " . $nick . " WHERE `id` = '" . $res['refid'] . "' AND `type` = 't'");
                    header('Location: ' . $url . '?id=' . $res_l['refid']);
                } else {
                    DB::PDO()->exec("UPDATE `forum` SET `close` = '1', `close_who` = " . $nick . " WHERE `id` = " . Vars::$ID);
                    header('Location: ' . $url . '?id=' . $res['refid'] . '&page=' . $page);
                }
            }
            break;

        case 'del':
            /*
            -----------------------------------------------------------------
            Удаление поста, предварительное напоминание
            -----------------------------------------------------------------
            */
            echo '<div class="phdr"><a href="' . $link . '"><b>' . __('forum') . '</b></a> | ' . __('delete_post') . '</div>' .
                '<div class="rmenu"><p>';
            if ($posts == 1)
                echo __('delete_last_post_warning') . '<br />';
            echo __('delete_confirmation') . '</p>' .
                '<p><a href="' . $link . '">' . __('cancel') . '</a> | <a href="' . $url . '?act=editpost&amp;mod=delete&amp;id=' . Vars::$ID . '">' . __('delete') . '</a>';
            if (Vars::$USER_RIGHTS == 9)
                echo ' | <a href="' . $url . '?act=editpost&amp;mod=delete&amp;hide&amp;id=' . Vars::$ID . '">' . __('hide') . '</a>';
            echo '</p></div>';
            echo '<div class="phdr"><small>' . __('delete_post_help') . '</small></div>';
            break;

        default:
            /*
            -----------------------------------------------------------------
            Редактирование поста
            -----------------------------------------------------------------
            */
            $msg = isset($_POST['msg']) ? trim($_POST['msg']) : '';
            if (isset($_POST['submit'])) {
                if (empty($_POST['msg'])) {
                    echo Functions::displayError(__('error_empty_message'), '<a href="' . $url . '?act=editpost&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
                    exit;
                }

                $STH = DB::PDO()->prepare('
                    UPDATE `forum`
                    (`tedit`, `edit`, `kedit`, `text`)
                    VALUES (?, ?, ?, ?)
                    WHERE `id` = ' . Vars::$ID
                );

                $STH->execute(array(
                    time(),
                    Vars::$USER_NICKNAME,
                    ++$res['kedit'],
                    $msg
                ));
                $STH = null;

                header('Location: ' . $url . '?id=' . $res['refid'] . '&page=' . $page);
            } else {
                $msg_pre = Validate::checkout($msg, 1, 1);
                if (Vars::$USER_SET['smilies'])
                    $msg_pre = Functions::smilies($msg_pre, Vars::$USER_RIGHTS ? 1 : 0);
                $msg_pre = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $msg_pre);
                echo '<div class="phdr"><a href="' . $link . '"><b>' . __('forum') . '</b></a> | ' . __('edit_message') . '</div>';
                if ($msg && !isset($_POST['submit'])) {
                    $user = DB::PDO()->query("SELECT * FROM `users` WHERE `id` = '" . $res['user_id'] . "' LIMIT 1")->fetch();
                    echo '<div class="list1">' . Functions::displayUser($user, array('iphide' => 1, 'header' => '<span class="gray">(' . Functions::displayDate($res['time']) . ')</span>', 'body' => $msg_pre)) . '</div>';
                }
                echo '<div class="rmenu"><form name="form" action="?act=editpost&amp;id=' . Vars::$ID . '&amp;start=' . Vars::$START . '" method="post"><p>';
                if (!Vars::$IS_MOBILE)
                    echo TextParser::autoBB('form', 'msg');
                echo '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg">' . (empty($_POST['msg']) ? htmlentities($res['text'], ENT_QUOTES, 'UTF-8') : Validate::checkout($_POST['msg'])) . '</textarea><br/>';
                if (Vars::$USER_SET['translit'])
                    echo '<input type="checkbox" name="msgtrans" value="1" ' . (isset($_POST['msgtrans']) ? 'checked="checked" ' : '') . '/> ' . __('translit');
                echo '</p><p><input type="submit" name="submit" value="' . __('save') . '" style="width: 107px; cursor: pointer;"/> ' .
                    ($set_forum['preview'] ? '<input type="submit" value="' . __('preview') . '" style="width: 107px; cursor: pointer;"/>' : '') .
                    '</p></form></div>' .
                    '<div class="phdr"><a href="../pages/faq.php?act=trans">' . __('translit') . '</a> | <a href="../pages/faq.php?act=smilies">' . __('smilies') . '</a></div>' .
                    '<p><a href="' . $link . '">' . __('back') . '</a></p>';
                //TODO: Исправить ссылку на каталог смайлов
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