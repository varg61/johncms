<?php

/*
-----------------------------------------------------------------
Блок информации
-----------------------------------------------------------------
*/
echo'<div class="phdr"><b>' . lng('information') . '</b></div>' .
    $this->mp->news .
    '<div class="menu"><a href="' . Vars::$HOME_URL . '/news">' . lng('news_archive') . '</a> (' . $this->mp->newscount . ')</div>' .
    '<div class="menu"><a href="' . Vars::$HOME_URL . '/help">' . lng('information') . ', FAQ</a></div>' .
    '<div class="phdr"><b>' . lng('dialogue') . '</b></div>';

// Ссылка на гостевую
if (Vars::$SYSTEM_SET['mod_guest'] || Vars::$USER_RIGHTS >= 7) {
    echo'<div class="menu"><a href="guestbook/index.php">' . lng('guestbook') . '</a> (' .
        $this->count->guestbook .
        (Vars::$USER_RIGHTS ? '&#160;/&#160;<span class="red">' . $this->count->adminclub . '</span>' : '') .
        ')</div>';
}

// Ссылка на Форум
if (Vars::$SYSTEM_SET['mod_forum'] || Vars::$USER_RIGHTS >= 7) {
    $new_messages = Counters::forumMessagesNew();
    echo'<div class="menu"><a href="forum/">' . lng('forum') . '</a> (' .
        $this->count->forum_topics . '&#160;/&#160;' . $this->count->forum_messages .
        ($new_messages ? '&#160;/&#160;<span class="red">+' . $new_messages . '</span>' : '') .
        ')</div>';
}

echo'<div class="phdr"><b>' . lng('useful') . '</b></div>';

// Ссылка на загрузки
if (Vars::$SYSTEM_SET['mod_down'] || Vars::$USER_RIGHTS >= 7) {
    echo'<div class="menu"><a href="download/">' . lng('downloads') . '</a> (' .
        $this->count->downloads .
        ($this->count->downloads_new ? '&#160;/&#160;<span class="red">+' . $this->count->downloads_new . '</span>' : '') .
        ')</div>';
}

// Ссылка на библиотеку
if (Vars::$SYSTEM_SET['mod_lib'] || Vars::$USER_RIGHTS >= 7) {
    echo'<div class="menu"><a href="library/">' . lng('library') . '</a> (' .
        $this->count->library .
        ($this->count->library_new ? '&#160;/&#160;<span class="red">+' . $this->count->library_new . '</span>' : '') .
        ($this->count->library_mod ? '&#160;/&#160;<span class="red"><a href="' . Vars::$HOME_URL . '/library/index.php?act=moder">mod:' . $this->count->library_mod . '</a></span>' : '') .
        ')</div>';
}

// Ссылка на Галерею
if (Vars::$SYSTEM_SET['mod_gal'] || Vars::$USER_RIGHTS >= 7) {
    echo'<div class="menu"><a href="gallery/">' . lng('gallery') . '</a> (' .
        $this->count->gallery .
        ($this->count->gallery_new ? '&#160;/&#160;<span class="red">+' . $this->count->gallery_new . '</span>' : '') .
        ')</div>';
}

// Ссылки на пользователей и фотоальбомы
if (Vars::$USER_ID || Vars::$SYSTEM_SET['active']) {
    echo'<div class="phdr"><b>' . lng('community') . '</b></div>' .
        '<div class="menu"><a href="users">' . lng('users') . '</a> (' .
        $this->count->users . ($this->count->users_new ? '&#160;/&#160;<span class="red">+' . $this->count->users_new . '</span>' : '') .
        ')</div>' .
        '<div class="menu"><a href="album">' . lng('photo_albums') . '</a> (' .
        $this->count->album . '&#160;/&#160;' . $this->count->album_photo . ($this->count->album_photo_new ? '&#160;/&#160;<span class="red">+' . $this->count->album_photo_new . '</span>' : '') .
        ')</div>';
}

echo '<div class="phdr"><a href="http://gazenwagen.com">Gazenwagen</a></div>';