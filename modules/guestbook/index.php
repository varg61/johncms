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

if (isset($_SESSION['ref']))
    unset($_SESSION['ref']);

// Проверяем права доступа в Админ-Клуб
if (isset($_SESSION['ga']) && Vars::$USER_RIGHTS < 1)
    unset($_SESSION['ga']);

// Задаем заголовки страницы
$textl = isset($_SESSION['ga']) ? Vars::$LNG['admin_club'] : Vars::$LNG['guestbook'];

// Если гостевая закрыта, выводим сообщение и закрываем доступ (кроме Админов)
if (!Vars::$SYSTEM_SET['mod_guest'] && Vars::$USER_RIGHTS < 7) {
    echo '<div class="rmenu"><p>' . Vars::$LNG['guestbook_closed'] . '</p></div>';
    exit;
}
switch (Vars::$ACT) {
    case 'delpost':
        /*
        -----------------------------------------------------------------
        Удаление отдельного поста
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 6 && Vars::$ID) {
            if (isset($_GET['yes'])) {
                mysql_query("DELETE FROM `guest` WHERE `id` = " . Vars::$ID);
                header("Location: index.php");
            } else {
                echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['guestbook'] . '</b></a> | ' . Vars::$LNG['delete_message'] . '</div>' .
                     '<div class="rmenu"><p>' . Vars::$LNG['delete_confirmation'] . '?<br/>' .
                     '<a href="index.php?act=delpost&amp;id=' . Vars::$ID . '&amp;yes">' . Vars::$LNG['delete'] . '</a> | ' .
                     '<a href="index.php">' . Vars::$LNG['cancel'] . '</a></p></div>';
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
        $captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
        $from = Vars::$USER_ID ? Vars::$USER_NICKNAME : mysql_real_escape_string($name);

        // Транслит сообщения
        if ($trans)
            $msg = Functions::translit($msg);

        // Проверяем на ошибки
        $error = array();
        $flood = false;
        if (!Vars::$USER_ID && empty($_POST['name']))
            $error[] = Vars::$LNG['error_empty_name'];
        if (empty($_POST['msg']))
            $error[] = Vars::$LNG['error_empty_message'];
        //TODO: Обработать Бан!
        //if (Vars::$USER_BAN['1'] || Vars::$USER_BAN['13'])
        //    $error[] = Vars::$LNG['access_forbidden'];

        // CAPTCHA для гостей
        if (!Vars::$USER_ID && !Captcha::check())
            $error[] = Vars::$LNG['error_wrong_captcha'];
        unset($_SESSION['captcha']);
        if (Vars::$USER_ID) {
            // Антифлуд для зарегистрированных пользователей
            $flood = Functions::antiFlood();
        } else {
            // Антифлуд для гостей
            $req = mysql_query("SELECT `time` FROM `guest` WHERE `ip` = '" . Vars::$IP . "' AND `browser` = '" . mysql_real_escape_string(Vars::$USER_AGENT) . "' AND `time` > '" . (time() - 60) . "'");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                $flood = time() - $res['time'];
            }
        }
        if ($flood)
            $error = Vars::$LNG['error_flood'] . ' ' . $flood . '&#160;' . Vars::$LNG['seconds'];
        if (!$error) {
            // Проверка на одинаковые сообщения
            $req = mysql_query("SELECT * FROM `guest` WHERE `user_id` = " . Vars::$USER_ID . " ORDER BY `time` DESC");
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
                `time` = '" . time() . "',
                `user_id` = " . Vars::$USER_ID . ",
                `name` = '$from',
                `text` = '" . mysql_real_escape_string($msg) . "',
                `ip` = '" . Vars::$IP . "',
                `browser` = '" . mysql_real_escape_string(Vars::$USER_AGENT) . "'
            ");
            // Фиксируем время последнего поста (антиспам)
            //TODO: Разобраться с записью последней активности
            //if (Vars::$USER_ID) {
            //    $postguest = $datauser['postguest'] + 1;
            //    mysql_query("UPDATE `users` SET `postguest` = '$postguest', `lastpost` = '" . time() . "' WHERE `id` = " . Vars::$USER_ID);
            //}
            header('location: index.php');
        } else {
            echo Functions::displayError($error, '<a href="index.php">' . Vars::$LNG['back'] . '</a>');
        }
        break;

    case 'otvet':
        /*
        -----------------------------------------------------------------
        Добавление "ответа Админа"
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 6 && Vars::$ID) {
            if (isset($_POST['submit'])) {
                mysql_query("UPDATE `guest` SET
                    `admin` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                    `otvet` = '" . mysql_real_escape_string(mb_substr($_POST['otv'], 0, 5000)) . "',
                    `otime` = '" . time() . "'
                    WHERE `id` = " . Vars::$ID
                );
                header("location: index.php");
            } else {
                echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['guestbook'] . '</b></a> | ' . Vars::$LNG['reply'] . '</div>';
                $req = mysql_query("SELECT * FROM `guest` WHERE `id` = " . Vars::$ID);
                $res = mysql_fetch_assoc($req);
                echo '<div class="menu">' .
                     '<div class="quote"><b>' . $res['name'] . '</b>' .
                     '<br />' . Validate::filterString($res['text']) . '</div>' .
                     '<form name="form" action="index.php?act=otvet&amp;id=' . Vars::$ID . '" method="post">' .
                     '<p><h3>' . Vars::$LNG['reply'] . '</h3>' . TextParser::autoBB('form', 'otv') .
                     '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="otv">' . Validate::filterString($res['otvet']) . '</textarea></p>' .
                     '<p><input type="submit" name="submit" value="' . Vars::$LNG['reply'] . '"/></p>' .
                     '</form></div>' .
                     '<div class="phdr"><a href="faq.php?act=trans">' . Vars::$LNG['translit'] . '</a> | <a href="faq.php?act=smileys">' . Vars::$LNG['smileys'] . '</a></div>' .
                     '<p><a href="index.php">' . Vars::$LNG['back'] . '</a></p>';
            }
        }
        break;

    case 'edit':
        /*
        -----------------------------------------------------------------
        Редактирование поста
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 6 && Vars::$ID) {
            if (isset($_POST['submit'])) {
                $req = mysql_query("SELECT `edit_count` FROM `guest` WHERE `id` = " . Vars::$ID);
                $res = mysql_fetch_array($req);
                $edit_count = $res['edit_count'] + 1;
                $msg = mb_substr($_POST['msg'], 0, 5000);
                mysql_query("UPDATE `guest` SET
                    `text` = '" . mysql_real_escape_string($msg) . "',
                    `edit_who` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                    `edit_time` = '" . time() . "',
                    `edit_count` = '$edit_count'
                    WHERE `id` = " . Vars::$ID
                );
                header("location: index.php");
            } else {
                $req = mysql_query("SELECT * FROM `guest` WHERE `id` = " . Vars::$ID);
                $res = mysql_fetch_assoc($req);
                $text = htmlentities($res['text'], ENT_QUOTES, 'UTF-8');
                echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['guestbook'] . '</b></a> | ' . Vars::$LNG['edit'] . '</div>' .
                     '<div class="rmenu">' .
                     '<form action="index.php?act=edit&amp;id=' . Vars::$ID . '" method="post">' .
                     '<p><b>' . Vars::$LNG['author'] . ':</b> ' . $res['name'] . '</p>' .
                     '<p><textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg">' . $text . '</textarea></p>' .
                     '<p><input type="submit" name="submit" value="' . Vars::$LNG['save'] . '"/></p>' .
                     '</form></div>' .
                     '<div class="phdr"><a href="faq.php?act=trans">' . Vars::$LNG['translit'] . '</a> | <a href="faq.php?act=smileys">' . Vars::$LNG['smileys'] . '</a></div>' .
                     '<p><a href="index.php">' . Vars::$LNG['back'] . '</a></p>';
            }
        }
        break;

    case 'clean':
        /*
        -----------------------------------------------------------------
        Очистка Гостевой
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 7) {
            if (isset($_POST['submit'])) {
                // Проводим очистку Гостевой, согласно заданным параметрам
                $adm = isset($_SESSION['ga']) ? 1 : 0;
                $cl = isset($_POST['cl']) ? intval($_POST['cl']) : '';
                switch ($cl) {
                    case '1':
                        // Чистим сообщения, старше 1 дня
                        mysql_query("DELETE FROM `guest` WHERE `adm`='$adm' AND `time` < '" . (time() - 86400) . "'");
                        echo '<p>' . Vars::$LNG['clear_day_ok'] . '</p>';
                        break;

                    case '2':
                        // Проводим полную очистку
                        mysql_query("DELETE FROM `guest` WHERE `adm`='$adm'");
                        echo '<p>' . Vars::$LNG['clear_full_ok'] . '</p>';
                        break;
                    default :
                        // Чистим сообщения, старше 1 недели
                        mysql_query("DELETE FROM `guest` WHERE `adm`='$adm' AND `time`<='" . (time() - 604800) . "';");
                        echo '<p>' . Vars::$LNG['clear_week_ok'] . '</p>';
                }
                mysql_query("OPTIMIZE TABLE `guest`");
                echo '<p><a href="index.php">' . Vars::$LNG['guestbook'] . '</a></p>';
            } else {
                // Запрос параметров очистки
                echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['guestbook'] . '</b></a> | ' . Vars::$LNG['clear'] . '</div>' .
                     '<div class="menu">' .
                     '<form id="clean" method="post" action="index.php?act=clean">' .
                     '<p><h3>' . Vars::$LNG['clear_param'] . '</h3>' .
                     '<input type="radio" name="cl" value="0" checked="checked" />' . Vars::$LNG['clear_param_week'] . '<br />' .
                     '<input type="radio" name="cl" value="1" />' . Vars::$LNG['clear_param_day'] . '<br />' .
                     '<input type="radio" name="cl" value="2" />' . Vars::$LNG['clear_param_all'] . '</p>' .
                     '<p><input type="submit" name="submit" value="' . Vars::$LNG['clear'] . '" /></p>' .
                     '</form></div>' .
                     '<div class="phdr"><a href="index.php">' . Vars::$LNG['cancel'] . '</a></div>';
            }
        }
        break;

    case 'ga':
        /*
        -----------------------------------------------------------------
        Переключение режима работы Гостевая / Админ-клуб
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 1) {
            if (isset($_GET['do']) && $_GET['do'] == 'set') {
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
        if (!Vars::$SYSTEM_SET['mod_guest'])
            echo '<div class="alarm">' . Vars::$LNG['guestbook_closed'] . '</div>';
        echo '<div class="phdr"><b>' . Vars::$LNG['guestbook'] . '</b></div>';
        if (Vars::$USER_RIGHTS > 0) {
            $menu = array();
            $menu[] = isset($_SESSION['ga']) ? '<a href="index.php?act=ga">' . Vars::$LNG['guestbook'] . '</a>' : '<b>' . Vars::$LNG['guestbook'] . '</b>';
            $menu[] = isset($_SESSION['ga']) ? '<b>' . Vars::$LNG['admin_club'] . '</b>' : '<a href="index.php?act=ga&amp;do=set">' . Vars::$LNG['admin_club'] . '</a>';
            if (Vars::$USER_RIGHTS >= 7)
                $menu[] = '<a href="index.php?act=clean">' . Vars::$LNG['clear'] . '</a>';
            echo '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
        }
        // Форма ввода нового сообщения
        if ((Vars::$USER_ID || Vars::$SYSTEM_SET['mod_guest'] == 2) && !isset(Vars::$USER_BAN['1']) && !isset(Vars::$USER_BAN['13'])) {
            echo '<div class="gmenu"><form name="form" action="index.php?act=say" method="post">';
            if (!Vars::$USER_ID)
                echo Vars::$LNG['name'] . ' (max 25):<br/><input type="text" name="name" maxlength="25"/><br/>';
            echo '<b>' . Vars::$LNG['message'] . '</b> <small>(max 5000)</small>:<br/>';
            if (!Vars::$IS_MOBILE)
                echo TextParser::autoBB('form', 'msg');
            echo '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg"></textarea><br/>';
            if (Vars::$USER_SET['translit'])
                echo '<input type="checkbox" name="msgtrans" value="1" />&nbsp;' . Vars::$LNG['translit'] . '<br/>';
            if (!Vars::$USER_ID) {
                // CAPTCHA для гостей
                echo Captcha::display() . '<br />';
            }
            echo '<input type="submit" name="submit" value="' . Vars::$LNG['sent'] . '"/></form></div>';
        } else {
            echo '<div class="rmenu">' . Vars::$LNG['access_guest_forbidden'] . '</div>';
        }
        if (isset($_SESSION['ga']) && Vars::$USER_RIGHTS >= "1") {
            $req = mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='1'");
        } else {
            $req = mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='0'");
        }
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='" . (isset($_SESSION['ga']) ? 1 : 0) . "'"), 0);
        echo '<div class="phdr"><b>' . Vars::$LNG['comments'] . '</b></div>';
        if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('index.php?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        if ($total) {
            if (isset($_SESSION['ga']) && Vars::$USER_RIGHTS >= "1") {
                // Запрос для Админ клуба
                echo '<div class="rmenu"><b>АДМИН-КЛУБ</b></div>';
                $req = mysql_query("SELECT `guest`.*, `guest`.`id` AS `gid`, `users`.`rights`, `users`.`lastdate`, `users`.`sex`, `users`.`status`, `users`.`datereg`, `users`.`id`
                FROM `guest` LEFT JOIN `users` ON `guest`.`user_id` = `users`.`id`
                WHERE `guest`.`adm`='1' ORDER BY `time` DESC" . Vars::db_pagination());
            } else {
                // Запрос для обычной Гастивухи
                $req = mysql_query("SELECT `guest`.*, `guest`.`id` AS `gid`, `users`.`rights`, `users`.`last_visit`, `users`.`sex`, `users`.`status`, `users`.`join_date`, `users`.`id`
                FROM `guest` LEFT JOIN `users` ON `guest`.`user_id` = `users`.`id`
                WHERE `guest`.`adm`='0' ORDER BY `time` DESC" . Vars::db_pagination());
            }
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                $text = '';
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                // Время создания поста
                $text = ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span>';
                if ($res['user_id']) {
                    // Для зарегистрированных показываем ссылки и смайлы
                    $post = Validate::filterString($res['text'], 1, 1);
                    if (Vars::$USER_SET['smileys'])
                        $post = Functions::smileys($post, $res['rights'] >= 1 ? 1 : 0);
                } else {
                    // Для гостей обрабатываем имя и фильтруем ссылки
                    $res['name'] = Validate::filterString($res['name']);
                    $post = Functions::antiLink(Validate::filterString($res['text'], 0, 2));
                }
                if ($res['edit_count']) {
                    // Если пост редактировался, показываем кем и когда
                    $post .= '<br /><span class="gray"><small>Изм. <b>' . $res['edit_who'] . '</b> (' . Functions::displayDate($res['edit_time']) . ') <b>[' . $res['edit_count'] . ']</b></small></span>';
                }
                if (!empty($res['otvet'])) {
                    // Ответ Администрации
                    $otvet = Validate::filterString($res['otvet'], 1, 1);
                    if (Vars::$USER_SET['smileys'])
                        $otvet = Functions::smileys($otvet, 1);
                    $post .= '<div class="reply"><b>' . $res['admin'] . '</b>: (' . Functions::displayDate($res['otime']) . ')<br/>' . $otvet . '</div>';
                }
                if (Vars::$USER_RIGHTS >= 6) {
                    $subtext = '<a href="index.php?act=otvet&amp;id=' . $res['gid'] . '">' . Vars::$LNG['reply'] . '</a>' .
                               (Vars::$USER_RIGHTS >= $res['rights'] ? ' | <a href="index.php?act=edit&amp;id=' . $res['gid'] . '">' . Vars::$LNG['edit'] . '</a> | <a href="index.php?act=delpost&amp;id=' . $res['gid'] . '">' . Vars::$LNG['delete'] . '</a>' : '');
                } else {
                    $subtext = '';
                }
                $arg = array(
                    'header' => $text,
                    'body' => $post,
                    'sub' => $subtext
                );
                echo Functions::displayUser($res, $arg);
                echo '</div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . Vars::$LNG['guestbook_empty'] . '</p></div>';
        }
        echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo '<div class="topmenu">' . Functions::displayPagination('index.php?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                 '<p><form action="index.php" method="get"><input type="text" name="page" size="2"/>' .
                 '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/></form></p>';
        }
}