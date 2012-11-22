<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class SiteMap
{
    // Настройки карты форума
    private $cache_forum_map = 72;                                             // Время кэширования карты форума (часов)
    private $cache_forum_contents = 48;                                        // Время кэширования оглавления форума (часов)
    private $cache_forum_file = 'map_forum';                                   // Имя файла кэша (без расширения)

    // Настройки карты Библиотеки
    private $cache_lib_map = 72;                                               // Время кэширования карты библиотеки (часов)
    private $cache_lib_contents = 48;                                          // Время кэширования оглавления библиотеки (часов)
    private $cache_lib_file = 'map_lib';                                       // Имя файла кэша (без расширения)

    // Системные настройки
    private $links_count = 140;                                                // Число ссылок в блоке
    private $set;                                                              // Системные настройки модуля
    private $page;

    /*
    -----------------------------------------------------------------
    Задаем настройки
    -----------------------------------------------------------------
    */
    function __construct()
    {
        global $set;
        $this->set = isset(Vars::$SYSTEM_SET['sitemap']) ? unserialize(Vars::$SYSTEM_SET['sitemap']) : array();
        $this->page = isset($_GET['p']) ? abs(intval($_GET['p'])) : 0;
    }

    /*
    -----------------------------------------------------------------
    Карта сайта
    -----------------------------------------------------------------
    */
    public function mapGeneral()
    {
        return ($this->set['forum'] ? '<p><b>Forum Map</b><br />' . $this->mapForumContent() . '</p>' : '') .
               ($this->set['lib'] ? '<p><b>Library Map</b><br />' . $this->mapLibraryContent() . '</p>' : '');
    }

    /*
    -----------------------------------------------------------------
    Содержание разделов форума
    -----------------------------------------------------------------
    */
    public function mapForum()
    {
        global $rootpath;
        $file = $rootpath . 'files/cache/' . $this->cache_forum_file . '_' . Vars::$ID . ($this->page ? '_' . $this->page : '') . '.dat';
        if (!Vars::$ID)
            return Functions::displayError(Vars::$LNG['error_wrong_data']);
        if (file_exists($file) && filemtime($file) > (time() - $this->cache_forum_contents * 3600)) {
            return file_get_contents($file);
        } else {
            $req = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 'r'");
            if (mysql_num_rows($req)) {
                $row = array();
                $res = mysql_fetch_assoc($req);
                $req_t = mysql_query("SELECT * FROM `forum` WHERE `refid` = " . Vars::$ID . " AND `type` = 't' AND `close` != '1' ORDER BY `time` DESC LIMIT " . ($this->page * $this->links_count) . ", " . $this->links_count);
                if (mysql_num_rows($req_t)) {
                    while (($res_t = mysql_fetch_assoc($req_t)) !== false) $row[] = '<a href="' . Vars::$HOME_URL . '/forum/index.php?id=' . $res_t['id'] . '">' . $res_t['text'] . '</a>';
                    $out = '<div class="phdr"><b>' . Vars::$LNG['forum'] . '</b> | ' . $res['text'] . '</div><div class="menu">' . implode('<br />' . "\r\n", $row) . '</div>';
                    return file_put_contents($file, $out) ? $out : 'Forum Contents cache error';
                }
            }
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Содержание разделов Библиотеки
    -----------------------------------------------------------------
    */
    public function mapLibrary()
    {
        global $rootpath;
        $file = $rootpath . 'files/cache/' . $this->cache_lib_file . '_' . Vars::$ID . ($this->page ? '_' . $this->page : '') . '.dat';
        if (!Vars::$ID)
            return Functions::displayError(Vars::$LNG['error_wrong_data']);
        if (file_exists($file) && filemtime($file) > (time() - $this->cache_lib_contents * 3600)) {
            return file_get_contents($file);
        } else {
            $req = mysql_query("SELECT * FROM `lib` WHERE `id` = " . Vars::$ID . " AND `type` = 'cat' AND `ip` = '0'");
            if (mysql_num_rows($req)) {
                $row = array();
                $res = mysql_fetch_assoc($req);
                $req_a = mysql_query("SELECT * FROM `lib` WHERE `refid` = " . Vars::$ID . " AND `type` = 'bk' AND `moder` = '1' ORDER BY `time` ASC LIMIT " . ($this->page * $this->links_count) . ", " . $this->links_count);
                if (mysql_num_rows($req_a)) {
                    while (($res_a = mysql_fetch_assoc($req_a)) !== false) $row[] = '<a href="' . Vars::$HOME_URL . '/library/index.php?id=' . $res_a['id'] . '">' . Validate::checkout($res_a['name']) . '</a>';
                    $out = '<div class="phdr"><b>' . Vars::$LNG['library'] . '</b> | ' . $res['text'] . '</div><div class="menu">' . implode('<br />' . "\r\n", $row) . '</div>';
                    return file_put_contents($file, $out) ? $out : 'Library Contents cache error';
                }
            }
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Кэш карты Форума
    -----------------------------------------------------------------
    */
    private function mapForumContent()
    {
        global $rootpath;
        $file = $rootpath . 'files/cache/' . $this->cache_forum_file . '.dat';
        if (file_exists($file) && filemtime($file) > (time() - $this->cache_forum_map * 3600)) {
            return file_get_contents($file);
        } else {
            $req = mysql_query("SELECT * FROM `forum` WHERE `type` = 'r'");
            if (mysql_num_rows($req)) {
                while (($res = mysql_fetch_assoc($req)) !== false) {
                    $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['id'] . "' AND `type` = 't' AND `close` != '1'"), 0);
                    if ($count) {
                        $text = html_entity_decode($res['text']);
                        $text = mb_substr($text, 0, 40);
                        $pages = ceil($count / $this->links_count);
                        if ($pages > 1) {
                            for ($i = 0; $i < $pages; $i++) {
                                $out[] = '<a href="' . Vars::$HOME_URL . '/forum/contents.php?id=' . $res['id'] . '&amp;p=' . $i . '">' . Validate::checkout($text) . ' (' . ($i + 1) . ')</a>';
                            }
                        } else {
                            $out[] = '<a href="' . Vars::$HOME_URL . '/forum/contents.php?id=' . $res['id'] . '">' . Validate::checkout($text) . '</a>';
                        }
                    }
                }
                if (isset($out))
                    return file_put_contents($file, implode('<br />' . "\r\n", $out)) ? implode('<br />', $out) : 'Forum cache error';
            }
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Кэш карты Библиотеки
    -----------------------------------------------------------------
    */
    private function mapLibraryContent()
    {
        global $rootpath, $set;
        $file = $rootpath . 'files/cache/' . $this->cache_lib_file . '.dat';
        if (file_exists($file) && filemtime($file) > (time() - $this->cache_lib_map * 3600)) {
            return file_get_contents($file);
        } else {
            $req = mysql_query("SELECT * FROM `lib` WHERE `type` = 'cat' AND `ip` = '0'");
            if (mysql_num_rows($req)) {
                while (($res = mysql_fetch_assoc($req)) !== false) {
                    $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `refid` = '" . $res['id'] . "' AND `type` = 'bk' AND `moder` = '1'"), 0);
                    if ($count) {
                        $text = html_entity_decode($res['text']);
                        $text = mb_substr($text, 0, 40);
                        $pages = ceil($count / $this->links_count);
                        if ($pages > 1) {
                            for ($i = 0; $i < $pages; $i++) {
                                $out[] = '<a href="' . Vars::$HOME_URL . '/library/contents.php?id=' . $res['id'] . '&amp;p=' . $i . '">' . Validate::checkout($text) . ' (' . ($i + 1) . ')</a>';
                            }
                        } else {
                            $out [] = '<a href="../library/contents.php?id=' . $res['id'] . '">' . Validate::checkout($text) . '</a>';
                        }
                    }
                }
                if (isset($out))
                    return file_put_contents($file, implode('<br />' . "\r\n", $out)) ? implode('<br />', $out) : 'Library cache error';
            }
        }
        return false;
    }
}