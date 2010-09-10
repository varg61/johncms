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

defined('_IN_JOHNADM') or die('Error: restricted access');

// Проверяем права доступа
if ($rights < 9) {
    header('Location: http://johncms.com/?err');
    exit;
}

switch ($mod) {
    case 'change':
    /*
    -----------------------------------------------------------------
    Меняем язык системы по умолчанию
    -----------------------------------------------------------------
    */
    break;

    case 'delete':
        /*
        -----------------------------------------------------------------
        Удаляем язык
        -----------------------------------------------------------------
        */
        $lng_del = isset($_GET['del']) ? check(trim($_GET['del'])) : false;
        $error = array ();
        if (!$lng_del)
            $error[] = $lng['error_wrong_data'];
        if ($lng_del == $sys_language)
            $error[] = $lng['language_delete_error'];
        $req = mysql_query("SELECT * FROM `cms_languages` WHERE `iso` = '$lng_del' AND `var` = 'language_name' LIMIT 1");
        if (!mysql_num_rows($req))
            $error[] = $lng['language_select_error'];
        if (!$error) {
            if (isset($_POST['submit'])) {
                mysql_query("UPDATE `users` SET `set_language` = '$sys_language' WHERE `set_language` = '$lng_del'");
                mysql_query("DELETE FROM `cms_languages` WHERE `iso` = '$lng_del'");
                mysql_query("OPTIMIZE TABLE `cms_languages`");
                header('Location: index.php?act=sys_lng');
            } else {
                $res = mysql_fetch_assoc($req);
                echo '<div class="phdr"><a href="index.php?act=sys_lng"><b>' . $lng['language'] . '</b></a> | ' . $lng['delete'] . '</div>' .
                    '<div class="rmenu"><form action="index.php?act=sys_lng&amp;mod=delete&amp;del=' . $lng_del . '" method="post">' .
                    '<p>' . $lng['language_delete_warning'] . ': <b>' . $res['default'] . '</b>?</p>' .
                    '<p><input type="submit" name="submit" value="' . $lng['delete'] . '" /></p>' .
                    '</form></div>' .
                    '<div class="phdr"><a href="index.php?act=sys_lng">' . $lng['cancel'] . '</a></div>';
            }
        } else {
            echo display_error($error, '<a href="index.php?act=sys_lng">' . $lng['back'] . '</a>');
            require('../incfiles/end.php');
            exit;
        }
        break;

    case 'delcustom':
    /*
    -----------------------------------------------------------------
    Очищаем пользовательские фразы
    -----------------------------------------------------------------
    */
    break;

    case 'edit':
    /*
    -----------------------------------------------------------------
    Редактируем пользовательские фразы
    -----------------------------------------------------------------
    */
    break;

    default:
        /*
        -----------------------------------------------------------------
        Выводим список доступных языков
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>' . $lng['admin_panel'] . '</b></a> | ' . $lng['language_default'] . '</div>';
        if (isset($_POST['submit'])) {
            // Устанавливаем системный язык
            $lng_select = isset($_POST['lng']) ? check(trim($_POST['lng'])) : false;
            if ($lng_select && $lng_select != $sys_language) {
                $req = mysql_query("SELECT * FROM `cms_languages` WHERE `iso` = '$lng_select' AND `var` = 'language_name' LIMIT 1");
                if (mysql_num_rows($req)) {
                    if (!isset($set['language'])) {
                        mysql_query("INSERT INTO `cms_settings` SET `key` = 'language', `val` = '$lng_select'");
                    } else {
                        mysql_query("UPDATE `cms_settings` SET `val` = '$lng_select' WHERE `key` = 'language'");
                    }
                    $sys_language = $lng_select;
                    $res = mysql_fetch_assoc($req);
                    echo '<div class="gmenu">' . $lng['language_set'] . ': <b>' . $res['default'] . '</b></div>';
                }
            }
        }
        echo '<div class="menu"><form action="index.php?act=sys_lng" method="post"><p>' .
            '<h3>' . $lng['language_system'] . '</h3>';
        $req = mysql_query("SELECT DISTINCT `iso` FROM `cms_languages`");
        while ($res = mysql_fetch_assoc($req)) {
            $req_l = mysql_query("SELECT * FROM `cms_languages` WHERE `iso` = '" . $res['iso'] . "' AND `var` = 'language_name' LIMIT 1");
            $res_l = mysql_fetch_assoc($req_l);
            echo '<div><input type="radio" value="' . $res['iso'] . '" name="lng" ' . ($res['iso'] == $sys_language ? 'checked="checked"' : '') . '/>&#160;' .
                '<a href="">' . $res_l['default'] . '</a>' . ($res['iso'] == $sys_language ? '' : '&nbsp;<a href="index.php?act=sys_lng&amp;mod=delete&amp;del=' . $res['iso'] . '">[x]</a>') .
                '</div>';
        }
        echo '</p><p><input type="submit" name="submit" value="' . $lng['save'] . '" /></p>' .
            '</form></div><div class="phdr">' . $lng['total'] . ': ' . mysql_num_rows($req) . '</div>' .
            '<p><a href="index.php">' . $lng['admin_panel'] . '</a></p>';
}
?>