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

$mp = new HomePage();
$count = new Counters();

/*
-----------------------------------------------------------------
Блок информации
-----------------------------------------------------------------
*/
echo'<div class="phdr"><b>' . Vars::$LNG['information'] . '</b></div>';
echo $mp->news;
echo'<div class="menu"><a href="news/index.php">' . Vars::$LNG['news_archive'] . '</a> (' . $mp->newscount . ')</div>' .
    '<div class="menu"><a href="pages/faq.php">' . Vars::$LNG['information'] . ', FAQ</a></div>';

echo'<div class="phdr"><b>' . Vars::$LNG['dialogue'] . '</b></div>';

// Ссылка на гостевую
if (Vars::$SYSTEM_SET['mod_guest'] || Vars::$USER_RIGHTS >= 7) {
    echo'<div class="menu"><a href="guestbook/index.php">' . Vars::$LNG['guestbook'] . '</a> (' .
        $count->guestbook .
        (Vars::$USER_RIGHTS ? '&#160;/&#160;<span class="red">' . $count->adminclub . '</span>' : '') .
        ')</div>';
}

// Ссылка на Форум
if (Vars::$SYSTEM_SET['mod_forum'] || Vars::$USER_RIGHTS >= 7) {
    $new_messages = Counters::forumMessagesNew();
    echo'<div class="menu"><a href="forum/">' . Vars::$LNG['forum'] . '</a> (' .
        $count->forum_topics . '&#160;/&#160;' . $count->forum_messages .
        ($new_messages ? '&#160;/&#160;<span class="red">+' . $new_messages . '</span>' : '') .
        ')</div>';
}

echo'<div class="phdr"><b>' . Vars::$LNG['useful'] . '</b></div>';

// Ссылка на загрузки
if (Vars::$SYSTEM_SET['mod_down'] || Vars::$USER_RIGHTS >= 7) {
    echo'<div class="menu"><a href="download/">' . Vars::$LNG['downloads'] . '</a> (' .
        $count->downloads .
        ($count->downloads_new ? '&#160;/&#160;<span class="red">+' . $count->downloads_new . '</span>' : '') .
        ')</div>';
}

// Ссылка на библиотеку
if (Vars::$SYSTEM_SET['mod_lib'] || Vars::$USER_RIGHTS >= 7) {
    echo'<div class="menu"><a href="library/">' . Vars::$LNG['library'] . '</a> (' .
        $count->library .
        ($count->library_new ? '&#160;/&#160;<span class="red">+' . $count->library_new . '</span>' : '') .
        ($count->library_mod ? '&#160;/&#160;<span class="red"><a href="' . Vars::$SYSTEM_SET['homeurl'] . '/library/index.php?act=moder">mod:' . $count->library_mod . '</a></span>' : '') .
        ')</div>';
}

// Ссылка на Галерею
if (Vars::$SYSTEM_SET['mod_gal'] || Vars::$USER_RIGHTS >= 7) {
    echo'<div class="menu"><a href="gallery/">' . Vars::$LNG['gallery'] . '</a> (' .
        $count->gallery .
        ($count->gallery_new ? '&#160;/&#160;<span class="red">+' . $count->gallery_new . '</span>' : '') .
        ')</div>';
}

// Ссылки на пользователей и фотоальбомы
if (Vars::$USER_ID || Vars::$SYSTEM_SET['active']) {
    echo'<div class="phdr"><b>' . Vars::$LNG['community'] . '</b></div>' .
        '<div class="menu"><a href="users/index.php">' . Vars::$LNG['users'] . '</a> (' .
        $count->users . ($count->users_new ? '&#160;/&#160;<span class="red">+' . $count->users_new . '</span>' : '') .
        ')</div>' .
        '<div class="menu"><a href="users/album.php">' . Vars::$LNG['photo_albums'] . '</a> (' .
        $count->album . '&#160;/&#160;' . $count->album_photo . ($count->album_photo_new ? '&#160;/&#160;<span class="red">+' . $count->album_photo_new . '</span>' : '') .
        ')</div>';
}

echo '<div class="phdr"><a href="http://gazenwagen.com">Gazenwagen</a></div>';