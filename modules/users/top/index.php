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

/*
-----------------------------------------------------------------
Закрываем от неавторизованных юзеров
-----------------------------------------------------------------
*/
if (!Vars::$USER_ID && !Vars::$SYSTEM_SET['active']) {
    echo Functions::displayError(lng('access_guest_forbidden'));
    exit;
}

/*
-----------------------------------------------------------------
Функция отображения списков
-----------------------------------------------------------------
*/
function get_top($order = 'postforum')
{
    $req = mysql_query("SELECT * FROM `users` WHERE `$order` > 0 ORDER BY `$order` DESC LIMIT 9");
    if (mysql_num_rows($req)) {
        $out = '';
        for ($i = 0; $res = mysql_fetch_assoc($req); ++$i) {
            $out .= $i % 2 ? '<div class="list2">' : '<div class="list1">';
            $out .= Functions::displayUser($res, array('header' => ('<b>' . $res[$order]) . '</b>')) . '</div>';
        }
        return $out;
    } else {
        return '<div class="menu"><p>' . lng('list_empty') . '</p></div>';
    }
}

/*
-----------------------------------------------------------------
Меню выбора
-----------------------------------------------------------------
*/
$menu = array(
    (!Vars::$ACT ? '<b>' . lng('forum') . '</b>' : '<a href="' . Vars::$URI . '">' . lng('forum') . '</a>'),
    (Vars::$ACT == 'comm' ? '<b>' . lng('comments') . '</b>' : '<a href="' . Vars::$URI . '?act=comm">' . lng('comments') . '</a>')
);

//TODO: Добавить ТОП Кармы

switch (Vars::$ACT) {
    case 'comm':
        /*
        -----------------------------------------------------------------
        Топ комментариев
        -----------------------------------------------------------------
        */
        echo'<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . lng('community') . '</b></a> | ' . lng('top_comm') . '</div>' .
            '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>' .
            get_top('count_comments') .
            '<div class="phdr"><a href="' . Vars::$HOME_URL . '">' . lng('homepage') . '</a></div>';
        break;

    case 'karma':
        /*
        -----------------------------------------------------------------
        Топ Кармы
        -----------------------------------------------------------------
        */
        if ($set_karma['on']) {
            echo'<div class="phdr"><a href="index.php"><b>' . lng('community') . '</b></a> | ' . lng('top_karma') . '</div>' .
                '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';
            $req = mysql_query("SELECT *, (`karma_plus` - `karma_minus`) AS `karma` FROM `users` WHERE (`karma_plus` - `karma_minus`) > 0 ORDER BY `karma` DESC LIMIT 9");
            if (mysql_num_rows($req)) {
                $i = 0;
                while ($res = mysql_fetch_assoc($req)) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo Functions::displayUser($res, array('header' => ('<b>' . $res['karma']) . '</b>')) . '</div>';
                    ++$i;
                }
            } else {
                echo '<div class="menu"><p>' . lng('list_empty') . '</p></div>';
            }
            echo '<div class="phdr"><a href="../index.php">' . lng('homepage') . '</a></div>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Топ Форума
        -----------------------------------------------------------------
        */
        echo'<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . lng('community') . '</b></a> | ' . lng('top_forum') . '</div>' .
            '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>' .
            get_top('count_forum') .
            '<div class="phdr"><a href="' . Vars::$HOME_URL . '/forum">' . lng('forum') . '</a></div>';
}
echo '<p><a href="' . Vars::$MODULE_URI . '">' . lng('back') . '</a></p>';