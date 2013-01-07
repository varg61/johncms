<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

// Проверяем права доступа
if (Vars::$USER_RIGHTS != 9) {
    echo Functions::displayError(__('access_forbidden'));
    exit;
}

$backLink = Router::getUrl(2);

switch (Vars::$ACT) {
    case 'view':
        /*
        -----------------------------------------------------------------
        Предварительный просмотр счетчиков
        -----------------------------------------------------------------
        */
        if (Vars::$ID) {
            $req = mysql_query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
            if (mysql_num_rows($req)) {
                if (isset($_GET['go']) && $_GET['go'] == 'on') {
                    mysql_query("UPDATE `cms_counters` SET `switch` = '1' WHERE `id` = " . Vars::$ID);
                    $req = mysql_query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
                } elseif (isset($_GET['go']) && $_GET['go'] == 'off') {
                    mysql_query("UPDATE `cms_counters` SET `switch` = '0' WHERE `id` = " . Vars::$ID);
                    $req = mysql_query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
                }
                $res = mysql_fetch_array($req);
                echo'<div class="phdr"><a href="' . Vars::$URI . '"><b>' . __('counters') . '</b></a> | ' . __('viewing') . '</div>' .
                    '<div class="menu">' . ($res['switch'] == 1 ? '<span class="green">[ON]</span>' : '<span class="red">[OFF]</span>') . '&#160;<b>' . Validate::checkout($res['name']) . '</b></div>' .
                    ($res['switch'] == 1 ? '<div class="gmenu">' : '<div class="rmenu">') . '<p><h3>' . __('counter_mod1') . '</h3>' . $res['link1'] . '</p>' .
                    '<p><h3>' . __('counter_mod2') . '</h3>' . $res['link2'] . '</p>' .
                    '<p><h3>' . __('display_mode') . '</h3>';
                switch ($res['mode']) {
                    case 2:
                        echo __('counter_help1');
                        break;

                    case 3:
                        echo __('counter_help2');
                        break;

                    default:
                        echo __('counter_help12');
                }
                echo '</p></div>';
                echo '<div class="phdr">'
                    . ($res['switch'] == 1 ? '<a href="' . Vars::$URI . '?act=view&amp;go=off&amp;id=' . Vars::$ID . '">' . __('lng_off') . '</a>'
                        : '<a href="' . Vars::$URI . '?act=view&amp;go=on&amp;id=' . Vars::$ID . '">' . __('lng_on') . '</a>')
                    . ' | <a href="' . Vars::$URI . '?act=edit&amp;id=' . Vars::$ID . '">' . __('edit') . '</a> | <a href="' . Vars::$URI . '?act=del&amp;id=' . Vars::$ID . '">' . __('delete') . '</a></div>';
            } else {
                echo Functions::displayError(__('error_wrong_data'));
            }
        }
        break;

    case 'up':
        /*
        -----------------------------------------------------------------
        Перемещение счетчика на одну позицию вверх
        -----------------------------------------------------------------
        */
        if (Vars::$ID) {
            $req = mysql_query("SELECT `sort` FROM `cms_counters` WHERE `id` = " . Vars::$ID);
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                $sort = $res['sort'];
                $req = mysql_query("SELECT * FROM `cms_counters` WHERE `sort` < '$sort' ORDER BY `sort` DESC LIMIT 1");
                if (mysql_num_rows($req)) {
                    $res = mysql_fetch_assoc($req);
                    $id2 = $res['id'];
                    $sort2 = $res['sort'];
                    mysql_query("UPDATE `cms_counters` SET `sort` = '$sort2' WHERE `id` = " . Vars::$ID);
                    mysql_query("UPDATE `cms_counters` SET `sort` = '$sort' WHERE `id` = '$id2'");
                }
            }
        }
        header('Location: ' . Vars::$URI);
        break;

    case 'down':
        /*
        -----------------------------------------------------------------
        Перемещение счетчика на одну позицию вниз
        -----------------------------------------------------------------
        */
        if (Vars::$ID) {
            $req = mysql_query("SELECT `sort` FROM `cms_counters` WHERE `id` = " . Vars::$ID);
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                $sort = $res['sort'];
                $req = mysql_query("SELECT * FROM `cms_counters` WHERE `sort` > '$sort' ORDER BY `sort` ASC LIMIT 1");
                if (mysql_num_rows($req)) {
                    $res = mysql_fetch_assoc($req);
                    $id2 = $res['id'];
                    $sort2 = $res['sort'];
                    mysql_query("UPDATE `cms_counters` SET `sort` = '$sort2' WHERE `id` = " . Vars::$ID);
                    mysql_query("UPDATE `cms_counters` SET `sort` = '$sort' WHERE `id` = '$id2'");
                }
            }
        }
        header('Location: ' . Vars::$URI);
        break;

    case 'del':
        /*
        -----------------------------------------------------------------
        Удаление счетчика
        -----------------------------------------------------------------
        */
        if (!Vars::$ID) {
            echo Functions::displayError(__('error_wrong_data'), '<a href="' . Vars::$URI . '">' . __('back') . '</a>');
            exit;
        }
        $req = mysql_query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
        if (mysql_num_rows($req)) {
            if (isset($_POST['submit'])) {
                mysql_query("DELETE FROM `cms_counters` WHERE `id` = " . Vars::$ID);
                echo '<p>' . __('counter_deleted') . '<br/><a href="' . Vars::$URI . '">' . __('continue') . '</a></p>';
                exit;
            } else {
                echo '<form action="' . Vars::$URI . '?act=del&amp;id=' . Vars::$ID . '" method="post">';
                echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . __('counters') . '</b></a> | ' . __('delete') . '</div>';
                $res = mysql_fetch_array($req);
                echo '<div class="rmenu"><p><h3>' . Validate::checkout($res['name']) . '</h3>' . __('delete_confirmation') . '</p><p><input type="submit" value="' . __('delete') . '" name="submit" /></p></div>';
                echo '<div class="phdr"><a href="' . Vars::$URI . '">' . __('cancel') . '</a></div></form>';
            }
        } else {
            echo Functions::displayError(__('error_wrong_data'), '<a href="' . Vars::$URI . '">' . __('back') . '</a>');
            exit;
        }
        break;

    case 'edit':
        /*
        -----------------------------------------------------------------
        Форма добавления счетчика
        -----------------------------------------------------------------
        */
        if (isset($_POST['submit'])) {
            // Предварительный просмотр
            $name = isset($_POST['name']) ? mb_substr(trim($_POST['name']), 0, 25) : '';
            $link1 = isset($_POST['link1']) ? trim($_POST['link1']) : '';
            $link2 = isset($_POST['link2']) ? trim($_POST['link2']) : '';
            $mode = isset($_POST['mode']) ? intval($_POST['mode']) : 1;
            if (empty($name) || empty($link1)) {
                echo Functions::displayError(__('error_empty_fields'), '<a href="' . Vars::$URI . '?act=edit' . (Vars::$ID ? '&amp;id=' . Vars::$ID : '') . '">' . __('back') . '</a>');
                exit;
            }
            echo'<div class="phdr"><a href="' . Vars::$URI . '"><b>' . __('counters') . '</b></a> | ' . __('preview') . '</div>' .
                '<div class="menu"><p><h3>' . __('title') . '</h3><b>' . Validate::checkout($name) . '</b></p>' .
                '<p><h3>' . __('counter_mod1') . '</h3>' . $link1 . '</p>' .
                '<p><h3>' . __('counter_mod2') . '</h3>' . $link2 . '</p></div>' .
                '<div class="rmenu">' . __('counter_preview_help') . '</div>' .
                '<form action="' . Vars::$URI . '?act=add" method="post">' .
                '<input type="hidden" value="' . $name . '" name="name" />' .
                '<input type="hidden" value="' . htmlspecialchars($link1) . '" name="link1" />' .
                '<input type="hidden" value="' . htmlspecialchars($link2) . '" name="link2" />' .
                '<input type="hidden" value="' . $mode . '" name="mode" />';
            if (Vars::$ID) {
                echo '<input type="hidden" value="' . Vars::$ID . '" name="id" />';
            }
            echo'<div class="bmenu"><input type="submit" value="' . __('save') . '" name="submit" /></div>' .
                '</form>';
        } else {
            $name = '';
            $link1 = '';
            $link2 = '';
            $mode = 0;
            if (Vars::$ID) {
                // запрос к базе, если счетчик редактируется
                $req = mysql_query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
                if (mysql_num_rows($req) > 0) {
                    $res = mysql_fetch_array($req);
                    $name = Validate::checkout($res['name']);
                    $link1 = htmlspecialchars($res['link1']);
                    $link2 = htmlspecialchars($res['link2']);
                    $mode = $res['mode'];
                    $switch = 1;
                } else {
                    echo Functions::displayError(__('error_wrong_data'), '<a href="' . Vars::$URI . '">' . __('back') . '</a>');
                    exit;
                }
            }
            echo'<form action="' . Vars::$URI . '?act=edit" method="post">' .
                '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . __('counters') . '</b></a> | ' . __('add') . '</div>' .
                '<div class="menu"><p><h3>' . __('title') . '</h3><input type="text" name="name" value="' . $name . '" /></p>' .
                '<p><h3>' . __('counter_mod1') . '</h3><textarea rows="3" name="link1">' . $link1 . '</textarea><br /><small>' . __('counter_mod1_description') . '</small></p>' .
                '<p><h3>' . __('counter_mod2') . '</h3><textarea rows="3" name="link2">' . $link2 . '</textarea><br /><small>' . __('counter_mod2_description') . '</small></p>' .
                '<p><h3>' . __('view_mode') . '</h3>' . '<input type="radio" value="1" ' . ($mode == 0 || $mode == 1 ? 'checked="checked" ' : '') . 'name="mode" />&#160;' . __('default') . '<br />' .
                '<small>' . __('counter_mod_default_help') . '</small></p><p>' .
                '<input type="radio" value="2" ' . ($mode == 2 ? 'checked="checked" ' : '') . 'name="mode" />&#160;' . __('counter_mod1') . '<br />' .
                '<input type="radio" value="3" ' . ($mode == 3 ? 'checked="checked" ' : '') . 'name="mode" />&#160;' . __('counter_mod2') . '</p></div>' .
                '<div class="rmenu"><small>' . __('counter_add_help') . '</small></div>';
            if (Vars::$ID)
                echo '<input type="hidden" value="' . Vars::$ID . '" name="id" />';
            echo '<div class="bmenu"><input type="submit" value="' . __('viewing') . '" name="submit" /></div>';
            echo '</form>';
        }
        break;

    case 'add':
        /*
        -----------------------------------------------------------------
        Запись счетчика в базу
        -----------------------------------------------------------------
        */
        $name = isset($_POST['name']) ? mb_substr($_POST['name'], 0, 25) : '';
        $link1 = isset($_POST['link1']) ? $_POST['link1'] : '';
        $link2 = isset($_POST['link2']) ? $_POST['link2'] : '';
        $mode = isset($_POST['mode']) ? intval($_POST['mode']) : 1;
        if (empty($name) || empty($link1)) {
            echo Functions::displayError(__('error_empty_fields'), '<a href="' . Vars::$URI . '?act=edit' . (Vars::$ID ? '&amp;id=' . Vars::$ID : '') . '">' . __('back') . '</a>');
            exit;
        }
        if (Vars::$ID) {
            // Режим редактирования
            $req = mysql_query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
            if (mysql_num_rows($req) != 1) {
                echo Functions::displayError(__('error_wrong_data'));
                exit;
            }
            mysql_query("UPDATE `cms_counters` SET
            `name` = '" . mysql_real_escape_string(Validate::checkout($name)) . "',
            `link1` = '" . mysql_real_escape_string($link1) . "',
            `link2` = '" . mysql_real_escape_string($link2) . "',
            `mode` = '$mode'
            WHERE `id` = " . Vars::$ID);
        } else {
            // Получаем значение сортировки
            $req = mysql_query("SELECT `sort` FROM `cms_counters` ORDER BY `sort` DESC LIMIT 1");
            if (mysql_num_rows($req) > 0) {
                $res = mysql_fetch_array($req);
                $sort = $res['sort'] + 1;
            } else {
                $sort = 1;
            }
            // Режим добавления
            mysql_query("INSERT INTO `cms_counters` SET
            `name` = '" . mysql_real_escape_string(Validate::checkout($name)) . "',
            `sort` = '$sort',
            `link1` = '" . mysql_real_escape_string($link1) . "',
            `link2` = '" . mysql_real_escape_string($link2) . "',
            `mode` = '$mode'");
        }
        echo'<div class="gmenu"><p>' . (Vars::$ID ? __('counter_edit_conf') : __('counter_add_conf')) . '<br/>' .
            '<a href="' . Vars::$URI . '">' . __('continue') . '</a>' .
            '</p></div>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Вывод списка счетчиков
        -----------------------------------------------------------------
        */
        echo'<div class="phdr"><a href="' . $backLink . '"><b>' . __('admin_panel') . '</b></a> | ' . __('counters') . '</div>' .
            '<div class="gmenu"><form action="' . Vars::$URI . '?act=edit" method="post"><input type="submit" name="delete" value="' . __('add') . '"/></form></div>';
        $req = mysql_query("SELECT * FROM `cms_counters` ORDER BY `sort` ASC");
        if ($total = mysql_num_rows($req)) {
            for ($i = 0; $res = mysql_fetch_assoc($req); ++$i) {
                echo($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                    Functions::getImage(($res['switch'] == 1 ? 'green' : 'red') . '.png', '', 'class="left"') . '&#160;' .
                    '<a href="' . Vars::$URI . '?act=view&amp;id=' . $res['id'] . '"><b>' . Validate::checkout($res['name']) . '</b></a><br />' .
                    '<div class="sub">' .
                    '<a href="' . Vars::$URI . '?act=up&amp;id=' . $res['id'] . '">' . __('up') . '</a> | ' .
                    '<a href="' . Vars::$URI . '?act=down&amp;id=' . $res['id'] . '">' . __('down') . '</a> | ' .
                    '<a href="' . Vars::$URI . '?act=edit&amp;id=' . $res['id'] . '">' . __('edit') . '</a> | ' .
                    '<a href="' . Vars::$URI . '?act=del&amp;id=' . $res['id'] . '">' . __('delete') . '</a>' .
                    '</div>' .
                    '</div>';
            }
        } else {
            echo'<div class="menu"><p>' . __('list_empty') . '</p></div>';
        }
        echo'<div class="phdr">' . __('total') . ': ' . $total . '</div>';
}
echo'<p>' . (Vars::$MOD ? '<a href="' . Vars::$URI . '">' . __('counters') . '</a><br />' : '') .
    '<a href="' . $backLink . '">' . __('admin_panel') . '</a></p>';