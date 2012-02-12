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

//$lng = Vars::loadLanguage(1);

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
    echo Functions::displayError(Vars::$LNG['access_guest_forbidden']);
    exit;
}

/*
-----------------------------------------------------------------
Получаем данные пользователя
-----------------------------------------------------------------
*/
$user = Functions::getUser($user);
if (!$user) {
    echo Functions::displayError(Vars::$LNG['user_does_not_exist']);
    exit;
}

/*
-----------------------------------------------------------------
Функция голосований за фотографии
-----------------------------------------------------------------
*/
function vote_photo($arg = null)
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
        $out = '<div class="gray">' . Vars::$LNG['rating'] . ': <span style="color:#000;background-color:#' . $color . '">&#160;&#160;<big><b>' . $rating . '</b></big>&#160;&#160;</span> ' .
            '(' . Vars::$LNG['vote_against'] . ': ' . $arg['vote_minus'] . ', ' . Vars::$LNG['vote_for'] . ': ' . $arg['vote_plus'] . ')';
        if (Vars::$USER_ID != $arg['user_id'] && !Vars::$USER_BAN && $datauser['postforum'] > 10 && $datauser['total_on_site'] > 1200) {
            // Проверяем, имеет ли юзер право голоса
            $req = mysql_query("SELECT * FROM `cms_album_votes` WHERE `user_id` = " . Vars::$USER_ID . " AND `file_id` = '" . $arg['id'] . "' LIMIT 1");
            if (!mysql_num_rows($req))
                $out .= '<br />' . Vars::$LNG['vote'] . ': <a href="album.php?act=vote&amp;mod=minus&amp;img=' . $arg['id'] . '">&lt;&lt; -1</a> | ' .
                    '<a href="album.php?act=vote&amp;mod=plus&amp;img=' . $arg['id'] . '">+1 &gt;&gt;</a>';
        }
        $out .= '</div>';
        return $out;
    } else {
        return false;
    }
}

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$array = array(
    'comments' => 'includes/album',
    'delete' => 'includes/album',
    'edit' => 'includes/album',
    'image_delete' => 'includes/album',
    'image_download' => 'includes/album',
    'image_edit' => 'includes/album',
    'image_move' => 'includes/album',
    'image_upload' => 'includes/album',
    'list' => 'includes/album',
    'new_comm' => 'includes/album',
    'show' => 'includes/album',
    'sort' => 'includes/album',
    'top' => 'includes/album',
    'users' => 'includes/album',
    'vote' => 'includes/album'
);
$path = !empty($array[Vars::$ACT]) ? $array[Vars::$ACT] . '/' : '';
if (array_key_exists(Vars::$ACT, $array) && file_exists($path . Vars::$ACT . '.php')) {
    require_once($path . Vars::$ACT . '.php');
} else {
    $tpl = Template::getInstance();
    $tpl->lng = Vars::loadLanguage(1);
    $tpl->new = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `time` > '" . (time() - 259200) . "' AND `access` > '1'"), 0);
    $tpl->count = mysql_result(mysql_query("SELECT COUNT(DISTINCT `user_id`) FROM `cms_album_files`"), 0);
    $tpl->contents = $tpl->includeTpl('index');
}