<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

$headmod = 'news';
require_once('../includes/core.php');
$lng_news = Vars::loadLanguage('news'); // Загружаем язык модуля
$textl = Vars::$LNG['news'];
require_once('../includes/head.php');

//TODO: Переделать с $do на $mod
switch ($do) {
    case 'add':
        /*
        -----------------------------------------------------------------
        Добавление новости
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 6) {
            echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['news'] . '</b></a> | ' . Vars::$LNG['add'] . '</div>';
            $old = 20;
            if (isset($_POST['submit'])) {
                $error = array();
                $name = isset($_POST['name']) ? Validate::filterString($_POST['name']) : false;
                $text = isset($_POST['text']) ? trim($_POST['text']) : false;
                if (!$name)
                    $error[] = $lng_news['error_title'];
                if (!$text)
                    $error[] = $lng_news['error_text'];
                $flood = Functions::antiFlood();
                if ($flood)
                    $error[] = Vars::$LNG['error_flood'] . ' ' . $flood . '&#160;' . Vars::$LNG['seconds'];
                if (!$error) {
                    $rid = 0;
                    if (!empty($_POST['pf']) && ($_POST['pf'] != '0')) {
                        $pf = intval($_POST['pf']);
                        $rz = $_POST['rz'];
                        $pr = mysql_query("SELECT * FROM `forum` WHERE `refid` = '$pf' AND `type` = 'r'");
                        while ($pr1 = mysql_fetch_array($pr)) {
                            $arr[] = $pr1['id'];
                        }
                        foreach ($rz as $v) {
                            if (in_array($v, $arr)) {
                                mysql_query("INSERT INTO `forum` SET
                                    `refid` = '$v',
                                    `type` = 't',
                                    `time` = '" . time() . "',
                                    `user_id` = " . Vars::$USER_ID . ",
                                    `from` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                                    `text` = '" . mysql_real_escape_string($name) . "'
                                ");
                                $rid = mysql_insert_id();
                                mysql_query("INSERT INTO `forum` SET
                                    `refid` = '$rid',
                                    `type` = 'm',
                                    `time` = '" . time() . "',
                                    `user_id` = " . Vars::$USER_ID . ",
                                    `from` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                                    `ip` = '" . long2ip(Vars::$IP) . "',
                                    `soft` = '" . mysql_real_escape_string(Vars::$USERAGENT) . "',
                                    `text` = '" . mysql_real_escape_string($text) . "'
                                ");
                            }
                        }
                    }
                    mysql_query("INSERT INTO `news` SET
                        `time` = '" . time() . "',
                        `avt` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                        `name` = '" . mysql_real_escape_string($name) . "',
                        `text` = '" . mysql_real_escape_string($text) . "',
                        `kom` = '$rid'
                    ");
                    mysql_query("UPDATE `users` SET
                        `lastpost` = '" . time() . "'
                        WHERE `id` = " . Vars::$USER_ID
                    );
                    echo '<p>' . $lng_news['article_added'] . '<br /><a href="index.php">' . $lng_news['to_news'] . '</a></p>';
                } else {
                    echo Functions::displayError($error, '<a href="index.php">' . $lng_news['to_news'] . '</a>');
                }
            } else {
                echo '<form action="index.php?do=add" method="post"><div class="menu">' .
                     '<p><h3>' . $lng_news['article_title'] . '</h3>' .
                     '<input type="text" name="name"/></p>' .
                     '<p><h3>' . Vars::$LNG['text'] . '</h3>' .
                     '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="text"></textarea></p>' .
                     '<p><h3>' . $lng_news['discuss'] . '</h3>';
                $fr = mysql_query("SELECT * FROM `forum` WHERE `type` = 'f'");
                echo '<input type="radio" name="pf" value="0" checked="checked" />' . $lng_news['discuss_off'] . '<br />';
                while ($fr1 = mysql_fetch_array($fr)) {
                    echo '<input type="radio" name="pf" value="' . $fr1['id'] . '"/>' . $fr1['text'] . '<select name="rz[]">';
                    $pr = mysql_query("SELECT * FROM `forum` WHERE `type` = 'r' AND `refid` = '" . $fr1['id'] . "'");
                    while ($pr1 = mysql_fetch_array($pr)) {
                        echo '<option value="' . $pr1['id'] . '">' . $pr1['text'] . '</option>';
                    }
                    echo '</select><br/>';
                }
                echo '</p></div><div class="bmenu">' .
                     '<input type="submit" name="submit" value="' . Vars::$LNG['save'] . '"/>' .
                     '</div></form>' .
                     '<p><a href="index.php">' . $lng_news['to_news'] . '</a></p>';
            }
        } else {
            header("location: index.php");
        }
        break;

    case 'edit':
        /*
        -----------------------------------------------------------------
        Редактирование новости
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 6) {
            echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['news'] . '</b></a> | ' . Vars::$LNG['edit'] . '</div>';
            if (!Vars::$ID) {
                echo Functions::displayError($lng['error_wrong_data'], '<a href="index.php">' . $lng_news['to_news'] . '</a>');
                require_once('../includes/end.php');
                exit;
            }
            if (isset($_POST['submit'])) {
                $error = array();
                if (empty($_POST['name']))
                    $error[] = $lng_news['error_title'];
                if (empty($_POST['text']))
                    $error[] = $lng_news['error_text'];
                $name = Validate::filterString($_POST['name']);
                $text = mysql_real_escape_string(trim($_POST['text']));
                if (!$error) {
                    mysql_query("UPDATE `news` SET
                        `name` = '" . mysql_real_escape_string($name) . "',
                        `text` = '$text'
                        WHERE `id` = " . Vars::$ID
                    );
                } else {
                    echo Functions::displayError($error, '<a href="index.php?act=edit&amp;id=' . Vars::$ID . '">' . Vars::$LNG['repeat'] . '</a>');
                }
                echo '<p>' . $lng_news['article_changed'] . '<br /><a href="index.php">' . Vars::$LNG['continue'] . '</a></p>';
            } else {
                $req = mysql_query("SELECT * FROM `news` WHERE `id` = " . Vars::$ID);
                $res = mysql_fetch_assoc($req);
                echo '<div class="menu"><form action="index.php?do=edit&amp;id=' . Vars::$ID . '" method="post">' .
                     '<p><h3>' . $lng_news['article_title'] . '</h3>' .
                     '<input type="text" name="name" value="' . $res['name'] . '"/></p>' .
                     '<p><h3>' . Vars::$LNG['text'] . '</h3>' .
                     '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="text">' . htmlentities($res['text'], ENT_QUOTES, 'UTF-8') . '</textarea></p>' .
                     '<p><input type="submit" name="submit" value="' . Vars::$LNG['save'] . '"/></p>' .
                     '</form></div>' .
                     '<div class="phdr"><a href="index.php">' . $lng_news['to_news'] . '</a></div>';
            }
        } else {
            header('location: index.php');
        }
        break;

    case 'clean':
        /*
        -----------------------------------------------------------------
        Чистка новостей
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 7) {
            echo '<div class="phdr"><a href="index.php"><b>' . $lng_news['site_news'] . '</b></a> | ' . Vars::$LNG['clear'] . '</div>';
            if (isset($_POST['submit'])) {
                $cl = isset($_POST['cl']) ? intval($_POST['cl']) : '';
                switch ($cl) {
                    case '1':
                        // Чистим новости, старше 1 недели
                        mysql_query("DELETE FROM `news` WHERE `time`<='" . (time() - 604800) . "'");
                        mysql_query("OPTIMIZE TABLE `news`");
                        echo '<p>' . $lng_news['clear_week_confirmation'] . '</p><p><a href="index.php">' . $lng_news['to_news'] . '</a></p>';
                        break;

                    case '2':
                        // Проводим полную очистку
                        mysql_query("TRUNCATE TABLE `news`");
                        echo '<p>' . $lng_news['clear_all_confirmation'] . '</p><p><a href="index.php">' . $lng_news['to_news'] . '</a></p>';
                        break;
                    default :
                        // Чистим сообщения, старше 1 месяца
                        mysql_query("DELETE FROM `news` WHERE `time`<='" . (time() - 2592000) . "'");
                        mysql_query("OPTIMIZE TABLE `news`;");
                        echo '<p>' . $lng_news['clear_month_confirmation'] . '</p><p><a href="index.php">' . $lng_news['to_news'] . '</a></p>';
                }
            } else {
                echo '<div class="menu"><form id="clean" method="post" action="index.php?do=clean">' .
                     '<p><h3>' . Vars::$LNG['clear_param'] . '</h3>' .
                     '<input type="radio" name="cl" value="0" checked="checked" />' . $lng_news['clear_month'] . '<br />' .
                     '<input type="radio" name="cl" value="1" />' . $lng_news['clear_week'] . '<br />' .
                     '<input type="radio" name="cl" value="2" />' . Vars::$LNG['clear_all'] . '</p>' .
                     '<p><input type="submit" name="submit" value="' . Vars::$LNG['clear'] . '" /></p>' .
                     '</form></div>' .
                     '<div class="phdr"><a href="index.php">' . Vars::$LNG['cancel'] . '</a></div>';
            }
        } else {
            header("location: index.php");
        }
        break;

    case 'del':
        /*
        -----------------------------------------------------------------
        Удаление новости
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 6) {
            echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['site_news'] . '</b></a> | ' . Vars::$LNG['delete'] . '</div>';
            if (isset($_GET['yes'])) {
                mysql_query("DELETE FROM `news` WHERE `id` = " . Vars::$ID);
                echo '<p>' . $lng_news['article_deleted'] . '<br/><a href="index.php">' . $lng_news['to_news'] . '</a></p>';
            } else {
                echo '<p>' . Vars::$LNG['delete_confirmation'] . '<br/>' .
                     '<a href="index.php?do=del&amp;id=' . Vars::$ID . '&amp;yes">' . Vars::$LNG['delete'] . '</a> | <a href="index.php">' . Vars::$LNG['cancel'] . '</a></p>';
            }
        } else {
            header("location: index.php");
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Вывод списка новостей
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . Vars::$LNG['site_news'] . '</b></div>';
        if (Vars::$USER_RIGHTS >= 6)
            echo '<div class="topmenu"><a href="index.php?do=add">' . Vars::$LNG['add'] . '</a> | <a href="index.php?do=clean">' . Vars::$LNG['clear'] . '</a></div>';
        $req = mysql_query("SELECT COUNT(*) FROM `news`");
        $total = mysql_result($req, 0);
        $req = mysql_query("SELECT * FROM `news` ORDER BY `time` DESC LIMIT " . Vars::db_pagination());
        $i = 0;
        while ($res = mysql_fetch_array($req)) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            $text = Validate::filterString($res['text'], 1, 1);
            if (Vars::$USER_SET['smileys'])
                $text = Functions::smileys($text, 1);
            echo '<h3>' . $res['name'] . '</h3>' .
                 '<span class="gray"><small>' . Vars::$LNG['author'] . ': ' . $res['avt'] . ' (' . Functions::displayDate($res['time']) . ')</small></span>' .
                 '<br />' . $text . '<div class="sub">';
            if ($res['kom'] != 0 && $res['kom'] != "") {
                $mes = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '" . $res['kom'] . "'");
                $komm = mysql_result($mes, 0) - 1;
                if ($komm >= 0)
                    echo '<a href="../forum/?id=' . $res['kom'] . '">' . $lng_news['discuss_on_forum'] . ' (' . $komm . ')</a><br/>';
            }
            if (Vars::$USER_RIGHTS >= 6) {
                echo '<a href="index.php?do=edit&amp;id=' . $res['id'] . '">' . Vars::$LNG['edit'] . '</a> | ' .
                     '<a href="index.php?do=del&amp;id=' . $res['id'] . '">' . Vars::$LNG['delete'] . '</a>';
            }
            echo '</div></div>';
            ++$i;
        }
        echo '<div class="phdr">' . Vars::$LNG['total'] . ':&#160;' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo '<div class="topmenu">' . Functions::displayPagination('index.php?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                 '<p><form action="index.php" method="post">' .
                 '<input type="text" name="page" size="2"/>' .
                 '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/></form></p>';
        }
}

require_once('../includes/end.php');