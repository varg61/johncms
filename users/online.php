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

define('_IN_JOHNCMS', 1);
$headmod = 'online';
require('../incfiles/core.php');
$textl = $lng['online'];
$lng_online = load_lng('online');
require('../incfiles/head.php');

/*
-----------------------------------------------------------------
Показываем список Online
-----------------------------------------------------------------
*/
echo '<div class="phdr"><b>' . $lng_online['who_on_site'] . '</b></div>';
if ($rights > 0)
    echo '<div class="topmenu">' . ($act == 'guest' ? '<a href="online.php">' . $lng['authorized'] . '</a> | ' . $lng['guests'] : $lng['authorized'] . ' | <a href="online.php?act=guest">' . $lng['guests'] . '</a>') . '</div>';
$onltime = $realtime - 300;
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . ($act == 'guest' ? 'cms_guests' : 'users') . "` WHERE `lastdate` > '$onltime'"), 0);
if(!$user_id)
    $total = 0;
if ($total) {
    $req = mysql_query("SELECT * FROM `" . ($act == 'guest' ? 'cms_guests' : 'users') . "` WHERE `lastdate` > '$onltime' ORDER BY " . ($act == 'guest' ? "`movings` DESC" : "`name` ASC") . " LIMIT $start,$kmess");
    while ($res = mysql_fetch_assoc($req)) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        if ($user_id) {
            // Вычисляем местоположение
            $where = explode(",", $res['place']);
            switch ($where[0]) {
                case 'forumfiles':
                    $place = '<a href="../forum/index.php?act=files">' . $lng_online['where_forum_files'] . '</a>';
                    break;

                case 'forumwho':
                    $place = '<a href="../forum/index.php?act=who">' . $lng_online['where_forum_who'] . '</a>';
                    break;

                case 'anketa':
                    $place = '<a href="profile/index.php">' . $lng_online['where_profile'] . '</a>';
                    break;

                case 'settings':
                    $place = '<a href="usset.php">' . $lng_online['where_settings'] . '</a>';
                    break;

                case 'users':
                    $place = '<a href="users.php">' . $lng_online['where_users_list'] . '</a>';
                    break;

                case 'online':
                    $place = $lng_online['where_here'];
                    break;

                case 'privat':
                case 'pradd':
                    $place = '<a href="my_cabinet.php">' . $lng_online['where_pm'] . '</a>';
                    break;

                case 'birth':
                    $place = '<a href="brd.php">' . $lng_online['where_birth'] . '</a>';
                    break;

                case 'read':
                    $place = '<a href="faq.php">' . $lng_online['where_faq'] . '</a>';
                    break;

                case 'load':
                    $place = '<a href="../download/index.php">' . $lng_online['where_downloads'] . '</a>';
                    break;

                case 'gallery':
                    $place = '<a href="../gallery/index.php">' . $lng_online['where_gallery'] . '</a>';
                    break;

                case 'forum':
                case 'forums':
                    $place = '<a href="../forum/index.php">' . $lng_online['where_forum'] . '</a>&#160;/&#160;<a href="../forum/index.php?act=who">&gt;&gt;</a>';
                    break;

                case 'chat':
                    $place = '<a href="../chat/index.php">' . $lng_online['where_chat'] . '</a>';
                    break;

                case 'guest':
                    $place = '<a href="../guestbook/index.php">' . $lng_online['where_guestbook'] . '</a>';
                    break;

                case 'lib':
                    $place = '<a href="../library/index.php">' . $lng_online['where_library'] . '</a>';
                    break;

                case 'mainpage':
                default:
                    $place = '<a href="../index.php">' . $lng_online['where_homepage'] . '</a>';
                    break;
            }
        }
        $arg = array (
            'stshide' => 1,
            'header' => (' (' . $res['movings'] . ' - ' . timecount($realtime - $res['sestime']) . ')<br /><img src="../images/info.png" width="16" height="16" align="middle" />&#160;' . $place)
        );
        echo display_user($res, $arg);
        echo '</div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
}
echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
if ($total > $kmess) {
    echo '<p>' . display_pagination('online.php?' . ($act == 'guest' ? 'act=guest&amp;' : ''), $start, $total, $kmess) . '</p>';
    echo '<p><form action="online.php" method="get">' .
        '<input type="text" name="page" size="2"/>' . ($act == 'guest' ? '<input type="hidden" value="guest" name="act" />' : '') .
        '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
        '</form></p>';
}

require('../incfiles/end.php');
?>