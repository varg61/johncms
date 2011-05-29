<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
$headmod = 'online';
$textl = $lng['online'];
$lng_online = core::load_lng('online');
require('../incfiles/head.php');

/*
-----------------------------------------------------------------
Показываем список Online
-----------------------------------------------------------------
*/
$menu[] = !$mod ? '<b>' . $lng['users'] . '</b>' : '<a href="index.php?act=online">' . $lng['users'] . '</a>';
$menu[] = $mod == 'history' ? '<b>' . $lng['history'] . '</b>' : '<a href="index.php?act=online&amp;mod=history">' . $lng['history'] . '</a> ';
if (core::$user_rights) $menu[] = $mod == 'guest' ? '<b>' . $lng['guests'] . '</b>' : '<a href="index.php?act=online&amp;mod=guest">' . $lng['guests'] . '</a>';
echo '<div class="phdr"><b>' . $lng_online['who_on_site'] . '</b></div>' .
     '<div class="topmenu">' . functions::display_menu($menu) . '</div>';

switch ($mod) {
    case 'guest':
        // Список гостей Онлайн
        $sql_total = "SELECT COUNT(*) FROM `cms_sessions` WHERE `lastdate` > " . (time() - 300);
        $sql_list = "SELECT * FROM `cms_sessions` WHERE `lastdate` > " . (time() - 300) . " ORDER BY `movings` DESC LIMIT $start, $kmess";
        break;

    case 'history':
        // История посетилелей за последние 2 суток
        $sql_total = "SELECT COUNT(*) FROM `users` WHERE `lastdate` > " . (time() - 172800 . " AND `lastdate` < " . (time() - 300));
        $sql_list = "SELECT * FROM `users` WHERE `lastdate` > " . (time() - 172800) . " AND `lastdate` < " . (time() - 300) . " ORDER BY `sestime` DESC LIMIT $start, $kmess";
        break;

    default:
        // Список посетителей Онлайн
        $sql_total = "SELECT COUNT(*) FROM `users` WHERE `lastdate` > " . (time() - 300);
        $sql_list = "SELECT * FROM `users` WHERE `lastdate` > " . (time() - 300) . " ORDER BY `name` ASC LIMIT $start, $kmess";
}

$total = mysql_result(mysql_query($sql_total), 0);
if ($total) {
    $req = mysql_query($sql_list);
    $i = 0;
    while (($res = mysql_fetch_assoc($req)) !== false) {
        if ($res['id'] == core::$user_id) echo '<div class="gmenu">';
        else echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        $where = explode(",", $res['place']);
        // Список возможных местоположений
        $places = array(
            'admlist' => '<a href="index.php?act=admlist">' . $lng_online['where_adm_list'] . '</a>',
            'album' => '<a href="album.php">' . $lng_online['where_album'] . '</a>',
            'birth' => '<a href="index.php?act=birth">' . $lng_online['where_birth'] . '</a>',
            'downloads' => '<a href="../download/index.php">' . $lng_online['where_downloads'] . '</a>',
            'faq' => '<a href="../pages/faq.php">' . $lng_online['where_faq'] . '</a>',
            'forum' => '<a href="../forum/index.php">' . $lng_online['where_forum'] . '</a>&#160;/&#160;<a href="../forum/index.php?act=who">&gt;&gt;</a>',
            'forumfiles' => '<a href="../forum/index.php?act=files">' . $lng_online['where_forum_files'] . '</a>',
            'forumwho' => '<a href="../forum/index.php?act=who">' . $lng_online['where_forum_who'] . '</a>',
            'gallery' => '<a href="../gallery/index.php">' . $lng_online['where_gallery'] . '</a>',
            'guest' => '<a href="../guestbook/index.php">' . $lng_online['where_guestbook'] . '</a>',
            'library' => '<a href="../library/index.php">' . $lng_online['where_library'] . '</a>',
            'news' => '<a href="../news/index.php">' . $lng_online['where_news'] . '</a>',
            'online' => $lng_online['where_here'],
            'pm' => $lng_online['where_pm'],
            'profile' => '<a href="profile.php">' . $lng_online['where_profile'] . '</a>',
            'userlist' => '<a href="index.php?act=userlist">' . $lng_online['where_users_list'] . '</a>',
            'users' => '<a href="index.php">' . $lng['community'] . '</a>',
            'userstop' => '<a href="index.php?act=top">' . $lng_online['where_users_top'] . '</a>'
        );
        // Вычисляем местоположение
        $place = array_key_exists($where[0], $places) ? $places[$where[0]] : '<a href="../index.php">' . $lng_online['where_homepage'] . '</a>';
        $arg['stshide'] = 1;
        $arg['header'] = ' <span class="gray">(';
        if($mod == 'history') $arg['header'] .= functions::display_date($res['sestime']);
        else $arg['header'] .= $res['movings'] . ' - ' . functions::timecount(time() - $res['sestime']);
        $arg['header'] .= ')</span><br /><img src="../images/info.png" width="16" height="16" align="middle" />&#160;' . $place;
        echo functions::display_user($res, $arg);
        echo '</div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
}
echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
if ($total > $kmess) {
    echo '<p>' . functions::display_pagination('index.php?act=online&amp;' . ($mod ? 'mod=' . $mod . '&amp;'
                          : ''), $start, $total, $kmess) . '</p>';
    echo '<p><form action="index.php?act=online' . ($mod == 'guest' ? '&amp;mod=guest' : '') . '" method="post">' .
         '<input type="text" name="page" size="2"/>' .
         '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
         '</form></p>';
}
?>