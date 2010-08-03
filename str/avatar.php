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
$textl = $lng['avatars'];
require('../incfiles/core.php');
require('../incfiles/head.php');
if (!$user_id) {
    display_error($lng['access_guest_forbidden']);
    require('../incfiles/end.php');
    exit;
}
switch ($act) {
    case 'choice':
        if ($_GET['ava'] && intval($_GET['cat'])) {
            $ava = intval($_GET['ava']);
            $cat = intval($_GET['cat']);
            $av = '../images/avatars/' . $cat . '/' . $ava . '.png';
            copy($av, '../files/users/avatar/' . $user_id . '.png');
        }
        echo '<p>' . $lng['avatar_applied'] . '<br /><a href="my_data.php?id=' . $user_id . '">' . $lng['continue'] . '</a><br/><a href="avatar.php">' . $lng['catalogue'] . '</a></p>';
        break;

    case 'cat':
        if (!is_dir($rootpath . 'images/avatars/' . $id)) {
            echo display_error($lng['error_wrong_data'], '<a href="avatar.php">' . $lng['catalogue'] . '</a>');
            require('../incfiles/end.php');
            exit;
        }
        echo '<div class="phdr"><a href="avatar.php"><b>' . $lng['avatars'] . '</b></a> | ' . htmlentities(file_get_contents($rootpath . 'images/avatars/' . $id . '/name.dat'), ENT_QUOTES, 'utf-8') . '</div>';
        $array = glob($rootpath . 'images/avatars/' . $id . '/*.png');
        $total = count($array);
        $end = $start + $kmess;
        if ($end > $total)
            $end = $total;
        if ($total > 0) {
            for ($i = $start; $i < $end; $i++) {
                $ava = preg_replace('#^' . $rootpath . 'images/avatars/' . $id . '/(.*?).png$#isU', '$1', $array[$i], 1);
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                echo '<img src="' . $array[$i] . '" alt="" /> - <a href="avatar.php?act=choice&amp;cat=' . $id . '&amp;ava=' . $ava . '">' . $lng['select'] . '</a></div>';
            }
        } else {
            echo '<div class="menu">' . $lng['list_empty'] . '</div>';
        }
        echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
        if ($total > $kmess) {
            echo '<p>' . display_pagination('avatar.php?act=cat&amp;id=' . $id . '&amp;', $start, $total, $kmess) . '</p>';
            echo '<p><form action="avatar.php" method="get"><input type="hidden" value="cat" name="act" /><input type="hidden" value="' . $id .
                '" name="id" /><input type="text" name="page" size="2"/><input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
        }
        echo '<p><a href="avatar.php">' . $lng['catalogue'] . '</a></p>';
        break;

    default:
        if (empty($_SESSION['refsm'])) {
            $_SESSION['refsm'] = htmlspecialchars($_SERVER['HTTP_REFERER']);
        }
        echo '<div class="phdr"><b>' . $lng['avatars'] . '</b></div>';
        $dir = glob($rootpath . 'images/avatars/*', GLOB_ONLYDIR);
        $total_dir = count($dir);
        for ($i = 0; $i < $total_dir; $i++) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            echo '<a href="avatar.php?act=cat&amp;id=' . preg_replace('#^' . $rootpath . 'images/avatars/#isU', '', $dir[$i], 1) . '">' . htmlentities(file_get_contents($dir[$i] . '/name.dat'), ENT_QUOTES, 'utf-8') .
                '</a> (' . (int)count(glob($dir[$i] . '/*.png')) . ')</div>';
        }
        echo '<div class="phdr"><a href="' . $_SESSION['refsm'] . '">' . $lng['back'] . '</a></div>';
        break;
}

require('../incfiles/end.php');
?>