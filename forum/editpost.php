<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Error: restricted access');
require('../incfiles/head.php');
if (!$user_id || !$id) {
    echo functions::display_error($lng['error_wrong_data']);
    require('../incfiles/end.php');
    exit;
}
$req = mysql_query("SELECT * FROM `forum` WHERE `id` = '$id' AND `type` = 'm' " . ($rights >= 7 ? "" : " AND `close` != '1'") . " LIMIT 1");
if (mysql_num_rows($req)) {
    /*
    -----------------------------------------------------------------
    Предварительные проверки
    -----------------------------------------------------------------
    */
    $res = mysql_fetch_assoc($req);
    $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '$id'" . ($rights < 7 ? " AND `close` != '1'" : '')), 0) / $kmess);
    $posts = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `close` != '1'"), 0);
    $link = 'index.php?id=' . $res['refid'] . '&amp;page=' . $page;
    $error = false;
    if ($rights == 3 || $rights >= 6) {
        // Проверка для Администрации
        if ($res['user_id'] != $user_id) {
            $req_u = mysql_query("SELECT * FROM `users` WHERE `id` = '" . $res['user_id'] . "' LIMIT 1");
            if (mysql_num_rows($req_u)) {
                $res_u = mysql_fetch_assoc($req_u);
                if ($res_u['rights'] > $datauser['rights'])
                    $error = $lng_forum['error_edit_rights'] . '<br /><a href="' . $link . '">' . $lng['back'] . '</a>';
            }
        }
    } else {
        // Проверка для обычных юзеров
        if ($res['user_id'] != $user_id)
            $error = $lng_forum['error_edit_another'] . '<br /><a href="' . $link . '">' . $lng['back'] . '</a>';
        if (!$error) {
            $req_m = mysql_query("SELECT * FROM `forum` WHERE `refid` = '" . $res['refid'] . "' ORDER BY `id` DESC LIMIT 1");
            $res_m = mysql_fetch_assoc($req_m);
            if ($res_m['user_id'] != $user_id)
                $error = $lng_forum['error_edit_last'] . '<br /><a href="' . $link . '">' . $lng['back'] . '</a>';
            elseif ($res['time'] < $realtime - 300)
                $error = $lng_forum['error_edit_timeout'] . '<br /><a href="' . $link . '">' . $lng['back'] . '</a>';
        }
    }
} else {
    $error = $lng_forum['error_post_deleted'] . '<br /><a href="index.php">' . $lng['forum'] . '</a>';
}
if (!$error) {
    switch ($do) {
        case 'restore':
            /*
            -----------------------------------------------------------------
            Восстановление удаленного поста
            -----------------------------------------------------------------
            */
            $req_u = mysql_query("SELECT `postforum` FROM `users` WHERE `id` = '" . $res['user_id'] . "' LIMIT 1");
            if (mysql_num_rows($req_u)) {
                // Добавляем один балл к счетчику постов юзера
                $res_u = mysql_fetch_assoc($req_u);
                mysql_query("UPDATE `users` SET `postforum` = '" . ($res_u['postforum'] + 1) . "' WHERE `id` = '" . $res['user_id'] . "' LIMIT 1");
            }
            mysql_query("UPDATE `forum` SET `close` = '0', `close_who` = '$login' WHERE `id` = '$id' LIMIT 1");
            header('Location: ' . $link);
            break;

        case 'delete':
            /*
            -----------------------------------------------------------------
            Удаление поста и прикрепленного файла
            -----------------------------------------------------------------
            */
            if ($res['close'] != 1) {
                $req_u = mysql_query("SELECT `postforum` FROM `users` WHERE `id` = '" . $res['user_id'] . "' LIMIT 1");
                if (mysql_num_rows($req_u)) {
                    // Вычитаем один балл из счетчика постов юзера
                    $res_u = mysql_fetch_assoc($req_u);
                    $postforum = $res_u['postforum'] > 0 ? $res_u['postforum'] - 1 : 0;
                    mysql_query("UPDATE `users` SET `postforum` = '" . $postforum . "' WHERE `id` = '" . $res['user_id'] . "' LIMIT 1");
                }
            }
            if ($rights == 9 && !isset($_GET['hide'])) {
                // Удаление поста (для Супервизоров)
                $req_f = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = '$id' LIMIT 1");
                if (mysql_num_rows($req_f)) {
                    // Если есть прикрепленный файл, удаляем его
                    $res_f = mysql_fetch_assoc($req_f);
                    unlink('../files/forum/attach/' . $res_f['filename']);
                    mysql_query("DELETE FROM `cms_forum_files` WHERE `post` = '$id' LIMIT 1");
                }
                // Формируем ссылку на нужную страницу темы
                $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">" : "<") . " '$id'"), 0) / $kmess);
                mysql_query("DELETE FROM `forum` WHERE `id` = '$id' LIMIT 1");
                if ($posts < 2) {
                    // Пересылка на удаление всей темы
                    header('Location: index.php?act=deltema&id=' . $res['refid']);
                } else {
                    header('Location: index.php?id=' . $res['refid'] . '&page=' . $page);
                }
            } else {
                // Скрытие поста
                $req_f = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = '$id' LIMIT 1");
                if (mysql_num_rows($req_f)) {
                    // Если есть прикрепленный файл, скрываем его
                    mysql_query("UPDATE `cms_forum_files` SET `del` = '1' WHERE `post` = '$id' LIMIT 1");
                }
                if ($posts == 1) {
                    // Если это был последний пост темы, то скрываем саму тему
                    $res_l = mysql_fetch_assoc(mysql_query("SELECT `refid` FROM `forum` WHERE `id` = '" . $res['refid'] . "' LIMIT 1"));
                    mysql_query("UPDATE `forum` SET `close` = '1', `close_who` = '$login' WHERE `id` = '" . $res['refid'] . "' AND `type` = 't' LIMIT 1");
                    header('Location: index.php?id=' . $res_l['refid']);
                } else {
                    mysql_query("UPDATE `forum` SET `close` = '1', `close_who` = '$login' WHERE `id` = '$id' LIMIT 1");
                    header('Location: index.php?id=' . $res['refid'] . '&page=' . $page);
                }
            }
            break;

        case 'del':
            /*
            -----------------------------------------------------------------
            Удаление поста, предварительное напоминание
            -----------------------------------------------------------------
            */
            echo '<div class="phdr"><a href="' . $link . '"><b>' . $lng['forum'] . '</b></a> | ' . $lng_forum['delete_post'] . '</div>' .
                '<div class="rmenu"><p>';
            if ($posts == 1)
                echo $lng_forum['delete_last_post_warning'] . '<br />';
            echo $lng['delete_confirmation'] . '</p>' .
                '<p><a href="' . $link . '">' . $lng['cancel'] . '</a> | <a href="index.php?act=editpost&amp;do=delete&amp;id=' . $id . '">' . $lng['delete'] . '</a>';
            if ($rights == 9)
                echo ' | <a href="index.php?act=editpost&amp;do=delete&amp;hide&amp;id=' . $id . '">' . $lng['hide'] . '</a>';
            echo '</p></div>';
            echo '<div class="phdr"><small>' . $lng_forum['delete_post_help'] . '</small></div>';
            break;

        default:
            /*
            -----------------------------------------------------------------
            Редактирование поста
            -----------------------------------------------------------------
            */
            if (isset($_POST['submit'])) {
                if (empty($_POST['msg'])) {
                    echo functions::display_error($lng_forum['error_message_empty'], '<a href="index.php?act=editpost&amp;id=' . $id . '">' . $lng['repeat'] . '</a>');
                    require('../incfiles/end.php');
                    exit;
                }
                $msg = mysql_real_escape_string(trim($_POST['msg']));
                if ($_POST['msgtrans'] == 1) {
                    $msg = functions::trans($msg);
                }
                mysql_query("UPDATE `forum` SET
                    `tedit` = '$realtime',
                    `edit` = '$login',
                    `kedit` = '" . ($res['kedit'] + 1) . "',
                    `text` = '$msg'
                    WHERE `id` = '$id'
                ");
                header('Location: index.php?id=' . $res['refid'] . '&page=' . $page);
            } else {
                echo '<div class="phdr"><a href="' . $link . '"><b>' . $lng['forum'] . '</b></a> | ' . $lng_forum['edit_message'] . '</div>' .
                    '<div class="rmenu"><form action="?act=editpost&amp;id=' . $id . '&amp;start=' . $start . '" method="post">' .
                    '<textarea cols="' . $set_user['field_w'] . '" rows="' . $set_user['field_h'] . '" name="msg">' . htmlentities($res['text'], ENT_QUOTES, 'UTF-8') . '</textarea><br/>';
                if ($set_user['translit'])
                    echo '<input type="checkbox" name="msgtrans" value="1" /> ' . $lng['translit'] . '<br/>';
                echo '<input type="submit" name="submit" value="' . $lng['save'] . '"/></form></div>' .
                    '<div class="phdr"><a href="../pages/faq.php?act=trans">' . $lng['translit'] . '</a> | <a href="../pages/faq.php?act=smileys">' . $lng['smileys'] . '</a></div>' .
                    '<p><a href="' . $link . '">' . $lng['back'] . '</a></p>';
            }
    }
} else {
    /*
    -----------------------------------------------------------------
    Выводим сообщения об ошибках
    -----------------------------------------------------------------
    */
    echo functions::display_error($error);
}
?>