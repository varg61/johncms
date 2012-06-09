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

global $tpl, $user, $al;

/*
-----------------------------------------------------------------
Создать / изменить альбом
-----------------------------------------------------------------
*/
if ($user['id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 7) {
    if ($al) {
        $req = mysql_query("SELECT * FROM `cms_album_cat` WHERE `id` = '$al' AND `user_id` = '" . $user['id'] . "'");
        if (mysql_num_rows($req)) {
            echo '<div class="phdr"><b>' . lng('album_edit') . '</b></div>';
            $res = mysql_fetch_assoc($req);
            $name = htmlspecialchars($res['name']);
            $description = htmlspecialchars($res['description']);
            $password = htmlspecialchars($res['password']);
            $access = $res['access'];
        } else {
            echo Functions::displayError(lng('error_wrong_data'));
            exit;
        }
    } else {
        echo '<div class="phdr"><b>' . lng('album_create') . '</b></div>';
        $name = '';
        $description = '';
        $password = '';
        $access = 0;
    }
    $error = array();
    if (isset($_POST['submit'])) {
        // Принимаем данные
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $access = isset($_POST['access']) ? abs(intval($_POST['access'])) : NULL;
        // Проверяем на ошибки
        if (empty($name))
            $error[] = lng('error_empty_title');
        elseif (mb_strlen($name) < 2 || mb_strlen($name) > 50)
            $error[] = lng('title') . ': ' . lng('error_wrong_lenght');
        $description = mb_substr($description, 0, 500);
        if ($access == 2 && empty($password))
            $error[] = lng('error_empty_password');
        elseif ($access == 2 && mb_strlen($password) < 3 || mb_strlen($password) > 15)
            $error[] = lng('password') . ': ' . lng('error_wrong_lenght');
        if ($access < 1 || $access > 4)
            $error[] = lng('error_wrong_data');
        // Проверяем, есть ли уже альбом с таким же именем?
        if (!$al && mysql_num_rows(mysql_query("SELECT * FROM `cms_album_cat` WHERE `name` = '" . mysql_real_escape_string($name) . "' AND `user_id` = '" . $user['id'] . "' LIMIT 1")))
            $error[] = lng('error_album_exists');
        if (!$error) {
            if ($al) {
                // Изменяем данные в базе
                mysql_query("UPDATE `cms_album_files` SET `access` = '$access' WHERE `album_id` = '$al' AND `user_id` = '" . $user['id'] . "'");
                mysql_query("UPDATE `cms_album_cat` SET
                    `name` = '" . mysql_real_escape_string($name) . "',
                    `description` = '" . mysql_real_escape_string($description) . "',
                    `password` = '" . mysql_real_escape_string($password) . "',
                    `access` = '$access'
                    WHERE `id` = '$al' AND `user_id` = '" . $user['id'] . "'
                ");
            } else {
                // Вычисляем сортировку
                $req = mysql_query("SELECT * FROM `cms_album_cat` WHERE `user_id` = '" . $user['id'] . "' ORDER BY `sort` DESC LIMIT 1");
                if (mysql_num_rows($req)) {
                    $res = mysql_fetch_assoc($req);
                    $sort = $res['sort'] + 1;
                } else {
                    $sort = 1;
                }
                // Заносим данные в базу
                mysql_query("INSERT INTO `cms_album_cat` SET
                    `user_id` = '" . $user['id'] . "',
                    `name` = '" . mysql_real_escape_string($name) . "',
                    `description` = '" . mysql_real_escape_string($description) . "',
                    `password` = '" . mysql_real_escape_string($password) . "',
                    `access` = '$access',
                    `sort` = '$sort'
                ");
            }
            echo '<div class="gmenu"><p>' . ($al ? lng('album_changed') : lng('album_created')) . '<br />' .
                '<a href="' . Vars::$URI . '?act=list&amp;user=' . $user['id'] . '">' . lng('continue') . '</a></p></div>';
            exit;
        }
    }
    if ($error) {
        $tpl->error = Functions::displayError($error);
    }
    $tpl->name = $name;
    $tpl->access = $access;
    $tpl->password = $password;
    $tpl->description = $description;
    $tpl->contents = $tpl->includeTpl('album_edit');
}