<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Restricted access');
class sitemap {
    // Настройки карты форума
    private $cache_forum_map = 72;               // Время кэширования карты форума (часов)
    private $cache_forum_contents = 48;          // Время кэширования оглавления форума (часов)
    private $cache_forum_file = 'map_forum.dat'; // Файл кэша карты Форума

    // Настройки карты Библиотеки
    private $cache_lib_map = 72;             // Время кэширования карты библиотеки (часов)
    private $cache_lib_contents = 48;        // Время кэширования оглавления библиотеки (часов)
    private $cache_lib_file = 'map_lib.dat'; // Файл кэша карты Библиотеки

    // Системные настройки
    private $links_count = 140; // Число ссылок в блоке
    private $set = array (
        'forum' => 1,
        'lib' => 1
    );

    /*
    -----------------------------------------------------------------
    Задаем настройки
    -----------------------------------------------------------------
    */
    function __construct() {
        global $set;

        if (isset($set['sitemap']))
            $this->set = unserialize($set['sitemap']);
    }

    /*
    -----------------------------------------------------------------
    Карта сайта
    -----------------------------------------------------------------
    */
    public function site() { return ($this->set['forum'] ? '<p><b>Forum Map</b><br />' . $this->cache_forum() . '</p>' : '') . ($this->set['lib'] ? '<p><b>Library Map</b><br />' . $this->cache_lib() . '</p>' : ''); }

    /*
    -----------------------------------------------------------------
    Кэш карты Форума
    -----------------------------------------------------------------
    */
    private function cache_forum() {
        global $rootpath, $realtime, $set;
        $file = $rootpath . 'files/cache/' . $this->cache_forum_file;

        if (file_exists($file) && filemtime($file) > ($realtime - $this->cache_forum_map * 3600)) {
            return file_get_contents($file);
        } else {
            $out = '';
            $req = mysql_query("SELECT * FROM `forum` WHERE `type` = 'r'");
            if (mysql_num_rows($req)) {
                while ($res = mysql_fetch_assoc($req)) {
                    $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['id'] . "' AND `type` = 't' AND `close` != '1'"), 0);
                    if ($count) {
                        $text = html_entity_decode($res['text']);
                        $text = mb_substr($text, 0, 40);
                        $pages = ceil($count / $this->links_count);
                        if ($pages > 1) {
                            for ($i = 0; $i < $pages; $i++) {
                                $out .= '<div><a href="' . $set['homeurl'] . '/forum/contents.php?id=' . $res['id'] . '&amp;p=' . $i . '">' . functions::checkout($text) . ' (' . ($i + 1) . ')</a></div>' . "\r\n";
                            }
                        } else {
                            $out .= '<div><a href="' . $set['homeurl'] . '/forum/contents.php?id=' . $res['id'] . '">' . functions::checkout($text) . '</a></div>' . "\r\n";
                        }
                    }
                }
                if (!file_put_contents($file, $out))
                    return 'Forum cache write error';
            }
            return $out;
        }
    }

    /*
    -----------------------------------------------------------------
    Кэш карты Библиотеки
    -----------------------------------------------------------------
    */
    private function cache_lib() {
        global $rootpath, $realtime, $set;
        $file = $rootpath . 'files/cache/' . $this->cache_lib_file;

        if (file_exists($file) && filemtime($file) > ($realtime - 604800)) {
            return file_get_contents($file);
        } else {
            $out = '';
            $req = mysql_query("SELECT * FROM `lib` WHERE `type` = 'cat' AND `ip` = '0'");
            if (mysql_num_rows($req)) {
                while ($res = mysql_fetch_assoc($req)) {
                    $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `refid` = '" . $res['id'] . "' AND `type` = 'bk' AND `moder` = '1'"), 0);
                    if ($count) {
                        $text = html_entity_decode($res['text']);
                        $text = mb_substr($text, 0, 40);
                        $pages = ceil($count / $this->links_count);
                        if ($pages > 1) {
                            for ($i = 0; $i < $pages; $i++) {
                                $out .= '<div><a href="' . $set['homeurl'] . '/library/contents.php?id=' . $res['id'] . '&amp;p=' . $i . '">' . functions::checkout($text) . ' (' . ($i + 1) . ')</a></div>' . "\r\n";
                            }
                        } else {
                            $out .= '<div><a href="../library/contents.php?id=' . $res['id'] . '">' . functions::checkout($text) . '</a></div>' . "\r\n";
                        }
                    }
                }
                if (!file_put_contents($file, $out))
                    return 'Library cache write error';
            }
            return $out;
        }
    }
}
?>