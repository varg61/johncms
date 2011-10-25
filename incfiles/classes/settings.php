<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Restricted access');

class settings
{
    /*
    -----------------------------------------------------------------
    Получаем пользовательские данные и возвращаем в виде массива
    -----------------------------------------------------------------
    */
    public static function user_data_get($key = '')
    {
        if (core::$user_id && !empty($key)) {
            $req = mysql_query("SELECT * FROM `cms_users_settings` WHERE `user_id` = '" . core::$user_id . "' AND `key` = '" . $key . "' LIMIT 1");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                if (!empty($res['value'])) return unserialize($res['value']);
            }
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Добавляем / обновляем / удаляем пользовательские данные
    -----------------------------------------------------------------
    */
    public static function user_data_put($key = '', $val = '')
    {
        if(empty($key) || !empty($val) && !is_array($val)) return false;
        if(empty($val)){
            @mysql_query("DELETE FROM `cms_users_settings` WHERE `user_id` = '" . core::$user_id . "' AND `key` = '" . $key . "' LIMIT 1");
            return true;
        }
        $val = mysql_real_escape_string(serialize($val));
        if (self::user_data_get($key) === false) {
            // Добавляем новую запись
            mysql_query("INSERT INTO `cms_users_settings` SET
                `user_id` = '" . core::$user_id . "',
                `key` = '$key',
                `value` = '$val'
            ");
        } else {
            // Обновляем имеющуюся запись
            mysql_query("UPDATE `cms_users_settings` SET
                `value` = '$val'
                WHERE `user_id` = '" . core::$user_id . "' AND `key` = '$key'
                LIMIT 1
            ");
        }
        return true;
    }

    /*
    -----------------------------------------------------------------
    Настройки пользователя по-умолчанию
    -----------------------------------------------------------------
    */
    public static function set_user_default()
    {
        return array(
            'avatar'         => 1,                                             // Показывать аватары
            'digest'         => 0,                                             // Показывать Дайджест
            'direct_url'     => 0,                                             // Внешние ссылки
            'field_h'        => 3,                                             // Высота текстового поля ввода
            'field_w'        => (core::$is_mobile ? 20 : 40),                  // Ширина текстового поля ввода
            'kmess'          => 10,                                            // Число сообщений на страницу
            'quick_go'       => 1,                                             // Быстрый переход
            'timeshift'      => 0,                                             // Временной сдвиг
            'skin'           => core::$system_set['skindef'],                  // Тема оформления
            'smileys'        => 1,                                             // Включить(1) выключить(0) смайлы
            'translit'       => 0                                              // Транслит
        );
    }

    /*
    -----------------------------------------------------------------
    Настройки форума по-умолчанию
    -----------------------------------------------------------------
    */
    public static function set_forum_default()
    {
        return array(
            'farea' => 0,
            'upfp' => 0,
            'preview' => 1,
            'postclip' => 1,
            'postcut' => 2
        );
    }
}