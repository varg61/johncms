<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 *
 * Главное меню сайта
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

// Сколько смайлов разрешено выбрать пользователям?
$user_smileys = 20;

// Обрабатываем ссылку для возврата
if (empty($_SESSION['ref'])) {
    $_SESSION['ref'] = htmlspecialchars($_SERVER['HTTP_REFERER']);
}

// Обрабатываем глобальные переменные
$cat_list = array();
$dir_list = glob(ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'smileys' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
foreach ($dir_list as $val) {
    $dir = basename($val);
    $smileys_cat[$dir] = lng($dir);
    $cat_list[] = $dir;
}
$cat = isset($_GET['cat']) && in_array(trim($_GET['cat']), $cat_list) ? trim($_GET['cat']) : $cat_list[0];

switch (Vars::$ACT) {
    case 'list':
        /*
        -----------------------------------------------------------------
        Каталог пользовательских Смайлов
        -----------------------------------------------------------------
        */
        $smileys = glob(ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'smileys' . DIRECTORY_SEPARATOR . $cat . DIRECTORY_SEPARATOR . '*.{gif,jpg,png}', GLOB_BRACE);
        $total = count($smileys);
        $end = Vars::$START + Vars::$USER_SET['page_size'];
        if ($end > $total) {
            $end = $total;
        }
        echo'<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('smileys') . '</b></a> | ' . lng($cat) . '</div>';
        if ($total) {
            if (Vars::$USER_ID && !Vars::$IS_MOBILE) {
                if (($user_sm = Vars::getUserData('smileys')) === false) {
                    $user_sm = array();
                }
                echo'<div class="topmenu">' .
                    '<a href="' . Vars::$URI . '?act=my_smileys">' . lng('my_smileys') . '</a>  (' . count($user_sm) . ' / ' . $user_smileys . ')' .
                    '</div>' .
                    '<form action="' . Vars::$URI . '?act=set&amp;cat=' . $cat . '&amp;start=' . Vars::$START . '" method="post">';
            }
            if ($total > Vars::$USER_SET['page_size']) {
                echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=list&amp;cat=' . urlencode($cat) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            }
            for ($i = Vars::$START; $i < $end; $i++) {
                $smile = preg_replace('#^(.*?).(gif|jpg|png)$#isU', '$1', basename($smileys[$i], 1));
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                if (Vars::$USER_ID && !Vars::$IS_MOBILE) {
                    echo (in_array($smile, $user_sm) ? '' : '<input type="checkbox" name="add_sm[]" value="' . $smile . '" />&#160;');
                }
                echo '<img src="' . Vars::$HOME_URL . '/images/smileys/' . $cat . '/' . basename($smileys[$i]) . '" alt="" />&#160;:' . $smile . ': ' . lng('lng_or') . ' :' . Functions::translit($smile) . ':' .
                    '</div>';
            }
            if (Vars::$USER_ID && !Vars::$IS_MOBILE) {
                echo '<div class="gmenu"><input type="submit" name="add" value=" ' . lng('add') . ' "/></div></form>';
            }
        } else {
            echo '<div class="menu"><p>' . lng('list_empty') . '</p></div>';
        }
        echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=list&amp;cat=' . urlencode($cat) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                '<p><form action="' . Vars::$URI . '?act=list&amp;cat=' . urlencode($cat) . '" method="post">' .
                '<input type="text" name="page" size="2"/>' .
                '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
        }
        echo '<p><a href="' . $_SESSION['ref'] . '">' . lng('back') . '</a></p>';
        break;

    case 'my_smileys':
        /*
        -----------------------------------------------------------------
        Список своих смайлов
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('smileys') . '</b></a> | ' . lng('my_smileys') . '</div>';
        if (($smileys = Vars::getUserData('smileys')) === false) $smileys = array();
        $total = count($smileys);
        if ($total)
            echo '<form action="' . Vars::$URI . '?act=set_my_sm&amp;start=' . Vars::$START . '" method="post">';
        if ($total > Vars::$USER_SET['page_size']) {
            $smileys = array_chunk($smileys, Vars::$USER_SET['page_size'], TRUE);
            if (Vars::$START) {
                $key = (Vars::$START - Vars::$START % Vars::$USER_SET['page_size']) / Vars::$USER_SET['page_size'];
                $smileys_view = $smileys[$key];
                if (!count($smileys_view))
                    $smileys_view = $smileys[0];
                $smileys = $smileys_view;
            } else {
                $smileys = $smileys[0];
            }
        }
        $i = 0;
        foreach ($smileys as $value) {
            $smile = ':' . $value . ':';
            echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                '<input type="checkbox" name="delete_sm[]" value="' . $value . '" />&#160;' .
                Functions::smileys($smile, Vars::$USER_RIGHTS >= 1 ? 1 : 0) . '&#160;' . $smile . ' ' . lng('lng_or') . ' ' . Functions::translit($smile) . '</div>';
            $i++;
        }
        if ($total) {
            echo '<div class="rmenu"><input type="submit" name="delete" value=" ' . lng('delete') . ' "/></div></form>';
        } else {
            echo '<div class="menu"><p>' . lng('list_empty') . '<br /><a href="' . Vars::$URI . '">' . lng('add_smileys') . '</a></p></div>';
        }
        echo '<div class="phdr">' . lng('total') . ': ' . $total . ' / ' . $user_smileys . '</div>';
        if ($total > Vars::$USER_SET['page_size'])
            echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=my_smileys&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        echo '<p>' . ($total ? '<a href="' . Vars::$URI . '?act=set_my_sm&amp;clean">' . lng('clear') . '</a><br />'
            : '') . '<a href="' . $_SESSION['ref'] . '">' . lng('back') . '</a></p>';
        break;

    case 'set':
        /*
        -----------------------------------------------------------------
        Настраиваем список своих смайлов
        -----------------------------------------------------------------
        */
        $add = isset($_POST['add']);
        $delete = isset($_POST['delete']);
        if (Vars::$IS_MOBILE || ($delete && !$_POST['delete_sm']) || ($add && !$_POST['add_sm'])) {
            echo Functions::displayError(lng('error_wrong_data'), '<a href="' . Vars::$URI . '">' . lng('smileys') . '</a>');
            exit;
        }
        if (($smileys = Vars::getUserData('smileys')) === false) $smileys = array();
        if (!is_array($smileys))
            $smileys = array();
        if ($delete)
            $smileys = array_diff($smileys, $_POST['delete_sm']);
        if ($add) {
            $add_sm = $_POST['add_sm'];
            $smileys = array_unique(array_merge($smileys, $add_sm));
        }
        if (isset($_GET['clean']))
            $smileys = array();
        if (count($smileys) > $user_smileys) {
            $smileys = array_chunk($smileys, $user_smileys, TRUE);
            $smileys = $smileys[0];
        }
        Vars::setUserData('smileys', $smileys);
        if ($delete || isset($_GET['clean'])) {
            header('location: ' . Vars::$URI . '?act=my_smileys&start=' . Vars::$START . '');
        } else {
            header('location: ' . Vars::$URI . '?act=list&cat=' . urlencode($cat) . '&start=' . Vars::$START . '');
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Выводим каталог смайлов
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . Vars::$HOME_URL . '/help"><b>F.A.Q.</b></a> | ' . lng('smileys') . '</div>';

        // Меню личных смайлов
        if (Vars::$USER_ID && !Vars::$IS_MOBILE) {
            if (($smileys = Vars::getUserData('smileys')) === false) $smileys = array();
            $mycount = !empty($smileys) ? count($smileys) : '0';
            echo '<div class="topmenu"><a href="' . Vars::$URI . '?act=my_smileys">' . lng('my_smileys') . '</a> (' . $mycount . ' / ' . $user_smileys . ')</div>';
        }

        $i = 0;
        asort($smileys_cat);
        foreach ($smileys_cat as $key => $val) {
            $count = count(glob(ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'smileys' . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . '*.{gif,jpg,png}', GLOB_BRACE));
            echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                '<a href="' . Vars::$URI . '?act=list&amp;cat=' . urlencode($key) . '">' . htmlspecialchars($val) . '</a>' .
                ' (' . $count . ')' .
                '</div>';
            ++$i;
        }
        echo '<div class="phdr"><a href="' . $_SESSION['ref'] . '">' . lng('back') . '</a></div>';
}