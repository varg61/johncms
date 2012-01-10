<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Counters extends Vars
{
    public $users;           // Зарегистрированные пользователи
    public $users_new;       // Новые зарегистрированные пользователи
    public $album;           // Пользовательские альбомы
    public $album_photo;     // Пользовательские фотографии
    public $album_photo_new; // Новые пользовательские фотографии
    public $downloads;       // Счетчик файлов в Загруз-центре
    public $downloads_new;   // Счетчик новых файлов в Загруз-центре
    public $forum_topics;    // Счетчик топиков Форума
    public $forum_messages;  // Счетчик постов Форума
    public $library;         // Счетчик статей Библиотеки
    public $library_new;     // Счетчик новых статей Библиотеки
    public $library_mod;     // Счетчик статей Библиотеки, находящихся на модерации
    public $gallery;         // Счетчик картинок в Галерее
    public $gallery_new;     // Счетчик новых картинок в Галерее
    public $guestbook;       // Счетчик постов в Гостевой за последние сутки
    public $adminclub;       // Счетчик постов в Админ-клубе за последние сутки

    private $cache_file = 'cache_counters.dat';
    private $update_cache = false;

    function __construct()
    {
        /*
        -----------------------------------------------------------------
        Считываем кэш
        -----------------------------------------------------------------
        */
        $count = $this->_cacheRead();

        /*
        -----------------------------------------------------------------
        Обрабатываем счетчики
        -----------------------------------------------------------------
        */
        $this->users = $this->_users($count['1']);
        $this->users_new = $this->_usersNew($count['2']);
        $this->album = $this->_album($count['3']);
        $this->album_photo = $this->_albumPhoto($count['4']);
        $this->album_photo_new = $this->_albumPhotoNew($count['5']);
        $this->downloads = $this->_downloads($count['6']);
        $this->downloads_new = $this->_downloadsNew($count['7']);
        $this->forum_topics = $this->_forumTopics($count['8']);
        $this->forum_messages = $this->_forumMessages($count['9']);
        $this->library = $this->_library($count['10']);
        $this->library_new = $this->_libraryNew($count['11']);
        $this->library_mod = $this->_libraryMod($count['12']);
        $this->gallery = $this->_gallery($count['13']);
        $this->gallery_new = $this->_galleryNew($count['14']);
        $this->guestbook = $this->_guestBook($count['15']);
        $this->adminclub = $this->_adminClub($count['16']);

        /*
        -----------------------------------------------------------------
        Записываем кэш
        -----------------------------------------------------------------
        */
        if ($this->update_cache) {
            $this->_cacheWrite($count);
        }
    }

    /*
    -----------------------------------------------------------------
    Счетчик посетителей Онлайн
    -----------------------------------------------------------------
    */
    public static function usersOnline()
    {
        $sql = "AND `session_timestamp` > " . (time() - 300);
        $users = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_sessions` WHERE `user_id` > 0 $sql"), 0);
        $guests = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_sessions` WHERE `user_id` = 0 $sql"), 0);
        return (parent::$USER_ID || parent::$SYSTEM_SET['active']
            ? '<a href="' . parent::$HOME_URL . '/users/index.php?act=online">' . Vars::$LNG['online'] . ': ' . $users . ' / ' . $guests . '</a>'
            : Vars::$LNG['online'] . ': ' . $users . ' / ' . $guests);
    }

    /*
    -----------------------------------------------------------------
    Считываем данные из Кэша
    -----------------------------------------------------------------
    */
    private function _cacheRead()
    {
        $out = array();
        $file = CACHEPATH . $this->cache_file;
        if (file_exists($file)) {
            $in = fopen($file, "r");
            while ($block = fread($in, 10)) {
                $tmp = unpack('Skey/Lcount/Ltime', $block);
                $out[$tmp['key']] = array('count' => $tmp['count'], 'time' => $tmp['time']);
            }
            fclose($in);
            return $out;
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Записываем данные в Кэш
    -----------------------------------------------------------------
    */
    private function _cacheWrite($data = array())
    {
        $file = CACHEPATH . $this->cache_file;
        $in = fopen($file, "w+");
        flock($in, LOCK_EX);
        ftruncate($in, 0);
        foreach ($data as $key => $val) {
            fwrite($in, pack('SLL', $key, $val['count'], $val['time']));
        }
        fclose($in);
    }

    /*
    -----------------------------------------------------------------
    Счетчик зарегистрированных пользователей сайта
    -----------------------------------------------------------------
    */
    private function _users(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `users`"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик новых зарегистрированных пользователей сайта (за 1 день)
    -----------------------------------------------------------------
    */
    private function _usersNew(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `join_date` > '" . (time() - 86400) . "'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик Фотоальбомов
    -----------------------------------------------------------------
    */
    private function _album(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(DISTINCT `user_id`) FROM `cms_album_files`"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик картинок
    -----------------------------------------------------------------
    */
    private function _albumPhoto(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files`"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик новых картинок
    -----------------------------------------------------------------
    */
    private function _albumPhotoNew(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `time` > '" . (time() - 259200) . "' AND `access` > '1'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик файлов в загруз-центре
    -----------------------------------------------------------------
    */
    private function _downloads(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `type` = 'file'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик новых файлов в загруз-центре
    -----------------------------------------------------------------
    */
    private function _downloadsNew(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `time` > '" . (time() - 259200) . "' AND `type` = 'file'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик топиков Форума
    -----------------------------------------------------------------
    */
    private function _forumTopics(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `close` != '1'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик постов Форума
    -----------------------------------------------------------------
    */
    private function _forumMessages(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `close` != '1'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик непрочитанных топиков Форума
    -----------------------------------------------------------------
    */
    public static function forumMessagesNew()
    {
        if (!Vars::$USER_ID) {
            return false;
        }
        return mysql_result(mysql_query("SELECT COUNT(*) FROM `forum`
                LEFT JOIN `cms_forum_rdm` ON `forum`.`id` = `cms_forum_rdm`.`topic_id` AND `cms_forum_rdm`.`user_id` = '" . Vars::$USER_ID . "'
                WHERE `forum`.`type`='t'" . (Vars::$USER_RIGHTS >= 7 ? "" : " AND `forum`.`close` != '1'") . "
                AND (`cms_forum_rdm`.`topic_id` Is Null
                OR `forum`.`time` > `cms_forum_rdm`.`time`)"), 0);
    }

    /*
    -----------------------------------------------------------------
    Счетчик статей в Библиотеке
    -----------------------------------------------------------------
    */
    private function _library(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = '1'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик новых статей в Библиотеке (за 2 дня)
    -----------------------------------------------------------------
    */
    private function _libraryNew(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `time` > '" . (time() - 259200) . "' AND `type` = 'bk' AND `moder` = '1'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик статей на модерации в Библиотеке
    -----------------------------------------------------------------
    */
    private function _libraryMod(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = '0'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик картинок в Галерее
    -----------------------------------------------------------------
    */
    private function _gallery(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery` WHERE `type` = 'ft'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик новых картинок в Галерее (за 2 дня)
    -----------------------------------------------------------------
    */
    private function _galleryNew(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery` WHERE `time` > '" . (time() - 259200) . "' AND `type` = 'ft'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик новых постов в гостевой
    -----------------------------------------------------------------
    */
    private function _guestBook(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 60) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='0' AND `time` > '" . (time() - 86400) . "'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }

    /*
    -----------------------------------------------------------------
    Счетчик новых постов в Админ-клубе
    -----------------------------------------------------------------
    */
    private function _adminClub(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 60) {
            $this->update_cache = true;
            $var['count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='1' AND `time` > '" . (time() - 86400) . "'"), 0);
            $var['time'] = time();
        }
        return $var['count'];
    }
}