<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_FORUM') or die('Error: restricted access');
$uri = Router::getUri(3);

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

$mod = isset(Router::$ROUTE[2]) ? Router::$ROUTE[2] : FALSE;
$tpl = Template::getInstance();

switch ($mod) {
    case 'del':
        // Удаление категории, или раздела
        if (!Vars::$ID) {
            echo Functions::displayError(__('error_wrong_data'), '<a href="index.php?act=forum">' . __('forum_management') . '</a>');
            exit;
        }
        $req = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND (`type` = 'f' OR `type` = 'r')");
        if ($req->rowCount()) {
            $res = $req->fetch();
            echo '<div class="phdr"><b>' . ($res['type'] == 'r' ? __('delete_section') : __('delete_catrgory')) . ':</b> ' . $res['text'] . '</div>';
            // Проверяем, есть ли подчиненная информация
            $total = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `refid` = " . Vars::$ID . " AND (`type` = 'f' OR `type` = 'r' OR `type` = 't')")->fetchColumn();
            if ($total) {
                if ($res['type'] == 'f') {
                    // Удаление категории с подчиненными данными
                    if (isset($_POST['submit'])) {
                        $category = isset($_POST['category']) ? intval($_POST['category']) : 0;
                        if (!$category || $category == Vars::$ID) {
                            echo Functions::displayError(__('error_wrong_data'));
                            exit;
                        }
                        $check = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `id` = '$category' AND `type` = 'f'")->fetchColumn();
                        if (!$check) {
                            echo Functions::displayError(__('error_wrong_data'));
                            exit;
                        }
                        // Вычисляем правила сортировки и перемещаем разделы
                        $sort = DB::PDO()->query("SELECT * FROM `forum` WHERE `refid` = '$category' AND `type` ='r' ORDER BY `realid` DESC")->fetch();
                        $sortnum = !empty($sort['realid']) && $sort['realid'] > 0 ? $sort['realid'] + 1 : 1;
                        $req_c = DB::PDO()->query("SELECT * FROM `forum` WHERE `refid` = " . Vars::$ID . " AND `type` = 'r'");
                        while ($res_c = $req_c->fetch()) {
                            DB::PDO()->exec("UPDATE `forum` SET `refid` = '" . $category . "', `realid` = '$sortnum' WHERE `id` = '" . $res_c['id'] . "'");
                            ++$sortnum;
                        }
                        // Перемещаем файлы в выбранную категорию
                        DB::PDO()->exec("UPDATE `cms_forum_files` SET `cat` = '" . $category . "' WHERE `cat` = '" . $res['refid'] . "'");
                        DB::PDO()->exec("DELETE FROM `forum` WHERE `id` = " . Vars::$ID);
                        echo '<div class="rmenu"><p><h3>' . __('category_deleted') . '</h3>' . __('contents_moved_to') . ' <a href="../forum/index.php?id=' . $category . '">' . __('selected_category') . '</a></p></div>';
                    } else {
                        echo '<form action="index.php?act=forum&amp;mod=del&amp;id=' . Vars::$ID . '" method="POST">' .
                            '<div class="rmenu"><p>' . __('contents_move_warning') . '</p>' .
                            '<p><h3>' . __('select_category') . '</h3><select name="category" size="1">';
                        $req_c = DB::PDO()->query("SELECT * FROM `forum` WHERE `type` = 'f' AND `id` != " . Vars::$ID . " ORDER BY `realid` ASC");
                        while ($res_c = $req_c->fetch()) {
                            echo '<option value="' . $res_c['id'] . '">' . $res_c['text'] . '</option>';
                        }
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
                    // Удаление раздела с подчиненными данными
                    if (isset($_POST['submit'])) {
                        // Предварительные проверки
                        $subcat = isset($_POST['subcat']) ? intval($_POST['subcat']) : 0;
                        if (!$subcat || $subcat == Vars::$ID) {
                            echo Functions::displayError(__('error_wrong_data'), '<a href="index.php?act=forum">' . __('forum_management') . '</a>');
                            exit;
                        }
                        $check = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `id` = '$subcat' AND `type` = 'r'")->fetchColumn();
                        if (!$check) {
                            echo Functions::displayError(__('error_wrong_data'), '<a href="index.php?act=forum">' . __('forum_management') . '</a>');
                            exit;
                        }
                        DB::PDO()->exec("UPDATE `forum` SET `refid` = '$subcat' WHERE `refid` = " . Vars::$ID);
                        DB::PDO()->exec("UPDATE `cms_forum_files` SET `subcat` = '$subcat' WHERE `subcat` = " . Vars::$ID);
                        DB::PDO()->exec("DELETE FROM `forum` WHERE `id` = " . Vars::$ID);
                        echo '<div class="rmenu"><p><h3>' . __('section_deleted') . '</h3>' . __('themes_moved_to') . ' <a href="../forum/index.php?id=' . $subcat . '">' . __('selected_section') . '</a>.' .
                            '</p></div>';
                    } elseif (isset($_POST['delete'])) {
                        if (Vars::$USER_RIGHTS != 9) {
                            echo Functions::displayError(__('access_forbidden'));
                            exit;
                        }
                        // Удаляем файлы
                        $req_f = DB::PDO()->query("SELECT * FROM `cms_forum_files` WHERE `subcat` = " . Vars::$ID);
                        while ($res_f = $req_f->fetch()) {
                            unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $res_f['filename']);
                        }
                        DB::PDO()->exec("DELETE FROM `cms_forum_files` WHERE `subcat` = " . Vars::$ID);
                        // Удаляем посты, голосования и метки прочтений
                        $req_t = DB::PDO()->query("SELECT `id` FROM `forum` WHERE `refid` = " . Vars::$ID . " AND `type` = 't'");
                        while ($res_t = $req_t->fetch()) {
                            DB::PDO()->exec("DELETE FROM `forum` WHERE `refid` = '" . $res_t['id'] . "'");
                            DB::PDO()->exec("DELETE FROM `cms_forum_vote` WHERE `topic` = '" . $res_t['id'] . "'");
                            DB::PDO()->exec("DELETE FROM `cms_forum_vote_users` WHERE `topic` = '" . $res_t['id'] . "'");
                            DB::PDO()->exec("DELETE FROM `cms_forum_rdm` WHERE `topic_id` = '" . $res_t['id'] . "'");
                        }
                        // Удаляем темы
                        DB::PDO()->exec("DELETE FROM `forum` WHERE `refid` = " . Vars::$ID);
                        // Удаляем раздел
                        DB::PDO()->exec("DELETE FROM `forum` WHERE `id` = " . Vars::$ID);
                        // Оптимизируем таблицы
                        DB::PDO()->query("OPTIMIZE TABLE `cms_forum_files` , `cms_forum_rdm` , `forum` , `cms_forum_vote` , `cms_forum_vote_users`");
                        echo'<div class="rmenu"><p>' . __('section_themes_deleted') . '<br />' .
                            '<a href="index.php?act=forum&amp;mod=cat&amp;id=' . $res['refid'] . '">' . __('to_category') . '</a></p></div>';
                    } else {
                        echo '<form action="index.php?act=forum&amp;mod=del&amp;id=' . Vars::$ID . '" method="POST"><div class="rmenu">' .
                            '<p>' . __('section_move_warning') . '</p>' . '<p><h3>' . __('select_section') . '</h3>';
                        $cat = isset($_GET['cat']) ? abs(intval($_GET['cat'])) : 0;
                        $ref = $cat ? $cat : $res['refid'];
                        $req_r = DB::PDO()->query("SELECT * FROM `forum` WHERE `refid` = '$ref' AND `id` != " . Vars::$ID . " AND `type` = 'r' ORDER BY `realid` ASC");
                        while ($res_r = $req_r->fetch()) {
                            echo '<input type="radio" name="subcat" value="' . $res_r['id'] . '" />&#160;' . $res_r['text'] . '<br />';
                        }
                        echo '</p><p><h3>' . __('another_category') . '</h3><ul>';
                        $req_c = DB::PDO()->query("SELECT * FROM `forum` WHERE `type` = 'f' AND `id` != '$ref' ORDER BY `realid` ASC");
                        while ($res_c = $req_c->fetch()) {
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
                // Удаление пустого раздела, или категории
                if (isset($_POST['submit'])) {
                    DB::PDO()->exec("DELETE FROM `forum` WHERE `id` = " . Vars::$ID);
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
        // Добавление категории
        $cat_name = '';
        if (Vars::$ID) {
            // Проверяем наличие категории
            $req = DB::PDO()->query("SELECT `text` FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 'f'");
            if ($req->rowCount()) {
                $res = $req->fetch();
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
                $req = DB::PDO()->query("SELECT `realid` FROM `forum` WHERE " . (Vars::$ID ? "`refid` = " . Vars::$ID . " AND `type` = 'r'" : "`type` = 'f'") . " ORDER BY `realid` DESC LIMIT 1");
                if ($req->rowCount()) {
                    $res = $req->fetch();
                    $sort = $res['realid'] + 1;
                } else {
                    $sort = 1;
                }

                $STH = DB::PDO()->prepare('
                    INSERT INTO `forum` SET
                    `refid` = :refid,
                    `type` = :type,
                    `text` = :text,
                    `soft` = :soft,
                    `realid` = :realid,
                    `edit` = "",
                    `curators` = ""
                ');

                $STH->bindValue(':refid', (Vars::$ID ? Vars::$ID : 0));
                $STH->bindValue(':type', (Vars::$ID ? 'r' : 'f'));
                $STH->bindParam(':text', $name);
                $STH->bindParam(':soft', $desc);
                $STH->bindParam(':realid', $sort);
                $STH->execute();
                $STH = NULL;

                header('Location: ' . $uri . 'cat/' . (Vars::$ID ? '?id=' . Vars::$ID : ''));
            } else {
                // Выводим сообщение об ошибках
                echo Functions::displayError($error);
            }
        } else {
            // Форма ввода
            echo '<div class="phdr"><b>' . (Vars::$ID ? __('add_section') : __('add_category')) . '</b></div>';
            if (Vars::$ID)
                echo '<div class="bmenu"><b>' . __('to_category') . ':</b> ' . $cat_name . '</div>';
            echo '<form action="' . $uri . 'add/' . (Vars::$ID ? '?id=' . Vars::$ID : '') . '" method="post">' .
                '<div class="gmenu">' .
                '<p><h3>' . __('title') . '</h3>' .
                '<input type="text" name="name" />' .
                '<br /><small>' . __('minmax_2_30') . '</small></p>' .
                '<p><h3>' . __('description') . '</h3>' .
                '<textarea name="desc" rows="' . Vars::$USER_SET['field_h'] . '"></textarea>' .
                '<br /><small>' . __('not_mandatory_field') . '<br />' . __('minmax_2_500') . '</small></p>' .
                '<p><input type="submit" value="' . __('add') . '" name="submit" />' .
                '</p></div></form>' .
                '<div class="phdr"><a href="' . $uri . 'cat/' . (Vars::$ID ? '?id=' . Vars::$ID : '') . '">' . __('back') . '</a></div>';
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
        $req = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID);
        if ($req->rowCount()) {
            $res = $req->fetch();
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
                    elseif ($res['type'] == 'r' && !DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `id` = '$category' AND `type` = 'f'")->fetchColumn())
                        $error[] = __('error_category_select');
                    if (!$name)
                        $error[] = __('error_empty_title');
                    if ($name && (mb_strlen($name) < 2 || mb_strlen($name) > 30))
                        $error[] = __('title') . ': ' . __('error_wrong_lenght');
                    if ($desc && mb_strlen($desc) < 2)
                        $error[] = __('error_description_lenght');
                    if (!$error) {
                        // Записываем в базу
                        $STH = DB::PDO()->prepare('
                            UPDATE `forum` SET
                            `text`     = :text,
                            `soft`     = :soft
                            WHERE `id` = :id
                        ');

                        $STH->bindParam(':text', $name);
                        $STH->bindParam(':soft', $desc);
                        $STH->bindValue(':id', Vars::$ID);
                        $STH->execute();
                        $STH = NULL;

                        if ($res['type'] == 'r' && $category != $res['refid']) {
                            // Вычисляем сортировку
                            $res_s = DB::PDO()->query("SELECT `realid` FROM `forum` WHERE `refid` = '$category' AND `type` = 'r' ORDER BY `realid` DESC LIMIT 1")->fetch();
                            $sort = $res_s['realid'] + 1;
                            // Меняем категорию
                            DB::PDO()->exec("UPDATE `forum` SET `refid` = '$category', `realid` = '$sort' WHERE `id` = " . Vars::$ID);
                            // Меняем категорию для прикрепленных файлов
                            DB::PDO()->exec("UPDATE `cms_forum_files` SET `cat` = '$category' WHERE `cat` = '" . $res['refid'] . "'");
                        }
                        header('Location: ' . $uri . 'cat/' . ($res['type'] == 'r' ? '?id=' . $res['refid'] : ''));
                    } else {
                        // Выводим сообщение об ошибках
                        echo Functions::displayError($error);
                    }
                } else {
                    // Форма ввода
                    echo'<div class="phdr"><b>' . ($res['type'] == 'r' ? __('section_edit') : __('category_edit')) . '</b></div>' .
                        '<form action="' . $uri . 'edit/?id=' . Vars::$ID . '" method="post">' .
                        '<div class="gmenu">' .
                        '<p><h3>' . __('title') . '</h3>' .
                        '<input type="text" name="name" value="' . $res['text'] . '"/>' .
                        '<br /><small>' . __('minmax_2_30') . '</small></p>' .
                        '<p><h3>' . __('description') . '</h3>' .
                        '<textarea name="desc" rows="' . Vars::$USER_SET['field_h'] . '">' . str_replace('<br />', "\r\n", $res['soft']) . '</textarea>' .
                        '<br /><small>' . __('not_mandatory_field') . '<br />' . __('minmax_2_500') . '</small></p>';
                    if ($res['type'] == 'r') {
                        echo '<p><h3>' . __('category') . '</h3><select name="category" size="1">';
                        $req_c = DB::PDO()->query("SELECT * FROM `forum` WHERE `type` = 'f' ORDER BY `realid` ASC");
                        while ($res_c = $req_c->fetch()) {
                            echo '<option value="' . $res_c['id'] . '"' . ($res_c['id'] == $res['refid'] ? ' selected="selected"' : '') . '>' . $res_c['text'] . '</option>';
                        }
                        echo '</select></p>';
                    }
                    echo'<p><input type="submit" value="' . __('save') . '" name="submit" />' .
                        '</p></div></form>' .
                        '<div class="phdr"><a href="' . $uri . 'cat/' . ($res['type'] == 'r' ? '?id=' . $res['refid'] : '') . '">' . __('back') . '</a></div>';
                }
            } else {
                header('Location: ' . $uri . 'cat/');
            }
        } else {
            header('Location: ' . $uri . 'cat/');
        }
        break;

    case 'up':
        /*
        -----------------------------------------------------------------
        Перемещение на одну позицию вверх
        -----------------------------------------------------------------
        */
        if (Vars::$ID) {
            $req1 = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID);
            if ($req1->rowCount()) {
                $res1 = $req1->fetch();
                $sort = $res1['realid'];
                $req2 = DB::PDO()->query("SELECT * FROM `forum` WHERE `type` = '" . ($res1['type'] == 'f' ? 'f' : 'r') . "' AND `realid` < '$sort' ORDER BY `realid` DESC LIMIT 1");
                if ($req2->rowCount()) {
                    $res2 = $req2->fetch();
                    $id2 = $res2['id'];
                    $sort2 = $res2['realid'];
                    DB::PDO()->exec("UPDATE `forum` SET `realid` = '$sort2' WHERE `id` = " . Vars::$ID);
                    DB::PDO()->exec("UPDATE `forum` SET `realid` = '$sort' WHERE `id` = '$id2'");
                }
            }
        }
        header('Location: ' . $uri . 'cat/' . (isset($res1['type']) && $res1['type'] == 'r' ? '?id=' . $res1['refid'] : ''));
        break;

    case 'down':
        /*
        -----------------------------------------------------------------
        Перемещение на одну позицию вниз
        -----------------------------------------------------------------
        */
        if (Vars::$ID) {
            $req1 = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID);
            if ($req1->rowCount()) {
                $res1 = $req1->fetch();
                $sort = $res1['realid'];
                $req2 = DB::PDO()->query("SELECT * FROM `forum` WHERE `type` = '" . ($res1['type'] == 'f' ? 'f' : 'r') . "' AND `realid` > '$sort' ORDER BY `realid` ASC LIMIT 1");
                if ($req2->rowCount()) {
                    $res2 = $req2->fetch();
                    $id2 = $res2['id'];
                    $sort2 = $res2['realid'];
                    DB::PDO()->exec("UPDATE `forum` SET `realid` = '$sort2' WHERE `id` = " . Vars::$ID);
                    DB::PDO()->exec("UPDATE `forum` SET `realid` = '$sort' WHERE `id` = '$id2'");
                }
            }
        }
        header('Location: ' . $uri . 'cat/' . (isset($res1['type']) && $res1['type'] == 'r' ? '?id=' . $res1['refid'] : ''));
        break;

    case 'cat':
        // Управление категориями и разделами
        echo '<div class="phdr"><a href="' . $uri . '"><b>' . __('forum_management') . '</b></a> | ' . __('forum_structure') . '</div>';
        if (Vars::$ID) {
            // Управление разделами
            $cat = DB::PDO()->query("SELECT `text` FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 'f'")->fetch();
            echo '<div class="bmenu"><a href="' . $uri . 'cat/"><b>' . $cat['text'] . '</b></a> | ' . __('section_list') . '</div>';
            $req = DB::PDO()->query("SELECT * FROM `forum` WHERE `refid` = " . Vars::$ID . " AND `type` = 'r' ORDER BY `realid` ASC");
            if ($req->rowCount()) {
                for ($i = 0; $res = $req->fetch(); ++$i) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo '<b>' . $res['text'] . '</b>' .
                        '&#160;<a href="' . Vars::$HOME_URL . 'forum/?id=' . $res['id'] . '">&gt;&gt;</a>';
                    if (!empty($res['soft']))
                        echo '<br /><span class="gray"><small>' . $res['soft'] . '</small></span><br />';
                    echo'<div class="sub">' .
                        '<a href="' . $uri . 'up/?id=' . $res['id'] . '">' . __('up') . '</a> | ' .
                        '<a href="' . $uri . 'down/?id=' . $res['id'] . '">' . __('down') . '</a> | ' .
                        '<a href="' . $uri . 'edit/?id=' . $res['id'] . '">' . __('edit') . '</a> | ' .
                        '<a href="' . $uri . 'del/?id=' . $res['id'] . '">' . __('delete') . '</a>' .
                        '</div></div>';
                }
            } else {
                echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
            }
        } else {
            // Управление категориями
            echo '<div class="bmenu">' . __('category_list') . '</div>';
            $req = DB::PDO()->query("SELECT * FROM `forum` WHERE `type` = 'f' ORDER BY `realid` ASC");
            for ($i = 0; $res = $req->fetch(); ++$i) {
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                echo '<a href="' . $uri . 'cat/?id=' . $res['id'] . '"><b>' . $res['text'] . '</b></a> ' .
                    '(' . DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'r' AND `refid` = '" . $res['id'] . "'")->fetchColumn() . ')' .
                    '&#160;<a href="' . Vars::$HOME_URL . 'forum/?id=' . $res['id'] . '">&gt;&gt;</a>';
                if (!empty($res['soft']))
                    echo '<br /><span class="gray"><small>' . $res['soft'] . '</small></span><br />';
                echo '<div class="sub">' .
                    '<a href="' . $uri . 'up/?id=' . $res['id'] . '">' . __('up') . '</a> | ' .
                    '<a href="' . $uri . 'down/?id=' . $res['id'] . '">' . __('down') . '</a> | ' .
                    '<a href="' . $uri . 'edit/?id=' . $res['id'] . '">' . __('edit') . '</a> | ' .
                    '<a href="' . $uri . 'del/?id=' . $res['id'] . '">' . __('delete') . '</a>' .
                    '</div></div>';
            }
        }
        echo'<div class="gmenu">' .
            '<form action="' . $uri . 'add/' . (Vars::$ID ? '?id=' . Vars::$ID : '') . '" method="post">' .
            '<input type="submit" value="' . __('add') . '" />' .
            '</form></div>' .
            '<div class="phdr">' . (Vars::$MOD == 'cat' && Vars::$ID ? '<a href="' . $uri . '?mod=cat">' . __('category_list') . '</a>' : '<a href="' . $uri . '">' . __('forum_management') . '</a>') . '</div>';
        break;

    case 'htopics':
        /*
        -----------------------------------------------------------------
        Управление скрытыми темами форума
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . $uri . '"><b>' . __('forum_management') . '</b></a> | ' . __('hidden_topics') . '</div>';
        $sort = '';
        if (isset($_GET['usort'])) {
            $sort = " AND `forum`.`user_id` = '" . abs(intval($_GET['usort'])) . "'";
            $uri .= '&amp;usort=' . abs(intval($_GET['usort']));
            echo '<div class="bmenu">' . __('filter_on_author') . ' <a href="index.php?act=forum&amp;mod=htopics">[x]</a></div>';
        }
        if (isset($_GET['rsort'])) {
            $sort = " AND `forum`.`refid` = '" . abs(intval($_GET['rsort'])) . "'";
            $uri .= '&amp;rsort=' . abs(intval($_GET['rsort']));
            echo '<div class="bmenu">' . __('filter_on_section') . ' <a href="index.php?act=forum&amp;mod=htopics">[x]</a></div>';
        }
        if (isset($_POST['deltopic'])) {
            if (Vars::$USER_RIGHTS != 9) {
                echo Functions::displayError(__('access_forbidden'));
                exit;
            }
            $req = DB::PDO()->query("SELECT `id` FROM `forum` WHERE `type` = 't' AND `close` = '1' " . $sort);
            while ($res = $req->fetch()) {
                $req_f = DB::PDO()->query("SELECT * FROM `cms_forum_files` WHERE `topic` = '" . $res['id'] . "'");
                if ($req_f->rowCount()) {
                    // Удаляем файлы
                    while ($res_f = $req_f->fetch()) {
                        unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $res_f['filename']);
                    }
                    DB::PDO()->exec("DELETE FROM `cms_forum_files` WHERE `topic` = '" . $res['id'] . "'");
                }
                // Удаляем посты
                DB::PDO()->exec("DELETE FROM `forum` WHERE `type` = 'm' AND `refid` = '" . $res['id'] . "'");
            }
            // Удаляем темы
            DB::PDO()->exec("DELETE FROM `forum` WHERE `type` = 't' AND `close` = '1' " . $sort);
            header('Location: ' . $uri . 'htopics/');
        } else {
            $total = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `close` = '1' " . $sort)->fetch();
            if ($total > Vars::$USER_SET['page_size']) {
                echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=forum&amp;mod=htopics&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            }
            $req = DB::PDO()->query("SELECT `forum`.*, `forum`.`id` AS `fid`, `forum`.`user_id` AS `id`, `forum`.`from` AS `name`, `forum`.`soft` AS `browser`, `users`.`rights`, `users`.`last_visit`, `users`.`sex`, `users`.`status`, `users`.`join_date`
            FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
            WHERE `forum`.`type` = 't'
            AND `forum`.`close` = '1' $sort
            ORDER BY `forum`.`id` DESC " . Vars::db_pagination());
            if ($req->rowCount()) {
                $i = 0;
                while ($res = $req->fetch()) {
                    $subcat = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'")->fetch();
                    $cat = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = '" . $subcat['refid'] . "'")->fetch();
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
                    echo '<form action="index.php?act=forum&amp;mod=htopics' . $uri . '" method="POST">' .
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
        $uri = '';
        if (isset($_GET['tsort'])) {
            $sort = " AND `forum`.`refid` = '" . abs(intval($_GET['tsort'])) . "'";
            $uri = '&amp;tsort=' . abs(intval($_GET['tsort']));
            echo '<div class="bmenu">' . __('filter_on_theme') . ' <a href="index.php?act=forum&amp;mod=hposts">[x]</a></div>';
        } elseif (isset($_GET['usort'])) {
            $sort = " AND `forum`.`user_id` = '" . abs(intval($_GET['usort'])) . "'";
            $uri = '&amp;usort=' . abs(intval($_GET['usort']));
            echo '<div class="bmenu">' . __('filter_on_author') . ' <a href="index.php?act=forum&amp;mod=hposts">[x]</a></div>';
        }
        if (isset($_POST['delpost'])) {
            if (Vars::$USER_RIGHTS != 9) {
                echo Functions::displayError(__('access_forbidden'));
                exit;
            }
            $req = DB::PDO()->query("SELECT `id` FROM `forum` WHERE `type` = 'm' AND `close` = '1' " . $sort);
            while ($res = $req->fetch()) {
                $req_f = DB::PDO()->query("SELECT * FROM `cms_forum_files` WHERE `post` = '" . $res['id'] . "' LIMIT 1");
                if ($req_f->rowCount()) {
                    $res_f = $req_f->fetch();
                    // Удаляем файлы
                    unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $res_f['filename']);
                    DB::PDO()->exec("DELETE FROM `cms_forum_files` WHERE `post` = '" . $res['id'] . "' LIMIT 1");
                }
            }
            // Удаляем посты
            DB::PDO()->exec("DELETE FROM `forum` WHERE `type` = 'm' AND `close` = '1' " . $sort);
            header('Location: ' . $uri . 'hposts/');
        } else {
            $total = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `close` = '1' " . $sort)->fetchColumn();
            if ($total > Vars::$USER_SET['page_size']) {
                echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=forum&amp;mod=hposts&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            }
            $req = DB::PDO()->query("SELECT `forum`.*, `forum`.`id` AS `fid`, `forum`.`user_id` AS `id`, `forum`.`from` AS `name`, `forum`.`soft` AS `browser`, `users`.`rights`, `users`.`last_visit`, `users`.`sex`, `users`.`status`, `users`.`join_date`
            FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
            WHERE `forum`.`type` = 'm'
            AND `forum`.`close` = '1' $sort
            ORDER BY `forum`.`id` DESC " . Vars::db_pagination());
            if ($req->rowCount()) {
                $i = 0;
                while ($res = $req->fetch()) {
                    $res['ip'] = ip2long($res['ip']);
                    $posttime = ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span>';
                    $page = ceil(DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '" . $res['fid'] . "'")->fetchColumn() / Vars::$USER_SET['page_size']);
                    $text = mb_substr($res['text'], 0, 500);
                    $text = Validate::checkout($text, 1, 0);
                    $text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
                    $theme = DB::PDO()->query("SELECT `id`, `text` FROM `forum` WHERE `id` = '" . $res['refid'] . "'")->fetch();
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
                    echo '<form action="index.php?act=forum&amp;mod=hposts' . $uri . '" method="POST"><div class="rmenu"><input type="submit" name="delpost" value="' . __('delete_all') . '" /></div></form>';
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

    default:
        // Панель управления форумом
        $tpl->total_cat = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'f'")->fetchColumn();
        $tpl->total_sub = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'r'")->fetchColumn();
        $tpl->total_thm = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't'")->fetchColumn();
        $tpl->total_thm_del = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `close` = '1'")->fetchColumn();
        $tpl->total_msg = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm'")->fetchColumn();
        $tpl->total_msg_del = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `close` = '1'")->fetchColumn();
        $tpl->total_files = DB::PDO()->query("SELECT COUNT(*) FROM `cms_forum_files`")->fetchColumn();
        $tpl->total_votes = DB::PDO()->query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type` = '1'")->fetchColumn();

        $tpl->uri = $uri;
        $tpl->contents = $tpl->includeTpl('admin_main');
}
echo '<p><a href="' . Vars::$HOME_URL . 'admin/">' . __('admin_panel') . '</a></p>';