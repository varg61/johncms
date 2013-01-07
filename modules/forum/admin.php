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

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 7) {
    header('Location: http://johncms.com/404');
    exit;
}

// Задаем пользовательские настройки форума
if (($set_forum = Vars::getUserData('set_forum')) === FALSE) {
    $set_forum = array(
        'farea'    => 0,
        'upfp'     => 0,
        'preview'  => 1,
        'postclip' => 1,
        'postcut'  => 2
    );
}
switch (Vars::$MOD) {
    case 'del':
        /*
        -----------------------------------------------------------------
        Удаление категории, или раздела
        -----------------------------------------------------------------
        */
        if (!Vars::$ID) {
            echo Functions::displayError(__('error_wrong_data'), '<a href="index.php?act=forum">' . __('forum_management') . '</a>');
            exit;
        }
        $req = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND (`type` = 'f' OR `type` = 'r')");
        if (mysql_num_rows($req)) {
            $res = mysql_fetch_assoc($req);
            echo '<div class="phdr"><b>' . ($res['type'] == 'r' ? __('delete_section') : __('delete_catrgory')) . ':</b> ' . $res['text'] . '</div>';
            // Проверяем, есть ли подчиненная информация
            $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = " . Vars::$ID . " AND (`type` = 'f' OR `type` = 'r' OR `type` = 't')"), 0);
            if ($total) {
                if ($res['type'] == 'f') {
                    ////////////////////////////////////////////////////////////
                    // Удаление категории с подчиненными данными              //
                    ////////////////////////////////////////////////////////////
                    if (isset($_POST['submit'])) {
                        $category = isset($_POST['category']) ? intval($_POST['category']) : 0;
                        if (!$category || $category == Vars::$ID) {
                            echo Functions::displayError(__('error_wrong_data'));
                            exit;
                        }
                        $check = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `id` = '$category' AND `type` = 'f'"), 0);
                        if (!$check) {
                            echo Functions::displayError(__('error_wrong_data'));
                            exit;
                        }
                        // Вычисляем правила сортировки и перемещаем разделы
                        $sort = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `refid` = '$category' AND `type` ='r' ORDER BY `realid` DESC"));
                        $sortnum = !empty($sort['realid']) && $sort['realid'] > 0 ? $sort['realid'] + 1 : 1;
                        $req_c = mysql_query("SELECT * FROM `forum` WHERE `refid` = " . Vars::$ID . " AND `type` = 'r'");
                        while ($res_c = mysql_fetch_assoc($req_c)) {
                            mysql_query("UPDATE `forum` SET `refid` = '" . $category . "', `realid` = '$sortnum' WHERE `id` = '" . $res_c['id'] . "'");
                            ++$sortnum;
                        }
                        // Перемещаем файлы в выбранную категорию
                        mysql_query("UPDATE `cms_forum_files` SET `cat` = '" . $category . "' WHERE `cat` = '" . $res['refid'] . "'");
                        mysql_query("DELETE FROM `forum` WHERE `id` = " . Vars::$ID);
                        echo '<div class="rmenu"><p><h3>' . __('category_deleted') . '</h3>' . __('contents_moved_to') . ' <a href="../forum/index.php?id=' . $category . '">' . __('selected_category') . '</a></p></div>';
                    } else {
                        echo '<form action="index.php?act=forum&amp;mod=del&amp;id=' . Vars::$ID . '" method="POST">' .
                            '<div class="rmenu"><p>' . __('contents_move_warning') . '</p>' .
                            '<p><h3>' . __('select_category') . '</h3><select name="category" size="1">';
                        $req_c = mysql_query("SELECT * FROM `forum` WHERE `type` = 'f' AND `id` != " . Vars::$ID . " ORDER BY `realid` ASC");
                        while ($res_c = mysql_fetch_assoc($req_c)) echo '<option value="' . $res_c['id'] . '">' . $res_c['text'] . '</option>';
                        echo '</select><br /><small>' . __('contents_move_description') . '</small></p>' .
                            '<p><input type="submit" name="submit" value="' . __('move') . '" /></p></div>';
                        if (Vars::$USER_RIGHTS == 9) {
                            // Для супервайзоров запрос на полное удаление
                            echo '<div class="rmenu"><p><h3>' . __('delete_full') . '</h3>' . __('delete_full_note') . ' <a href="index.php?act=forum&amp;mod=cat&amp;id=' . Vars::$ID . '">' . __('child_section') . '</a></p>' .
                                '</div>';
                        }
                        echo '</form>';
                    }
                } else {
                    ////////////////////////////////////////////////////////////
                    // Удаление раздела с подчиненными данными                //
                    ////////////////////////////////////////////////////////////
                    if (isset($_POST['submit'])) {
                        // Предварительные проверки
                        $subcat = isset($_POST['subcat']) ? intval($_POST['subcat']) : 0;
                        if (!$subcat || $subcat == Vars::$ID) {
                            echo Functions::displayError(__('error_wrong_data'), '<a href="index.php?act=forum">' . __('forum_management') . '</a>');
                            exit;
                        }
                        $check = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `id` = '$subcat' AND `type` = 'r'"), 0);
                        if (!$check) {
                            echo Functions::displayError(__('error_wrong_data'), '<a href="index.php?act=forum">' . __('forum_management') . '</a>');
                            exit;
                        }
                        mysql_query("UPDATE `forum` SET `refid` = '$subcat' WHERE `refid` = " . Vars::$ID);
                        mysql_query("UPDATE `cms_forum_files` SET `subcat` = '$subcat' WHERE `subcat` = " . Vars::$ID);
                        mysql_query("DELETE FROM `forum` WHERE `id` = " . Vars::$ID);
                        echo '<div class="rmenu"><p><h3>' . __('section_deleted') . '</h3>' . __('themes_moved_to') . ' <a href="../forum/index.php?id=' . $subcat . '">' . __('selected_section') . '</a>.' .
                            '</p></div>';
                    } elseif (isset($_POST['delete'])) {
                        if (Vars::$USER_RIGHTS != 9) {
                            echo Functions::displayError(__('access_forbidden'));
                            exit;
                        }
                        // Удаляем файлы
                        $req_f = mysql_query("SELECT * FROM `cms_forum_files` WHERE `subcat` = " . Vars::$ID);
                        while ($res_f = mysql_fetch_assoc($req_f)) {
                            unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $res_f['filename']);
                        }
                        mysql_query("DELETE FROM `cms_forum_files` WHERE `subcat` = " . Vars::$ID);
                        // Удаляем посты, голосования и метки прочтений
                        $req_t = mysql_query("SELECT `id` FROM `forum` WHERE `refid` = " . Vars::$ID . " AND `type` = 't'");
                        while ($res_t = mysql_fetch_assoc($req_t)) {
                            mysql_query("DELETE FROM `forum` WHERE `refid` = '" . $res_t['id'] . "'");
                            mysql_query("DELETE FROM `cms_forum_vote` WHERE `topic` = '" . $res_t['id'] . "'");
                            mysql_query("DELETE FROM `cms_forum_vote_users` WHERE `topic` = '" . $res_t['id'] . "'");
                            mysql_query("DELETE FROM `cms_forum_rdm` WHERE `topic_id` = '" . $res_t['id'] . "'");
                        }
                        // Удаляем темы
                        mysql_query("DELETE FROM `forum` WHERE `refid` = " . Vars::$ID);
                        // Удаляем раздел
                        mysql_query("DELETE FROM `forum` WHERE `id` = " . Vars::$ID);
                        // Оптимизируем таблицы
                        mysql_query("OPTIMIZE TABLE `cms_forum_files` , `cms_forum_rdm` , `forum` , `cms_forum_vote` , `cms_forum_vote_users`");
                        echo'<div class="rmenu"><p>' . __('section_themes_deleted') . '<br />' .
                            '<a href="index.php?act=forum&amp;mod=cat&amp;id=' . $res['refid'] . '">' . __('to_category') . '</a></p></div>';
                    } else {
                        echo '<form action="index.php?act=forum&amp;mod=del&amp;id=' . Vars::$ID . '" method="POST"><div class="rmenu">' .
                            '<p>' . __('section_move_warning') . '</p>' . '<p><h3>' . __('select_section') . '</h3>';
                        $cat = isset($_GET['cat']) ? abs(intval($_GET['cat'])) : 0;
                        $ref = $cat ? $cat : $res['refid'];
                        $req_r = mysql_query("SELECT * FROM `forum` WHERE `refid` = '$ref' AND `id` != " . Vars::$ID . " AND `type` = 'r' ORDER BY `realid` ASC");
                        while ($res_r = mysql_fetch_assoc($req_r)) {
                            echo '<input type="radio" name="subcat" value="' . $res_r['id'] . '" />&#160;' . $res_r['text'] . '<br />';
                        }
                        echo '</p><p><h3>' . __('another_category') . '</h3><ul>';
                        $req_c = mysql_query("SELECT * FROM `forum` WHERE `type` = 'f' AND `id` != '$ref' ORDER BY `realid` ASC");
                        while ($res_c = mysql_fetch_assoc($req_c)) {
                            echo '<li><a href="index.php?act=forum&amp;mod=del&amp;id=' . Vars::$ID . '&amp;cat=' . $res_c['id'] . '">' . $res_c['text'] . '</a></li>';
                        }
                        echo '</ul><small>' . __('section_move_description') . '</small></p>' .
                            '<p><input type="submit" name="submit" value="' . __('move') . '" /></p></div>';
                        if (Vars::$USER_RIGHTS == 9) {
                            // Для супервайзоров запрос на полное удаление
                            echo '<div class="rmenu"><p><h3>' . __('delete_full') . '</h3>' . __('delete_full_warning');
                            echo '</p><p><input type="submit" name="delete" value="' . __('delete') . '" /></p></div>';
                        }
                        echo '</form>';
                    }
                }
            } else {
                ////////////////////////////////////////////////////////////
                // Удаление пустого раздела, или категории                //
                ////////////////////////////////////////////////////////////
                if (isset($_POST['submit'])) {
                    mysql_query("DELETE FROM `forum` WHERE `id` = " . Vars::$ID);
                    echo '<div class="rmenu"><p>' . ($res['type'] == 'r' ? __('section_deleted') : __('category_deleted')) . '</p></div>';
                } else {
                    echo '<div class="rmenu"><p>' . __('delete_confirmation') . '</p>' .
                        '<p><form action="index.php?act=forum&amp;mod=del&amp;id=' . Vars::$ID . '" method="POST">' .
                        '<input type="submit" name="submit" value="' . __('delete') . '" />' .
                        '</form></p></div>';
                }
            }
            echo '<div class="phdr"><a href="index.php?act=forum&amp;mod=cat">' . __('back') . '</a></div>';
        } else {
            header('Location: index.php?act=forum&mod=cat');
        }
        break;

    case 'add':
        /*
        -----------------------------------------------------------------
        Добавление категории
        -----------------------------------------------------------------
        */
        if (Vars::$ID) {
            // Проверяем наличие категории
            $req = mysql_query("SELECT `text` FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 'f'");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_array($req);
                $cat_name = $res['text'];
            } else {
                echo Functions::displayError(__('error_wrong_data'), '<a href="index.php?act=forum">' . __('forum_management') . '</a>');
                exit;
            }
        }
        if (isset($_POST['submit'])) {
            // Принимаем данные
            $name = isset($_POST['name']) ? Validate::checkout($_POST['name']) : '';
            $desc = isset($_POST['desc']) ? Validate::checkout($_POST['desc']) : '';
            // Проверяем на ошибки
            $error = array();
            if (!$name)
                $error[] = __('error_empty_title');
            if ($name && (mb_strlen($name) < 2 || mb_strlen($name) > 30))
                $error[] = __('title') . ': ' . __('error_wrong_lenght');
            if ($desc && mb_strlen($desc) < 2)
                $error[] = __('error_description_lenght');
            if (!$error) {
                // Добавляем в базу категорию
                $req = mysql_query("SELECT `realid` FROM `forum` WHERE " . (Vars::$ID ? "`refid` = " . Vars::$ID . " AND `type` = 'r'" : "`type` = 'f'") . " ORDER BY `realid` DESC LIMIT 1");
                if (mysql_num_rows($req)) {
                    $res = mysql_fetch_assoc($req);
                    $sort = $res['realid'] + 1;
                } else {
                    $sort = 1;
                }
                mysql_query("INSERT INTO `forum` SET
                    `refid` = '" . (Vars::$ID ? Vars::$ID : 0) . "',
                    `type` = '" . (Vars::$ID ? 'r' : 'f') . "',
                    `text` = '" . mysql_real_escape_string($name) . "',
                    `soft` = '" . mysql_real_escape_string($desc) . "',
                    `realid` = '$sort',
                    `edit` = '',
                    `curators` = ''
                ") or die(mysql_error());
                header('Location: ' . Vars::$URI . '?mod=cat' . (Vars::$ID ? '&id=' . Vars::$ID : ''));
            } else {
                // Выводим сообщение об ошибках
                echo Functions::displayError($error);
            }
        } else {
            // Форма ввода
            echo '<div class="phdr"><b>' . (Vars::$ID ? __('add_section') : __('add_category')) . '</b></div>';
            if (Vars::$ID)
                echo '<div class="bmenu"><b>' . __('to_category') . ':</b> ' . $cat_name . '</div>';
            echo '<form action="' . Vars::$URI . '?mod=add' . (Vars::$ID ? '&amp;id=' . Vars::$ID : '') . '" method="post">' .
                '<div class="gmenu">' .
                '<p><h3>' . __('title') . '</h3>' .
                '<input type="text" name="name" />' .
                '<br /><small>' . __('minmax_2_30') . '</small></p>' .
                '<p><h3>' . __('description') . '</h3>' .
                '<textarea name="desc" rows="' . Vars::$USER_SET['field_h'] . '"></textarea>' .
                '<br /><small>' . __('not_mandatory_field') . '<br />' . __('minmax_2_500') . '</small></p>' .
                '<p><input type="submit" value="' . __('add') . '" name="submit" />' .
                '</p></div></form>' .
                '<div class="phdr"><a href="' . Vars::$URI . '?mod=cat' . (Vars::$ID ? '&amp;id=' . Vars::$ID : '') . '">' . __('back') . '</a></div>';
        }
        break;

    case 'edit':
        /*
        -----------------------------------------------------------------
        Редактирование выбранной категории, или раздела
        -----------------------------------------------------------------
        */
        if (!Vars::$ID) {
            echo Functions::displayError(__('error_wrong_data'), '<a href="index.php?act=forum">' . __('forum_management') . '</a>');
            exit;
        }
        $req = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID);
        if (mysql_num_rows($req)) {
            $res = mysql_fetch_assoc($req);
            if ($res['type'] == 'f' || $res['type'] == 'r') {
                if (isset($_POST['submit'])) {
                    // Принимаем данные
                    $name = isset($_POST['name']) ? Validate::checkout($_POST['name']) : '';
                    $desc = isset($_POST['desc']) ? Validate::checkout($_POST['desc']) : '';
                    $category = isset($_POST['category']) ? intval($_POST['category']) : 0;
                    // проверяем на ошибки
                    $error = array();
                    if ($res['type'] == 'r' && !$category)
                        $error[] = __('error_category_select');
                    elseif ($res['type'] == 'r' && !mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `id` = '$category' AND `type` = 'f'"), 0))
                        $error[] = __('error_category_select');
                    if (!$name)
                        $error[] = __('error_empty_title');
                    if ($name && (mb_strlen($name) < 2 || mb_strlen($name) > 30))
                        $error[] = __('title') . ': ' . __('error_wrong_lenght');
                    if ($desc && mb_strlen($desc) < 2)
                        $error[] = __('error_description_lenght');
                    if (!$error) {
                        // Записываем в базу
                        mysql_query("UPDATE `forum` SET
                            `text` = '" . mysql_real_escape_string($name) . "',
                            `soft` = '" . mysql_real_escape_string($desc) . "'
                            WHERE `id` = " . Vars::$ID);
                        if ($res['type'] == 'r' && $category != $res['refid']) {
                            // Вычисляем сортировку
                            $req_s = mysql_query("SELECT `realid` FROM `forum` WHERE `refid` = '$category' AND `type` = 'r' ORDER BY `realid` DESC LIMIT 1");
                            $res_s = mysql_fetch_assoc($req_s);
                            $sort = $res_s['realid'] + 1;
                            // Меняем категорию
                            mysql_query("UPDATE `forum` SET `refid` = '$category', `realid` = '$sort' WHERE `id` = " . Vars::$ID);
                            // Меняем категорию для прикрепленных файлов
                            mysql_query("UPDATE `cms_forum_files` SET `cat` = '$category' WHERE `cat` = '" . $res['refid'] . "'");
                        }
                        header('Location: ' . Vars::$URI . '?mod=cat' . ($res['type'] == 'r' ? '&id=' . $res['refid'] : ''));
                    } else {
                        // Выводим сообщение об ошибках
                        echo Functions::displayError($error);
                    }
                } else {
                    // Форма ввода
                    echo'<div class="phdr"><b>' . ($res['type'] == 'r' ? __('section_edit') : __('category_edit')) . '</b></div>' .
                        '<form action="' . Vars::$URI . '?mod=edit&amp;id=' . Vars::$ID . '" method="post">' .
                        '<div class="gmenu">' .
                        '<p><h3>' . __('title') . '</h3>' .
                        '<input type="text" name="name" value="' . $res['text'] . '"/>' .
                        '<br /><small>' . __('minmax_2_30') . '</small></p>' .
                        '<p><h3>' . __('description') . '</h3>' .
                        '<textarea name="desc" rows="' . Vars::$USER_SET['field_h'] . '">' . str_replace('<br />', "\r\n", $res['soft']) . '</textarea>' .
                        '<br /><small>' . __('not_mandatory_field') . '<br />' . __('minmax_2_500') . '</small></p>';
                    if ($res['type'] == 'r') {
                        echo '<p><h3>' . __('category') . '</h3><select name="category" size="1">';
                        $req_c = mysql_query("SELECT * FROM `forum` WHERE `type` = 'f' ORDER BY `realid` ASC");
                        while ($res_c = mysql_fetch_assoc($req_c)) {
                            echo '<option value="' . $res_c['id'] . '"' . ($res_c['id'] == $res['refid'] ? ' selected="selected"' : '') . '>' . $res_c['text'] . '</option>';
                        }
                        echo '</select></p>';
                    }
                    echo'<p><input type="submit" value="' . __('save') . '" name="submit" />' .
                        '</p></div></form>' .
                        '<div class="phdr"><a href="' . Vars::$URI . '?mod=cat' . ($res['type'] == 'r' ? '&amp;id=' . $res['refid'] : '') . '">' . __('back') . '</a></div>';
                }
            } else {
                header('Location: ' . Vars::$URI . '?mod=cat');
            }
        } else {
            header('Location: ' . Vars::$URI . '?mod=cat');
        }
        break;

    case 'up':
        /*
        -----------------------------------------------------------------
        Перемещение на одну позицию вверх
        -----------------------------------------------------------------
        */
        if (Vars::$ID) {
            $req = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID);
            if (mysql_num_rows($req)) {
                $res1 = mysql_fetch_assoc($req);
                $sort = $res1['realid'];
                $req = mysql_query("SELECT * FROM `forum` WHERE `type` = '" . ($res1['type'] == 'f' ? 'f' : 'r') . "' AND `realid` < '$sort' ORDER BY `realid` DESC LIMIT 1");
                if (mysql_num_rows($req)) {
                    $res = mysql_fetch_assoc($req);
                    $id2 = $res['id'];
                    $sort2 = $res['realid'];
                    mysql_query("UPDATE `forum` SET `realid` = '$sort2' WHERE `id` = " . Vars::$ID);
                    mysql_query("UPDATE `forum` SET `realid` = '$sort' WHERE `id` = '$id2'");
                }
            }
        }
        header('Location: ' . Vars::$URI . '?mod=cat' . ($res1['type'] == 'r' ? '&id=' . $res1['refid'] : ''));
        break;

    case 'down':
        /*
        -----------------------------------------------------------------
        Перемещение на одну позицию вниз
        -----------------------------------------------------------------
        */
        if (Vars::$ID) {
            $req = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID);
            if (mysql_num_rows($req)) {
                $res1 = mysql_fetch_assoc($req);
                $sort = $res1['realid'];
                $req = mysql_query("SELECT * FROM `forum` WHERE `type` = '" . ($res1['type'] == 'f' ? 'f' : 'r') . "' AND `realid` > '$sort' ORDER BY `realid` ASC LIMIT 1");
                if (mysql_num_rows($req)) {
                    $res = mysql_fetch_assoc($req);
                    $id2 = $res['id'];
                    $sort2 = $res['realid'];
                    mysql_query("UPDATE `forum` SET `realid` = '$sort2' WHERE `id` = " . Vars::$ID);
                    mysql_query("UPDATE `forum` SET `realid` = '$sort' WHERE `id` = '$id2'");
                }
            }
        }
        header('Location: ' . Vars::$URI . '?mod=cat' . ($res1['type'] == 'r' ? '&id=' . $res1['refid'] : ''));
        break;

    case 'cat':
        /*
        -----------------------------------------------------------------
        Управление категориями и разделами
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . __('forum_management') . '</b></a> | ' . __('forum_structure') . '</div>';
        if (Vars::$ID) {
            // Управление разделами
            $req = mysql_query("SELECT `text` FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 'f'");
            $res = mysql_fetch_assoc($req);
            echo '<div class="bmenu"><a href="' . Vars::$URI . '?mod=cat"><b>' . $res['text'] . '</b></a> | ' . __('section_list') . '</div>';
            $req = mysql_query("SELECT * FROM `forum` WHERE `refid` = " . Vars::$ID . " AND `type` = 'r' ORDER BY `realid` ASC");
            if (mysql_num_rows($req)) {
                for ($i = 0; $res = mysql_fetch_assoc($req); ++$i) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo '<b>' . $res['text'] . '</b>' .
                        '&#160;<a href="' . Vars::$HOME_URL . '/forum?id=' . $res['id'] . '">&gt;&gt;</a>';
                    if (!empty($res['soft']))
                        echo '<br /><span class="gray"><small>' . $res['soft'] . '</small></span><br />';
                    echo'<div class="sub">' .
                        '<a href="' . Vars::$URI . '?mod=up&amp;id=' . $res['id'] . '">' . __('up') . '</a> | ' .
                        '<a href="' . Vars::$URI . '?mod=down&amp;id=' . $res['id'] . '">' . __('down') . '</a> | ' .
                        '<a href="' . Vars::$URI . '?mod=edit&amp;id=' . $res['id'] . '">' . __('edit') . '</a> | ' .
                        '<a href="' . Vars::$URI . '?mod=del&amp;id=' . $res['id'] . '">' . __('delete') . '</a>' .
                        '</div></div>';
                }
            } else {
                echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
            }
        } else {
            // Управление категориями
            echo '<div class="bmenu">' . __('category_list') . '</div>';
            $req = mysql_query("SELECT * FROM `forum` WHERE `type` = 'f' ORDER BY `realid` ASC");
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                echo '<a href="' . Vars::$URI . '?mod=cat&amp;id=' . $res['id'] . '"><b>' . $res['text'] . '</b></a> ' .
                    '(' . mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'r' AND `refid` = '" . $res['id'] . "'"), 0) . ')' .
                    '&#160;<a href="' . Vars::$HOME_URL . '/forum?id=' . $res['id'] . '">&gt;&gt;</a>';
                if (!empty($res['soft']))
                    echo '<br /><span class="gray"><small>' . $res['soft'] . '</small></span><br />';
                echo '<div class="sub">' .
                    '<a href="' . Vars::$URI . '?mod=up&amp;id=' . $res['id'] . '">' . __('up') . '</a> | ' .
                    '<a href="' . Vars::$URI . '?mod=down&amp;id=' . $res['id'] . '">' . __('down') . '</a> | ' .
                    '<a href="' . Vars::$URI . '?mod=edit&amp;id=' . $res['id'] . '">' . __('edit') . '</a> | ' .
                    '<a href="' . Vars::$URI . '?mod=del&amp;id=' . $res['id'] . '">' . __('delete') . '</a>' .
                    '</div></div>';
                ++$i;
            }
        }
        echo'<div class="gmenu">' .
            '<form action="' . Vars::$URI . '?mod=add' . (Vars::$ID ? '&amp;id=' . Vars::$ID : '') . '" method="post">' .
            '<input type="submit" value="' . __('add') . '" />' .
            '</form></div>' .
            '<div class="phdr">' . (Vars::$MOD == 'cat' && Vars::$ID ? '<a href="' . Vars::$URI . '?mod=cat">' . __('category_list') . '</a>' : '<a href="' . Vars::$URI . '">' . __('forum_management') . '</a>') . '</div>';
        break;

    case 'htopics':
        /*
        -----------------------------------------------------------------
        Управление скрытыми темами форума
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php?act=forum"><b>' . __('forum_management') . '</b></a> | ' . __('hidden_topics') . '</div>';
        $sort = '';
        $link = '';
        if (isset($_GET['usort'])) {
            $sort = " AND `forum`.`user_id` = '" . abs(intval($_GET['usort'])) . "'";
            $link = '&amp;usort=' . abs(intval($_GET['usort']));
            echo '<div class="bmenu">' . __('filter_on_author') . ' <a href="index.php?act=forum&amp;mod=htopics">[x]</a></div>';
        }
        if (isset($_GET['rsort'])) {
            $sort = " AND `forum`.`refid` = '" . abs(intval($_GET['rsort'])) . "'";
            $link = '&amp;rsort=' . abs(intval($_GET['rsort']));
            echo '<div class="bmenu">' . __('filter_on_section') . ' <a href="index.php?act=forum&amp;mod=htopics">[x]</a></div>';
        }
        if (isset($_POST['deltopic'])) {
            if (Vars::$USER_RIGHTS != 9) {
                echo Functions::displayError(__('access_forbidden'));
                exit;
            }
            $req = mysql_query("SELECT `id` FROM `forum` WHERE `type` = 't' AND `close` = '1' " . $sort);
            while ($res = mysql_fetch_assoc($req)) {
                $req_f = mysql_query("SELECT * FROM `cms_forum_files` WHERE `topic` = '" . $res['id'] . "'");
                if (mysql_num_rows($req_f)) {
                    // Удаляем файлы
                    while ($res_f = mysql_fetch_assoc($req_f)) {
                        unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $res_f['filename']);
                    }
                    mysql_query("DELETE FROM `cms_forum_files` WHERE `topic` = '" . $res['id'] . "'");
                }
                // Удаляем посты
                mysql_query("DELETE FROM `forum` WHERE `type` = 'm' AND `refid` = '" . $res['id'] . "'");
            }
            // Удаляем темы
            $req = mysql_query("DELETE FROM `forum` WHERE `type` = 't' AND `close` = '1' " . $sort);
            header('Location: index.php?act=forum&mod=htopics');
        } else {
            $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `close` = '1' " . $sort), 0);
            if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=forum&amp;mod=htopics&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            $req = mysql_query("SELECT `forum`.*, `forum`.`id` AS `fid`, `forum`.`user_id` AS `id`, `forum`.`from` AS `name`, `forum`.`soft` AS `browser`, `users`.`rights`, `users`.`last_visit`, `users`.`sex`, `users`.`status`, `users`.`join_date`
            FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
            WHERE `forum`.`type` = 't' AND `forum`.`close` = '1' $sort ORDER BY `forum`.`id` DESC " . Vars::db_pagination());
            if (mysql_num_rows($req)) {
                $i = 0;
                while ($res = mysql_fetch_assoc($req)) {
                    $subcat = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'"));
                    $cat = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $subcat['refid'] . "'"));
                    $ttime = '<span class="gray">(' . Functions::displayDate($res['time']) . ')</span>';
                    $text = '<a href="../forum/index.php?id=' . $res['fid'] . '"><b>' . $res['text'] . '</b></a>';
                    $text .= '<br /><small><a href="../forum/index.php?id=' . $cat['id'] . '">' . $cat['text'] . '</a> / <a href="../forum/index.php?id=' . $subcat['id'] . '">' . $subcat['text'] . '</a></small>';
                    $subtext = '<span class="gray">' . __('filter_to') . ':</span> ';
                    $subtext .= '<a href="index.php?act=forum&amp;mod=htopics&amp;rsort=' . $res['refid'] . '">' . __('by_section') . '</a> | ';
                    $subtext .= '<a href="index.php?act=forum&amp;mod=htopics&amp;usort=' . $res['user_id'] . '">' . __('by_author') . '</a>';
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo Functions::displayUser($res, array(
                        'header' => $ttime,
                        'body'   => $text,
                        'sub'    => $subtext
                    ));
                    echo '</div>';
                    ++$i;
                }
                if (Vars::$USER_RIGHTS == 9)
                    echo '<form action="index.php?act=forum&amp;mod=htopics' . $link . '" method="POST">' .
                        '<div class="rmenu">' .
                        '<input type="submit" name="deltopic" value="' . __('delete_all') . '" />' .
                        '</div></form>';
            } else {
                echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
            }
            echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
            if ($total > Vars::$USER_SET['page_size']) {
                echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=forum&amp;mod=htopics&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                    '<p><form action="index.php?act=forum&amp;mod=htopics" method="post">' .
                    '<input type="text" name="page" size="2"/>' .
                    '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
                    '</form></p>';
            }
        }
        break;

    case 'hposts':
        /*
        -----------------------------------------------------------------
        Управление скрытыми постави форума
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php?act=forum"><b>' . __('forum_management') . '</b></a> | ' . __('hidden_posts') . '</div>';
        $sort = '';
        $link = '';
        if (isset($_GET['tsort'])) {
            $sort = " AND `forum`.`refid` = '" . abs(intval($_GET['tsort'])) . "'";
            $link = '&amp;tsort=' . abs(intval($_GET['tsort']));
            echo '<div class="bmenu">' . __('filter_on_theme') . ' <a href="index.php?act=forum&amp;mod=hposts">[x]</a></div>';
        } elseif (isset($_GET['usort'])) {
            $sort = " AND `forum`.`user_id` = '" . abs(intval($_GET['usort'])) . "'";
            $link = '&amp;usort=' . abs(intval($_GET['usort']));
            echo '<div class="bmenu">' . __('filter_on_author') . ' <a href="index.php?act=forum&amp;mod=hposts">[x]</a></div>';
        }
        if (isset($_POST['delpost'])) {
            if (Vars::$USER_RIGHTS != 9) {
                echo Functions::displayError(__('access_forbidden'));
                exit;
            }
            $req = mysql_query("SELECT `id` FROM `forum` WHERE `type` = 'm' AND `close` = '1' " . $sort);
            while ($res = mysql_fetch_assoc($req)) {
                $req_f = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = '" . $res['id'] . "' LIMIT 1");
                if (mysql_num_rows($req_f)) {
                    $res_f = mysql_fetch_assoc($req_f);
                    // Удаляем файлы
                    unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $res_f['filename']);
                    mysql_query("DELETE FROM `cms_forum_files` WHERE `post` = '" . $res['id'] . "' LIMIT 1");
                }
            }
            // Удаляем посты
            mysql_query("DELETE FROM `forum` WHERE `type` = 'm' AND `close` = '1' " . $sort);
            header('Location: index.php?act=forum&mod=hposts');
        } else {
            $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `close` = '1' " . $sort), 0);
            if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=forum&amp;mod=hposts&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            $req = mysql_query("SELECT `forum`.*, `forum`.`id` AS `fid`, `forum`.`user_id` AS `id`, `forum`.`from` AS `name`, `forum`.`soft` AS `browser`, `users`.`rights`, `users`.`last_visit`, `users`.`sex`, `users`.`status`, `users`.`join_date`
            FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
            WHERE `forum`.`type` = 'm' AND `forum`.`close` = '1' $sort ORDER BY `forum`.`id` DESC " . Vars::db_pagination());
            if (mysql_num_rows($req)) {
                $i = 0;
                while ($res = mysql_fetch_assoc($req)) {
                    $res['ip'] = ip2long($res['ip']);
                    $posttime = ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span>';
                    $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '" . $res['fid'] . "'"), 0) / Vars::$USER_SET['page_size']);
                    $text = mb_substr($res['text'], 0, 500);
                    $text = Validate::checkout($text, 1, 0);
                    $text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
                    $theme = mysql_fetch_assoc(mysql_query("SELECT `id`, `text` FROM `forum` WHERE `id` = '" . $res['refid'] . "'"));
                    $text = '<b>' . $theme['text'] . '</b> <a href="../forum/index.php?id=' . $theme['id'] . '&amp;page=' . $page . '">&gt;&gt;</a><br />' . $text;
                    $subtext = '<span class="gray">' . __('filter_to') . ':</span> ';
                    $subtext .= '<a href="index.php?act=forum&amp;mod=hposts&amp;tsort=' . $theme['id'] . '">' . __('by_theme') . '</a> | ';
                    $subtext .= '<a href="index.php?act=forum&amp;mod=hposts&amp;usort=' . $res['user_id'] . '">' . __('by_author') . '</a>';
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo Functions::displayUser($res, array(
                        'header' => $posttime,
                        'body'   => $text,
                        'sub'    => $subtext
                    ));
                    echo '</div>';
                    ++$i;
                }
                if (Vars::$USER_RIGHTS == 9)
                    echo '<form action="index.php?act=forum&amp;mod=hposts' . $link . '" method="POST"><div class="rmenu"><input type="submit" name="delpost" value="' . __('delete_all') . '" /></div></form>';
            } else {
                echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
            }
            echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
            if ($total > Vars::$USER_SET['page_size']) {
                echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=forum&amp;mod=hposts&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                    '<p><form action="index.php?act=forum&amp;mod=hposts" method="post">' .
                    '<input type="text" name="page" size="2"/>' .
                    '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
                    '</form></p>';
            }
        }
        break;

    case 'moders':
        /*
        -----------------------------------------------------------------
        Управление модераторами разделов
        -----------------------------------------------------------------
        */
        if (isset($_POST['submit'])) {
            if (!Vars::$ID) {
                echo Functions::displayError(__('error_wrong_data'), '<a href="index.php?act=forum">' . __('forum_management') . '</a>');
                exit;
            }
            if (isset($_POST['moder'])) {
                $q = mysql_query("SELECT * FROM `forum` WHERE `type` = 'a' AND `refid` = " . Vars::$ID);
                while ($q1 = mysql_fetch_array($q)) {
                    if (!in_array($q1['from'], $_POST['moder'])) {
                        mysql_query("delete from `forum` where `id`='" . $q1['id'] . "'");
                    }
                }
                foreach ($_POST['moder'] as $v) {
                    $v = Validate::checkout($v);
                    $q2 = mysql_query("SELECT * FROM `forum` WHERE `type` = 'a' AND `from` = '" . mysql_real_escape_string($v) . "' AND `refid` = " . Vars::$ID);
                    $q3 = mysql_num_rows($q2);
                    if ($q3 == 0) {
                        mysql_query("INSERT INTO `forum` SET
                        `refid` = " . Vars::$ID . ",
                        `type` = 'a',
                        `from` = '" . mysql_real_escape_string($v) . "'");
                    }
                }
            } else {
                mysql_query("DELETE * FROM `forum` WHERE `type` = 'a' AND `refid` = " . Vars::$ID);
            }
            header("Location: index.php?act=forum&mod=moders&id=" . Vars::$ID);
        } else {
            echo '<div class="phdr"><a href="index.php?act=forum"><b>' . __('forum_management') . '</b></a> | ' . __('moderators_appoint') . '</div>';
            if (!empty($_GET['id'])) {
                $typ = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID);
                $ms = mysql_fetch_array($typ);
                if ($ms['type'] != "f") {
                    echo Functions::displayError(__('error_wrong_data'), '<a href="index.php?act=forum">' . __('forum_management') . '</a>');
                    exit;
                }
                echo '<div class="bmenu"><b>' . __('category') . ':</b> ' . $ms['text'] . '</div>';
                echo '<form action="index.php?act=forum&amp;mod=moders&amp;id=' . Vars::$ID . '" method="post">';
                $q = mysql_query("SELECT * FROM `users` WHERE `rights` = '3'");
                $i = 0;
                while ($q1 = mysql_fetch_assoc($q)) {
                    $q2 = mysql_query("SELECT * FROM `forum` WHERE `type` = 'a' AND `from` = '" . $q1['name'] . "' and `refid` = " . Vars::$ID);
                    $q3 = mysql_num_rows($q2);
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo '<input type="checkbox" name="moder[]" value="' . $q1['name'] . '"' . ($q3 ? ' checked="checked"' : '') . '/>' . $q1['name'] . '</div>';
                    ++$i;
                }
                echo '<div class="gmenu">' .
                    '<input type="submit" name="submit" value="' . __('save') . '"/>' .
                    '</div></form><div class="phdr">' .
                    '<a href="index.php?act=forum&amp;mod=moders">' . __('select_category') . '</a>' .
                    '</div>';
            } else {
                echo '<div class="bmenu">' . __('select_category') . '</div>';
                $q = mysql_query("select * from `forum` where type='f' order by realid;");
                $i = 0;
                while ($q1 = mysql_fetch_array($q)) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo '<a href="index.php?act=forum&amp;mod=moders&amp;id=' . $q1['id'] . '">' . $q1['text'] . '</a></div>';
                    ++$i;
                }
                echo '<div class="phdr"><a href="index.php?act=forum">' . __('forum_management') . '</a></div>';
            }
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Панель управления форумом
        -----------------------------------------------------------------
        */
        $total_cat = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'f'"), 0);
        $total_sub = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'r'"), 0);
        $total_thm = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't'"), 0);
        $total_thm_del = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `close` = '1'"), 0);
        $total_msg = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm'"), 0);
        $total_msg_del = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `close` = '1'"), 0);
        $total_files = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files`"), 0);
        $total_votes = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type` = '1'"), 0);
        echo'<div class="phdr"><a href="' . Vars::$HOME_URL . '/admin"><b>' . __('admin_panel') . '</b></a> | ' . __('forum_management') . '</div>' .
            '<div class="gmenu"><p><h3>' . __('statistics') . '</h3><ul>' .
            '<li>' . __('categories') . ':&#160;' . $total_cat . '</li>' .
            '<li>' . __('sections') . ':&#160;' . $total_sub . '</li>' .
            '<li>' . __('themes') . ':&#160;' . $total_thm . '&#160;/&#160;<span class="red">' . $total_thm_del . '</span></li>' .
            '<li>' . __('posts_adm') . ':&#160;' . $total_msg . '&#160;/&#160;<span class="red">' . $total_msg_del . '</span></li>' .
            '<li>' . __('files') . ':&#160;' . $total_files . '</li>' .
            '<li>' . __('votes') . ':&#160;' . $total_votes . '</li>' .
            '</ul></p></div>' .
            '<div class="menu"><p><h3>' . __('settings') . '</h3><ul>' .
            '<li><a href="' . Vars::$URI . '?mod=cat"><b>' . __('forum_structure') . '</b></a></li>' .
            '<li><a href="' . Vars::$URI . '?mod=hposts">' . __('hidden_posts') . '</a> (' . $total_msg_del . ')</li>' .
            '<li><a href="' . Vars::$URI . '?mod=htopics">' . __('hidden_topics') . '</a> (' . $total_thm_del . ')</li>' .
            '<li><a href="' . Vars::$URI . '?mod=moders">' . __('moders') . '</a></li>' .
            '</ul></p></div>' .
            '<div class="phdr"><a href="' . Router::getUrl(2) . '">' . __('to_forum') . '</a></div>';
}
echo '<p><a href="' . Vars::$HOME_URL . '/admin">' . __('admin_panel') . '</a></p>';