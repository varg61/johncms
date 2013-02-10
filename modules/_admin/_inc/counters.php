<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_ADMIN') or die('Error: restricted access');
$uri = Router::getUri(3);

switch (Vars::$ACT) {
    case 'view':
        // Предварительный просмотр счетчиков
        if (Vars::$ID) {
            $req = DB::PDO()->query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
            if ($req->rowCount()) {
                if (isset($_GET['go']) && $_GET['go'] == 'on') {
                    DB::PDO()->exec("UPDATE `cms_counters` SET `switch` = '1' WHERE `id` = " . Vars::$ID);
                    $req = DB::PDO()->query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
                } elseif (isset($_GET['go']) && $_GET['go'] == 'off') {
                    DB::PDO()->exec("UPDATE `cms_counters` SET `switch` = '0' WHERE `id` = " . Vars::$ID);
                    $req = DB::PDO()->query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
                }
                $res = $req->fetch();
                echo'<div class="phdr"><a href="' . $uri . '"><b>' . __('counters') . '</b></a> | ' . __('viewing') . '</div>' .
                    '<div class="menu">' . ($res['switch'] == 1 ? '<span class="green">[ON]</span>' : '<span class="red">[OFF]</span>') . '&#160;<b>' . Functions::checkout($res['name']) . '</b></div>' .
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
                    . ($res['switch'] == 1 ? '<a href="' . $uri . '?act=view&amp;go=off&amp;id=' . Vars::$ID . '">' . __('lng_off') . '</a>'
                        : '<a href="' . $uri . '?act=view&amp;go=on&amp;id=' . Vars::$ID . '">' . __('lng_on') . '</a>')
                    . ' | <a href="' . $uri . '?act=edit&amp;id=' . Vars::$ID . '">' . __('edit') . '</a> | <a href="' . $uri . '?act=del&amp;id=' . Vars::$ID . '">' . __('delete') . '</a></div>';
            } else {
                echo Functions::displayError(__('error_wrong_data'));
            }
        }
        break;

    case 'up':
        // Перемещение счетчика на одну позицию вверх
        if (Vars::$ID) {
            $req = DB::PDO()->query("SELECT `sort` FROM `cms_counters` WHERE `id` = " . Vars::$ID);
            if ($req->rowCount()) {
                $res = $req->fetch();
                $sort = $res['sort'];
                $req = DB::PDO()->query("SELECT * FROM `cms_counters` WHERE `sort` < '$sort' ORDER BY `sort` DESC LIMIT 1");
                if ($req->rowCount()) {
                    $res = $req->fetch();
                    $id2 = $res['id'];
                    $sort2 = $res['sort'];
                    DB::PDO()->exec("UPDATE `cms_counters` SET `sort` = '$sort2' WHERE `id` = " . Vars::$ID);
                    DB::PDO()->exec("UPDATE `cms_counters` SET `sort` = '$sort' WHERE `id` = '$id2'");
                }
            }
        }

        header('Location: ' . $uri);
        break;

    case 'down':
        // Перемещение счетчика на одну позицию вниз
        if (Vars::$ID) {
            $req = DB::PDO()->query("SELECT `sort` FROM `cms_counters` WHERE `id` = " . Vars::$ID);
            if ($req->rowCount()) {
                $res = $req->fetch();
                $sort = $res['sort'];
                $req = DB::PDO()->query("SELECT * FROM `cms_counters` WHERE `sort` > '$sort' ORDER BY `sort` ASC LIMIT 1");
                if ($req->rowCount()) {
                    $res = $req->fetch();
                    $id2 = $res['id'];
                    $sort2 = $res['sort'];
                    DB::PDO()->exec("UPDATE `cms_counters` SET `sort` = '$sort2' WHERE `id` = " . Vars::$ID);
                    DB::PDO()->exec("UPDATE `cms_counters` SET `sort` = '$sort' WHERE `id` = '$id2'");
                }
            }
        }

        header('Location: ' . $uri);
        break;

    case 'del':
        // Удаление счетчика
        if (!Vars::$ID) {
            echo Functions::displayError(__('error_wrong_data'), '<a href="' . $uri . '">' . __('back') . '</a>');
            exit;
        }
        $req = DB::PDO()->query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
        if ($req->rowCount()) {
            if (isset($_POST['submit'])) {
                DB::PDO()->exec("DELETE FROM `cms_counters` WHERE `id` = " . Vars::$ID);
                echo '<p>' . __('counter_deleted') . '<br/><a href="' . $uri . '">' . __('continue') . '</a></p>';
                exit;
            } else {
                echo '<form action="' . $uri . '?act=del&amp;id=' . Vars::$ID . '" method="post">';
                echo '<div class="phdr"><a href="' . $uri . '"><b>' . __('counters') . '</b></a> | ' . __('delete') . '</div>';
                $res = $req->fetch();
                echo '<div class="rmenu"><p><h3>' . Functions::checkout($res['name']) . '</h3>' . __('delete_confirmation') . '</p><p><input type="submit" value="' . __('delete') . '" name="submit" /></p></div>';
                echo '<div class="phdr"><a href="' . $uri . '">' . __('cancel') . '</a></div></form>';
            }
        } else {
            echo Functions::displayError(__('error_wrong_data'), '<a href="' . $uri . '">' . __('back') . '</a>');
            exit;
        }
        break;

    case 'edit':
        // Форма добавления счетчика
        if (isset($_POST['submit'])) {
            // Предварительный просмотр
            $name = isset($_POST['name']) ? mb_substr(trim($_POST['name']), 0, 25) : '';
            $link1 = isset($_POST['link1']) ? trim($_POST['link1']) : '';
            $link2 = isset($_POST['link2']) ? trim($_POST['link2']) : '';
            $mode = isset($_POST['mode']) ? intval($_POST['mode']) : 1;
            if (empty($name) || empty($link1)) {
                echo Functions::displayError(__('error_empty_fields'), '<a href="' . $uri . '?act=edit' . (Vars::$ID ? '&amp;id=' . Vars::$ID : '') . '">' . __('back') . '</a>');
                exit;
            }
            echo'<div class="phdr"><a href="' . $uri . '"><b>' . __('counters') . '</b></a> | ' . __('preview') . '</div>' .
                '<div class="menu"><p><h3>' . __('title') . '</h3><b>' . Functions::checkout($name) . '</b></p>' .
                '<p><h3>' . __('counter_mod1') . '</h3>' . $link1 . '</p>' .
                '<p><h3>' . __('counter_mod2') . '</h3>' . $link2 . '</p></div>' .
                '<div class="rmenu">' . __('counter_preview_help') . '</div>' .
                '<form action="' . $uri . '?act=add" method="post">' .
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
                $req = DB::PDO()->query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
                if ($req->rowCount()) {
                    $res = $req->fetch();
                    $name = Functions::checkout($res['name']);
                    $link1 = htmlspecialchars($res['link1']);
                    $link2 = htmlspecialchars($res['link2']);
                    $mode = $res['mode'];
                    $switch = 1;
                } else {
                    echo Functions::displayError(__('error_wrong_data'), '<a href="' . $uri . '">' . __('back') . '</a>');
                    exit;
                }
            }
            echo'<form action="' . $uri . '?act=edit" method="post">' .
                '<div class="phdr"><a href="' . $uri . '"><b>' . __('counters') . '</b></a> | ' . __('add') . '</div>' .
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
        // Запись счетчика в базу
        $name = isset($_POST['name']) ? mb_substr($_POST['name'], 0, 25) : '';
        $link1 = isset($_POST['link1']) ? $_POST['link1'] : '';
        $link2 = isset($_POST['link2']) ? $_POST['link2'] : '';
        $mode = isset($_POST['mode']) ? intval($_POST['mode']) : 1;
        if (empty($name) || empty($link1)) {
            echo Functions::displayError(__('error_empty_fields'), '<a href="' . $uri . '?act=edit' . (Vars::$ID ? '&amp;id=' . Vars::$ID : '') . '">' . __('back') . '</a>');
            exit;
        }
        if (Vars::$ID) {
            // Режим редактирования
            $req = DB::PDO()->query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
            if ($req->rowCount() != 1) {
                echo Functions::displayError(__('error_wrong_data'));
                exit;
            }

            $STH = DB::PDO()->prepare('
                UPDATE `cms_counters` SET
                `name`     = ?,
                `link1`    = ?,
                `link2`    = ?,
                `mode`     = ?
                WHERE `id` = ?
            ');

            $STH->execute(array(
                Functions::checkout($name),
                $link1,
                $link2,
                $mode,
                Vars::$ID
            ));
            $STH = NULL;
        } else {
            // Получаем значение сортировки
            $req = DB::PDO()->query("SELECT `sort` FROM `cms_counters` ORDER BY `sort` DESC LIMIT 1");
            if ($req->rowCount()) {
                $res = $req->fetch();
                $sort = $res['sort'] + 1;
            } else {
                $sort = 1;
            }

            // Режим добавления
            $STH = DB::PDO()->prepare('
                INSERT INTO `cms_counters`
                (`name`, `sort`, `link1`, `link2`, `mode`)
                VALUES (?, ?, ?, ?, ?)
            ');

            $STH->execute(array(
                Functions::checkout($name),
                $sort,
                $link1,
                $link2,
                $mode
            ));
            $STH = NULL;
        }
        echo'<div class="gmenu"><p>' . (Vars::$ID ? __('counter_edit_conf') : __('counter_add_conf')) . '<br/>' .
            '<a href="' . $uri . '">' . __('continue') . '</a>' .
            '</p></div>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Вывод списка счетчиков
        -----------------------------------------------------------------
        */
        echo'<div class="phdr"><a href="' . Router::getUri(2) . '"><b>' . __('admin_panel') . '</b></a> | ' . __('counters') . '</div>' .
            '<div class="gmenu"><form action="' . $uri . '?act=edit" method="post"><input type="submit" name="delete" value="' . __('add') . '"/></form></div>';
        $req = DB::PDO()->query('SELECT * FROM `cms_counters` ORDER BY `sort` ASC');
        $total = $req->rowCount();
        if ($total) {
            for ($i = 0; $res = $req->fetch(); ++$i) {
                echo($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                    Functions::getImage(($res['switch'] == 1 ? 'green' : 'red') . '.png', '', 'class="left"') . '&#160;' .
                    '<a href="' . $uri . '?act=view&amp;id=' . $res['id'] . '"><b>' . Functions::checkout($res['name']) . '</b></a><br />' .
                    '<div class="sub">' .
                    '<a href="' . $uri . '?act=up&amp;id=' . $res['id'] . '">' . __('up') . '</a> | ' .
                    '<a href="' . $uri . '?act=down&amp;id=' . $res['id'] . '">' . __('down') . '</a> | ' .
                    '<a href="' . $uri . '?act=edit&amp;id=' . $res['id'] . '">' . __('edit') . '</a> | ' .
                    '<a href="' . $uri . '?act=del&amp;id=' . $res['id'] . '">' . __('delete') . '</a>' .
                    '</div>' .
                    '</div>';
            }
        } else {
            echo'<div class="menu"><p>' . __('list_empty') . '</p></div>';
        }
        echo'<div class="phdr">' . __('total') . ': ' . $total . '</div>';
}
echo'<p>' . (Vars::$MOD ? '<a href="' . $uri . '">' . __('counters') . '</a><br />' : '') .
    '<a href="' . Router::getUri(2) . '">' . __('admin_panel') . '</a></p>';