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
    public static $HOME_URL;                     // URL сайта
    public static $IP;                           // IP адрес
    public static $IP_VIA_PROXY = 0;             // IP адрес за прокси-сервером
    public static $IP_REQUESTS_LIST = array();   // Счетчик обращений с IP адреса
    public static $USER_AGENT;                   // User Agent
    public static $IS_MOBILE = FALSE;            // Мобильный браузер
    public static $PLACE = '';                   // Текущее местоположение на сайте

    public static $USER_ID = 0;                  // Идентификатор пользователя
    public static $USER_NICKNAME = FALSE;        // Ник пользователя
    public static $USER_RIGHTS = 0;              // Права доступа
    public static $USER_DATA = array();          // Все данные пользователя
    public static $USER_BAN = array();           // Бан

    public static $ID;                           // int    $_REQUEST['id']
    public static $ACT;                          // string $_REQUEST['act']
    public static $MOD;                          // string $_REQUEST['mod']
    public static $PAGE = 1;                     // int    $_REQUEST['page']
    public static $START = 0;                    // int    $_REQUEST['start']

    // Системные настройки по-умолчанию
    public static $SYSTEM_SET = array(
        'lng'                => 'en',            // Системный язык
        'lngswitch'          => TRUE,            // Разрешить выбор языка
        'timeshift'          => 0,               // Сдвиг времени (+-12)
        'copyright'          => 'Powered by JohnCMS',  // Копирайт сайта
        'email'              => '',              // E-mail сайта
        'filesize'           => 2000,            // Макс. размер выгружаемых файлов
        'generation'         => 1,               // Профилировка: показывать время генерации
        'memory'             => 0,               // Профилировка: показывать используемую память
        'hometitle'          => 'Welcome!',      // Заголовок Главной страницы
        'meta_key'           => 'johncms',       // meta name="keywords"
        'meta_desc'          => 'JohnCMS',       // meta name="description"
        'clean_time'         => 0                // Время последней очистки системы
    );

    // Контроль доступа к модулям
    public static $ACL = array(
        'forum'              => 2,
        'album'              => 2,
        'albumcomm'          => 1,
        'guestbook'          => 1,
        'library'            => 2,
        'libcomm'            => 1,
        'downloads'          => 2,
        'downcomm'           => 1
    );

    // Системные настройки для пользователей по-умолчанию
    public static $USER_SYS = array(
        'autologin'          => 0,               // Разрешить логин по ссылке (АвтоЛогин)
        'change_nickname'    => 0,               // Разрешить смену Ника
        'change_period'      => 30,              // Разрешенный период (дней) для смены ника
        'change_sex'         => 0,               // Разрешить смену пола пользователем
        'change_status'      => 1,               // Разрешить смену статуса пользователем
        'digits_only'        => 0,               // Разрешить ники состоящие из одних цифр
        'flood_day'          => 10,              // Время АнтиФлуда днем (сек.)
        'flood_mode'         => 2,               // Режим работы системы АнтиФлуда
        'flood_night'        => 30,              // Время АнтиФлуда ночью (сек.)
        'reg_email'          => 0,               // Регистрация с подтверждением на E-mail
        'registration'       => 2,               // Открыть/закрыть регистрацию на сайте
        'reg_quarantine'     => 0,               // Включить карантин
        'reg_welcome'        => 1,               // Письмо с приветствем после регистрации
        'upload_avatars'     => 1,               // Разрешить выгрузку аватаров
        'viev_history'       => 1,               // Просмотр гостями истории онлайн
        'view_online'        => 1,               // Просмотр гостями списков онлайн
        'view_profiles'      => 0,               // Просмотр анкет гостями
        'view_userlist'      => 1,               // Просмотр гостями списка пользователей
    );

    // Индивидуальные пользователские настройки по-умолчанию
    public static $USER_SET = array(
        'avatar'             => 1,               // Показывать аватары
        'direct_url'         => 0,               // Внешние ссылки
        'field_h'            => 3,               // Высота текстового поля ввода
        'lng'                => '#',             // Язык (# - автовыбор)
        'page_size'          => 10,              // Число сообщений на страницу в списках
        'skin'               => 'default',       // Тема оформления
        'smilies'            => 1,               // Включить(1) выключить(0) смайлы
        'timeshift'          => 0,               // Временной сдвиг
    );

    /**
     * Получаем пользовательские настройки
     *
     * @param string $key
     *
     * @return bool|array
     */
    public static function getUserData($key = '')
    {
        if (static::$USER_ID && !empty($key)) {
            $STH = DB::PDO()->prepare('
                SELECT `value` FROM `cms_user_settings`
                WHERE `user_id` = ' . static::$USER_ID . '
                AND `key`       = ?
                LIMIT 1
            ');

            $STH->execute(array($key));

            if ($STH->rowCount()) {
                $result = $STH->fetch();
                $STH = NULL;

                return unserialize($result['value']);
            }
            $STH = NULL;
        }

        return FALSE;
    }

    /**
     * Добавляем, обновляем, удаляем пользовательские настройки
     *
     * @param string $key
     * @param array  $val
     *
     * @return bool
     */
    public static function setUserData($key = '', $val = array())
    {
        if (!static::$USER_ID || empty($key) || (!empty($val) && !is_array($val))) {
            return FALSE;
        }

        if (empty($val)) {
            // Удаляем пользовательские данные
            $STH = DB::PDO()->prepare('
                DELETE FROM `cms_user_settings`
                WHERE `user_id` = ' . static::$USER_ID . '
                AND `key`       = ?
                LIMIT 1
            ');

            $STH->execute(array($key));
        } else {
            $STH = DB::PDO()->prepare('
            REPLACE INTO `cms_user_settings` SET
            `user_id` = ' . static::$USER_ID . ',
            `key`     = ?,
            `value`   = ?
            ');

            $STH->execute(array($key, serialize($val)));
        }
        $STH = NULL;

        return TRUE;
    }

    /**
     * Уничтожаем данные авторизации юзера
     *
     * @param bool $clear_token
     */
    public static function userUnset($clear_token = FALSE)
    {
        if (static::$USER_ID && $clear_token) {
            DB::PDO()->exec("UPDATE `users` SET `token` = '' WHERE `id` = " . static::$USER_ID);
        }
        static::$USER_ID = FALSE;
        static::$USER_RIGHTS = 0;
        static::$USER_DATA = array();
        setcookie('uid', '', time() - 3600, '/');
        setcookie('token', '', time() - 3600, '/');
        session_destroy();
    }

    /**
     * Исправление вызова несуществующей страницы
     *
     * @param $total
     */
    public static function fixPage($total)
    {
        if ($total < static::$START) {
            $page = ceil($total / static::$USER_SET['page_size']);
            static::$START = $page * static::$USER_SET['page_size'] - static::$USER_SET['page_size'];
        }
    }

    /**
     * @return string
     */
    public static function db_pagination()
    {
        return ' LIMIT ' . static::$START . ',' . static::$USER_SET['page_size'];
    }
}
