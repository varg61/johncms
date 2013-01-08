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
if (!Vars::$USER_ID && (!isset(Vars::$SYSTEM_SET['active']) || !Vars::$SYSTEM_SET['active'])) {
    echo Functions::displayError(__('access_guest_forbidden'));
    exit;
}

$backLink = Router::getUrl(2);

/*
-----------------------------------------------------------------
Функция отображения списков
-----------------------------------------------------------------
*/
function get_top($order = 'count_forum')
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
        return '<div class="menu"><p>' . __('list_empty') . '</p></div>';
    }
}

/*
-----------------------------------------------------------------
Меню выбора
-----------------------------------------------------------------
*/
$menu = array(
    (!Vars::$ACT ? '<b>' . __('forum') . '</b>' : '<a href="' . Router::getUrl(3) . '">' . __('forum') . '</a>'),
    (Vars::$ACT == 'comm' ? '<b>' . __('comments') . '</b>' : '<a href="' . Router::getUrl(3) . '?act=comm">' . __('comments') . '</a>')
);

//TODO: Добавить ТОП Кармы

switch (Vars::$ACT) {
    case 'comm':
        /*
        -----------------------------------------------------------------
        Топ комментариев
        -----------------------------------------------------------------
        */
        echo'<div class="phdr"><a href="' . $backLink . '"><b>' . __('community') . '</b></a> | ' . __('top_comm') . '</div>' .
            '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>' .
            get_top('count_comments') .
            '<div class="phdr"><a href="' . Vars::$HOME_URL . '">' . __('homepage') . '</a></div>';
        break;

    case 'karma':
        /*
        -----------------------------------------------------------------
        Топ Кармы
        -----------------------------------------------------------------
        */
        if ($set_karma['on']) {
            echo'<div class="phdr"><a href="index.php"><b>' . __('community') . '</b></a> | ' . __('top_karma') . '</div>' .
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
                echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
            }
            echo '<div class="phdr"><a href="../index.php">' . __('homepage') . '</a></div>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Топ Форума
        -----------------------------------------------------------------
        */
        echo'<div class="phdr"><a href="' . $backLink . '"><b>' . __('community') . '</b></a> | ' . __('top_forum') . '</div>' .
            '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>' .
            get_top('count_forum') .
            '<div class="phdr"><a href="' . Vars::$HOME_URL . '/forum">' . __('forum') . '</a></div>';
}
echo '<p><a href="' . $backLink . '">' . __('back') . '</a></p>';