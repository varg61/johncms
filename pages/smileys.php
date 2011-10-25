<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require('../incfiles/core.php');
$lng_smileys = core::load_lng('smileys');
$textl = 'FAQ';
$headmod = 'smileys';
require('../incfiles/head.php');

// Сколько смайлов разрешено выбрать пользователям?
$user_smileys = 20;

// Обрабатываем ссылку для возврата
if (empty($_SESSION['ref'])) {
    $_SESSION['ref'] = htmlspecialchars($_SERVER['HTTP_REFERER']);
}

switch ($act) {
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
        $end = $start + $kmess;
        if ($end > $total) $end = $total;
        echo '<div class="phdr"><a href="smileys.php"><b>' . $lng['smileys'] . '</b></a> | ' .
             (array_key_exists($cat, $lng_smileys) ? $lng_smileys[$cat] : ucfirst(htmlspecialchars($cat))) .
             '</div>';
        if ($total) {
            if (!$is_mobile) {
                if (($user_sm = settings::user_data_get('smileys')) === false) $user_sm = array();
                echo '<div class="topmenu">' .
                     '<a href="smileys.php?act=my_smileys">' . $lng['my_smileys'] . '</a>  (' . count($user_sm) . ' / ' . $user_smileys . ')</div>' .
                     '<form action="smileys.php?act=set_my_sm&amp;cat=' . $cat . '&amp;start=' . $start . '" method="post">';
            }
            if ($total > $kmess) echo '<div class="topmenu">' . functions::display_pagination('smileys.php?act=smusr&amp;cat=' . urlencode($cat) . '&amp;', $start, $total, $kmess) . '</div>';
            for ($i = $start; $i < $end; $i++) {
                $smile = preg_replace('#^(.*?).(gif|jpg|png)$#isU', '$1', basename($smileys[$i], 1));
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                if (!$is_mobile) echo (in_array($smile, $user_sm) ? '' : '<input type="checkbox" name="add_sm[]" value="' . $smile . '" />&#160;');
                echo '<img src="../images/smileys/user/' . $cat . '/' . basename($smileys[$i]) . '" alt="" />&#160;:' . $smile . ': ' . $lng['lng_or'] . ' :' . functions::trans($smile) . ':' .
                     '</div>';
            }
            if (!$is_mobile) echo '<div class="gmenu"><input type="submit" name="add" value=" ' . $lng['add'] . ' "/></div></form>';
        } else {
            echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
        }
        echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
        if ($total > $kmess) {
            echo '<div class="topmenu">' . functions::display_pagination('smileys.php?act=smusr&amp;cat=' . urlencode($cat) . '&amp;', $start, $total, $kmess) . '</div>' .
                 '<p><form action="smileys.php?act=smusr&amp;cat=' . urlencode($cat) . '" method="post">' .
                 '<input type="text" name="page" size="2"/>' .
                 '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
        }
        echo '<p><a href="' . $_SESSION['ref'] . '">' . $lng['back'] . '</a></p>';
        break;

    case 'smadm':
        /*
        -----------------------------------------------------------------
        Каталог Админских Смайлов
        -----------------------------------------------------------------
        */
        if ($rights < 1) {
            echo functions::display_error($lng['error_wrong_data'], '<a href="smileys.php">' . $lng['back'] . '</a>');
            require('../incfiles/end.php');
            exit;
        }
        echo '<div class="phdr"><a href="smileys.php"><b>' . $lng['smileys'] . '</b></a> | ' . $lng['admin_smileys'] . '</div>';
        if (!$is_mobile) {
            if (($user_sm = settings::user_data_get('smileys')) === false) $user_sm = array();
            echo '<div class="topmenu"><a href="smileys.php?act=my_smileys">' . $lng['my_smileys'] . '</a>  (' . count($user_sm) . ' / ' . $user_smileys . ')</div>' .
                 '<form action="smileys.php?act=set_my_sm&amp;start=' . $start . '&amp;adm" method="post">';
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
            $end = $start + $kmess;
            if ($end > $total)
                $end = $total;
            for ($i = $start; $i < $end; $i++) {
                $smile = preg_replace('#^(.*?).(gif|jpg|png)$#isU', '$1', $array[$i], 1);
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                if (!$is_mobile)
                    $smileys = (in_array($smile, $user_sm) ? ''
                            : '<input type="checkbox" name="add_sm[]" value="' . $smile . '" />&#160;');
                echo $smileys . '<img src="../images/smileys/admin/' . $array[$i] . '" alt="" /> - :' . $smile . ': ' . $lng['lng_or'] . ' :' . functions::trans($smile) . ':</div>';
            }
        } else {
            echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
        }
        if (!$is_mobile)
            echo '<div class="gmenu"><input type="submit" name="add" value=" ' . $lng['add'] . ' "/></div></form>';
        echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
        if ($total > $kmess) {
            echo '<div class="topmenu">' . functions::display_pagination('smileys.php?act=smadm&amp;', $start, $total, $kmess) . '</div>' .
                 '<p><form action="smileys.php?act=smadm" method="post">' .
                 '<input type="text" name="page" size="2"/>' .
                 '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
        }
        echo '<p><a href="' . $_SESSION['ref'] . '">' . $lng['back'] . '</a></p>';
        break;

    case 'my_smileys':
        /*
        -----------------------------------------------------------------
        Список своих смайлов
        -----------------------------------------------------------------
        */
        if ($is_mobile) {
            echo functions::display_error($lng['error_wrong_data'], '<a href="smileys.php">' . $lng['smileys'] . '</a>');
            require('../incfiles/end.php');
            exit;
        }
        echo '<div class="phdr"><a href="smileys.php"><b>' . $lng['smileys'] . '</b></a> | ' . $lng['my_smileys'] . '</div>';
        if (($smileys = settings::user_data_get('smileys')) === false) $smileys = array();
        $total = count($smileys);
        if ($total)
            echo '<form action="smileys.php?act=set_my_sm&amp;start=' . $start . '" method="post">';
        if ($total > $kmess) {
            $smileys = array_chunk($smileys, $kmess, TRUE);
            if ($start) {
                $key = ($start - $start % $kmess) / $kmess;
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
                 functions::smileys($smile, $rights >= 1 ? 1 : 0) . '&#160;' . $smile . ' ' . $lng['lng_or'] . ' ' . functions::trans($smile) . '</div>';
            $i++;
        }
        if ($total) {
            echo '<div class="rmenu"><input type="submit" name="delete" value=" ' . $lng['delete'] . ' "/></div></form>';
        } else {
            echo '<div class="menu"><p>' . $lng['list_empty'] . '<br /><a href="smileys.php">' . $lng['add_smileys'] . '</a></p></div>';
        }
        echo '<div class="phdr">' . $lng['total'] . ': ' . $total . ' / ' . $user_smileys . '</div>';
        if ($total > $kmess)
            echo '<div class="topmenu">' . functions::display_pagination('smileys.php?act=my_smileys&amp;', $start, $total, $kmess) . '</div>';
        echo '<p>' . ($total ? '<a href="smileys.php?act=set_my_sm&amp;clean">' . $lng['clear'] . '</a><br />'
                : '') . '<a href="' . $_SESSION['ref'] . '">' . $lng['back'] . '</a></p>';
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
        if ($is_mobile || ($adm && !$rights) || ($add && !$adm && !$cat) || ($delete && !$_POST['delete_sm']) || ($add && !$_POST['add_sm'])) {
            echo functions::display_error($lng['error_wrong_data'], '<a href="smileys.php">' . $lng['smileys'] . '</a>');
            require('../incfiles/end.php');
            exit;
        }
        if (($smileys = settings::user_data_get('smileys')) === false) $smileys = array();
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
            header('location: smileys.php?act=my_smileys&start=' . $start . '');
        } else {
            header('location: smileys.php?act=' . ($adm ? 'smadm' : 'smusr&cat=' . urlencode($cat) . '') . '&start=' . $start . '');
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Выводим каталог смайлов
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="faq.php"><b>F.A.Q.</b></a> | ' . $lng['smileys'] . '</div>';
        if ($user_id && !$is_mobile) {
            if (($smileys = settings::user_data_get('smileys')) === false) $smileys = array();
            $mycount = !empty($smileys) ? count($smileys) : '0';
            echo '<div class="topmenu"><a href="smileys.php?act=my_smileys">' . $lng['my_smileys'] . '</a> (' . $mycount . ' / ' . $user_smileys . ')</div>';
        }
        if ($rights >= 1)
            echo '<div class="gmenu"><a href="smileys.php?act=smadm">' . $lng['admin_smileys'] . '</a> (' . count(glob($rootpath . 'images/smileys/admin/*.gif')) . ')</div>';
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
        echo '<div class="phdr"><a href="' . htmlspecialchars($_SESSION['ref']) . '">' . $lng['back'] . '</a></div>';
}

require('../incfiles/end.php');