<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

$tpl = Template::getInstance();
$tpl->template = FALSE;

header('content-type: application/rss+xml');
echo '<?xml version="1.0" encoding="utf-8"?>' .
    '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/"><channel>' .
    '<title>' . htmlspecialchars(Vars::$SYSTEM_SET['copyright']) . ' | News</title>' .
    '<link>' . Vars::$HOME_URL . '</link>' .
    '<description>News</description>' .
    '<language>ru-RU</language>' .
    '<webMaster>' . Vars::$SYSTEM_SET['email'] . '</webMaster> ';

// Новости
$req = DB::PDO()->query('SELECT * FROM `news` ORDER BY `id` DESC LIMIT 15');
if ($req->rowCount()) {
    while ($res = $req->fetch()) {
        echo '<item>' .
            '<title>News: ' . $res['name'] . '</title>' .
            '<link>' . Vars::$HOME_URL . 'news/</link>' .
            '<author>' . $res['avt'] . '</author>' .
            '<description>' . $res['text'] . '</description>' .
            '<pubDate>' . date('r', $res['time']) .
            '</pubDate>' .
            '</item>';
    }
}

// Библиотека
$req = DB::PDO()->query("SELECT * FROM `lib` WHERE `type` = 'bk' AND `moder` = '1' ORDER BY `time` DESC LIMIT 15");
if ($req->rowCount()) {
    while ($res = $req->fetch()) {
        echo '<item>' .
            '<title>Library: ' . $res['name'] . '</title>' .
            '<link>' . Vars::$HOME_URL . 'library/?id=' . $res['id'] . '</link>' .
            '<author>' . $res['avtor'] . '</author>' .
            '<description>' . $res['announce'] .
            '</description>' .
            '<pubDate>' . date('r', $res['time']) . '</pubDate>' .
            '</item>';
    }
}
echo '</channel></rss>';