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
    /**
     * @var int Зарегистрированные пользователи
     */
    public $users;

    /**
     * @var int Новые зарегистрированные пользователи
     */
    public $users_new;

    /**
     * @var int Пользовательские альбомы
     */
    public $album;

    /**
     * @var int Пользовательские фотографии
     */
    public $album_photo;

    /**
     * @var int Новые пользовательские фотографии
     */
    public $album_photo_new;

    /**
     * @var int Счетчик файлов в Загруз-центре
     */
    public $downloads;

    /**
     * @var int Счетчик файлов в Загруз-центре
     */
    public $downloads_mod;

    /**
     * @var int Счетчик новых файлов в Загруз-центре
     */
    public $downloads_new;

    /**
     * @var int Счетчик топиков Форума
     */
    public $forum_topics;

    /**
     * @var int Счетчик постов Форума
     */
    public $forum_messages;

    /**
     * @var int Счетчик статей Библиотеки
     */
    public $library;

    /**
     * @var int Счетчик новых статей Библиотеки
     */
    public $library_new;

    /**
     * @var int Счетчик статей Библиотеки, находящихся на модерации
     */
    public $library_mod;

    /**
     * @var int Счетчик постов в Гостевой за последние сутки
     */
    public $guestbook;

    /**
     * @var int Счетчик постов в Админ-клубе за последние сутки
     */
    public $adminclub;

    private $cache_file = 'cache_counters.dat';
    private $update_cache = FALSE;

    function __construct()
    {
        $count = $this->_cacheRead();

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
        $this->guestbook = $this->_guestBook($count['15']);
        $this->adminclub = $this->_adminClub($count['16']);
        $this->downloads_mod = $this->_downloadsMod($count['17']);

        if ($this->update_cache) {
            $this->_cacheWrite($count);
        }
    }

    /**
     * Счетчик посетителей Онлайн
     *
     * @return integer
     */
    public static function usersOnline()
    {
        return DB::PDO()->query('SELECT COUNT(*) FROM `cms_sessions` WHERE `user_id` > 0 AND `session_timestamp` > ' . (time() - 300))->fetchColumn();
    }

    /**
     * Счетчик гостей Онлайн
     *
     * @return integer
     */
    public static function guestaOnline()
    {
        return DB::PDO()->query('SELECT COUNT(*) FROM `cms_sessions` WHERE `user_id` = 0 AND `session_timestamp` > ' . (time() - 300))->fetchColumn();
    }

    /**
     * Считываем данные из Кэша
     *
     * @return array|bool
     */
    private function _cacheRead()
    {
        $out = array();
        $file = CACHEPATH . $this->cache_file;
        if (file_exists($file)) {
            $in = fopen($file, "r");
            while ($block = fread($in, 10)) {
                $tmp = unpack('Skey/Lcount/Ltime', $block);
                $out[$tmp['key']] = array('count' => $tmp['count'],
                                          'time'  => $tmp['time']);
            }
            fclose($in);

            return $out;
        }

        return FALSE;
    }

    /**
     * Записываем данные в Кэш
     *
     * @param array $data
     */
    private function _cacheWrite(array $data = array())
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

    /**
     * Счетчик зарегистрированных пользователей сайта
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _users(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query('SELECT COUNT(*) FROM `users` WHERE `level` > 0')->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик новых зарегистрированных пользователей сайта (за 1 день)
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _usersNew(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query('SELECT COUNT(*) FROM `users` WHERE `join_date` > ' . (time() - 86400))->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик Фотоальбомов
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _album(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query('SELECT COUNT(DISTINCT `user_id`) FROM `cms_album_files`')->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик картинок
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _albumPhoto(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query('SELECT COUNT(*) FROM `cms_album_files`')->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик новых картинок
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _albumPhotoNew(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query('SELECT COUNT(*) FROM `cms_album_files` WHERE `time` > ' . (time() - 259200) . ' AND `access` > 1')->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик файлов в загруз-центре
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _downloads(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query('SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = 2')->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик новых файлов в загруз-центре
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _downloadsNew(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query('SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = 2 AND `time` > ' . (time() - 259200))->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик файлов на модерации в загруз-центре
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _downloadsMod(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query('SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = 3')->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик топиков Форума
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _forumTopics(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `close` != '1'")->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик постов Форума
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _forumMessages(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `close` != '1'")->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик непрочитанных топиков Форума
     *
     * @return bool|integer
     */
    public static function forumMessagesNew()
    {
        if (!Vars::$USER_ID) {
            return FALSE;
        }

        return DB::PDO()->query("SELECT COUNT(*) FROM `forum`
                LEFT JOIN `cms_forum_rdm` ON `forum`.`id` = `cms_forum_rdm`.`topic_id` AND `cms_forum_rdm`.`user_id` = '" . Vars::$USER_ID . "'
                WHERE `forum`.`type`='t'" . (Vars::$USER_RIGHTS >= 7 ? "" : " AND `forum`.`close` != '1'") . "
                AND (`cms_forum_rdm`.`topic_id` Is Null
                OR `forum`.`time` > `cms_forum_rdm`.`time`)")->fetchColumn();
    }

    /**
     * Счетчик статей в Библиотеке
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _library(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = '1'")->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик новых статей в Библиотеке (за 2 дня)
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _libraryNew(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 3600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `time` > '" . (time() - 259200) . "' AND `type` = 'bk' AND `moder` = '1'")->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик статей на модерации в Библиотеке
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _libraryMod(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 600) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = '0'")->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик новых постов в гостевой
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _guestBook(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 60) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query('SELECT COUNT(*) FROM `guest` WHERE `adm` = 0 AND `time` > ' . (time() - 86400))->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }

    /**
     * Счетчик новых постов в Админ-клубе
     *
     * @param integer $var
     *
     * @return integer
     */
    private function _adminClub(&$var)
    {
        if (!isset($var) || $var['time'] < time() - 60) {
            $this->update_cache = TRUE;
            $var['count'] = DB::PDO()->query('SELECT COUNT(*) FROM `guest` WHERE `adm` = 1 AND `time` > ' . (time() - 86400))->fetchColumn();
            $var['time'] = time();
        }

        return $var['count'];
    }
}