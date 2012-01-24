<?php

echo '<div class="phdr"><b>' . Vars::$LNG['digest'] . '</b></div>';
echo '<div class="gmenu"><p>' . Vars::$LNG['hi'] . ', <b>' . Vars::$USER_DATA['nickname'] . '</b><br/>' . Vars::$LNG['welcome_to'] . ' ' . Vars::$SYSTEM_SET['copyright'] . '!<br /><a href="index.php">' . Vars::$LNG['enter_on_site'] . '</a></p></div>';
// Дайджест Администратора
if (Vars::$USER_RIGHTS) {
    echo '<div class="menu"><p><h3>' . Vars::$LNG['administrative_events'] . '</h3><ul>';
    if ($count->users_new)
        echo '<li><a href="users/index.php?act=userlist">' . Vars::$LNG['users_new'] . '</a> (' . $count->users_new . ')</li>';
    if ($reg_total > 0 && Vars::$USER_RIGHTS >= 7)
        echo '<li><a href="' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=reg">' . Vars::$LNG['users_on_reg'] . '</a> (' . $reg_total . ')</li>';
    if ($ban_total)
        echo '<li><a href="' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=ban_panel">' . Vars::$LNG['users_on_ban'] . '</a> (' . $ban_total . ')</li>';
    if ($count->library_mod && Vars::$USER_RIGHTS >= 6)
        echo '<li><a href="library/index.php?act=moder">' . Vars::$LNG['library_on_moderation'] . '</a> (' . $count->library_mod . ')</li>';
    if ($total_admin)
        echo '<li><a href="guestbook/index.php?act=ga&amp;do=set">' . Vars::$LNG['admin_club'] . '</a> (' . $total_admin . ')</li>';
    if (!$count->users_new && !$reg_total && !$ban_total && !$count->library_mod && !$total_admin)
        echo '<li>' . Vars::$LNG['events_no_new'] . '</li>';
    echo '</ul></p></div>';
}
// Дайджест юзеров
echo '<div class="menu"><p><h3>' . Vars::$LNG['site_new'] . '</h3><ul>';
if ($total_news)
    echo '<li><a href="news/index.php">' . Vars::$LNG['news'] . '</a> (' . $total_news . ')</li>';
if ($total_forum)
    echo '<li><a href="forum/index.php?act=new">' . Vars::$LNG['forum'] . '</a> (' . $total_forum . ')</li>';
if ($total_guest)
    echo '<li><a href="guestbook/index.php?act=ga">' . Vars::$LNG['guestbook'] . '</a> (' . $total_guest . ')</li>';
if ($count->gallery_new)
    echo '<li><a href="gallery/index.php?act=new">' . Vars::$LNG['gallery'] . '</a> (' . $count->gallery_new . ')</li>';
if ($total_lib)
    echo '<li><a href="library/index.php?act=new">' . Vars::$LNG['library'] . '</a> (' . $total_lib . ')</li>';
if ($total_album) echo '<li><a href="users/album.php?act=top">' . Vars::$LNG['photo_albums'] . '</a> (' . $total_album . ')</li>';
// Если нового нет, выводим сообщение
if (!$total_news && !$total_forum && !$total_guest && !$count->gallery_new && !$total_lib && !$total_karma)
    echo '<li>' . Vars::$LNG['events_no_new'] . '</li>';
// Дата последнего посещения
echo '</ul></p></div><div class="phdr">' . Vars::$LNG['last_visit'] . ': ' . date("d.m.Y (H:i)", $last) . '</div>';