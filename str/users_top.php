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

$headmod = 'sitetop';
require('../incfiles/core.php');
$textl = $lng['users_top'];
$lng_stat = load_lng('stat');
require('../incfiles/head.php');

/*
-----------------------------------------------------------------
Функция отображения списков
-----------------------------------------------------------------
*/
function get_top($order = 'postforum') {
    $req = mysql_query("SELECT * FROM `users` WHERE `$order` > 0 ORDER BY `$order` DESC LIMIT 9");
    if (mysql_num_rows($req)) {
        $out = '';
        while ($res = mysql_fetch_assoc($req)) {
            $out .= $i % 2 ? '<div class="list2">' : '<div class="list1">';
            $out .= display_user($res, array('header' => ('<b>' . $res[$order]) . '</b>')) . '</div>';
            ++$i;
        }
        return $out;
    } else {
        return '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
    }
}

/*
-----------------------------------------------------------------
Меню выбора
-----------------------------------------------------------------
*/
$menu = array(
    (!$act ? '<b>' . $lng['forum'] . '</b>' : '<a href="users_top.php">' . $lng['forum'] . '</a>'),
    ($act == 'guest' ? '<b>' . $lng['guestbook'] . '</b>' : '<a href="users_top.php?act=guest">' . $lng['guestbook'] . '</a>'),
    ($act == 'chat' ? '<b>' . $lng['chat'] . '</b>' : '<a href="users_top.php?act=chat">' . $lng['chat'] . '</a>'),
    ($act == 'vic' ? '<b>' . $lng['quiz'] . '</b>' : '<a href="users_top.php?act=vic">' . $lng['quiz'] . '</a>'),
    ($act == 'bal' ? '<b>' . $lng['balance'] . '</b>' : '<a href="users_top.php?act=bal">' . $lng['balance'] . '</a>'),
    ($act == 'comm' ? '<b>' . $lng['comments'] . '</b>' : '<a href="users_top.php?act=comm">' . $lng['comments'] . '</a>')
);
if($set_karma['on'])
    $menu[] =  $act == 'karma' ? '<b>' . $lng['karma'] . '</b>' : '<a href="users_top.php?act=karma">' . $lng['karma'] . '</a>';


switch ($act) {
    case 'guest':
        /*
        -----------------------------------------------------------------
        Топ Гостевой
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . $lng_stat['top_guest'] . '</b></div>';
        echo '<div class="topmenu">' . display_menu($menu) . '</div>';
        echo get_top('postguest');
        echo '<div class="phdr"><a href="../str/guest.php">' . $lng['guestbook'] . '</a></div>';
        break;

    case 'chat':
        /*
        -----------------------------------------------------------------
        Топ Чата
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . $lng_stat['top_chat'] . '</b></div>';
        echo '<div class="topmenu">' . display_menu($menu) . '</div>';
        echo get_top('postchat');
        echo '<div class="phdr"><a href="../chat/index.php">' . $lng['chat'] . '</a></div>';
        break;

    case 'vic':
        /*
        -----------------------------------------------------------------
        Топ Викторины
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . $lng_stat['top_quiz'] . '</b></div>';
        echo '<div class="topmenu">' . display_menu($menu) . '</div>';
        echo get_top('otvetov');
        echo '<div class="phdr"><a href="../chat/index.php">' . $lng['chat'] . '</a></div>';
        break;

    case 'bal':
        /*
        -----------------------------------------------------------------
        Топ игрового баланса
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . $lng_stat['top_bal'] . '</b></div>';
        echo '<div class="topmenu">' . display_menu($menu) . '</div>';
        echo get_top('balans');
        echo '<div class="phdr"><a href="../index.php">' . $lng['homepage'] . '</a></div>';
        break;

    case 'comm':
        /*
        -----------------------------------------------------------------
        Топ комментариев
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . $lng_stat['top_comm'] . '</b></div>';
        echo '<div class="topmenu">' . display_menu($menu) . '</div>';
        echo get_top('komm');
        echo '<div class="phdr"><a href="../index.php">' . $lng['homepage'] . '</a></div>';
        break;

    case 'karma':
        /*
        -----------------------------------------------------------------
        Топ Кармы
        -----------------------------------------------------------------
        */
        if ($set_karma['on']) {
            echo '<div class="phdr"><b>' . $lng_stat['top_karma'] . '</b></div>';
            echo '<div class="topmenu">' . display_menu($menu) . '</div>';
            echo get_top('karma');
            echo '<div class="phdr"><a href="../index.php">' . $lng['homepage'] . '</a></div>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Топ Форума
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . $lng_stat['top_forum'] . '</b></div>';
        echo '<div class="topmenu">' . display_menu($menu) . '</div>';
        echo get_top('postforum');
        echo '<div class="phdr"><a href="../forum/index.php">' . $lng['forum'] . '</a></div>';
}
echo '<p><a href="../index.php?act=users">' . $lng['community'] . '</a></p>';
require('../incfiles/end.php');

?>