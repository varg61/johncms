<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require('../incfiles/core.php');
$lng_pm = core::load_lng('pm');
$max_rcp = 5; //TODO: Временно. Привязать к настройкам в Админке

/*
-----------------------------------------------------------------
Закрываем от неавторизованных юзеров
-----------------------------------------------------------------
*/
if (!$user_id) {
    require('../incfiles/head.php');
    echo functions::display_error($lng['access_guest_forbidden']);
    require('../incfiles/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Инициализируем класс
-----------------------------------------------------------------
*/
$pm = new pm;

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$array = array(
    'write' => 'includes/pm'
);
$path = !empty($array[$act]) ? $array[$act] . '/' : '';
if (array_key_exists($act, $array) && file_exists($path . $act . '.php')) {
    require_once($path . $act . '.php');
} else {
    /*
    -----------------------------------------------------------------
    Главное меню Почты
    -----------------------------------------------------------------
    */
    require('../incfiles/head.php');
    echo '<div class="phdr"><b>' . $lng_pm['my_mail'] . '</b></div>' .
         //'<div class="gmenu"><form action="pm.php?act=write" method="post"><input type="submit" value=" Написать " /></form></div>' .
         '<div class="list2"><p>' .
         '</p><p>' .
         '<div>' . functions::get_image('mail-inbox.png') . '&#160;<a href="">Входящие</a>&nbsp;()</div>' .
         '<div>' . functions::get_image('mail-outbox.png') . '&#160;<a href="">Отправленные</a>&nbsp;()</div>' .
         '<div>' . functions::get_image('mail-notice.png') . '&#160;<a href="">Уведомления</a>&nbsp;()</div>' .
         '</p><p>' .
         '<div>' . functions::get_image('mail-draft.png') . '&#160;<a href="">Черновики</a>&nbsp;()</div>' .
         '<div>' . functions::get_image('mail-file.png') . '&#160;<a href="">Файлы</a>&nbsp;()</div>' .
         '</p><p>' .
         '<div>' . functions::get_image('trash.png') . '&#160;<a href="">Корзина</a>&nbsp;()</div>' .
         (!isset($ban['1']) && !isset($ban['3']) ? '<p><form action="pm.php?act=write" method="post"><input type="submit" value=" Написать " /></form></p>' : '') .
         '</p></div>' .
         '<div class="menu"><p>' .
         '<h3>' . functions::get_image('contacts.png') . '&#160;' . $lng_pm['folders'] . '</h3><ul>' .
         '<li><a href="">Важное</a> ()</li>' .
         '<li><a href="">Разобраться</a> ()</li>' .
         '<li><a href="">Ответить</a> ()</li>' .
         '</ul></p><p><h3>' . functions::get_image('contacts.png') . '&#160;' . core::$lng['contacts'] . '</h3><ul>' .
         '<li><a href="">Контакты</a>&nbsp;()</li>' .
         '<li><a href="">Списки</a>&nbsp;()</li>' .
         '<li><a href="">Игнор</a>&nbsp;()</li>' .
         '</ul></p></div><div class="bmenu"><p>' .
         '<h3>' . functions::get_image('settings.png') . '&nbsp;Управление</h3><ul>' .
         '<li><a href="profile.php?act=pm&amp;mod=set">Настройки</a></li>' .
         '<li><a href="">Очистка</a></li>' .
         '</ul></p>' .
         '</div><div class="phdr"></div>';
}

require_once('../incfiles/end.php');