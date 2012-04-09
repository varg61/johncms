<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

abstract class Vars
{
    /*
    -----------------------------------------------------------------
    Системные переменные
    -----------------------------------------------------------------
    */
    public static $SYSTEM_SET;                   // Системные настройки
    public static $CORE_ERRORS = array();        // Ошибки ядра

    public static $LNG_ISO = 'en';               // Двухбуквенный ISO код языка
    public static $LNG_LIST = array();           // Список имеющихся языков

    public static $HOME_URL;                     // URL сайта
    public static $IP;                           // IP адрес
    public static $IP_VIA_PROXY = 0;             // IP адрес за прокси-сервером
    public static $IP_REQUESTS_LIST = array();   // Счетчик обращений с IP адреса
    public static $USER_AGENT;                   // User Agent
    public static $IS_MOBILE = FALSE;            // Мобильный браузер
    public static $PLACE = '';                   // Местоположение на сайте
    public static $MODULE_INCLUDE = '';          // Подключаемый файл Роутера
    public static $MODULE_URI = '';              // URI модуля
    public static $URI = '';                     // URI активного скрипта
    public static $MODULE = 'mainmenu';          // Активный модуль

    /*
    -----------------------------------------------------------------
    Пользовательские переменные
    -----------------------------------------------------------------
    */
    public static $USER_ID = 0;                  // Идентификатор пользователя
    public static $USER_NICKNAME = FALSE;        // Ник пользователя
    public static $USER_RIGHTS = 0;              // Права доступа
    public static $USER_DATA = array();          // Все данные пользователя
    public static $USER_BAN = array();           // Бан

    /*
    -----------------------------------------------------------------
    Пользователские настройки
    -----------------------------------------------------------------
    */

    // Индивидуальные пользователские настройки по-умолчанию
    public static $USER_SET = array(
        'avatar'           => 1,                 // Показывать аватары
        'digest'           => 0,                 // Показывать Дайджест
        'direct_url'       => 0,                 // Внешние ссылки
        'field_h'          => 3,                 // Высота текстового поля ввода
        'page_size'        => 10,                // Число сообщений на страницу в списках
        'quick_go'         => 1,                 // Быстрый переход
        'timeshift'        => 0,                 // Временной сдвиг
        'skin'             => 'default',         // Тема оформления
        'smileys'          => 1,                 // Включить(1) выключить(0) смайлы
        'translit'         => 0,                 // Транслит
    );

    // Системные настройки для пользователей по-умолчанию
    public static $USER_SYS = array(
        'autologin'        => 1,                 // Разрешить логин по ссылке (АвтоЛогин)
        'change_nickname'  => 0,                 // Разрешить смену Ника
        'change_sex'       => 0,                 // Разрешить смену пола пользователем
        'change_status'    => 1,                 // Разрешить смену статуса пользователем
        'flood_day'        => 10,                // Время АнтиФлуда днем (сек.)
        'flood_mode'       => 2,                 // Режим работы системы АнтиФлуда
        'flood_night'      => 30,                // Время АнтиФлуда ночью (сек.)
        'reg_email'        => 0,                 // Регистрация с подтверждением на E-mail
        'reg_moderation'   => 0,                 // Включить модерацию новых регистраций
        'reg_open'         => 1,                 // Открыть/закрыть регистрацию на сайте
        'reg_quarantine'   => 0,                 // Включить карантин
        'reg_welcome'      => 1,                 // Письмо с приветствем после регистрации
        'upload_animation' => 1,                 // Разрешить выгрузку анимированных аватаров
        'upload_avatars'   => 1,                 // Разрешить выгрузку аватаров
        'viev_history'     => 1,                 // Просмотр гостями истории онлайн
        'view_online'      => 1,                 // Просмотр гостями списков онлайн
        'view_profiles'    => 0,                 // Просмотр анкет гостями
        'view_userlist'    => 1,                 // Просмотр гостями списка пользователей
    );

    /*
    -----------------------------------------------------------------
    Суперглобальные переменные
    -----------------------------------------------------------------
    */
    public static $ID;
    public static $ACT;
    public static $MOD;
    public static $USER;
    public static $PAGE = 1;
    public static $START = 0;

    /*
    -----------------------------------------------------------------
    Получаем данные пользователя
    -----------------------------------------------------------------
    */
    public static function getUser()
    {
        if (self::$USER && self::$USER != self::$USER_ID) {
            $req = mysql_query("SELECT * FROM `users` WHERE `id` = " . self::$USER);
            if (mysql_num_rows($req)) {
                return mysql_fetch_assoc($req);
            } else {
                return FALSE;
            }
        } else {
            return self::$USER_DATA;
        }
    }

    /*
    -----------------------------------------------------------------
    Получаем пользовательские настройки
    -----------------------------------------------------------------
    */
    public static function getUserData($key = '')
    {
        if (self::$USER_ID && !empty($key)) {
            $req = mysql_query("SELECT * FROM `cms_user_settings` WHERE `user_id` = '" . self::$USER_ID . "' AND `key` = '" . $key . "' LIMIT 1");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                if (!empty($res['value'])) {
                    return unserialize($res['value']);
                }
            }
        }
        return FALSE;
    }

    /*
    -----------------------------------------------------------------
    Добавляем, обновляем, удаляем пользовательские настройки
    -----------------------------------------------------------------
    */
    public static function setUserData($key = '', $val = '')
    {
        if (!self::$USER_ID || empty($key) || !empty($val) && !is_array($val)) {
            return FALSE;
        }
        if (!empty($val)) {
            $val = mysql_real_escape_string(serialize($val));
            mysql_query("INSERT INTO `cms_user_settings` SET
                `user_id` = '" . self::$USER_ID . "',
                `key` = '$key',
                `value` = '$val'
                ON DUPLICATE KEY UPDATE `value` = '$val'
            ");
        } else {
            @mysql_query("DELETE FROM `cms_user_settings` WHERE `user_id` = '" . self::$USER_ID . "' AND `key` = '" . $key . "' LIMIT 1");
        }
        return TRUE;
    }

    /*
    -----------------------------------------------------------------
    Уничтожаем данные авторизации юзера
    -----------------------------------------------------------------
    */
    public static function userUnset($clear_token = FALSE)
    {
        if (self::$USER_ID && $clear_token) {
            mysql_query("UPDATE `users` SET `token` = '' WHERE `id` = " . self::$USER_ID);
        }
        self::$USER_ID = FALSE;
        self::$USER_RIGHTS = 0;
        self::$USER_DATA = array();
        setcookie('uid', '', time() - 3600, '/');
        setcookie('token', '', time() - 3600, '/');
        session_destroy();
    }

    /*
    -----------------------------------------------------------------
    Исправление вызова несуществующей страницы
    -----------------------------------------------------------------
    */
    public static function fixPage($total)
    {
        if ($total < self::$START) {
            $page = ceil($total / self::$USER_SET['page_size']);
            self::$START = $page * self::$USER_SET['page_size'] - self::$USER_SET['page_size'];
        }
    }

    public static function db_pagination()
    {
        return ' LIMIT ' . self::$START . ',' . self::$USER_SET['page_size'];
    }
}
