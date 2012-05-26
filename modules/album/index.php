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

/*
-----------------------------------------------------------------
Функция голосований за фотографии
-----------------------------------------------------------------
*/
function vote_photo($arg = NULL)
{
    global $datauser;
    //TODO: Разобраться со счетчиками
    if ($arg) {
        $rating = $arg['vote_plus'] - $arg['vote_minus'];
        if ($rating > 0)
            $color = 'C0FFC0';
        elseif ($rating < 0)
            $color = 'F196A8';
        else
            $color = 'CCC';
        $out = '<div class="gray">' . lng('rating') . ': <span style="color:#000;background-color:#' . $color . '">&#160;&#160;<big><b>' . $rating . '</b></big>&#160;&#160;</span> ' .
            '(' . lng('vote_against') . ': ' . $arg['vote_minus'] . ', ' . lng('vote_for') . ': ' . $arg['vote_plus'] . ')';
        if (Vars::$USER_ID != $arg['user_id'] && !Vars::$USER_BAN && $datauser['count_forum'] > 10 && $datauser['total_on_site'] > 1200) {
            // Проверяем, имеет ли юзер право голоса
            $req = mysql_query("SELECT * FROM `cms_album_votes` WHERE `user_id` = " . Vars::$USER_ID . " AND `file_id` = '" . $arg['id'] . "' LIMIT 1");
            if (!mysql_num_rows($req))
                $out .= '<br />' . lng('vote') . ': <a href="' . Vars::$URI . '?act=vote&amp;mod=minus&amp;img=' . $arg['id'] . '">&lt;&lt; -1</a> | ' .
                    '<a href="' . Vars::$URI . '?act=vote&amp;mod=plus&amp;img=' . $arg['id'] . '">+1 &gt;&gt;</a>';
        }
        $out .= '</div>';
        return $out;
    } else {
        return FALSE;
    }
}

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
    $tpl->count = mysql_result(mysql_query("SELECT COUNT(DISTINCT `user_id`) FROM `cms_album_files`"), 0);
    $tpl->contents = $tpl->includeTpl('index');
}