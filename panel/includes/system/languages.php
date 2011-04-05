<?php

/**
* @package     JohnCMS
* @link        http://johncms.com
* @copyright   Copyright (C) 2008-2011 JohnCMS Community
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      http://johncms.com/about
*/

defined('_IN_JOHNADM') or die('Error: restricted access');

// Проверяем права доступа
if ($rights < 9) {
    header('Location: http://johncms.com/?err');
    exit;
}

/*
-----------------------------------------------------------------
Читаем каталог с файлами языков
-----------------------------------------------------------------
*/
$lng_iso = array ();
$lng_desc = array ();
$languages = glob('../incfiles/languages/*/_lng.ini');
foreach ($languages as $val) {
    $array = explode('/', dirname($val));
    $iso = array_pop($array);
    $lng_iso[] = $iso;
    $lng_desc[$iso] = parse_ini_file($val);
}

/*
-----------------------------------------------------------------
Автоустановка языков
-----------------------------------------------------------------
*/
$lng_add = array_diff($lng_iso, $core->language_list);
$lng_del = array_diff($core->language_list, $lng_iso);
if (!empty($lng_add) || !empty($lng_del)) {
    if (!empty($lng_del) && in_array($set['lng_iso'], $lng_del)) {
        // Если удаленный язык был системный, то меняем на первый доступный
        mysql_query("UPDATE `cms_settings` SET `val` = '" . $lng_iso[0] . "' WHERE `key` = 'lng_iso' LIMIT 1");
    }
    $req = mysql_query("SELECT * FROM `cms_settings` WHERE `key` = 'lng_list'");
    if (mysql_num_rows($req)) {
        mysql_query("UPDATE `cms_settings` SET `val` = '" . serialize($lng_iso) . "' WHERE `key` = 'lng_list' LIMIT 1");
    } else {
        mysql_query("INSERT INTO `cms_settings` SET `key` = 'lng_list', `val` = '" . serialize($lng_iso) . "'");
    }
}

switch ($mod) {
    case 'set':
        /*
        -----------------------------------------------------------------
        Устанавливаем системный язык
        -----------------------------------------------------------------
        */
        $iso = isset($_POST['iso']) ? trim($_POST['iso']) : false;
        if($iso && in_array($iso, $lng_iso) && $iso != $core->language_iso){
            mysql_query("UPDATE `cms_settings` SET `val` = '" . mysql_real_escape_string($iso) . "' WHERE `key` = 'lng_iso'");
        }
        header('Location: index.php?act=languages');
        break;

    default:
        /*
        -----------------------------------------------------------------
        Выводим список доступных языков
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>' . $lng['admin_panel'] . '</b></a> | ' . $lng['language_default'] . '</div>';
        echo '<div class="menu"><form action="index.php?act=languages&amp;mod=set" method="post"><p>';
        echo '<table><tr><td>&nbsp;</td><td style="padding-bottom:4px"><h3>' . $lng['language_system'] . '</h3></td></tr>';
        foreach ($lng_desc as $key => $val) {
            $lng_menu = array (
                (!empty($val['author']) ? '<span class="gray">' . $lng['author'] . ':</span> ' . $val['author'] : ''),
                (!empty($val['author_email']) ? '<span class="gray">E-mail:</span> ' . $val['author_email'] : ''),
                (!empty($val['author_url']) ? '<span class="gray">URL:</span> ' . $val['author_url'] : ''),
                (!empty($val['description']) ? '<span class="gray">' . $lng['description'] . ':</span> ' . $val['description'] : '')
            );
            echo '<tr>' .
                '<td valign="top"><input type="radio" value="' . $key . '" name="iso" ' . ($key == $set['lng_iso'] ? 'checked="checked"' : '') . '/></td>' .
                '<td style="padding-bottom:6px"><b>' . $val['name'] . '</b>&#160;<span class="green">[' . $key . ']</span>' .
                '<div class="sub">' . functions::display_menu($lng_menu, '<br />') . '</div></td>' .
                '</tr>';
        }
        echo '<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="' . $lng['save'] . '" /></td></tr>' .
            '</table></p>' .
            '</form></div><div class="phdr">' . $lng['total'] . ': ' . mysql_num_rows($req) . '</div>' .
            '<p><a href="index.php">' . $lng['admin_panel'] . '</a></p>';
}
?>