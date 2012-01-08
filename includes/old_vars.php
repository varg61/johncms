<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 *
 * Данный файл предназначен для совместимости со старыми доп. модулями от JohnCMS
 * Если Вы не используете старых модулей, то НЕ ПОДКЛЮЧАЙТЕ этот файл!
 */

$ip        = Vars::$IP;                        // Адрес IP
$agn       = Vars::$USERAGENT;                 // User Agent
$set       = Vars::$SYSTEM_SET;                // Системные настройки
$lng       = Vars::$LNG;                       // Фразы языка
$is_mobile = Vars::$IS_MOBILE;                 // Определение мобильного браузера
$home      = Vars::$SYSTEM_SET['homeurl'];     // Домашняя страница
$user_id   = Vars::$USER_ID;                   // Идентификатор пользователя
$rights    = Vars::$USER_RIGHTS;               // Права доступа
$datauser  = Vars::$USER_DATA;                 // Все данные пользователя
$set_user  = Vars::$USER_SET;                  // Пользовательские настройки
//$ban       = Vars::$user_ban;                    // Бан
$kmess     = Vars::$USER_SET['page_size'];     // Число сообщений на страницу
$login     = isset(Vars::$USER_DATA['nickname']) ? Vars::$USER_DATA['nickname'] : false;

$id        = Vars::$ID;
$act       = Vars::$ACT;
$mod       = Vars::$MOD;
$page      = Vars::$PAGE;
$start     = Vars::$START;

class bbcode extends TextParser
{

}