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
$lng_karma = load_lng('karma');
$textl = $lng['karma'];
require('../incfiles/head.php');
if ($set_karma['on']) {
    switch ($mod) {
        case 'vote':
            /*
            -----------------------------------------------------------------
            Отдаем голос за пользователя
            -----------------------------------------------------------------
            */
            if (!$datauser['karma_off']) {
                $error = array ();
                if ($user['rights'] && $set_karma['adm'])
                    $error[] = $lng_karma['error_not_for_admins'];
                if ($user['ip'] == $ipl)
                    $error[] = $lng_karma['error_rogue'];
                if ($datauser['total_on_site'] < $set_karma['karma_time'] || $datauser['postforum'] < $set_karma['forum'])
                    $error[] = $lng_karma['error_terms_1'] . ' '
                        . ($set_karma['time'] ? ($set_karma['karma_time'] / 3600) . $lng['hours'] : ($set_karma['karma_time'] / 86400) . $lng['days']) . ' ' . $lng_karma['error_terms_2'] . ' ' . $set_karma['forum'] . ' '
                        . $lng_karma['posts'];
                $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `user_id` = '$user_id' AND `karma_user` = '" . $user['id'] . "' AND `time` > '" . ($realtime - 86400) . "'"), 0);
                if ($count)
                    $error[] = $lng_karma['error_terms_3'];
                $sum = mysql_result(mysql_query("SELECT SUM(`points`) FROM `karma_users` WHERE `user_id` = '$user_id' AND `time` >= '" . $datauser['karma_time'] . "'"), 0);
                if (($set_karma['karma_points'] - $sum) <= 0)
                    $error[] = $lng_karma['error_limit'] . ' ' . date('d.m.y в H:i:s', ($datauser['karma_time'] + 86400));
                if ($error) {
                    echo display_error($error, '<a href="profile.php?user=' . $user['id'] . '">' . $lng['back'] . '</a>');
                } else {
                    if (isset($_POST['submit'])) {
                        $text = trim($_POST['text']);
                        $type = intval($_POST['type']) ? 1 : 0;
                        $points = abs(intval($_POST['points']));
                        if (!$points || $points > ($set_karma['karma_points'] - $sum))
                            $points = 1;
                        $text = mysql_real_escape_string(mb_substr($text, 0, 500));
                        mysql_query("INSERT INTO `karma_users` SET
                            `user_id` = '$user_id',
                            `name` = '$login',
                            `karma_user` = '" . $user['id'] . "',
                            `points` = '$points',
                            `type` = '$type',
                            `time` = '$realtime',
                            `text` = '$text'
                        ");
                        $plm = explode('|', $res['plus_minus']);
                        if ($type) {
                            $karma = $res['karma'] + $points;
                            $plm[0] = $plm[0] + $points;
                        } else {
                            $karma = $res['karma'] - $points;
                            $plm[1] = $plm[1] + $points;
                        }
                        $plus_minus = $plm[0] . '|' . $plm[1];
                        mysql_query("UPDATE `users` SET
                            `karma` = '$karma',
                            `plus_minus` = '$plus_minus'
                            WHERE `id` = '" . $user['id'] . "' LIMIT 1
                        ");
                        echo '<div class="gmenu">' . $lng_karma['done'] . '!<br /><a href="profile.php?user=' . $user['id'] . '">' . $lng['continue'] . '</a></div>';
                    } else {
                        echo '<div class="phdr"><b>' . $lng_karma['vote_to'] . ' ' . $res['name'] . '</b></div>' .
                            '<form action="profile.php?act=karma&amp;mod=vote&amp;user=' . $user['id'] . '" method="post">' .
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
                            '<small>' . $lng['minmax_2_500'] . '</small>' .
                            '<p><input type="submit" name="submit" value="' . $lng['vote'] . '"/></p>' .
                            '</div></form>' .
                            '<div class="list2"><a href="profile/index.php?user=' . $user['id'] . '">' . $lng['profile'] . '</a></div>';
                    }
                }
            } else {
                echo display_error($lng_karma['error_forbidden'], '<a href="profile.php?user=' . $user['id'] . '">' . $lng['back'] . '</a>');
            }
            break;

        case 'delete':
            /*
            -----------------------------------------------------------------
            Удаляем отдельный голос
            -----------------------------------------------------------------
            */
            if ($rights == 9) {
                $type = isset($_GET['type']) ? abs(intval($_GET['type'])) : NULL;
                $req = mysql_query("SELECT * FROM `karma_users` WHERE `id` = '$id' AND `karma_user` = '" . $user['id'] . "' LIMIT 1");
                if (mysql_num_rows($req)) {
                    if (isset($_GET['yes'])) {
                        $res = mysql_fetch_assoc($req);
                        $plm = explode('|', $user['plus_minus']);
                        if ($res['type']) {
                            $karma = $user['karma'] - $res['points'];
                            $plus_minus = ($plm[0] - $res['points']) . '|' . $plm[1];
                        } else {
                            $karma = $user['karma'] + $res['points'];
                            $plus_minus = $plm[0] . '|' . ($plm[1] - $res['points']);
                        }
                        mysql_query("DELETE FROM `karma_users` WHERE `id` = '$id' LIMIT 1");
                        mysql_query("UPDATE `users` SET
                            `karma`='$karma',
                            `plus_minus`='$plus_minus'
                            WHERE `id` = '" . $user['id'] . "' LIMIT 1
                        ");
                        header('Location: profile.php?act=karma&user=' . $user['id'] . '&type=' . $type);
                    } else {
                        echo '<p>' . $lng_karma['deletion_warning'] . '?<br/>' .
                            '<a href="profile.php?act=karma&amp;mod=delete&amp;user=' . $user['id'] . '&amp;id=' . $id . '&amp;type=' . $type . '&amp;yes">' . $lng['delete'] . '</a> | ' .
                            '<a href="profile.php?act=karma&amp;user=' . $user['id'] . '&amp;type=' . $type . '">' . $lng['cancel'] . '</a></p>';
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
            if ($rights == 9) {
                if (isset($_GET['yes'])) {
                    mysql_query("DELETE FROM `karma_users` WHERE `karma_user` = '" . $user['id'] . "'");
                    mysql_query("UPDATE `users` SET `karma` = '0', `plus_minus` = '0|0' WHERE `id` = '" . $user['id'] . "' LIMIT 1");
                    header('Location: profile.php?act=karma&user=' . $user['id']);
                } else {
                    echo '<p>' . $lng_karma['clear_warning'] . '?<br/>' .
                        '<a href="profile.php?act=karma&amp;mod=clean&amp;user=' . $user['id'] . '&amp;yes">' . $lng['delete'] . '</a> | ' .
                        '<a href="profile.php?act=karma&amp;user=' . $user['id'] . '">' . $lng['cancel'] . '</a></p>';
                }
            }
            break;

        case 'new':
            /*
            -----------------------------------------------------------------
            Список новых отзывов (комментариев)
            -----------------------------------------------------------------
            */
            echo '<div class="phdr"><b>' . $lng_karma['new_responses'] . '</b></div>';
            $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `karma_user` = '$user_id' AND `time` > " . ($realtime - 86400)), 0);
            if ($total) {
                $req = mysql_query("SELECT * FROM `karma_users` WHERE `karma_user`='$user_id' AND `time` > " . ($realtime - 86400) . " ORDER BY `time` DESC LIMIT $start, $kmess");
                while ($res = mysql_fetch_assoc($req)) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo $res['type'] ? '<span class="green">+' . $res['points'] . '</span> ' : '<span class="red">-' . $res['points'] . '</span> ';
                    echo $user_id == $res['user_id'] || !$res['user_id'] ? '<b>' . $res['name'] . '</b>' : '<a href="profile/index.php?id=' . $res['user_id'] . '"><b>' . $res['name'] . '</b></a>';
                    echo ' <span class="gray">(' . date("d.m.y / H:i", $res['time'] + $set_user['sdvig'] * 3600) . ')</span>';
                    if (!empty($res['text']))
                        echo '<div class="sub">' . checkout($res['text']) . '</div>';
                    echo '</div>';
                    ++$i;
                }
            } else {
                echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
            }
            echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
            if ($total > $kmess) {
                echo '<p>' . display_pagination('karma.php?act=new&amp;', $start, $total, $kmess) . '</p>' .
                    '<p><form action="karma.php" method="get">' .
                    '<input type="hidden" name="act" value="new"/>' .
                    '<input type="text" name="page" size="2"/>' .
                    '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
            }
            echo '<p><a href="profile/index.php?">' . $lng['profile'] . '</a></p>';
            break;

        default:
            /*
            -----------------------------------------------------------------
            Главная страница Кармы, список отзывов
            -----------------------------------------------------------------
            */
            $exp = explode('|', $user['plus_minus']);
            $type = isset($_GET['type']) ? abs(intval($_GET['type'])) : 0;
            $menu = array(
                (!$type ? '<b>' . $lng_karma['all'] . '</b>' : '<a href="profile.php?act=karma&amp;user=' . $user['id'] . '">' . $lng_karma['all'] . '</a>'),
                ($type == 1 ? '<b>' . $lng_karma['positive'] . '</b>' : '<a href="profile.php?act=karma&amp;user=' . $user['id'] . '&amp;type=1">' . $lng_karma['positive'] . '</a>'),
                ($type == 2 ? '<b>' . $lng_karma['negative'] . '</b>' : '<a href="profile.php?act=karma&amp;user=' . $user['id'] . '&amp;type=2">' . $lng_karma['negative'] . '</a>')
            );
            echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '"><b>' . $lng['profile'] . '</b></a> | ' . $lng['karma'] . ' ' . $user['karma'] . ' (<span class="green">'
                . $exp[0] . '</span>/<span class="red">' . $exp[1] . '</span>)</div>';
            echo '<div class="topmenu">' . display_menu($menu) . '</div>';
            echo '<div class="user"><p>' . display_user($user, array ('iphide' => 1,)) . '</p></div>';
            $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `karma_user` = '" . $user['id'] . "'" . ($type ? " AND `type` = '$type'" : '')), 0);
            if ($total) {
                $req = mysql_query("SELECT * FROM `karma_users` WHERE `karma_user` = '" . $user['id'] . "'" . ($type ? " AND `type` = '$type'" : '') . " ORDER BY `time` DESC LIMIT $start, $kmess");
                while ($res = mysql_fetch_assoc($req)) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo $res['type'] ? '<span class="green">+' . $res['points'] . '</span> ' : '<span class="red">-' . $res['points'] . '</span> ';
                    echo $user_id == $res['user_id'] || !$res['user_id'] ? '<b>' . $res['name'] . '</b>' : '<a href="profile.php?user=' . $res['user_id'] . '"><b>' . $res['name'] . '</b></a>';
                    echo ' <span class="gray">(' . date("d.m.y / H:i", $res['time'] + $set_user['sdvig'] * 3600) . ')</span>';
                    if ($rights == 9)
                        echo ' <span class="red"><a href="profile.php?act=karma&amp;mod=delete&amp;user=' . $user['id'] . '&amp;id=' . $res['id'] . '&amp;type=' . $type . '">[X]</a></span>';
                    if (!empty($res['text']))
                        echo '<div class="sub">' . checkout($res['text']) . '</div>';
                    echo '</div>';
                    ++$i;
                }
            } else {
                echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
            }
            echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
            if ($total > $kmess) {
                echo '<p>' . display_pagination('profile.php?act=karma&amp;user=' . $user['id'] . '&amp;type=' . $type . '&amp;', $start, $total, $kmess) . '</p>' .
                    '<p><form action="profile.php?act=karma&amp;user=' . $user['id'] . '&amp;type=' . $type . '" method="post">' .
                    '<input type="text" name="page" size="2"/>' .
                    '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
            }
            if ($rights == 9)
                echo '<div class="func"><a href="profile.php?user=' . $user['id'] . '&amp;mod=clean">' . $lng_karma['reset'] . '</a></div>';
            echo '<p><a href="profile.php?user=' . $user['id'] . '">' . $lng['profile'] . '</a></p>';
    }
}
?>