<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);

$textl = '' . $lng['chat'] . '';
require_once('../incfiles/core.php');
$headmod = $id ? 'chat,' . $id : 'chat';

// Ограничиваем доступ к Чату
$error = '';
if (!$set['mod_chat'] && $rights < 7)
    $error = 'Чат закрыт';
elseif ($ban['1'] || $ban['12'])
    $error = 'Для Вас доступ в Чат закрыт';
elseif (!$user_id)
    $error = 'Доступ в Чат открыт только <a href="../in.php">авторизованным</a> посетителям';
if ($error) {
    require_once('../incfiles/head.php');
    echo display_error($error);
    require_once('../incfiles/end.php');
    exit;
}

// Пользовательские настройки Чата
$set_chat = unserialize($datauser['set_chat']);
if (empty($set_chat))
    $set_chat = array (
        'refresh' => 20,
        'chmes' => 10,
        'carea' => 0,
        'carea_w' => 20,
        'carea_h' => 2,
        'mood' => 'нейтральное'
    );

////////////////////////////////////////////////////////////
// Выбор режимов работы                                   //
////////////////////////////////////////////////////////////
$array = array (
    'say',
    'who',
    'clean'
);
if (in_array($act, $array) && file_exists($act . '.php')) {
    require_once($act . '.php');
}  elseif ($id) {
    // Отображаем комнату Чата
    $chat = 2;
    require_once('room.php');
} else {
    // Отображаем прихожую Чата
    require_once('hall.php');
}

?>