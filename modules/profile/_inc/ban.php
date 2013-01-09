<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_PROFILE') or die('Error: restricted access');

$ban = isset($_GET['ban']) ? intval($_GET['ban']) : 0;
switch (Vars::$MOD) {
    case 'do':
        /*
        -----------------------------------------------------------------
        Баним пользователя (добавляем Бан в базу)
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS < 1 || (Vars::$USER_RIGHTS < 6 && $user['rights']) || (Vars::$USER_RIGHTS <= $user['rights'])) {
            echo Functions::displayError(__('ban_rights'));
        } else {
            echo '<div class="phdr"><b>' . __('ban_do') . '</b></div>';
            echo '<div class="rmenu"><p>' . Functions::displayUser($user) . '</p></div>';
            if (isset($_POST['submit'])) {
                $error = FALSE;
                $term = isset($_POST['term']) ? intval($_POST['term']) : FALSE;
                $timeval = isset($_POST['timeval']) ? intval($_POST['timeval']) : FALSE;
                $time = isset($_POST['time']) ? intval($_POST['time']) : FALSE;
                $reason = !empty($_POST['reason']) ? trim($_POST['reason']) : '';
                $banref = isset($_POST['banref']) ? intval($_POST['banref']) : FALSE;
                if (empty($reason) && empty($banref))
                    $reason = __('reason_not_specified');
                if (empty($term) || empty($timeval) || empty($time) || $timeval < 1)
                    $error = __('error_data');
                if (Vars::$USER_RIGHTS == 1 && $term != 14 || Vars::$USER_RIGHTS == 2 && $term != 12 || Vars::$USER_RIGHTS == 3 && $term != 11 || Vars::$USER_RIGHTS == 4 && $term != 16 || Vars::$USER_RIGHTS == 5 && $term != 15)
                    $error = __('error_rights_section');
                if (mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $user['user_id'] . "' AND `ban_time` > '" . time() . "' AND `ban_type` = '$term'"), 0))
                    $error = __('error_ban_exist');
                switch ($time) {
                    case 2:
                        // Часы
                        if ($timeval > 24)
                            $timeval = 24;
                        $timeval = $timeval * 3600;
                        break;

                    case 3:
                        // Дни
                        if ($timeval > 30)
                            $timeval = 30;
                        $timeval = $timeval * 86400;
                        break;

                    case 4:
                        // До отмены (на 10 лет)
                        $timeval = 315360000;
                        break;

                    default:
                        // Минуты
                        if ($timeval > 60)
                            $timeval = 60;
                        $timeval = $timeval * 60;
                }
                if (Vars::$USER_RIGHTS < 6 && $timeval > 86400)
                    $timeval = 86400;
                if (Vars::$USER_RIGHTS < 7 && $timeval > 2592000)
                    $timeval = 2592000;
                if (!$error) {
                    // Заносим в базу
                    mysql_query("INSERT INTO `cms_ban_users` SET
                        `user_id` = '" . $user['user_id'] . "',
                        `ban_time` = '" . (time() + $timeval) . "',
                        `ban_while` = '" . time() . "',
                        `ban_type` = '$term',
                        `ban_who` = '" . Vars::$USER_DATA['nickname'] . "',
                        `ban_reason` = '" . mysql_real_escape_string($reason) . "'
                    ");
                    if ($set_karma['on']) {
                        $points = $set_karma['karma_points'] * 2;
                        mysql_query("INSERT INTO `karma_users` SET
                            `user_id` = '0',
                            `name` = '" . __('system') . "',
                            `karma_user` = '" . $user['user_id'] . "',
                            `points` = '$points',
                            `type` = '0',
                            `time` = '" . time() . "',
                            `text` = '" . __('ban') . " (" . __('ban_' . $term) . ")'
                        ");
                        mysql_query("UPDATE `users` SET
                            `karma_minus` = '" . ($user['karma_minus'] + $points) . "'
                            WHERE `id` = '" . $user['user_id'] . "'
                        ");
                        $text = ' ' . __('also_received') . ' <span class="red">-' . $points . ' ' . __('points') . '</span> ' . __('to_karma');
                    }
                    echo '<div class="rmenu"><p><h3>' . __('user_banned') . ' ' . $text . '</h3></p></div>';
                } else {
                    echo Functions::displayError($error);
                }
            } else {
                // Форма параметров бана
                echo '<form action="profile.php?act=ban&amp;mod=do&amp;user=' . $user['user_id'] . '" method="post">' .
                     '<div class="menu"><p><h3>' . __('ban_type') . '</h3>';
                if (Vars::$USER_RIGHTS >= 6) {
                    // Блокировка
                    echo '<div><input name="term" type="radio" value="1" checked="checked" />&#160;' . __('ban_1') . '</div>';
                    // Приват
                    echo '<div><input name="term" type="radio" value="3" />&#160;' . __('ban_3') . '</div>';
                    // Комментарии
                    echo '<div><input name="term" type="radio" value="10" />&#160;' . __('ban_10') . '</div>';
                    // Гостевая
                    echo '<div><input name="term" type="radio" value="13" />&#160;' . __('ban_13') . '</div>';
                }
                if (Vars::$USER_RIGHTS == 3 || Vars::$USER_RIGHTS >= 6) {
                    // Форум
                    echo '<div><input name="term" type="radio" value="11" ' . (Vars::$USER_RIGHTS == 3 ? 'checked="checked"'
                            : '') . '/>&#160;' . __('ban_11') . '</div>';
                }
                if (Vars::$USER_RIGHTS == 1 || Vars::$USER_RIGHTS >= 6) {
                    // Галерея
                    echo '<div><input name="term" type="radio" value="14" />&#160;' . __('ban_14') . '</div>';
                }
                if (Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) {
                    // Библиотека
                    echo '<div><input name="term" type="radio" value="15" />&#160;' . __('ban_15') . '</div>';
                }
                if (Vars::$USER_RIGHTS == 2 || Vars::$USER_RIGHTS >= 6) {
                    // Чат
                    echo '<div><input name="term" type="radio" value="12" />&#160;' . __('ban_12') . '</div>';
                }
                echo '</p><p><h3>' . __('ban_time') . '</h3>' .
                     '&#160;<input type="text" name="timeval" size="2" maxlength="2" value="12"/>&#160;' . __('time') . '<br/>' .
                     '<input name="time" type="radio" value="1" />&#160;' . __('ban_time_minutes') . '<br />' .
                     '<input name="time" type="radio" value="2" checked="checked" />&#160;' . __('ban_time_hours') . '<br />';
                if (Vars::$USER_RIGHTS >= 6)
                    echo '<input name="time" type="radio" value="3" />&#160;' . __('ban_time_days') . '<br />';
                if (Vars::$USER_RIGHTS >= 7)
                    echo '<input name="time" type="radio" value="4" />&#160;<span class="red">' . __('ban_time_before_cancel') . '</span>';
                echo '</p><p><h3>' . __('reason') . '</h3>';
                if (isset($_GET['fid'])) {
                    // Если бан из форума, фиксируем ID поста
                    $fid = intval($_GET['fid']);
                    echo '&#160;' . __('infringement') . ' <a href="' . Vars::$HOME_URL . 'forum/?act=post&amp;id=' . $fid . '">' . __('in_forum') . '</a><br />' .
                         '<input type="hidden" value="' . $fid . '" name="banref" />';
                }
                echo '&#160;<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="reason"></textarea>' .
                     '</p><p><input type="submit" value="' . __('ban_do') . '" name="submit" />' .
                     '</p></div></form>';
            }
            echo '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '">' . __('profile') . '</a></div>';
        }
        break;

    case 'cancel':
        /*
        -----------------------------------------------------------------
        Разбаниваем пользователя (с сохранением истории)
        -----------------------------------------------------------------
        */
        if (!$ban || $user['user_id'] == Vars::$USER_ID || Vars::$USER_RIGHTS < 7)
            echo Functions::displayError(__('error_wrong_data'));
        else {
            $req = mysql_query("SELECT * FROM `cms_ban_users` WHERE `id` = '$ban' AND `user_id` = '" . $user['user_id'] . "'");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                $error = FALSE;
                if ($res['ban_time'] < time())
                    $error = __('error_ban_not_active');
                if (!$error) {
                    echo '<div class="phdr"><b>' . __('ban_cancel') . '</b></div>';
                    echo '<div class="gmenu"><p>' . Functions::displayUser($user) . '</p></div>';
                    if (isset($_POST['submit'])) {
                        mysql_query("UPDATE `cms_ban_users` SET `ban_time` = '" . time() . "' WHERE `id` = '$ban'");
                        echo '<div class="gmenu"><p><h3>' . __('ban_cancel_confirmation') . '</h3></p></div>';
                    } else {
                        echo '<form action="profile.php?act=ban&amp;mod=cancel&amp;user=' . $user['user_id'] . '&amp;ban=' . $ban . '" method="POST">' .
                             '<div class="menu"><p>' . __('ban_cancel_help') . '</p>' .
                             '<p><input type="submit" name="submit" value="' . __('ban_cancel_do') . '" /></p>' .
                             '</div></form>' .
                             '<div class="phdr"><a href="profile.php?act=ban&amp;user=' . $user['user_id'] . '">' . __('back') . '</a></div>';
                    }
                } else {
                    echo Functions::displayError($error);
                }
            } else {
                echo Functions::displayError(__('error_wrong_data'));
            }
        }
        break;

    case 'delete':
        /*
        -----------------------------------------------------------------
        Удаляем бан (с удалением записи из истории)
        -----------------------------------------------------------------
        */
        if (!$ban || Vars::$USER_RIGHTS < 9)
            echo Functions::displayError(__('error_wrong_data'));
        else {
            $req = mysql_query("SELECT * FROM `cms_ban_users` WHERE `id` = '$ban' AND `user_id` = '" . $user['user_id'] . "'");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                echo '<div class="phdr"><b>' . __('ban_delete') . '</b></div>' .
                     '<div class="gmenu"><p>' . Functions::displayUser($user) . '</p></div>';
                if (isset($_POST['submit'])) {
                    mysql_query("DELETE FROM `karma_users` WHERE `karma_user` = '" . $user['user_id'] . "' AND `user_id` = '0' AND `time` = '" . $res['ban_while'] . "' LIMIT 1");
                    $points = $set_karma['karma_points'] * 2;
                    mysql_query("UPDATE `users` SET
                        `karma_minus` = '" . ($user['karma_minus'] > $points ? $user['karma_minus'] - $points : 0) . "'
                        WHERE `id` = '" . $user['user_id'] . "'
                    ");
                    mysql_query("DELETE FROM `cms_ban_users` WHERE `id` = '$ban'");
                    echo '<div class="gmenu"><p><h3>' . __('ban_deleted') . '</h3><a href="profile.php?act=ban&amp;user=' . $user['user_id'] . '">' . __('continue') . '</a></p></div>';
                } else {
                    echo '<form action="profile.php?act=ban&amp;mod=delete&amp;user=' . $user['user_id'] . '&amp;ban=' . $ban . '" method="POST">' .
                         '<div class="menu"><p>' . __('ban_delete_help') . '</p>' .
                         '<p><input type="submit" name="submit" value="' . __('delete') . '" /></p>' .
                         '</div></form>' .
                         '<div class="phdr"><a href="profile.php?act=ban&amp;user=' . $user['user_id'] . '">' . __('back') . '</a></div>';
                }
            } else {
                echo Functions::displayError(__('error_wrong_data'));
            }
        }
        break;

    case 'delhist':
        /*
        -----------------------------------------------------------------
        Очищаем историю нарушений юзера
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS == 9) {
            echo '<div class="phdr"><b>' . __('infringements_history') . '</b></div>' .
                 '<div class="gmenu"><p>' . Functions::displayUser($user) . '</p></div>';
            if (isset($_POST['submit'])) {
                mysql_query("DELETE FROM `cms_ban_users` WHERE `user_id` = '" . $user['user_id'] . "'");
                echo '<div class="gmenu"><h3>' . __('history_cleared') . '</h3></div>';
            } else {
                echo '<form action="profile.php?act=ban&amp;mod=delhist&amp;user=' . $user['user_id'] . '" method="post">' .
                     '<div class="menu"><p>' . __('clear_confirmation') . '</p>' .
                     '<p><input type="submit" value="' . __('clear') . '" name="submit" />' .
                     '</p></div></form>';
            }
            $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $user['user_id'] . "'"), 0);
            echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>' .
                 '<p>' . ($total
                    ? '<a href="profile.php?act=ban&amp;user=' . $user['user_id'] . '">' . __('infringements_history') . '</a><br />'
                    : '') .
                 '<a href="../admin?act=ban_panel">' . __('ban_panel') . '</a></p>';
        } else {
            echo Functions::displayError(__('error_rights_clear'));
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        История нарушений
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '"><b>' . __('profile') . '</b></a> | ' . __('infringements_history') . '</div>';
        // Меню
        $menu = array();
        if (Vars::$USER_RIGHTS >= 6)
            $menu[] = '<a href="../admin?act=ban_panel">' . __('ban_panel') . '</a>';
        if (Vars::$USER_RIGHTS == 9)
            $menu[] = '<a href="profile.php?act=ban&amp;mod=delhist&amp;user=' . $user['user_id'] . '">' . __('clear_history') . '</a>';
        if (!empty($menu))
            echo '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
        if ($user['user_id'] != Vars::$USER_ID)
            echo '<div class="user"><p>' . Functions::displayUser($user) . '</p></div>';
        else
            echo '<div class="list2"><p>' . __('my_infringements') . '</p></div>';
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $user['user_id'] . "'"), 0);
        if ($total) {
            $req = mysql_query("SELECT * FROM `cms_ban_users` WHERE `user_id` = '" . $user['user_id'] . "' ORDER BY `ban_time` DESC LIMIT " . Vars::db_pagination());
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                $remain = $res['ban_time'] - time();
                $period = $res['ban_time'] - $res['ban_while'];
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                echo Functions::getImage(($remain > 0 ? 'red' : 'green') . '.png') . '&#160;' .
                     '<b>' . __('ban_' . $res['ban_type']) . '</b>' .
                     ' <span class="gray">(' . date("d.m.Y / H:i", $res['ban_while']) . ')</span>' .
                     '<br />' . Validate::checkout($res['ban_reason']) .
                     '<div class="sub">';
                if (Vars::$USER_RIGHTS)
                    echo '<span class="gray">' . __('ban_who') . ':</span> ' . $res['ban_who'] . '<br />';
                echo '<span class="gray">' . __('term') . ':</span> ' . ($period < 86400000
                        ? Functions::timeCount($period) : __('ban_time_before_cancel'));
                if ($remain > 0)
                    echo '<br /><span class="gray">' . __('remains') . ':</span> ' . Functions::timeCount($remain);
                // Меню отдельного бана
                $menu = array();
                if (Vars::$USER_RIGHTS >= 7 && $remain > 0)
                    $menu[] = '<a href="profile.php?act=ban&amp;mod=cancel&amp;user=' . $user['user_id'] . '&amp;ban=' . $res['id'] . '">' . __('ban_cancel_do') . '</a>';
                if (Vars::$USER_RIGHTS == 9)
                    $menu[] = '<a href="profile.php?act=ban&amp;mod=delete&amp;user=' . $user['user_id'] . '&amp;ban=' . $res['id'] . '">' . __('ban_delete_do') . '</a>';
                if (!empty($menu))
                    echo '<div>' . Functions::displayMenu($menu) . '</div>';
                echo '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
        }
        echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo '<p>' . Functions::displayPagination('profile.php?act=ban&amp;user=' . $user['user_id'] . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>' .
                 '<p><form action="profile.php?act=ban&amp;user=' . $user['user_id'] . '" method="post">' .
                 '<input type="text" name="page" size="2"/>' .
                 '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
        }
}