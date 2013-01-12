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
    public static $LNG_ISO = 'en'; // Двухбуквенный ISO код языка
    public static $LNG_LIST = array(); // Список имеющихся языков

    public static $HOME_URL; // URL сайта
    public static $IP; // IP адрес
    public static $IP_VIA_PROXY = 0; // IP адрес за прокси-сервером
    public static $IP_REQUESTS_LIST = array(); // Счетчик обращений с IP адреса
    public static $USER_AGENT; // User Agent
    public static $IS_MOBILE = FALSE; // Мобильный браузер
    public static $PLACE = ''; // Текущее местоположение на сайте
    public static $SYSTEM_ERRORS = array(); //TODO: Написать вывод системных ошибок

    public static $ROUTE_FIRST = FALSE; //TODO: Удалить

    // Системные настройки
    public static $SYSTEM_SET = array(
        'lng'        => '#',
        'timeshift'  => 0,
        'copyright'  => 'Powered by JohnCMS',
        'email'      => '',
        'filesize'   => 2000,
        'generation' => 1,
        'memory'     => 0,
        'hometitle'  => 'Welcome!',
        'meta_key'   => 'johncms',
        'meta_desc'  => 'Powered by JohnCMS http://johncms.com'
    );

    // Контроль доступа к модулям
    public static $ACL = array();

    /*
    -----------------------------------------------------------------
    Пользовательские переменные
    -----------------------------------------------------------------
    */
    public static $USER_ID = 0; // Идентификатор пользователя
    public static $USER_NICKNAME = FALSE; // Ник пользователя
    public static $USER_RIGHTS = 0; // Права доступа
    public static $USER_DATA = array(); // Все данные пользователя
    public static $USER_BAN = array(); // Бан

    /*
    -----------------------------------------------------------------
    Пользователские настройки
    -----------------------------------------------------------------
    */

    // Индивидуальные пользователские настройки по-умолчанию
    public static $USER_SET = array(
        'avatar'     => 1, // Показывать аватары
        'direct_url' => 0, // Внешние ссылки
        'field_h'    => 3, // Высота текстового поля ввода
        'page_size'  => 10, // Число сообщений на страницу в списках
        'timeshift'  => 0, // Временной сдвиг
        'skin'       => 'default', // Тема оформления
        'smileys'    => 1, // Включить(1) выключить(0) смайлы
        'translit'   => 0, // Транслит
    );

    // Системные настройки для пользователей по-умолчанию
    public static $USER_SYS = array(
        'autologin'       => 0, // Разрешить логин по ссылке (АвтоЛогин)
        'change_nickname' => 0, // Разрешить смену Ника
        'change_period'   => 30, // Разрешенный период (дней) для смены ника
        'change_sex'      => 0, // Разрешить смену пола пользователем
        'change_status'   => 1, // Разрешить смену статуса пользователем
        'digits_only'     => 0, // Разрешить ники состоящие из одних цифр
        'flood_day'       => 10, // Время АнтиФлуда днем (сек.)
        'flood_mode'      => 2, // Режим работы системы АнтиФлуда
        'flood_night'     => 30, // Время АнтиФлуда ночью (сек.)
        'reg_email'       => 0, // Регистрация с подтверждением на E-mail
        'registration'    => 2, // Открыть/закрыть регистрацию на сайте
        'reg_quarantine'  => 0, // Включить карантин
        'reg_welcome'     => 1, // Письмо с приветствем после регистрации
        'upload_avatars'  => 1, // Разрешить выгрузку аватаров
        'viev_history'    => 1, // Просмотр гостями истории онлайн
        'view_online'     => 1, // Просмотр гостями списков онлайн
        'view_profiles'   => 0, // Просмотр анкет гостями
        'view_userlist'   => 1, // Просмотр гостями списка пользователей
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

    /**
     * Получаем пользовательские настройки
     *
     * @param string $key
     *
     * @return bool|array
     */
    public static function getUserData($key = '')
    {
        if (self::$USER_ID && !empty($key)) {
            $STH = DB::PDO()->prepare('
                SELECT `value` FROM `cms_user_settings`
                WHERE `user_id` = :uid
                AND `key`       = :key
                LIMIT 1
            ');

            $STH->bindValue(':uid', static::$USER_ID);
            $STH->bindParam(':key', $key);
            $STH->execute();

            if ($STH->rowCount()) {
                $result = $STH->fetch();
                if (!empty($result['value'])) {
                    return unserialize($result['value']);
                }
            }
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
        if (!self::$USER_ID || empty($key) || (!empty($val) && !is_array($val))) {
            return FALSE;
        }

        if (empty($val)) {
            // Удаляем пользовательские данные
            $STH = DB::PDO()->prepare('
                DELETE FROM `cms_user_settings`
                WHERE `user_id` = :uid
                AND `key`       = :key
                LIMIT 1
            ');

            $STH->bindValue(':uid', static::$USER_ID, PDO::PARAM_INT);
            $STH->bindParam(':key', $key);
            $STH->execute();
        } else {
            $STH = DB::PDO()->prepare('
            INSERT INTO `cms_user_settings` SET
            `user_id` = :uid,
            `key`     = :key,
            `value`   = :value
            ');

            $STH->bindValue(':uid', static::$USER_ID, PDO::PARAM_INT);
            $STH->bindParam(':key', $key);
            $STH->bindValue(':value', serialize($val), PDO::PARAM_STR);
            $STH->execute();
        }

        return TRUE;
    }

    /**
     * Уничтожаем данные авторизации юзера
     *
     * @param bool $clear_token
     */
    public static function userUnset($clear_token = FALSE)
    {
        if (self::$USER_ID && $clear_token) {
            DB::PDO()->exec("UPDATE `users` SET `token` = '' WHERE `id` = " . self::$USER_ID);
        }
        self::$USER_ID = FALSE;
        self::$USER_RIGHTS = 0;
        self::$USER_DATA = array();
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
        if ($total < self::$START) {
            $page = ceil($total / self::$USER_SET['page_size']);
            self::$START = $page * self::$USER_SET['page_size'] - self::$USER_SET['page_size'];
        }
    }

    /**
     * @return string
     */
    public static function db_pagination()
    {
        return ' LIMIT ' . self::$START . ',' . self::$USER_SET['page_size'];
    }
}
