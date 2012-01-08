<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNADM') or die('Error: restricted access');

//TODO: Доработать под новую таблицу юзеров!

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 9) {
    header('Location: http://mobicms.net/404.php');
    exit;
}

$user = false;
$error = false;
if (Vars::$ID && Vars::$ID != Vars::$USER_ID) {
    // Получаем данные юзера
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = " . Vars::$ID);
    if (mysql_num_rows($req)) {
        $user = mysql_fetch_assoc($req);
        if ($user['rights'] > Vars::$USER_RIGHTS)
            $error = $lng['error_usrdel_rights'];
    } else {
        $error = $lng['error_user_not_exist'];
    }
} else {
    $error = $lng['error_wrong_data'];
}
if (!$error) {
    // Считаем комментарии в галерее
    $comm_gal = mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery` WHERE `avtor` = '" . $user['name'] . "' AND `type` = 'km'"), 0);
    // Считаем комментарии в библиотеке
    $comm_lib = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `avtor` = '" . $user['name'] . "' AND `type` = 'komm'"), 0);
    // Считаем комментарии к загрузкам
    $comm_dl = mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `avtor` = '" . $user['name'] . "' AND `type` = 'komm'"), 0);
    // Считаем посты в личных гостевых
    $comm_gb = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_users_guestbook` WHERE `user_id` = '" . $user['user_id'] . "'"), 0);
    // Считаем комментарии в личных альбомах
    $comm_al = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_comments` WHERE `user_id` = '" . $user['user_id'] . "'"), 0);
    $comm_count = $comm_gal + $comm_lib + $comm_dl + $comm_gb + $comm_al;
    // Считаем посты в Гостевой
    $guest_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `user_id` = '" . $user['user_id'] . "'"), 0);
    // Считаем созданные темы на Форуме
    $forumt_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `user_id` = '" . $user['user_id'] . "' AND `type` = 't' AND `close` != '1'"), 0);
    // Считаем посты на Форуме
    $forump_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `user_id` = '" . $user['user_id'] . "' AND `type` = 'm'  AND `close` != '1'"), 0);
    echo '<div class="phdr"><a href="index.php"><b>' . $lng['admin_panel'] . '</b></a> | ' . $lng['user_del'] . '</div>';
    // Выводим краткие данные
    echo '<div class="user"><p>' . Functions::displayUser($user, array(
                                                                       'lastvisit' => 1,
                                                                       'iphist' => 1
                                                                  )) . '</p></div>';
    switch (Vars::$MOD) {
        case 'del':
            /*
            -----------------------------------------------------------------
            Удаляем личные данные
            -----------------------------------------------------------------
            */
            // Удаляем личную Гостевую
            mysql_query("DELETE FROM `cms_users_guestbook` WHERE `sub_id` = '" . $user['user_id'] . "'");
            // Удаляем личные Фотоальбомы
            $req = mysql_query("SELECT * FROM `cms_album_files` WHERE `user_id` = '" . $user['user_id'] . "'");
            if (mysql_num_rows($req)) {
                while ($res = mysql_fetch_assoc($req)) {
                    // Удаляем файлы картинок
                    @unlink('../files/users/album/' . $user['user_id'] . '/' . $res['img_name']);
                    @unlink('../files/users/album/' . $user['user_id'] . '/' . $res['tmb_name']);
                    // Удаляем комментарии к файлам
                    mysql_query("DELETE FROM `cms_album_comments` WHERE `sub_id` = '" . $res['id'] . "'");
                }
            }
            rmdir('../files/users/album/' . $user['user_id']);
            mysql_query("DELETE FROM `cms_album_files` WHERE `user_id` = '" . $user['user_id'] . "'");
            mysql_query("DELETE FROM `cms_album_cat` WHERE `user_id` = '" . $user['user_id'] . "'");
            mysql_query("DELETE FROM `cms_album_downloads` WHERE `user_id` = '" . $user['user_id'] . "'");
            mysql_query("DELETE FROM `cms_album_views` WHERE `user_id` = '" . $user['user_id'] . "'");
            mysql_query("DELETE FROM `cms_album_votes` WHERE `user_id` = '" . $user['user_id'] . "'");
            // Удаляем почту
            mysql_query("DELETE FROM `privat` WHERE `user` = '" . $user['name'] . "'");
            mysql_query("DELETE FROM `privat` WHERE `author` = '" . $user['name'] . "' AND `type` = 'out' AND `chit` = 'no'");
            // Удаляем карму
            mysql_query("DELETE FROM `karma_users` WHERE `karma_user` = '" . $user['user_id'] . "'");
            // Удаляем комментарии
            if (isset($_POST['comments'])) {
                if ($comm_gal) {
                    // Удаляем из Галреи
                    mysql_query("DELETE FROM `gallery` WHERE `avtor` = '" . $user['name'] . "' AND `type` = 'km'");
                }
                if ($comm_lib) {
                    // Удаляем из Библиотеки
                    mysql_query("DELETE FROM `lib` WHERE `avtor` = '" . $user['name'] . "' AND `type` = 'komm'");
                }
                if ($comm_dl) {
                    // Удаляем из Загрузок
                    mysql_query("DELETE FROM `download` WHERE `avtor` = '" . $user['name'] . "' AND `type` = 'komm'");
                }
                if ($comm_gb) {
                    // Удаляем комментарии из личных гостевых
                    mysql_query("DELETE FROM `cms_users_guestbook` WHERE `user_id` = '" . $user['user_id'] . "'");
                }
                if ($comm_al) {
                    // Удаляем комментарии из личных фотоальбомов
                    mysql_query("DELETE FROM `cms_album_comments` WHERE `user_id` = '" . $user['user_id'] . "'");
                }
            }
            // Удаляем посты в Гостевой
            if ($guest_count && isset($_POST['guest'])) {
                mysql_query("DELETE FROM `guest` WHERE `user_id` = '" . $user['user_id'] . "'");
            }
            // Скрываем темы на форуме
            if ($forumt_count && isset($_POST['forum'])) {
                mysql_query("UPDATE `forum` SET `close` = '1', `close_who` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "' WHERE `type` = 't' AND `user_id` = '" . $user['user_id'] . "'");
            }
            // Скрываем посты на форуме
            if (isset($_POST['forum'])) {
                mysql_query("UPDATE `forum` SET `close` = '1', `close_who` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "' WHERE `type` = 'm' AND `user_id` = '" . $user['user_id'] . "'");
            }
            // Удаляем метки прочтения на Форуме
            mysql_query("DELETE FROM `cms_forum_rdm` WHERE `user_id` = '" . $user['user_id'] . "'");
            // Удаляем историю нарушений
            mysql_query("DELETE FROM `cms_ban_users` WHERE `user_id` = '" . $user['user_id'] . "'");
            // Удаляем историю IP
            mysql_query("DELETE FROM `cms_users_iphistory` WHERE `user_id` = '" . $user['user_id'] . "'");
            // Удаляем пользователя
            mysql_query("DELETE FROM `users` WHERE `id` = '" . $user['user_id'] . "'");
            // Оптимизируем таблицы
            mysql_query("OPTIMIZE TABLE `cms_users_iphistory` , `cms_ban_users` , `guest`, `cms_album_comments`, `cms_users_guestbook`, `karma_users`, `cms_album_votes`, `cms_album_views`, `cms_album_downloads`, `cms_album_cat`, `cms_album_files`, `cms_forum_rdm`");
            echo '<div class="rmenu"><p><h3>' . $lng['user_deleted'] . '</h3></p></div>';
            break;

        default:
            ////////////////////////////////////////////////////////////
            // Форма параметров удаления                              //
            ////////////////////////////////////////////////////////////
            echo '<form action="index.php?act=usr_del&amp;mod=del&amp;id=' . $user['user_id'] . '" method="post"><div class="menu"><p><h3>' . $lng['user_del_activity'] . '</h3>';
            if ($comm_count)
                echo '<div><input type="checkbox" value="1" name="comments" checked="checked" />&#160;' . $lng['comments'] . ' <span class="red">(' . $comm_count . ')</span></div>';
            if ($guest_count)
                echo '<div><input type="checkbox" value="1" name="guest" checked="checked" />&#160;' . $lng['guestbook'] . ' <span class="red">(' . $guest_count . ')</span></div>';
            if ($forumt_count || $forump_count) {
                echo '<div><input type="checkbox" value="1" name="forum" checked="checked" />&#160;' . $lng['forum'] . ' <span class="red">(' . $forumt_count . '&nbsp;/&nbsp;' . $forump_count . ')</span></div>';
                echo '<small><span class="gray">' . $lng['user_del_forumnote'] . '</span></small>';
            }
            echo '</p></div><div class="rmenu"><p>' . $lng['user_del_confirm'];
            echo '</p><p><input type="submit" value="' . $lng['delete'] . '" name="submit" />';
            echo '</p></div></form>';
            echo '<div class="phdr"><a href="../users/profile.php?user=' . $user['user_id'] . '">' . $lng['to_form'] . '</a></div>';
    }
} else {
    echo Functions::displayError($error);
}
echo '<p><a href="index.php?act=users">' . $lng['users_list'] . '</a><br /><a href="index.php">' . $lng['admin_panel'] . '</a></p>';