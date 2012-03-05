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

// Обрабатываем ссылку для возврата
if (empty($_SESSION['ref'])) {
    $_SESSION['ref'] = htmlspecialchars($_SERVER['HTTP_REFERER']);
}

// Обрабатываем глобальные переменные
$cat_list = array();
$dir_list = glob(ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
foreach ($dir_list as $val) {
    $dir = basename($val);
    $avatar_cat[$dir] = lng($dir);
    $cat_list[] = $dir;
}
$cat = isset($_GET['cat']) && in_array(trim($_GET['cat']), $cat_list) ? trim($_GET['cat']) : $cat_list[0];

switch (Vars::$ACT) {
    case 'avset':
        $select = isset($_GET['select']) ? substr(trim($_GET['select']), 0, 20) : false;
        if (Vars::$USER_ID
            && $select
            && is_file(ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $cat . DIRECTORY_SEPARATOR . $select)
        ) {
            if (isset($_POST['submit'])) {
                // Устанавливаем пользовательский Аватар
                if (@copy(ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $cat . DIRECTORY_SEPARATOR . $select,
                    ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . Vars::$USER_ID . '.gif')
                ) {
                    echo '<div class="gmenu"><p>' . lng('avatar_applied') . '<br />' .
                        '<a href="' . $_SESSION['ref'] . '">' . lng('continue') . '</a></p></div>';
                } else {
                    echo Functions::displayError(lng('error_avatar_select'), '<a href="' . $_SESSION['ref'] . '">' . lng('back') . '</a>');
                }
            } else {
                echo'<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('avatars') . '</b></a> | ' . lng('set_to_profile') . '</div>' .
                    '<div class="rmenu"><p>' . lng('avatar_change_warning') . '</p>' .
                    '<p><img src="' . Vars::$HOME_URL . '/images/avatars/' . $cat . '/' . $select . '" alt="" /></p>' .
                    '<p><form action="' . Vars::$URI . '?act=avset&amp;cat=' . urlencode($cat) . '&amp;select=' . urlencode($select) . '" method="post">' .
                    '<input type="submit" name="submit" value="' . lng('save') . '"/>' .
                    '</form></p>' .
                    '</div>' .
                    '<div class="phdr"><a href="' . Vars::$URI . '?act=avlist&amp;cat=' . $cat . '">' . lng('cancel') . '</a></div>';
            }
        } else {
            echo Functions::displayError(lng('error_wrong_data'));
        }
        break;

    case 'list':
        /*
        -----------------------------------------------------------------
        Показываем список аватаров
        -----------------------------------------------------------------
        */
        $avatars = glob(ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $cat . DIRECTORY_SEPARATOR . '*.{gif,jpg,png}', GLOB_BRACE);
        $total = count($avatars);
        $end = Vars::$START + Vars::$USER_SET['page_size'];
        if ($end > $total) {
            $end = $total;
        }
        echo'<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('avatars') . '</b></a> | ' . lng($cat) . '</div>';
        if ($total) {
            if ($total > Vars::$USER_SET['page_size']) {
                echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=list&amp;cat=' . urlencode($cat) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            }
            for ($i = Vars::$START; $i < $end; $i++) {
                echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                    '<img src="' . Vars::$HOME_URL . '/images/avatars/' . $cat . '/' . basename($avatars[$i]) . '" alt="" />';
                if (Vars::$USER_ID) {
                    echo '&#160;<a href="' . Vars::$URI . '?act=avset&amp;cat=' . urlencode($cat) . '&amp;select=' . urlencode(basename($avatars[$i])) . '">' . lng('select') . '</a>';
                }
                echo '</div>';
            }
        } else {
            echo '<div class="menu"><p>' . lng('list_empty') . '</p></div>';
        }
        echo'<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=list&amp;cat=' . urlencode($cat) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                '<p><form action="' . Vars::$URI . '?act=list&amp;cat=' . urlencode($cat) . '" method="post">' .
                '<input type="text" name="page" size="2"/>' .
                '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
        }
        echo'<p><a href="' . $_SESSION['ref'] . '">' . lng('back') . '</a></p>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Выводим каталог Аватаров
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . Vars::$HOME_URL . '/help"><b>F.A.Q.</b></a> | ' . lng('avatars') . '</div>';
        asort($avatar_cat);
        $i = 0;
        foreach ($avatar_cat as $key => $val) {
            $count = count(glob(ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . '*.{gif,jpg,png}', GLOB_BRACE));
            echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                '<a href="' . Vars::$URI . '?act=list&amp;cat=' . urlencode($key) . '">' . htmlspecialchars($val) . '</a>' .
                ' (' . $count . ')' .
                '</div>';
            ++$i;
        }
        echo '<div class="phdr"><a href="' . $_SESSION['ref'] . '">' . lng('back') . '</a></div>';
}