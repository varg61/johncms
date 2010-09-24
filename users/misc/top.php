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

defined('_IN_JOHNCMS') or die('Error: restricted access');

$headmod = 'userstop';
$textl = $lng['users_top'];
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
    (!$mod ? '<b>' . $lng['forum'] . '</b>' : '<a href="index.php?act=top">' . $lng['forum'] . '</a>'),
    ($mod == 'guest' ? '<b>' . $lng['guestbook'] . '</b>' : '<a href="index.php?act=top&amp;mod=guest">' . $lng['guestbook'] . '</a>'),
    ($mod == 'chat' ? '<b>' . $lng['chat'] . '</b>' : '<a href="index.php?act=top&amp;mod=chat">' . $lng['chat'] . '</a>'),
    ($mod == 'vic' ? '<b>' . $lng['quiz'] . '</b>' : '<a href="index.php?act=top&amp;mod=vic">' . $lng['quiz'] . '</a>'),
    ($mod == 'bal' ? '<b>' . $lng['balance'] . '</b>' : '<a href="index.php?act=top&amp;mod=bal">' . $lng['balance'] . '</a>'),
    ($mod == 'comm' ? '<b>' . $lng['comments'] . '</b>' : '<a href="index.php?act=top&amp;mod=comm">' . $lng['comments'] . '</a>')
);
if($set_karma['on'])
    $menu[] =  $act == 'karma' ? '<b>' . $lng['karma'] . '</b>' : '<a href="index.php?act=top&amp;mod=karma">' . $lng['karma'] . '</a>';


switch ($mod) {
    case 'guest':
        /*
        -----------------------------------------------------------------
        Топ Гостевой
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>' . $lng['community'] . '</b></a> | ' . $lng['top_guest'] . '</div>';
        echo '<div class="topmenu">' . display_menu($menu) . '</div>';
        echo get_top('postguest');
        echo '<div class="phdr"><a href="../guestbook/index.php">' . $lng['guestbook'] . '</a></div>';
        break;

    case 'chat':
        /*
        -----------------------------------------------------------------
        Топ Чата
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>' . $lng['community'] . '</b></a> | ' . $lng['top_chat'] . '</div>';
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
        echo '<div class="phdr"><a href="index.php"><b>' . $lng['community'] . '</b></a> | ' . $lng['top_quiz'] . '</div>';
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
        echo '<div class="phdr"><a href="index.php"><b>' . $lng['community'] . '</b></a> | ' . $lng['top_bal'] . '</div>';
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
        echo '<div class="phdr"><a href="index.php"><b>' . $lng['community'] . '</b></a> | ' . $lng['top_comm'] . '</div>';
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
            echo '<div class="phdr"><a href="index.php"><b>' . $lng['community'] . '</b></a> | ' . $lng['top_karma'] . '</div>';
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
        echo '<div class="phdr"><a href="index.php"><b>' . $lng['community'] . '</b></a> | ' . $lng['top_forum'] . '</div>';
        echo '<div class="topmenu">' . display_menu($menu) . '</div>';
        echo get_top('postforum');
        echo '<div class="phdr"><a href="../forum/index.php">' . $lng['forum'] . '</a></div>';
}
echo '<p><a href="index.php">' . $lng['back'] . '</a></p>';

?>