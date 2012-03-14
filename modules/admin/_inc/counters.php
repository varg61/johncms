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

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 9) {
    header('Location: http://mobicms.net/404.php');
    exit;
}

switch (Vars::$MOD) {
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
                echo '<div class="phdr"><a href="index.php?act=counters"><b>' . lng('counters') . '</b></a> | ' . lng('viewing') . '</div>';
                echo '<div class="menu">' . ($res['switch'] == 1 ? '<span class="green">[ON]</span>' : '<span class="red">[OFF]</span>') . '&#160;<b>' . $res['name'] . '</b></div>';
                echo ($res['switch'] == 1 ? '<div class="gmenu">' : '<div class="rmenu">') . '<p><h3>' . lng('counter_mod1') . '</h3>' . $res['link1'] . '</p>';
                echo '<p><h3>' . lng('counter_mod2') . '</h3>' . $res['link2'] . '</p>';
                echo '<p><h3>' . lng('display_mode') . '</h3>';
                switch ($res['mode']) {
                    case 2:
                        echo lng('counter_help1');
                        break;

                    case 3:
                        echo lng('counter_help2');
                        break;

                    default:
                        echo lng('counter_help12');
                }
                echo '</p></div>';
                echo '<div class="phdr">'
                     . ($res['switch'] == 1 ? '<a href="index.php?act=counters&amp;mod=view&amp;go=off&amp;id=' . Vars::$ID . '">' . lng('lng_off') . '</a>'
                                : '<a href="index.php?act=counters&amp;mod=view&amp;go=on&amp;id=' . Vars::$ID . '">' . lng('lng_on') . '</a>')
                     . ' | <a href="index.php?act=counters&amp;mod=edit&amp;id=' . Vars::$ID . '">' . lng('edit') . '</a> | <a href="index.php?act=counters&amp;mod=del&amp;id=' . Vars::$ID . '">' . lng('delete') . '</a></div>';
            } else {
                echo Functions::displayError(lng('error_wrong_data'));
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
        header('Location: index.php?act=counters');
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
        header('Location: index.php?act=counters');
        break;

    case 'del':
        /*
        -----------------------------------------------------------------
        Удаление счетчика
        -----------------------------------------------------------------
        */
        if (!Vars::$ID) {
            echo Functions::displayError(lng('error_wrong_data'), '<a href="index.php?act=counters">' . lng('back') . '</a>');
            exit;
        }
        $req = mysql_query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
        if (mysql_num_rows($req)) {
            if (isset($_POST['submit'])) {
                mysql_query("DELETE FROM `cms_counters` WHERE `id` = " . Vars::$ID);
                echo '<p>' . lng('counter_deleted') . '<br/><a href="index.php?act=counters">' . lng('continue') . '</a></p>';
                exit;
            } else {
                echo '<form action="index.php?act=counters&amp;mod=del&amp;id=' . Vars::$ID . '" method="post">';
                echo '<div class="phdr"><a href="index.php?act=counters"><b>' . lng('counters') . '</b></a> | ' . lng('delete') . '</div>';
                $res = mysql_fetch_array($req);
                echo '<div class="rmenu"><p><h3>' . $res['name'] . '</h3>' . lng('delete_confirmation') . '</p><p><input type="submit" value="' . lng('delete') . '" name="submit" /></p></div>';
                echo '<div class="phdr"><a href="index.php?act=counters">' . lng('cancel') . '</a></div></form>';
            }
        } else {
            echo Functions::displayError(lng('error_wrong_data'), '<a href="index.php?act=counters">' . lng('back') . '</a>');
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
                echo Functions::displayError(lng('error_empty_fields'), '<a href="index.php?act=counters&amp;mod=edit' . (Vars::$ID ? '&amp;id=' . Vars::$ID : '') . '">' . lng('back') . '</a>');
                exit;
            }
            echo '<div class="phdr"><a href="index.php?act=counters"><b>' . lng('counters') . '</b></a> | ' . lng('preview') . '</div>' .
                 '<div class="menu"><p><h3>' . lng('title') . '</h3><b>' . Validate::filterString($name) . '</b></p>' .
                 '<p><h3>' . lng('counter_mod1') . '</h3>' . $link1 . '</p>' .
                 '<p><h3>' . lng('counter_mod2') . '</h3>' . $link2 . '</p></div>' .
                 '<div class="rmenu">' . lng('counter_preview_help') . '</div>' .
                 '<form action="index.php?act=counters&amp;mod=add" method="post">' .
                 '<input type="hidden" value="' . $name . '" name="name" />' .
                 '<input type="hidden" value="' . htmlspecialchars($link1) . '" name="link1" />' .
                 '<input type="hidden" value="' . htmlspecialchars($link2) . '" name="link2" />' .
                 '<input type="hidden" value="' . $mode . '" name="mode" />';
            if (Vars::$ID)
                echo '<input type="hidden" value="' . Vars::$ID . '" name="id" />';
            echo '<div class="bmenu"><input type="submit" value="' . lng('save') . '" name="submit" /></div>';
            echo '</form>';
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
                    $name = $res['name'];
                    $link1 = htmlspecialchars($res['link1']);
                    $link2 = htmlspecialchars($res['link2']);
                    $mode = $res['mode'];
                    $switch = 1;
                } else {
                    echo Functions::displayError(lng('error_wrong_data'), '<a href="index.php?act=counters">' . lng('back') . '</a>');
                    exit;
                }
            }
            echo '<form action="index.php?act=counters&amp;mod=edit" method="post">' .
                 '<div class="phdr"><a href="index.php?act=counters"><b>' . lng('counters') . '</b></a> | ' . lng('add') . '</div>' .
                 '<div class="menu"><p><h3>' . lng('title') . '</h3><input type="text" name="name" value="' . $name . '" /></p>' .
                 '<p><h3>' . lng('counter_mod1') . '</h3><textarea rows="3" name="link1">' . $link1 . '</textarea><br /><small>' . lng('counter_mod1_description') . '</small></p>' .
                 '<p><h3>' . lng('counter_mod2') . '</h3><textarea rows="3" name="link2">' . $link2 . '</textarea><br /><small>' . lng('counter_mod2_description') . '</small></p>' .
                 '<p><h3>' . lng('view_mode') . '</h3>' . '<input type="radio" value="1" ' . ($mode == 0 || $mode == 1 ? 'checked="checked" ' : '') . 'name="mode" />&#160;' . lng('default') . '<br />' .
                 '<small>' . lng('counter_mod_default_help') . '</small></p><p>' .
                 '<input type="radio" value="2" ' . ($mode == 2 ? 'checked="checked" ' : '') . 'name="mode" />&#160;' . lng('counter_mod1') . '<br />' .
                 '<input type="radio" value="3" ' . ($mode == 3 ? 'checked="checked" ' : '') . 'name="mode" />&#160;' . lng('counter_mod2') . '</p></div>' .
                 '<div class="rmenu"><small>' . lng('counter_add_help') . '</small></div>';
            if (Vars::$ID)
                echo '<input type="hidden" value="' . Vars::$ID . '" name="id" />';
            echo '<div class="bmenu"><input type="submit" value="' . lng('viewing') . '" name="submit" /></div>';
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
            echo Functions::displayError(lng('error_empty_fields'), '<a href="index.php?act=counters&amp;mod=edit' . (Vars::$ID ? '&amp;id=' . Vars::$ID : '') . '">' . lng('back') . '</a>');
            exit;
        }
        if (Vars::$ID) {
            // Режим редактирования
            $req = mysql_query("SELECT * FROM `cms_counters` WHERE `id` = " . Vars::$ID);
            if (mysql_num_rows($req) != 1) {
                echo Functions::displayError(lng('error_wrong_data'));
                exit;
            }
            mysql_query("UPDATE `cms_counters` SET
            `name` = '" . mysql_real_escape_string(Validate::filterString($name)) . "',
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
            `name` = '" . mysql_real_escape_string(Validate::filterString($name)) . "',
            `sort` = '$sort',
            `link1` = '" . mysql_real_escape_string($link1) . "',
            `link2` = '" . mysql_real_escape_string($link2) . "',
            `mode` = '$mode'");
        }
        echo '<div class="gmenu"><p>' . (Vars::$ID ? lng('counter_edit_conf') : lng('counter_add_conf')) . '</p></div>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Вывод списка счетчиков
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>' . lng('admin_panel') . '</b></a> | ' . lng('counters') . '</div>';
        $req = mysql_query("SELECT * FROM `cms_counters` ORDER BY `sort` ASC");
        if (mysql_num_rows($req)) {
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                echo Functions::getImage(($res['switch'] == 1 ? 'green' : 'red') . '.png', '', 'class="left"') . '&#160;';
                echo '<a href="index.php?act=counters&amp;mod=view&amp;id=' . $res['id'] . '"><b>' . $res['name'] . '</b></a><br />';
                echo '<div class="sub"><a href="index.php?act=counters&amp;mod=up&amp;id=' . $res['id'] . '">' . lng('up') . '</a> | ';
                echo '<a href="index.php?act=counters&amp;mod=down&amp;id=' . $res['id'] . '">' . lng('down') . '</a> | ';
                echo '<a href="index.php?act=counters&amp;mod=edit&amp;id=' . $res['id'] . '">' . lng('edit') . '</a> | ';
                echo '<a href="index.php?act=counters&amp;mod=del&amp;id=' . $res['id'] . '">' . lng('delete') . '</a></div></div>';
                ++$i;
            }
        }
        echo '<div class="phdr"><a href="index.php?act=counters&amp;mod=edit">' . lng('add') . '</a></div>';
}
echo '<p>' . (Vars::$MOD ? '<a href="index.php?act=counters">' . lng('counters') . '</a><br />' : '') . '<a href="index.php">' . lng('admin_panel') . '</a></p>';