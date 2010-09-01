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

define('_IN_JOHNCMS', 1);
$headmod = 'guest';
require('../incfiles/core.php');
$lng_guest = load_lng('guest');
if (isset($_SESSION['ref']))
    unset($_SESSION['ref']);

// Проверяем права доступа в Админ-Клуб
if (isset($_SESSION['ga']) && $rights < 1)
    unset($_SESSION['ga']);

// Задаем заголовки страницы
$textl = isset($_SESSION['ga']) ? $lng['admin_club'] : $lng['guestbook'];
require('../incfiles/head.php');

// Если гостевая закрыта, выводим сообщение и закрываем доступ (кроме Админов)
if (!$set['mod_guest'] && $rights < 7) {
    echo '<div class="rmenu"><p>' . $lng_guest['guestbook_closed'] . '</p></div>';
    require('../incfiles/end.php');
    exit;
}
switch ($act) {
    case 'delpost':
        /*
        -----------------------------------------------------------------
        Удаление отдельного поста
        -----------------------------------------------------------------
        */
        if ($rights >= 6 && $id) {
            if (isset($_GET['yes'])) {
                mysql_query("DELETE FROM `guest` WHERE `id`='" . $id . "' LIMIT 1");
                header("Location: index.php");
            } else {
                echo '<div class="phdr"><a href="index.php"><b>' . $lng['guestbook'] . '</b></a> | ' . $lng['delete_message'] . '</div>' .
                    '<div class="rmenu"><p>' . $lng['delete_confirmation'] . '?<br/>' .
                    '<a href="index.php?act=delpost&amp;id=' . $id . '&amp;yes">' . $lng['delete'] . '</a> | ' .
                    '<a href="index.php">' . $lng['cancel'] . '</a></p></div>';
            }
        }
        break;

    case 'say':
        /*
        -----------------------------------------------------------------
        Добавление нового поста
        -----------------------------------------------------------------
        */
        $admset = isset($_SESSION['ga']) ? 1 : 0; // Задаем куда вставляем, в Админ клуб (1), или в Гастивуху (0)
        // Принимаем и обрабатываем данные
        $name = isset($_POST['name']) ? mb_substr(trim($_POST['name']), 0, 20) : '';
        $msg = isset($_POST['msg']) ? mb_substr(trim($_POST['msg']), 0, 5000) : '';
        $trans = isset($_POST['msgtrans']) ? 1 : 0;
        $code = isset($_POST['code']) ? trim($_POST['code']) : '';
        $from = $user_id ? $login : mysql_real_escape_string($name);
        // Транслит сообщения
        if ($trans)
            $msg = trans($msg);
        // Проверяем на ошибки
        $error = array ();
        $flood = false;
        if (!$user_id && empty($_POST['name']))
            $error[] = $lng_guest['error_name_empty'];
        if (empty($_POST['msg']))
            $error[] = $lng_guest['error_message_empty'];
        if ($ban['1'] || $ban['13'])
            $error[] = $lng_guest['error_ban'];
        // CAPTCHA для гостей
        if (!$user_id && (empty($code) || mb_strlen($code) < 4 || $code != $_SESSION['code']))
            $error[] = $lng_guest['error_captcha_wrong'];
        unset($_SESSION['code']);
        if ($user_id) {
            // Антифлуд для зарегистрированных пользователей
            $flood = antiflood();
        } else {
            // Антифлуд для гостей
            $req = mysql_query("SELECT `time` FROM `guest` WHERE `ip` = '$ipl' AND `browser` = '" . mysql_real_escape_string($agn) . "' AND `time` > '" . ($realtime - 60) . "'");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                $flood = $realtime - $res['time'];
            }
        }
        if ($flood)
            $error = $lng_guest['error_flood'] . ' ' . $flood . '&#160;' . $lng_guest['seconds'];
        if (!$error) {
            // Проверка на одинаковые сообщения
            $req = mysql_query("SELECT * FROM `guest` WHERE `user_id` = '$user_id' ORDER BY `time` DESC");
            $res = mysql_fetch_array($req);
            if ($res['text'] == $msg) {
                header("location: index.php");
                exit;
            }
        }
        if (!$error) {
            // Вставляем сообщение в базу
            mysql_query("INSERT INTO `guest` SET
                `adm` = '$admset',
                `time` = '$realtime',
                `user_id` = '$user_id',
                `name` = '$from',
                `text` = '" . mysql_real_escape_string($msg) . "',
                `ip` = '$ipl',
                `browser` = '" . mysql_real_escape_string($agn) . "'
            ");
            // Фиксируем время последнего поста (антиспам)
            if ($user_id) {
                $postguest = $datauser['postguest'] + 1;
                mysql_query("UPDATE `users` SET `postguest` = '$postguest', `lastpost` = '$realtime' WHERE `id` = '$user_id'");
            }
            header('location: index.php');
        } else {
            echo display_error($error, '<a href="index.php">' . $lng['back'] . '</a>');
        }
        break;

    case 'otvet':
        /*
        -----------------------------------------------------------------
        Добавление "ответа Админа"
        -----------------------------------------------------------------
        */
        if ($rights >= 6 && $id) {
            if (isset($_POST['submit'])) {
                mysql_query("UPDATE `guest` SET
                    `admin` = '$login',
                    `otvet` = '" . mysql_real_escape_string(mb_substr($_POST['otv'], 0, 5000)) . "',
                    `otime` = '$realtime'
                    WHERE `id` = '$id'
                ");
                header("location: index.php");
            } else {
                echo '<div class="phdr"><a href="index.php"><b>' . $lng['guestbook'] . '</b></a> | ' . $lng_guest['reply'] . '</div>';
                $req = mysql_query("SELECT * FROM `guest` WHERE `id` = '$id' LIMIT 1");
                $res = mysql_fetch_assoc($req);
                if (!empty($res['otvet'])) {
                    echo '<div class="rmenu">' . $lng_guest['reply_already'] . '</div>';
                }
                echo '<div class="menu">' .
                    '<div class="quote"><b>' . $res['name'] . '</b>' .
                    '<br />' . checkout($res['text']) . '</div>' .
                    '<form action="index.php?act=otvet&amp;id=' . $id . '" method="post">' .
                    '<p><h3>' . $lng_guest['reply'] . '</h3>' .
                    '<textarea cols="' . $set_user['field_w'] . '" rows="' . $set_user['field_h'] . '" name="otv">' . checkout($res['otvet']) . '</textarea></p>' .
                    '<p><input type="submit" name="submit" value="' . $lng_guest['reply'] . '"/></p>' .
                    '</form></div>' .
                    '<div class="phdr"><a href="faq.php?act=trans">' . $lng['translit'] . '</a> | <a href="faq.php?act=smileys">' . $lng['smileys'] . '</a></div>' .
                    '<p><a href="index.php">' . $lng['back'] . '</a></p>';
            }
        }
        break;

    case 'edit':
        /*
        -----------------------------------------------------------------
        Редактирование поста
        -----------------------------------------------------------------
        */
        if ($rights >= 6 && $id) {
            if (isset($_POST['submit'])) {
                $req = mysql_query("SELECT `edit_count` FROM `guest` WHERE `id`='" . $id . "' LIMIT 1");
                $res = mysql_fetch_array($req);
                $edit_count = $res['edit_count'] + 1;
                $msg = mb_substr($_POST['msg'], 0, 5000);
                mysql_query("UPDATE `guest` SET
                    `text` = '" . mysql_real_escape_string($msg) . "',
                    `edit_who` = '$login',
                    `edit_time` = '$realtime',
                    `edit_count` = '$edit_count'
                    WHERE `id` = '$id'
                ");
                header("location: index.php");
            } else {
                $req = mysql_query("SELECT * FROM `guest` WHERE `id` = '" . $id . "' LIMIT 1");
                $res = mysql_fetch_assoc($req);
                $text = htmlentities($res['text'], ENT_QUOTES, 'UTF-8');
                echo '<div class="phdr"><a href="index.php"><b>' . $lng['guestbook'] . '</b></a> | ' . $lng_guest['edit_post'] . '</div>' .
                    '<div class="rmenu">' .
                    '<form action="index.php?act=edit&amp;id=' . $id . '" method="post">' .
                    '<p><b>' . $lng['author'] . ':</b> ' . $res['name'] . '</p>' .
                    '<p><textarea cols="' . $set_user['field_w'] . '" rows="' . $set_user['field_h'] . '" name="msg">' . $text . '</textarea></p>' .
                    '<p><input type="submit" name="submit" value="' . $lng['save'] . '"/></p>' .
                    '</form></div>' .
                    '<div class="phdr"><a href="faq.php?act=trans">' . $lng['translit'] . '</a> | <a href="faq.php?act=smileys">' . $lng['smileys'] . '</a></div>' .
                    '<p><a href="index.php">' . $lng['back'] . '</a></p>';
            }
        }
        break;

    case 'clean':
        /*
        -----------------------------------------------------------------
        Очистка Гостевой
        -----------------------------------------------------------------
        */
        if ($rights >= 7) {
            if (isset($_POST['submit'])) {
                // Проводим очистку Гостевой, согласно заданным параметрам
                $adm = isset($_SESSION['ga']) ? 1 : 0;
                $cl = isset($_POST['cl']) ? intval($_POST['cl']) : '';
                switch ($cl) {
                    case '1':
                        // Чистим сообщения, старше 1 дня
                        mysql_query("DELETE FROM `guest` WHERE `adm`='$adm' AND `time` < '" . ($realtime - 86400) . "'");
                        echo '<p>' . $lng_guest['clear_day_ok'] . '</p>';
                        break;

                    case '2':
                        // Проводим полную очистку
                        mysql_query("DELETE FROM `guest` WHERE `adm`='$adm'");
                        echo '<p>' . $lng_guest['clear_full_ok'] . '</p>';
                        break;
                        default :
                        // Чистим сообщения, старше 1 недели
                        mysql_query("DELETE FROM `guest` WHERE `adm`='$adm' AND `time`<='" . ($realtime - 604800) . "';");
                        echo '<p>' . $lng_guest['clear_week_ok'] . '</p>';
                }
                mysql_query("OPTIMIZE TABLE `guest`");
                echo '<p><a href="index.php">' . $lng['guestbook'] . '</a></p>';
            } else {
                // Запрос параметров очистки
                echo '<div class="phdr"><a href="index.php"><b>' . $lng['guestbook'] . '</b></a> | ' . $lng['clear'] . '</div>' .
                    '<div class="menu">' .
                    '<form id="clean" method="post" action="index.php?act=clean">' .
                    '<p><h3>' . $lng_guest['clear_parametres'] . '</h3>' .
                    '<input type="radio" name="cl" value="0" checked="checked" />' . $lng_guest['clear_param_week'] . '<br />' .
                    '<input type="radio" name="cl" value="1" />' . $lng_guest['clear_param_day'] . '<br />' .
                    '<input type="radio" name="cl" value="2" />' . $lng_guest['clear_param_all'] . '</p>' .
                    '<p><input type="submit" name="submit" value="' . $lng['clear'] . '" /></p>' .
                    '</form></div>' .
                    '<div class="phdr"><a href="index.php">' . $lng['cancel'] . '</a></div>';
            }
        }
        break;

    case 'ga':
        /*
        -----------------------------------------------------------------
        Переключение режима работы Гостевая / Админ-клуб
        -----------------------------------------------------------------
        */
        if ($rights >= 1) {
            if ($_GET['do'] == 'set') {
                $_SESSION['ga'] = 1;
            } else {
                unset($_SESSION['ga']);
            }
        }

    default:
        /*
        -----------------------------------------------------------------
        Отображаем Гостевую, или Админ клуб
        -----------------------------------------------------------------
        */
        if (!$set['mod_guest'])
            echo '<div class="alarm">' . $lng_guest['guestbook_closed'] . '</div>';
        echo '<div class="phdr"><b>' . $lng['guestbook'] . '</b></div>';
        if ($rights > 0) {
            $menu = array ();
            $menu[] = isset($_SESSION['ga']) ? '<a href="index.php?act=ga">' . $lng['guestbook'] . '</a>' : '<b>' . $lng['guestbook'] . '</b>';
            $menu[] = isset($_SESSION['ga']) ? '<b>' . $lng['admin_club'] . '</b>' : '<a href="index.php?act=ga&amp;do=set">' . $lng['admin_club'] . '</a>';
            if ($rights >= 7)
                $menu[] = '<a href="index.php?act=clean">' . $lng['clear'] . '</a>';
            echo '<div class="topmenu">' . display_menu($menu) . '</div>';
        }
        // Форма ввода нового сообщения
        if (($user_id || $set['mod_guest'] == 2) && !$ban['1'] && !$ban['13']) {
            echo '<div class="gmenu"><form action="index.php?act=say" method="post">';
            if (!$user_id)
                echo $lng_guest['name'] . ':<br/><input type="text" name="name" maxlength="25"/><br/>';
            echo $lng_guest['message'] . ':<br/><textarea cols="' . $set_user['field_w'] . '" rows="' . $set_user['field_h'] . '" name="msg"></textarea><br/>';
            if ($set_user['translit'])
                echo '<input type="checkbox" name="msgtrans" value="1" />&nbsp;' . $lng['translit'] . '<br/>';
            if (!$user_id) {
                // CAPTCHA для гостей
                echo '<img src="../captcha.php?r=' . rand(1000, 9999) . '" alt="' . $lng_guest['captcha'] . '"/><br />';
                echo '<input type="text" size="5" maxlength="5"  name="code"/>&#160;' . $lng_guest['captcha'] . '<br />';
            }
            echo '<input type="submit" name="submit" value="' . $lng_guest['sent'] . '"/></form></div>';
        } else {
            echo '<div class="rmenu">' . $lng_guest['only_authorized'] . '</div>';
        }
        if (isset($_SESSION['ga']) && ($login == $nickadmina || $login == $nickadmina2 || $rights >= "1")) {
            $req = mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='1'");
        } else {
            $req = mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='0'");
        }
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='" . (isset($_SESSION['ga']) ? 1 : 0) . "'"), 0);
        if ($total) {
            if (isset($_SESSION['ga']) && ($login == $nickadmina || $login == $nickadmina2 || $rights >= "1")) {
                // Запрос для Админ клуба
                echo '<div class="rmenu"><b>АДМИН-КЛУБ</b></div>';
                $req = mysql_query("SELECT `guest`.*, `guest`.`id` AS `gid`, `users`.`rights`, `users`.`lastdate`, `users`.`sex`, `users`.`status`, `users`.`datereg`, `users`.`id`
                FROM `guest` LEFT JOIN `users` ON `guest`.`user_id` = `users`.`id`
                WHERE `guest`.`adm`='1' ORDER BY `time` DESC LIMIT $start, $kmess");
            } else {
                // Запрос для обычной Гастивухи
                $req = mysql_query("SELECT `guest`.*, `guest`.`id` AS `gid`, `users`.`rights`, `users`.`lastdate`, `users`.`sex`, `users`.`status`, `users`.`datereg`, `users`.`id`
                FROM `guest` LEFT JOIN `users` ON `guest`.`user_id` = `users`.`id`
                WHERE `guest`.`adm`='0' ORDER BY `time` DESC LIMIT $start, $kmess");
            }
            while ($res = mysql_fetch_assoc($req)) {
                $text = '';
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                if (empty($res['id'])) {
                    // Запрос по гостям
                    $req_g = mysql_query("SELECT `lastdate` FROM `cms_guests` WHERE `session_id` = '" . md5($res['ip'] . $res['browser']) . "' LIMIT 1");
                    $res_g = mysql_fetch_assoc($req_g);
                    $res['lastdate'] = $res_g['lastdate'];
                }
                // Время создания поста
                $text = ' <span class="gray">(' . date("d.m.Y / H:i", $res['time'] + $set_user['sdvig'] * 3600) . ')</span>';
                if ($res['user_id']) {
                    // Для зарегистрированных показываем ссылки и смайлы
                    $post = checkout($res['text'], 1, 1);
                    if ($set_user['smileys'])
                        $post = smileys($post, $res['rights'] >= 1 ? 1 : 0);
                } else {
                    // Для гостей обрабатываем имя и фильтруем ссылки
                    $res['name'] = checkout($res['name']);
                    $post = antilink(checkout($res['text'], 0, 2));
                }
                if ($res['edit_count']) {
                    // Если пост редактировался, показываем кем и когда
                    $dizm = date("d.m /H:i", $res['edit_time'] + $set_user['sdvig'] * 3600);
                    $post .= '<br /><span class="gray"><small>Изм. <b>' . $res['edit_who'] . '</b> (' . $dizm . ') <b>[' . $res['edit_count'] . ']</b></small></span>';
                }
                if (!empty($res['otvet'])) {
                    // Ответ Администрации
                    $otvet = checkout($res['otvet'], 1, 1);
                    $vrp1 = $res['otime'] + $set_user['sdvig'] * 3600;
                    $vr1 = date("d.m.Y / H:i", $vrp1);
                    if ($set_user['smileys'])
                        $otvet = smileys($otvet, 1);
                    $post .= '<div class="reply"><b>' . $res['admin'] . '</b>: (' . $vr1 . ')<br/>' . $otvet . '</div>';
                }
                if ($rights >= 6) {
                    $subtext = '<a href="index.php?act=otvet&amp;id=' . $res['gid'] . '">' . $lng_guest['reply'] . '</a>' . 
                    ($rights >= $res['rights'] ? ' | <a href="index.php?act=edit&amp;id=' . $res['gid'] . '">' . $lng['edit'] . '</a> | <a href="index.php?act=delpost&amp;id=' . $res['gid'] . '">' . $lng['delete'] . '</a>' : '');
                } else {
                    $subtext = '';
                }
                $arg = array (
                    'header' => $text,
                    'body' => $post,
                    'sub' => $subtext
                );
                echo display_user($res, $arg);
                echo '</div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . $lng_guest['guestbook_empty'] . '</p></div>';
        }
        echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
        // Навигация по страницам
        if ($total > $kmess) {
            echo '<p>' . display_pagination('index.php?', $start, $total, $kmess) . '</p>';
            echo '<p><form action="index.php" method="get"><input type="text" name="page" size="2"/><input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
        }
        break;
}

require('../incfiles/end.php');
?>