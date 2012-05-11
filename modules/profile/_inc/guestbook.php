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

//if (Vars::$USER_ID && $user['id'] == Vars::$USER_ID) {
//    $datauser['comm_old'] = $datauser['comm_count'];
//    //TODO: Доработать!
//}

$context_top = '<div class="phdr"><a href="' . Vars::$URI . '?user=' . $user['id'] . '"><b>' . lng('profile') . '</b></a> | ' . lng('guestbook') . '</div>' .
    '<div class="user"><p>' . Functions::displayUser($user, array('iphide' => 1,)) . '</p></div>';

/*
-----------------------------------------------------------------
Параметры Гостевой
-----------------------------------------------------------------
*/
$arg = array(
    'comments_table' => 'cms_user_guestbook',              // Таблица Гостевой
    'object_table'   => 'users',                           // Таблица комментируемых объектов
    'script'         => Vars::$URI . '?act=guestbook',     // Имя скрипта (с параметрами вызова)
    'sub_id_name'    => 'user',                            // Имя (для URL) идентификатора комментируемого объекта
    'sub_id'         => $user['id'],                       // Идентификатор комментируемого объекта
    'owner'          => $user['id'],                       // Владелец объекта
    'owner_delete'   => TRUE,                              // Возможность владельцу удалять комментарий
    'owner_reply'    => TRUE,                              // Возможность владельцу отвечать на комментарий
    'title'          => lng('comments'),                   // Название раздела
    'context_top'    => $context_top                       // Выводится вверху списка
);

/*
-----------------------------------------------------------------
Показываем комментарии
-----------------------------------------------------------------
*/
$comm = new Comments($arg);

/*
-----------------------------------------------------------------
Обновляем счетчик непрочитанного
-----------------------------------------------------------------
*/
//TODO: Доработать!
//if (!$mod && $user['user_id'] == Vars::$user_id && $user['comm_count'] != $user['comm_old']) {
//    mysql_query("UPDATE `users` SET `comm_old` = '" . $user['comm_count'] . "' WHERE `id` = '$user_id'");
//}