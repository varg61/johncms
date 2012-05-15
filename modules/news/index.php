<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

switch (Vars::$ACT) {
    case 'add':
        /*
        -----------------------------------------------------------------
        Добавление новости
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 6) {
            echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('news') . '</b></a> | ' . lng('add') . '</div>';
            $old = 20;
            if (isset($_POST['submit'])) {
                $error = array();
                $name = isset($_POST['name']) ? Validate::filterString($_POST['name']) : FALSE;
                $text = isset($_POST['text']) ? trim($_POST['text']) : FALSE;
                if (!$name)
                    $error[] = lng('error_title');
                if (!$text)
                    $error[] = lng('error_text');
                $flood = Functions::antiFlood();
                if ($flood)
                    $error[] = lng('error_flood') . ' ' . $flood . '&#160;' . lng('seconds');
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
                                    `soft` = '" . mysql_real_escape_string(Vars::$USER_AGENT) . "',
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
                    echo '<p>' . lng('article_added') . '<br /><a href="' . Vars::$URI . '">' . lng('to_news') . '</a></p>';
                } else {
                    echo Functions::displayError($error, '<a href="' . Vars::$URI . '">' . lng('to_news') . '</a>');
                }
            } else {
                echo '<form action="' . Vars::$URI . '?act=add" method="post"><div class="menu">' .
                    '<p><h3>' . lng('article_title') . '</h3>' .
                    '<input type="text" name="name"/></p>' .
                    '<p><h3>' . lng('text') . '</h3>' .
                    '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="text"></textarea></p>' .
                    '<p><h3>' . lng('discuss') . '</h3>';
                $fr = mysql_query("SELECT * FROM `forum` WHERE `type` = 'f'");
                echo '<input type="radio" name="pf" value="0" checked="checked" />' . lng('discuss_off') . '<br />';
                while ($fr1 = mysql_fetch_array($fr)) {
                    echo '<input type="radio" name="pf" value="' . $fr1['id'] . '"/>' . $fr1['text'] . '<select name="rz[]">';
                    $pr = mysql_query("SELECT * FROM `forum` WHERE `type` = 'r' AND `refid` = '" . $fr1['id'] . "'");
                    while ($pr1 = mysql_fetch_array($pr)) {
                        echo '<option value="' . $pr1['id'] . '">' . $pr1['text'] . '</option>';
                    }
                    echo '</select><br/>';
                }
                echo '</p></div><div class="bmenu">' .
                    '<input type="submit" name="submit" value="' . lng('save') . '"/>' .
                    '</div></form>' .
                    '<p><a href="' . Vars::$URI . '">' . lng('to_news') . '</a></p>';
            }
        } else {
            header("location: " . Vars::$URI);
        }
        break;

    case 'edit':
        /*
        -----------------------------------------------------------------
        Редактирование новости
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 6) {
            echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('news') . '</b></a> | ' . lng('edit') . '</div>';
            if (!Vars::$ID) {
                echo Functions::displayError(lng('error_wrong_data'), '<a href="' . Vars::$URI . '">' . lng('to_news') . '</a>');
                exit;
            }
            if (isset($_POST['submit'])) {
                $error = array();
                if (empty($_POST['name']))
                    $error[] = lng('error_title');
                if (empty($_POST['text']))
                    $error[] = lng('error_text');
                $name = Validate::filterString($_POST['name']);
                $text = mysql_real_escape_string(trim($_POST['text']));
                if (!$error) {
                    mysql_query("UPDATE `news` SET
                        `name` = '" . mysql_real_escape_string($name) . "',
                        `text` = '$text'
                        WHERE `id` = " . Vars::$ID
                    );
                } else {
                    echo Functions::displayError($error, '<a href="' . Vars::$URI . '?act=edit&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a>');
                }
                echo '<p>' . lng('article_changed') . '<br /><a href="' . Vars::$URI . '">' . lng('continue') . '</a></p>';
            } else {
                $req = mysql_query("SELECT * FROM `news` WHERE `id` = " . Vars::$ID);
                $res = mysql_fetch_assoc($req);
                echo '<div class="menu"><form action="' . Vars::$URI . '?act=edit&amp;id=' . Vars::$ID . '" method="post">' .
                    '<p><h3>' . lng('article_title') . '</h3>' .
                    '<input type="text" name="name" value="' . $res['name'] . '"/></p>' .
                    '<p><h3>' . lng('text') . '</h3>' .
                    '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="text">' . htmlentities($res['text'], ENT_QUOTES, 'UTF-8') . '</textarea></p>' .
                    '<p><input type="submit" name="submit" value="' . lng('save') . '"/></p>' .
                    '</form></div>' .
                    '<div class="phdr"><a href="' . Vars::$URI . '">' . lng('to_news') . '</a></div>';
            }
        } else {
            header('location: ' . Vars::$URI);
        }
        break;

    case 'clean':
        /*
        -----------------------------------------------------------------
        Чистка новостей
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 7) {
            echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('site_news') . '</b></a> | ' . lng('clear') . '</div>';
            if (isset($_POST['submit'])) {
                $cl = isset($_POST['cl']) ? intval($_POST['cl']) : '';
                switch ($cl) {
                    case '1':
                        // Чистим новости, старше 1 недели
                        mysql_query("DELETE FROM `news` WHERE `time`<='" . (time() - 604800) . "'");
                        mysql_query("OPTIMIZE TABLE `news`");
                        echo '<p>' . lng('clear_week_confirmation') . '</p><p><a href="' . Vars::$URI . '">' . lng('to_news') . '</a></p>';
                        break;

                    case '2':
                        // Проводим полную очистку
                        mysql_query("TRUNCATE TABLE `news`");
                        echo '<p>' . lng('clear_all_confirmation') . '</p><p><a href="' . Vars::$URI . '">' . lng('to_news') . '</a></p>';
                        break;
                    default :
                        // Чистим сообщения, старше 1 месяца
                        mysql_query("DELETE FROM `news` WHERE `time`<='" . (time() - 2592000) . "'");
                        mysql_query("OPTIMIZE TABLE `news`;");
                        echo '<p>' . lng('clear_month_confirmation') . '</p><p><a href="' . Vars::$URI . '">' . lng('to_news') . '</a></p>';
                }
            } else {
                echo '<div class="menu"><form id="clean" method="post" action="' . Vars::$URI . '?act=clean">' .
                    '<p><h3>' . lng('clear_param') . '</h3>' .
                    '<input type="radio" name="cl" value="0" checked="checked" />' . lng('clear_month') . '<br />' .
                    '<input type="radio" name="cl" value="1" />' . lng('clear_week') . '<br />' .
                    '<input type="radio" name="cl" value="2" />' . lng('clear_all') . '</p>' .
                    '<p><input type="submit" name="submit" value="' . lng('clear') . '" /></p>' .
                    '</form></div>' .
                    '<div class="phdr"><a href="' . Vars::$URI . '">' . lng('cancel') . '</a></div>';
            }
        } else {
            header("location: " . Vars::$URI);
        }
        break;

    case 'del':
        /*
        -----------------------------------------------------------------
        Удаление новости
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 6) {
            echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('site_news') . '</b></a> | ' . lng('delete') . '</div>';
            if (isset($_GET['yes'])) {
                mysql_query("DELETE FROM `news` WHERE `id` = " . Vars::$ID);
                echo '<p>' . lng('article_deleted') . '<br/><a href="' . Vars::$URI . '">' . lng('to_news') . '</a></p>';
            } else {
                echo '<p>' . lng('delete_confirmation') . '<br/>' .
                    '<a href="' . Vars::$URI . '?act=del&amp;id=' . Vars::$ID . '&amp;yes">' . lng('delete') . '</a> | <a href="' . Vars::$URI . '">' . lng('cancel') . '</a></p>';
            }
        } else {
            header("location: " . Vars::$URI);
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Вывод списка новостей
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . lng('site_news') . '</b></div>';
        if (Vars::$USER_RIGHTS >= 6) {
            echo '<div class="topmenu"><a href="' . Vars::$URI . '?act=add">' . lng('add') . '</a> | <a href="' . Vars::$URI . '?act=clean">' . lng('clear') . '</a></div>';
        }
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `news`"), 0);
        if ($total) {
            $req = mysql_query("SELECT * FROM `news` ORDER BY `time` DESC " . Vars::db_pagination());
            for ($i = 0; $res = mysql_fetch_assoc($req); ++$i) {
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                $text = Validate::filterString($res['text'], 1, 1);
                if (Vars::$USER_SET['smileys']) {
                    $text = Functions::smileys($text, 1);
                }
                echo'<h3>' . $res['name'] . '</h3>' .
                    '<span class="gray"><small>' . lng('author') . ': ' . $res['avt'] . ' (' . Functions::displayDate($res['time']) . ')</small></span>' .
                    '<br />' . $text . '<div class="sub">';
                if ($res['kom'] != 0 && $res['kom'] != "") {
                    $mes = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '" . $res['kom'] . "'");
                    $komm = mysql_result($mes, 0) - 1;
                    if ($komm >= 0) {
                        echo '<a href="../forum/?id=' . $res['kom'] . '">' . lng('discuss_on_forum') . ' (' . $komm . ')</a><br/>';
                    }
                }
                if (Vars::$USER_RIGHTS >= 6) {
                    echo'<a href="' . Vars::$URI . '?act=edit&amp;id=' . $res['id'] . '">' . lng('edit') . '</a> | ' .
                        '<a href="' . Vars::$URI . '?act=del&amp;id=' . $res['id'] . '">' . lng('delete') . '</a>';
                }
                echo '</div></div>';
            }
        } else {
            echo'<div class="menu"><p>' . lng('list_empty') . '</p></div>';
        }
        echo '<div class="phdr">' . lng('total') . ':&#160;' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                '<p><form action="' . Vars::$URI . '" method="post">' .
                '<input type="text" name="page" size="2"/>' .
                '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
        }

        if(Vars::$USER_RIGHTS >= 7){
            echo'<p><a href="' . Vars::$URI . '/admin">' . lng('admin_panel') . '</a></p>';
        }
}