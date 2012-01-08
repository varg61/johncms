<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require_once('../includes/core.php');
$lng_avatars = Vars::loadLanguage('avatars');
$textl = 'FAQ';
$headmod = 'avatars';
require_once('../includes/head.php');

// Обрабатываем ссылку для возврата
if (empty($_SESSION['ref'])) $_SESSION['ref'] = htmlspecialchars($_SERVER['HTTP_REFERER']);
// Обрабатываем глобальные переменные
foreach (glob($rootpath . 'images/avatars/*', GLOB_ONLYDIR) as $val) {
    $dir = array_pop(explode('/', $val));
    if (array_key_exists($dir, $lng_avatars)) $avatar_cat[$dir] = $lng_avatars[$dir];
    else $avatar_cat[$dir] = ucfirst($dir);
    $cat_list[] = $dir;
}
$cat = isset($_GET['cat']) && in_array(trim($_GET['cat']), $cat_list) ? trim($_GET['cat']) : $cat_list[0];

switch (Vars::$ACT) {
    case 'avset':
        $select = isset($_GET['select']) ? substr(trim($_GET['select']), 0, 20) : false;
        if (Vars::$USER_ID && $select && is_file('../images/avatars/' . $cat . '/' . $select)) {
            if (isset($_POST['submit'])) {
                // Устанавливаем пользовательский Аватар
                if (@copy('../images/avatars/' . $cat . '/' . $select, '../files/users/avatar/' . Vars::$USER_ID . '.png')) {
                    echo '<div class="gmenu"><p>' . Vars::$LNG['avatar_applied'] . '<br />' .
                         '<a href="../users/profile.php?act=edit">' . Vars::$LNG['continue'] . '</a></p></div>';
                } else {
                    echo Functions::displayError($lng['error_avatar_select'], '<a href="' . $_SESSION['ref'] . '">' . Vars::$LNG['back'] . '</a>');
                }
            } else {
                echo '<div class="phdr"><a href="avatars.php"><b>' . Vars::$LNG['avatars'] . '</b></a> | ' . Vars::$LNG['set_to_profile'] . '</div>' .
                     '<div class="rmenu"><p>' . Vars::$LNG['avatar_change_warning'] . '</p>' .
                     '<p><img src="../images/avatars/' . $cat . '/' . $select . '" alt="" /></p>' .
                     '<p><form action="avatars.php?act=avset&amp;cat=' . urlencode($cat) . '&amp;select=' . urlencode($select) . '" method="post">' .
                     '<input type="submit" name="submit" value="' . Vars::$LNG['save'] . '"/>' .
                     '</form></p>' .
                     '</div>' .
                     '<div class="phdr"><a href="avatars.php?act=avlist&amp;cat=' . $cat . '">' . Vars::$LNG['cancel'] . '</a></div>';
            }
        } else {
            echo Functions::displayError(Vars::$LNG['error_wrong_data']);
        }
        break;

    case 'avlist':
        /*
        -----------------------------------------------------------------
        Показываем список аватаров
        -----------------------------------------------------------------
        */
        $avatars = glob($rootpath . 'images/avatars/' . $cat . '/*.{gif,jpg,png}', GLOB_BRACE);
        $total = count($avatars);
        $end = Vars::$START + Vars::$USER_SET['page_size'];
        if ($end > $total) $end = $total;
        echo '<div class="phdr"><a href="avatars.php"><b>' . Vars::$LNG['avatars'] . '</b></a> | ' . (array_key_exists($cat, $lng_avatars) ? $lng_avatars[$cat] : ucfirst(htmlspecialchars($cat))) . '</div>';
        if ($total) {
            if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('avatars.php?act=avlist&amp;cat=' . urlencode($cat) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            for ($i = Vars::$START; $i < $end; $i++) {
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                echo '<img src="../images/avatars/' . $cat . '/' . basename($avatars[$i]) . '" alt="" />';
                if (Vars::$USER_ID) echo '&#160;<a href="avatars.php?act=avset&amp;cat=' . urlencode($cat) . '&amp;select=' . urlencode(basename($avatars[$i])) . '">' . Vars::$LNG['select'] . '</a>';
                echo '</div>';
            }
        } else {
            echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
        }
        echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo '<div class="topmenu">' . Functions::displayPagination('avatars.php?act=avlist&amp;cat=' . urlencode($cat) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                 '<p><form action="avatars.php?act=avlist&amp;cat=' . urlencode($cat) . '" method="post">' .
                 '<input type="text" name="page" size="2"/>' .
                 '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/></form></p>';
        }
        echo '<p><a href="' . $_SESSION['ref'] . '">' . Vars::$LNG['back'] . '</a></p>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Выводим каталог Аватаров
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="faq.php"><b>F.A.Q.</b></a> | ' . Vars::$LNG['avatars'] . '</div>';
        asort($avatar_cat);
        $i = 0;
        foreach ($avatar_cat as $key => $val) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            echo '<a href="avatars.php?act=avlist&amp;cat=' . urlencode($key) . '">' . htmlspecialchars($val) . '</a>' .
                 ' (' . count(glob($rootpath . 'images/avatars/' . $key . '/*.{gif,jpg,png}', GLOB_BRACE)) . ')' .
                 '</div>';
            ++$i;
        }
        echo '<div class="phdr"><a href="' . htmlspecialchars($_SESSION['ref']) . '">' . Vars::$LNG['back'] . '</a></div>';
}

require_once('../includes/end.php');