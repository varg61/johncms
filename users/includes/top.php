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

$headmod = 'userstop';
$textl = Vars::$LNG['users_top'];
require_once('../includes/head.php');

/*
-----------------------------------------------------------------
Функция отображения списков
-----------------------------------------------------------------
*/
function get_top($order = 'postforum')
{
    global $lng;
    $req = mysql_query("SELECT * FROM `cms_user` WHERE `$order` > 0 ORDER BY `$order` DESC LIMIT 9");
    if (mysql_num_rows($req)) {
        $out = '';
        $i = 0;
        while ($res = mysql_fetch_assoc($req)) {
            $out .= $i % 2 ? '<div class="list2">' : '<div class="list1">';
            $out .= Functions::displayUser($res, array('header' => ('<b>' . $res[$order]) . '</b>')) . '</div>';
            ++$i;
        }
        return $out;
    } else {
        return '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
    }
}

/*
-----------------------------------------------------------------
Меню выбора
-----------------------------------------------------------------
*/
$menu = array(
    (!Vars::$MOD ? '<b>' . Vars::$LNG['forum'] . '</b>' : '<a href="index.php?act=top">' . Vars::$LNG['forum'] . '</a>'),
    (Vars::$MOD == 'guest' ? '<b>' . Vars::$LNG['guestbook'] . '</b>' : '<a href="index.php?act=top&amp;mod=guest">' . Vars::$LNG['guestbook'] . '</a>'),
    (Vars::$MOD == 'comm' ? '<b>' . Vars::$LNG['comments'] . '</b>' : '<a href="index.php?act=top&amp;mod=comm">' . Vars::$LNG['comments'] . '</a>')
);
if ($set_karma['on'])
    $menu[] = Vars::$MOD == 'karma' ? '<b>' . Vars::$LNG['karma'] . '</b>' : '<a href="index.php?act=top&amp;mod=karma">' . Vars::$LNG['karma'] . '</a>';
switch (Vars::$MOD) {
    case 'guest':
        /*
        -----------------------------------------------------------------
        Топ Гостевой
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['community'] . '</b></a> | ' . Vars::$LNG['top_guest'] . '</div>';
        echo '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
        echo get_top('postguest');
        echo '<div class="phdr"><a href="../guestbook/index.php">' . Vars::$LNG['guestbook'] . '</a></div>';
        break;

    case 'comm':
        /*
        -----------------------------------------------------------------
        Топ комментариев
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['community'] . '</b></a> | ' . Vars::$LNG['top_comm'] . '</div>';
        echo '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
        echo get_top('komm');
        echo '<div class="phdr"><a href="../index.php">' . Vars::$LNG['homepage'] . '</a></div>';
        break;

    case 'karma':
        /*
        -----------------------------------------------------------------
        Топ Кармы
        -----------------------------------------------------------------
        */
        if ($set_karma['on']) {
            echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['community'] . '</b></a> | ' . Vars::$LNG['top_karma'] . '</div>';
            echo '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
            $req = mysql_query("SELECT *, (`karma_plus` - `karma_minus`) AS `karma` FROM `users` WHERE (`karma_plus` - `karma_minus`) > 0 ORDER BY `karma` DESC LIMIT 9");
            if (mysql_num_rows($req)) {
                $i = 0;
                while ($res = mysql_fetch_assoc($req)) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo Functions::displayUser($res, array('header' => ('<b>' . $res['karma']) . '</b>')) . '</div>';
                    ++$i;
                }
            } else {
                echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
            }
            echo '<div class="phdr"><a href="../index.php">' . Vars::$LNG['homepage'] . '</a></div>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Топ Форума
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['community'] . '</b></a> | ' . Vars::$LNG['top_forum'] . '</div>';
        echo '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
        echo get_top('postforum');
        echo '<div class="phdr"><a href="../forum/index.php">' . Vars::$LNG['forum'] . '</a></div>';
}
echo '<p><a href="index.php">' . Vars::$LNG['back'] . '</a></p>';