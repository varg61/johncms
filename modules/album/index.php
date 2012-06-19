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

define('ALBUMPATH', FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'album' . DIRECTORY_SEPARATOR);
$max_album = 10;
$max_photo = 200;
$al = isset($_REQUEST['al']) ? abs(intval($_REQUEST['al'])) : NULL;
$img = isset($_REQUEST['img']) ? abs(intval($_REQUEST['img'])) : NULL;
$user = isset($_GET['user']) ? abs(intval($_GET['user'])) : NULL;

/*
-----------------------------------------------------------------
Закрываем от неавторизованных юзеров
-----------------------------------------------------------------
*/
if (!Vars::$USER_ID) {
    echo Functions::displayError(lng('access_guest_forbidden'));
    exit;
}

/*
-----------------------------------------------------------------
Получаем данные пользователя
-----------------------------------------------------------------
*/
$user = Functions::getUser($user);
if (!$user) {
    echo Functions::displayError(lng('user_does_not_exist'));
    exit;
}

$tpl = Template::getInstance();
$tpl->img = $img;
$tpl->user = $user;
$tpl->al = $al;

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$actions = array(
    'comments'       => 'comments.php',
    'delete'         => 'delete.php',
    'edit'           => 'edit.php',
    'image_delete'   => 'image_delete.php',
    'image_download' => 'image_download.php',
    'image_edit'     => 'image_edit.php',
    'image_move'     => 'image_move.php',
    'image_upload'   => 'image_upload.php',
    'list'           => 'list.php',
    'new'            => 'new.php',
    'new_comm'       => 'new_comm.php',
    'show'           => 'show.php',
    'sort'           => 'sort.php',
    'users'          => 'users.php',
    'vote'           => 'vote.php',
);

if (isset($actions[Vars::$ACT]) && is_file(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT])) {
    require_once(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT]);
} else {
    $tpl->new = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `time` > '" . (time() - 259200) . "' AND `access` > '1'"), 0);
    $tpl->count_m = mysql_result(mysql_query("SELECT COUNT(DISTINCT `user_id`) FROM `cms_album_files` LEFT JOIN `users` ON `cms_album_files`.`user_id` = `users`.`id` WHERE `users`.`sex` = 'm'"), 0);
    $tpl->count_w = mysql_result(mysql_query("SELECT COUNT(DISTINCT `user_id`) FROM `cms_album_files` LEFT JOIN `users` ON `cms_album_files`.`user_id` = `users`.`id` WHERE `users`.`sex` = 'w'"), 0);
    $tpl->count_my = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = " . Vars::$USER_ID), 0);
    $tpl->contents = $tpl->includeTpl('index');
}