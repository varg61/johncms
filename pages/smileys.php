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
$lng_smileys = Vars::loadLanguage('smileys');
$textl = 'FAQ';

// Сколько смайлов разрешено выбрать пользователям?
$user_smileys = 20;

// Обрабатываем ссылку для возврата
if (empty($_SESSION['ref'])) {
    $_SESSION['ref'] = htmlspecialchars($_SERVER['HTTP_REFERER']);
}

switch (Vars::$ACT) {
    case 'smusr':
        /*
        -----------------------------------------------------------------
        Каталог пользовательских Смайлов
        -----------------------------------------------------------------
        */
        $dir = glob($rootpath . 'images/smileys/user/*', GLOB_ONLYDIR);
        foreach ($dir as $val) $cat[] = array_pop(explode('/', $val));
        $cat = isset($_GET['cat']) && in_array(trim($_GET['cat']), $cat) ? trim($_GET['cat']) : $cat[0];
        $smileys = glob($rootpath . 'images/smileys/user/' . $cat . '/*.{gif,jpg,png}', GLOB_BRACE);
        $total = count($smileys);
        $end = Vars::$START + Vars::$USER_SET['page_size'];
        if ($end > $total) $end = $total;
        echo '<div class="phdr"><a href="smileys.php"><b>' . Vars::$LNG['smileys'] . '</b></a> | ' .
             (array_key_exists($cat, $lng_smileys) ? $lng_smileys[$cat] : ucfirst(htmlspecialchars($cat))) .
             '</div>';
        if ($total) {
            if (!Vars::$IS_MOBILE) {
                if (($user_sm = Vars::getUserData('smileys')) === false) $user_sm = array();
                echo '<div class="topmenu">' .
                     '<a href="smileys.php?act=my_smileys">' . Vars::$LNG['my_smileys'] . '</a>  (' . count($user_sm) . ' / ' . $user_smileys . ')</div>' .
                     '<form action="smileys.php?act=set_my_sm&amp;cat=' . $cat . '&amp;start=' . Vars::$START . '" method="post">';
            }
            if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('smileys.php?act=smusr&amp;cat=' . urlencode($cat) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            for ($i = Vars::$START; $i < $end; $i++) {
                $smile = preg_replace('#^(.*?).(gif|jpg|png)$#isU', '$1', basename($smileys[$i], 1));
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                if (!Vars::$IS_MOBILE) echo (in_array($smile, $user_sm) ? '' : '<input type="checkbox" name="add_sm[]" value="' . $smile . '" />&#160;');
                echo '<img src="../images/smileys/user/' . $cat . '/' . basename($smileys[$i]) . '" alt="" />&#160;:' . $smile . ': ' . Vars::$LNG['lng_or'] . ' :' . Functions::translit($smile) . ':' .
                     '</div>';
            }
            if (!Vars::$IS_MOBILE) echo '<div class="gmenu"><input type="submit" name="add" value=" ' . Vars::$LNG['add'] . ' "/></div></form>';
        } else {
            echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
        }
        echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo '<div class="topmenu">' . Functions::displayPagination('smileys.php?act=smusr&amp;cat=' . urlencode($cat) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                 '<p><form action="smileys.php?act=smusr&amp;cat=' . urlencode($cat) . '" method="post">' .
                 '<input type="text" name="page" size="2"/>' .
                 '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/></form></p>';
        }
        echo '<p><a href="' . $_SESSION['ref'] . '">' . Vars::$LNG['back'] . '</a></p>';
        break;

    case 'smadm':
        /*
        -----------------------------------------------------------------
        Каталог Админских Смайлов
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS < 1) {
            echo Functions::displayError($lng['error_wrong_data'], '<a href="smileys.php">' . Vars::$LNG['back'] . '</a>');
            exit;
        }
        echo '<div class="phdr"><a href="smileys.php"><b>' . Vars::$LNG['smileys'] . '</b></a> | ' . Vars::$LNG['admin_smileys'] . '</div>';
        if (!Vars::$IS_MOBILE) {
            if (($user_sm = Vars::getUserData('smileys')) === false) $user_sm = array();
            echo '<div class="topmenu"><a href="smileys.php?act=my_smileys">' . Vars::$LNG['my_smileys'] . '</a>  (' . count($user_sm) . ' / ' . $user_smileys . ')</div>' .
                 '<form action="smileys.php?act=set_my_sm&amp;start=' . Vars::$START . '&amp;adm" method="post">';
        }
        $array = array();
        $dir = opendir('../images/smileys/admin');
        while (($file = readdir($dir)) !== false) {
            if (($file != '.') && ($file != "..") && ($file != "name.dat") && ($file != ".svn") && ($file != "index.php")) {
                $array[] = $file;
            }
        }
        closedir($dir);
        $total = count($array);
        if ($total > 0) {
            $end = Vars::$START + Vars::$USER_SET['page_size'];
            if ($end > $total)
                $end = $total;
            for ($i = Vars::$START; $i < $end; $i++) {
                $smile = preg_replace('#^(.*?).(gif|jpg|png)$#isU', '$1', $array[$i], 1);
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                if (!Vars::$IS_MOBILE)
                    $smileys = (in_array($smile, $user_sm) ? ''
                            : '<input type="checkbox" name="add_sm[]" value="' . $smile . '" />&#160;');
                echo $smileys . '<img src="../images/smileys/admin/' . $array[$i] . '" alt="" /> - :' . $smile . ': ' . Vars::$LNG['lng_or'] . ' :' . Functions::translit($smile) . ':</div>';
            }
        } else {
            echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
        }
        if (!Vars::$IS_MOBILE)
            echo '<div class="gmenu"><input type="submit" name="add" value=" ' . Vars::$LNG['add'] . ' "/></div></form>';
        echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo '<div class="topmenu">' . Functions::displayPagination('smileys.php?act=smadm&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                 '<p><form action="smileys.php?act=smadm" method="post">' .
                 '<input type="text" name="page" size="2"/>' .
                 '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/></form></p>';
        }
        echo '<p><a href="' . $_SESSION['ref'] . '">' . Vars::$LNG['back'] . '</a></p>';
        break;

    case 'my_smileys':
        /*
        -----------------------------------------------------------------
        Список своих смайлов
        -----------------------------------------------------------------
        */
        if (Vars::$IS_MOBILE) {
            echo Functions::displayError($lng['error_wrong_data'], '<a href="smileys.php">' . Vars::$LNG['smileys'] . '</a>');
            exit;
        }
        echo '<div class="phdr"><a href="smileys.php"><b>' . Vars::$LNG['smileys'] . '</b></a> | ' . Vars::$LNG['my_smileys'] . '</div>';
        if (($smileys = Vars::getUserData('smileys')) === false) $smileys = array();
        $total = count($smileys);
        if ($total)
            echo '<form action="smileys.php?act=set_my_sm&amp;start=' . Vars::$START . '" method="post">';
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
                 Functions::smileys($smile, Vars::$USER_RIGHTS >= 1 ? 1 : 0) . '&#160;' . $smile . ' ' . Vars::$LNG['lng_or'] . ' ' . Functions::translit($smile) . '</div>';
            $i++;
        }
        if ($total) {
            echo '<div class="rmenu"><input type="submit" name="delete" value=" ' . Vars::$LNG['delete'] . ' "/></div></form>';
        } else {
            echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '<br /><a href="smileys.php">' . Vars::$LNG['add_smileys'] . '</a></p></div>';
        }
        echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . ' / ' . $user_smileys . '</div>';
        if ($total > Vars::$USER_SET['page_size'])
            echo '<div class="topmenu">' . Functions::displayPagination('smileys.php?act=my_smileys&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        echo '<p>' . ($total ? '<a href="smileys.php?act=set_my_sm&amp;clean">' . Vars::$LNG['clear'] . '</a><br />'
                : '') . '<a href="' . $_SESSION['ref'] . '">' . Vars::$LNG['back'] . '</a></p>';
        break;

    case 'set_my_sm':
        /*
        -----------------------------------------------------------------
        Настраиваем список своих смайлов
        -----------------------------------------------------------------
        */
        $adm = isset($_GET['adm']);
        $add = isset($_POST['add']);
        $delete = isset($_POST['delete']);
        $cat = isset($_GET['cat']) ? trim($_GET['cat']) : '';
        if (Vars::$IS_MOBILE || ($adm && !Vars::$USER_RIGHTS) || ($add && !$adm && !$cat) || ($delete && !$_POST['delete_sm']) || ($add && !$_POST['add_sm'])) {
            echo Functions::displayError(Vars::$LNG['error_wrong_data'], '<a href="smileys.php">' . Vars::$LNG['smileys'] . '</a>');
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
        settings::user_data_put('smileys', $smileys);
        if ($delete || isset($_GET['clean'])) {
            header('location: smileys.php?act=my_smileys&start=' . Vars::$START . '');
        } else {
            header('location: smileys.php?act=' . ($adm ? 'smadm' : 'smusr&cat=' . urlencode($cat) . '') . '&start=' . Vars::$START . '');
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Выводим каталог смайлов
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="faq.php"><b>F.A.Q.</b></a> | ' . Vars::$LNG['smileys'] . '</div>';
        if (Vars::$USER_ID && !Vars::$IS_MOBILE) {
            if (($smileys = Vars::getUserData('smileys')) === false) $smileys = array();
            $mycount = !empty($smileys) ? count($smileys) : '0';
            echo '<div class="topmenu"><a href="smileys.php?act=my_smileys">' . Vars::$LNG['my_smileys'] . '</a> (' . $mycount . ' / ' . $user_smileys . ')</div>';
        }
        if (Vars::$USER_RIGHTS >= 1)
            echo '<div class="gmenu"><a href="smileys.php?act=smadm">' . Vars::$LNG['admin_smileys'] . '</a> (' . count(glob($rootpath . 'images/smileys/admin/*.gif')) . ')</div>';
        $dir = glob($rootpath . 'images/smileys/user/*', GLOB_ONLYDIR);
        foreach ($dir as $val) {
            $cat = array_pop(explode('/', $val));
            if (array_key_exists($cat, $lng_smileys)) {
                $smileys_cat[$cat] = $lng_smileys[$cat];
            } else {
                $smileys_cat[$cat] = ucfirst($cat);
            }
        }
        asort($smileys_cat);
        $i = 0;
        foreach ($smileys_cat as $key => $val) {
            echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                 '<a href="smileys.php?act=smusr&amp;cat=' . urlencode($key) . '">' . htmlspecialchars($val) . '</a>' .
                 ' (' . count(glob($rootpath . 'images/smileys/user/' . $key . '/*.{gif,jpg,png}', GLOB_BRACE)) . ')' .
                 '</div>';
            ++$i;
        }
        echo '<div class="phdr"><a href="' . htmlspecialchars($_SESSION['ref']) . '">' . Vars::$LNG['back'] . '</a></div>';
}