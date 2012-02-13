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

//TODO: Доработать!

$lng_karma = Vars::loadLanguage('karma');

if ($set_karma['on']) {
    switch (Vars::$MOD) {
        case 'vote':
            /*
            -----------------------------------------------------------------
            Отдаем голос за пользователя
            -----------------------------------------------------------------
            */
            if (!$datauser['karma_off'] && empty(Vars::$USER_BAN)) {
                $error = array();
                if ($user['rights'] && $set_karma['adm'])
                    $error[] = $lng_karma['error_not_for_admins'];
                if ($user['ip'] == $ip)
                    $error[] = $lng_karma['error_rogue'];
                if ($datauser['total_on_site'] < $set_karma['karma_time'] || $datauser['postforum'] < $set_karma['forum'])
                    $error[] = $lng_karma['error_terms_1'] . ' '
                               . ($set_karma['time'] ? ($set_karma['karma_time'] / 3600) . Vars::$LNG['hours'] : ($set_karma['karma_time'] / 86400) . Vars::$LNG['days']) . ' ' . $lng_karma['error_terms_2'] . ' ' . $set_karma['forum'] . ' '
                               . $lng_karma['posts'];
                $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `user_id` = " . Vars::$USER_ID . " AND `karma_user` = '" . $user['user_id'] . "' AND `time` > '" . (time() - 86400) . "'"), 0);
                if ($count)
                    $error[] = $lng_karma['error_terms_3'];
                $sum = mysql_result(mysql_query("SELECT SUM(`points`) FROM `karma_users` WHERE `user_id` = " . Vars::$USER_ID . " AND `time` >= '" . $datauser['karma_time'] . "'"), 0);
                if (($set_karma['karma_points'] - $sum) <= 0)
                    $error[] = $lng_karma['error_limit'] . ' ' . date('d.m.y в H:i:s', ($datauser['karma_time'] + 86400));
                if ($error) {
                    echo Functions::displayError($error, '<a href="profile.php?user=' . $user['user_id'] . '">' . Vars::$LNG['back'] . '</a>');
                } else {
                    if (isset($_POST['submit'])) {
                        $text = isset($_POST['text']) ? mysql_real_escape_string(mb_substr(trim($_POST['text']), 0, 500)) : '';
                        $type = intval($_POST['type']) ? 1 : 0;
                        $points = abs(intval($_POST['points']));
                        if (!$points || $points > ($set_karma['karma_points'] - $sum))
                            $points = 1;
                        mysql_query("INSERT INTO `karma_users` SET
                            `user_id` = " . Vars::$USER_ID . ",
                            `name` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                            `karma_user` = '" . $user['user_id'] . "',
                            `points` = '$points',
                            `type` = '$type',
                            `time` = '" . time() . "',
                            `text` = '$text'
                        ");
                        $sql = $type ? "`karma_plus` = '" . ($user['karma_plus'] + $points) . "'" : "`karma_minus` = '" . ($user['karma_minus'] + $points) . "'";
                        mysql_query("UPDATE `users` SET $sql WHERE `id` = '" . $user['user_id'] . "'");
                        echo '<div class="gmenu">' . $lng_karma['done'] . '!<br /><a href="profile.php?user=' . $user['user_id'] . '">' . Vars::$LNG['continue'] . '</a></div>';
                    } else {
                        echo '<div class="phdr"><b>' . $lng_karma['vote_to'] . ' ' . $res['name'] . '</b>: ' . Validate::filterString($user['name']) . '</div>' .
                             '<form action="profile.php?act=karma&amp;mod=vote&amp;user=' . $user['user_id'] . '" method="post">' .
                             '<div class="gmenu"><b>' . $lng_karma['vote_type'] . ':</b><br />' .
                             '<input name="type" type="radio" value="1" checked="checked"/> ' . $lng_karma['plus'] . '<br />' .
                             '<input name="type" type="radio" value="0"/> ' . $lng_karma['minus'] . '<br />' .
                             '<b>' . $lng_karma['vote_qty'] . ':</b><br />' .
                             '<select size="1" name="points">';
                        for ($i = 1; $i < ($set_karma['karma_points'] - $sum + 1); $i++) {
                            echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                        echo '</select><b><br />' . $lng_karma['comment'] . ':</b><br />' .
                             '<input name="text" type="text" value=""/><br />' .
                             '<small>' . Vars::$LNG['minmax_2_500'] . '</small>' .
                             '<p><input type="submit" name="submit" value="' . Vars::$LNG['vote'] . '"/></p>' .
                             '</div></form>' .
                             '<div class="list2"><a href="profile.php?user=' . $user['user_id'] . '">' . Vars::$LNG['profile'] . '</a></div>';
                    }
                }
            } else {
                echo Functions::displayError($lng_karma['error_forbidden'], '<a href="profile.php?user=' . $user['user_id'] . '">' . Vars::$LNG['back'] . '</a>');
            }
            break;

        case 'delete':
            /*
            -----------------------------------------------------------------
            Удаляем отдельный голос
            -----------------------------------------------------------------
            */
            if (Vars::$USER_RIGHTS == 9) {
                $type = isset($_GET['type']) ? abs(intval($_GET['type'])) : NULL;
                $req = mysql_query("SELECT * FROM `karma_users` WHERE `id` = " . Vars::$ID . " AND `karma_user` = '" . $user['user_id'] . "'");
                if (mysql_num_rows($req)) {
                    $res = mysql_fetch_assoc($req);
                    if (isset($_GET['yes'])) {
                        mysql_query("DELETE FROM `karma_users` WHERE `id` = " . Vars::$ID);
                        //TODO: Доработать калькуляцию
                        if ($res['type']) {
                            $sql = "`karma_plus` = '" . ($user['karma_plus'] > $res['points'] ? $user['karma_plus'] - $res['points'] : 0) . "'";
                        } else {
                            $sql = "`karma_minus` = '" . ($user['karma_minus'] > $res['points'] ? $user['karma_minus'] - $res['points'] : 0) . "'";
                        }
                        mysql_query("UPDATE `users` SET $sql WHERE `id` = '" . $user['user_id'] . "'");
                        header('Location: profile.php?act=karma&user=' . $user['user_id'] . '&type=' . $type);
                    } else {
                        echo '<div class="rmenu"><p>' . $lng_karma['deletion_warning'] . '?<br/>' .
                             '<a href="profile.php?act=karma&amp;mod=delete&amp;user=' . $user['user_id'] . '&amp;id=' . Vars::$ID . '&amp;type=' . $type . '&amp;yes">' . Vars::$LNG['delete'] . '</a> | ' .
                             '<a href="profile.php?act=karma&amp;user=' . $user['user_id'] . '&amp;type=' . $type . '">' . Vars::$LNG['cancel'] . '</a></p></div>';
                    }
                }
            }
            break;

        case 'clean':
            /*
            -----------------------------------------------------------------
            Очищаем все голоса за пользователя
            -----------------------------------------------------------------
            */
            if (Vars::$USER_RIGHTS == 9) {
                if (isset($_GET['yes'])) {
                    mysql_query("DELETE FROM `karma_users` WHERE `karma_user` = '" . $user['user_id'] . "'");
                    mysql_query("OPTIMIZE TABLE `karma_users`");
                    mysql_query("UPDATE `users` SET `karma_plus` = '0', `karma_minus` = '0' WHERE `id` = '" . $user['user_id'] . "'");
                    header('Location: profile.php?user=' . $user['user_id']);
                } else {
                    echo '<div class="rmenu"><p>' . $lng_karma['clear_warning'] . '?<br/>' .
                         '<a href="profile.php?act=karma&amp;mod=clean&amp;user=' . $user['user_id'] . '&amp;yes">' . Vars::$LNG['delete'] . '</a> | ' .
                         '<a href="profile.php?act=karma&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['cancel'] . '</a></p></div>';
                }
            }
            break;

        case 'new':
            /*
            -----------------------------------------------------------------
            Список новых отзывов (комментариев)
            -----------------------------------------------------------------
            */
            echo '<div class="phdr"><a href="profile.php?act=karma&amp;type=2"><b>' . Vars::$LNG['karma'] . '</b></a> | ' . $lng_karma['new_responses'] . '</div>';
            $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `karma_user` = " . Vars::$USER_ID . " AND `time` > " . (time() - 86400)), 0);
            if ($total) {
                $req = mysql_query("SELECT * FROM `karma_users` WHERE `karma_user` = " . Vars::$USER_ID . " AND `time` > " . (time() - 86400) . " ORDER BY `time` DESC LIMIT " . Vars::db_pagination());
                while ($res = mysql_fetch_assoc($req)) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo $res['type'] ? '<span class="green">+' . $res['points'] . '</span> ' : '<span class="red">-' . $res['points'] . '</span> ';
                    echo Vars::$USER_ID == $res['user_id'] || !$res['user_id'] ? '<b>' . $res['name'] . '</b>' : '<a href="profile.php?user=' . $res['user_id'] . '"><b>' . $res['name'] . '</b></a>';
                    echo ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span>';
                    if (!empty($res['text']))
                        echo '<div class="sub">' . Validate::filterString($res['text']) . '</div>';
                    echo '</div>';
                    ++$i;
                }
            } else {
                echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
            }
            echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
            if ($total > Vars::$USER_SET['page_size']) {
                echo '<p>' . Functions::displayPagination('profile.php?act=karma&amp;mod=new&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>' .
                     '<p><form action="profile.php?act=karma&amp;mod=new" method="post">' .
                     '<input type="text" name="page" size="2"/>' .
                     '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/></form></p>';
            }
            echo '<p><a href="profile.php">' . Vars::$LNG['profile'] . '</a></p>';
            break;

        default:
            /*
            -----------------------------------------------------------------
            Главная страница Кармы, список отзывов
            -----------------------------------------------------------------
            */
            $type = isset($_GET['type']) ? abs(intval($_GET['type'])) : 0;
            $menu = array(
                ($type == 2 ? '<b>' . $lng_karma['all'] . '</b>' : '<a href="profile.php?act=karma&amp;user=' . $user['user_id'] . '&amp;type=2">' . $lng_karma['all'] . '</a>'),
                ($type == 1 ? '<b>' . $lng_karma['positive'] . '</b>' : '<a href="profile.php?act=karma&amp;user=' . $user['user_id'] . '&amp;type=1">' . $lng_karma['positive'] . '</a>'),
                (!$type ? '<b>' . $lng_karma['negative'] . '</b>' : '<a href="profile.php?act=karma&amp;user=' . $user['user_id'] . '">' . $lng_karma['negative'] . '</a>')
            );
            echo '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '"><b>' . Vars::$LNG['profile'] . '</b></a> | ' . Vars::$LNG['karma'] . '</div>' .
                 '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>' .
                 '<div class="user"><p>' . Functions::displayUser($user, array('iphide' => 1,)) . '</p></div>';
            $karma = $user['karma_plus'] - $user['karma_minus'];
            if ($karma > 0) {
                $images = ($user['karma_minus'] ? ceil($user['karma_plus'] / $user['karma_minus']) : $user['karma_plus']) > 10 ? '2' : '1';
                echo '<div class="gmenu">';
            } else if ($karma < 0) {
                $images = ($user['karma_plus'] ? ceil($user['karma_minus'] / $user['karma_plus']) : $user['karma_minus']) > 10 ? '-2' : '-1';
                echo '<div class="rmenu">';
            } else {
                $images = 0;
                echo '<div class="menu">';
            }
            echo '<table  width="100%"><tr><td width="22" valign="top">' . Functions::getImage('karma_' . $images . '.gif') . '</td><td>' .
                 '<b>' . Vars::$LNG['karma'] . ' (' . $karma . ')</b>' .
                 '<div class="sub">' .
                 '<span class="green">' . Vars::$LNG['vote_for'] . ' (' . $user['karma_plus'] . ')</span> | ' .
                 '<span class="red">' . Vars::$LNG['vote_against'] . ' (' . $user['karma_minus'] . ')</span>';
            echo '</div></td></tr></table></div>';
            $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `karma_user` = '" . $user['user_id'] . "'" . ($type == 2 ? "" : " AND `type` = '$type'")), 0);
            if ($total) {
                $req = mysql_query("SELECT * FROM `karma_users` WHERE `karma_user` = '" . $user['user_id'] . "'" . ($type == 2 ? "" : " AND `type` = '$type'") . " ORDER BY `time` DESC LIMIT " . Vars::db_pagination());
                while ($res = mysql_fetch_assoc($req)) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo $res['type'] ? '<span class="green">+' . $res['points'] . '</span> ' : '<span class="red">-' . $res['points'] . '</span> ';
                    echo Vars::$USER_ID == $res['user_id'] || !$res['user_id'] ? '<b>' . $res['name'] . '</b>' : '<a href="profile.php?user=' . $res['user_id'] . '"><b>' . $res['name'] . '</b></a>';
                    echo ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span>';
                    if (Vars::$USER_RIGHTS == 9)
                        echo ' <span class="red"><a href="profile.php?act=karma&amp;mod=delete&amp;user=' . $user['user_id'] . '&amp;id=' . $res['id'] . '&amp;type=' . $type . '">[X]</a></span>';
                    if (!empty($res['text']))
                        echo '<br />' . Functions::smileys(Validate::filterString($res['text']));
                    echo '</div>';
                    ++$i;
                }
            } else {
                echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
            }
            echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
            if ($total > Vars::$USER_SET['page_size']) {
                echo '<div class="topmenu">' . Functions::displayPagination('profile.php?act=karma&amp;user=' . $user['user_id'] . '&amp;type=' . $type . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                     '<p><form action="profile.php?act=karma&amp;user=' . $user['user_id'] . '&amp;type=' . $type . '" method="post">' .
                     '<input type="text" name="page" size="2"/>' .
                     '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/></form></p>';
            }
            echo '<p>' . (Vars::$USER_RIGHTS == 9 ? '<a href="profile.php?act=karma&amp;user=' . $user['user_id'] . '&amp;mod=clean">' . $lng_karma['reset'] . '</a><br />' : '') .
                 '<a href="profile.php?user=' . $user['user_id'] . '">' . Vars::$LNG['profile'] . '</a></p>';
    }
}