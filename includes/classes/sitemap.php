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
    private $cache_forum_map = 72; // Время кэширования карты форума (часов)
    private $cache_forum_contents = 48; // Время кэширования оглавления форума (часов)
    private $cache_forum_file = 'map_forum'; // Имя файла кэша (без расширения)

    // Настройки карты Библиотеки
    private $cache_lib_map = 72; // Время кэширования карты библиотеки (часов)
    private $cache_lib_contents = 48; // Время кэширования оглавления библиотеки (часов)
    private $cache_lib_file = 'map_lib'; // Имя файла кэша (без расширения)

    // Системные настройки
    private $links_count = 140; // Число ссылок в блоке
    private $set; // Системные настройки модуля
    private $page;

    function __construct()
    {
        global $set;
        $this->set = isset(Vars::$SYSTEM_SET['sitemap']) ? unserialize(Vars::$SYSTEM_SET['sitemap']) : array();
        $this->page = isset($_GET['p']) ? abs(intval($_GET['p'])) : 0;
    }

    /**
     * Карта сайта
     *
     * @return string
     */
    public function mapGeneral()
    {
        return ($this->set['forum'] ? '<p><b>Forum Map</b><br />' . $this->mapForumContent() . '</p>' : '') .
            ($this->set['lib'] ? '<p><b>Library Map</b><br />' . $this->mapLibraryContent() . '</p>' : '');
    }

    /**
     * Содержание разделов форума
     *
     * @return bool|string
     */
    public function mapForum()
    {
        $file = CACHEPATH . $this->cache_forum_file . '_' . Vars::$ID . ($this->page ? '_' . $this->page : '') . '.dat';
        if (!Vars::$ID)
            return Functions::displayError(__('error_wrong_data'));
        if (file_exists($file) && filemtime($file) > (time() - $this->cache_forum_contents * 3600)) {
            return file_get_contents($file);
        } else {
            $STH = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 'r'");
            if ($STH->rowCount()) {
                $row = array();
                $section = $STH->fetch();

                $STHT = DB::PDO()->query("SELECT * FROM `forum` WHERE `refid` = " . Vars::$ID . " AND `type` = 't' AND `close` != '1' ORDER BY `time` DESC LIMIT " . ($this->page * $this->links_count) . ", " . $this->links_count);
                if ($STHT->rowCount()) {
                    while ($topic = $STHT->fetch()) {
                        $row[] = '<a href="' . Vars::$HOME_URL . 'forum/?id=' . $topic['id'] . '">' . $topic['text'] . '</a>';
                    }
                    $out = '<div class="phdr"><b>' . __('forum') . '</b> | ' . $section['text'] . '</div><div class="menu">' . implode('<br />' . "\r\n", $row) . '</div>';

                    return file_put_contents($file, $out) ? $out : 'ERROR: forum contents cache';
                }
            }
        }

        return FALSE;
    }

    /**
     * Содержание разделов Библиотеки
     *
     * @return bool|string
     */
    public function mapLibrary()
    {
        $file = CACHEPATH . $this->cache_lib_file . '_' . Vars::$ID . ($this->page ? '_' . $this->page : '') . '.dat';
        if (!Vars::$ID)
            return Functions::displayError(__('error_wrong_data'));
        if (file_exists($file) && filemtime($file) > (time() - $this->cache_lib_contents * 3600)) {
            return file_get_contents($file);
        } else {
            $STH = DB::PDO()->query("SELECT * FROM `lib` WHERE `id` = " . Vars::$ID . " AND `type` = 'cat' AND `ip` = '0'");
            if ($STH->rowCount()) {
                $row = array();
                $section = $STH->fetch();

                $STHA = DB::PDO()->query("SELECT * FROM `lib` WHERE `refid` = " . Vars::$ID . " AND `type` = 'bk' AND `moder` = '1' ORDER BY `time` ASC LIMIT " . ($this->page * $this->links_count) . ", " . $this->links_count);
                if ($STHA->rowCount()) {
                    while ($article = $STHA->fetch()) {
                        $row[] = '<a href="' . Vars::$HOME_URL . 'library/?id=' . $article['id'] . '">' . Functions::checkout($article['name']) . '</a>';
                    }
                    $out = '<div class="phdr"><b>' . __('library') . '</b> | ' . $section['text'] . '</div><div class="menu">' . implode('<br />' . "\r\n", $row) . '</div>';

                    return file_put_contents($file, $out) ? $out : 'Library Contents cache error';
                }
            }
        }

        return FALSE;
    }

    /**
     * Кэш карты Форума
     *
     * @return bool|string
     */
    private function mapForumContent()
    {
        $file = CACHEPATH . $this->cache_forum_file . '.dat';
        if (file_exists($file) && filemtime($file) > (time() - $this->cache_forum_map * 3600)) {
            return file_get_contents($file);
        } else {
            $STH = DB::PDO()->query("SELECT * FROM `forum` WHERE `type` = 'r'");
            if ($STH->rowCount()) {
                while ($result = $STH->fetch()) {
                    $count = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $result['id'] . "' AND `type` = 't' AND `close` != '1'")->fetchColumn();
                    if ($count) {
                        $text = html_entity_decode($result['text']);
                        $text = mb_substr($text, 0, 40);
                        $pages = ceil($count / $this->links_count);
                        if ($pages > 1) {
                            for ($i = 0; $i < $pages; $i++) {
                                //TODO: Доработать ссылку
                                $out[] = '<a href="' . Vars::$HOME_URL . 'forum/contents.php?id=' . $result['id'] . '&amp;p=' . $i . '">' . Functions::checkout($text) . ' (' . ($i + 1) . ')</a>';
                            }
                        } else {
                            //TODO: Доработать ссылку
                            $out[] = '<a href="' . Vars::$HOME_URL . 'forum/contents.php?id=' . $result['id'] . '">' . Functions::checkout($text) . '</a>';
                        }
                    }
                }
                if (isset($out))
                    return file_put_contents($file, implode('<br />' . "\r\n", $out)) ? implode('<br />', $out) : 'Forum cache error';
            }
        }

        return FALSE;
    }

    /**
     * Кэш карты Библиотеки
     *
     * @return bool|string
     */
    private function mapLibraryContent()
    {
        $file = CACHEPATH . $this->cache_lib_file . '.dat';
        if (file_exists($file) && filemtime($file) > (time() - $this->cache_lib_map * 3600)) {
            return file_get_contents($file);
        } else {
            $STH = DB::PDO()->query("SELECT * FROM `lib` WHERE `type` = 'cat' AND `ip` = '0'");
            if ($STH->rowCount()) {
                while ($result = $STH->fetch()) {
                    $count = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `refid` = '" . $result['id'] . "' AND `type` = 'bk' AND `moder` = '1'")->fetchColumn();
                    if ($count) {
                        $text = html_entity_decode($result['text']);
                        $text = mb_substr($text, 0, 40);
                        $pages = ceil($count / $this->links_count);
                        if ($pages > 1) {
                            for ($i = 0; $i < $pages; $i++) {
                                //TODO: Доработать ссылку
                                $out[] = '<a href="' . Vars::$HOME_URL . 'library/contents.php?id=' . $result['id'] . '&amp;p=' . $i . '">' . Functions::checkout($text) . ' (' . ($i + 1) . ')</a>';
                            }
                        } else {
                            //TODO: Доработать ссылку
                            $out [] = '<a href="../library/contents.php?id=' . $result['id'] . '">' . Functions::checkout($text) . '</a>';
                        }
                    }
                }
                if (isset($out))
                    return file_put_contents($file, implode('<br />' . "\r\n", $out)) ? implode('<br />', $out) : 'Library cache error';
            }
        }

        return FALSE;
    }
}