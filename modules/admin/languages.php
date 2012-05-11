<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

// Проверяем права доступа
if (Vars::$USER_RIGHTS != 9) {
    echo Functions::displayError(lng('access_forbidden'));
    exit;
}

/*
-----------------------------------------------------------------
Читаем каталог с файлами языков
-----------------------------------------------------------------
*/
$lng_list = array();
$lng_desc = array();
foreach (glob(SYSPATH . 'languages' . DIRECTORY_SEPARATOR . '*.ini') as $val) {
    $iso = basename($val, '.ini');
    $desc = parse_ini_file($val);
    $lng_list[$iso] = isset($desc['name']) && !empty($desc['name']) ? $desc['name'] : $iso;
    $lng_desc[$iso] = $desc;
}

/*
-----------------------------------------------------------------
Автоустановка языков
-----------------------------------------------------------------
*/
if (isset($_GET['refresh'])) {
    mysql_query("DELETE FROM `cms_settings` WHERE `key` = 'lng_list'");
    Vars::$LNG_LIST = array();
}
$lng_add = array_diff(array_keys($lng_list), array_keys(Vars::$LNG_LIST));
$lng_del = array_diff(array_keys(Vars::$LNG_LIST), array_keys($lng_list));
if (!empty($lng_add) || !empty($lng_del)) {
    if (!empty($lng_del) && in_array(Vars::$SYSTEM_SET['lng'], $lng_del)) {
        // Если удаленный язык был системный, то меняем на первый доступный
        mysql_query("UPDATE `cms_settings` SET `val` = '" . key($lng_list) . "' WHERE `key` = 'lng' LIMIT 1");
    }
    $req = mysql_query("SELECT * FROM `cms_settings` WHERE `key` = 'lng_list'");
    if (mysql_num_rows($req)) {
        mysql_query("UPDATE `cms_settings` SET `val` = '" . mysql_real_escape_string(serialize($lng_list)) . "' WHERE `key` = 'lng_list' LIMIT 1");
    } else {
        mysql_query("INSERT INTO `cms_settings` SET `key` = 'lng_list', `val` = '" . mysql_real_escape_string(serialize($lng_list)) . "'");
    }
}

$language = isset($_GET['language']) ? trim($_GET['language']) : false;

/*
-----------------------------------------------------------------
Класс функций для работы с языковыми файлами
-----------------------------------------------------------------
*/
class ini_file
{
    public static function key_filter($str)
    {
        if (!$str)
            return false;
        $str = trim($str);
        $str = mb_substr($str, 0, 27);
        $str = preg_replace("/[^a-z0-9_]/", "", $str);
        if (!$str)
            return false;
        return $str;
    }

    public static function value_filter($str)
    {
        if (!$str)
            return false;
        $str = trim($str);
        $str = str_replace('"', '', $str);
        $str = str_replace("\r\n", "", $str);
        if (!$str)
            return false;
        return $str;
    }

    public static function parser($language, $name_module)
    {
        if (!$language || !$name_module)
            return false;
        $ini_file = '../includes/languages/' . $language . '/' . $name_module . '.lng';
        if (file_exists($ini_file)) {
            $out = parse_ini_file($ini_file);
            return $out;
        }
        return false;
    }

    public static function parser_edit($language)
    {
        if (!$language)
            return false;
        $ini_file = '../files/lng_edit/' . $language . '_iso.lng';
        if (file_exists($ini_file)) {
            $out = parse_ini_file($ini_file, true);
            return $out;
        }
        return false;
    }

    public static function update_lng($name_module, $lng_edit, $lng_module)
    {
        if (isset($lng_edit[$name_module])) {
            $lng_module_standart = array_diff_key($lng_module, $lng_edit[$name_module]);
            $lng_module = $lng_module_standart + $lng_edit[$name_module];
        }
        ksort($lng_module);
        return $lng_module;
    }

    public static function save_file($language, $lng_module)
    {
        $ini_file = '../files/lng_edit/' . $language . '_iso.lng';
        if (!empty($lng_module)) {
            $ini_text = array();
            foreach ($lng_module as $key => $val) {
                $ini_text[] = '[' . $key . ']';
                foreach ($val as $keyword => $phrase) {
                    $ini_text[] = $keyword . ' = "' . $phrase . '"';
                }
            }
            $ini_text = implode("\r\n", $ini_text);
            $open_ini_file = fopen($ini_file, w);
            fputs($open_ini_file, $ini_text);
            fclose($open_ini_file);
            @chmod($ini_file, 0777);
        } else {
            unlink($ini_file);
        }
    }
}

switch (Vars::$ACT) {
    case 'set':
        /*
        -----------------------------------------------------------------
        Меняем системный язык
        -----------------------------------------------------------------
        */
        $iso = isset($_POST['iso']) ? trim($_POST['iso']) : false;
        if ($iso && array_key_exists($iso, $lng_list)) {
            mysql_query("UPDATE `cms_settings` SET `val` = '" . mysql_real_escape_string($iso) . "' WHERE `key` = 'lng'");
        }
        header('Location: ' . Vars::$URI);
        break;

    case 'module':
        /*
        -----------------------------------------------------------------
        Вводим список языковых модулей
        -----------------------------------------------------------------
        */
        if (!$language || !array_key_exists($language, $lng_list)) {
            header('Location: index.php?act=languages&do=error');
            exit;
        }
        $array_module = glob('../includes/languages/' . $language . '/*.lng');
        $total = count($array_module);
        echo '<div class="phdr"><a href="index.php?act=languages"><b>' . lng('languages') . '</b></a> | <b>' . $lng_list[$language] . '</b>: ' . lng('modules') . '</div>';
        if ($do == 'error')
            echo '<div class="rmenu"><b>' . lng('error') . '!</b></div>';
        elseif ($do == 'reset')
            echo '<div class="rmenu"><b>' . lng('module_default') . '!</b></div>';
        if ($total) {
            $count = Vars::$START + Vars::$USER_SET['page_size'] > $total ? $total : Vars::$START + Vars::$USER_SET['page_size'];
            for ($i = Vars::$START; $i < $count; $i++) {
                echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                $name_module = preg_replace('#^' . $rootpath . 'includes/languages/' . $language . '/(.*?).lng$#isU', '$1', $array_module[$i], 1);
                $lng_module_standart = ini_file::parser($language, $name_module);
                $lng_edit = ini_file::parser_edit($language);
                $lng_module = ini_file::update_lng($name_module, $lng_edit, $lng_module_standart);
                echo '<a href="index.php?act=languages&amp;mod=info_module&amp;language=' . $language . '&amp;module=' . $name_module . '"><b>' . $name_module . '</b></a>' .
                    '<div class="sub">' .
                    '<a href="?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '">' . lng('phrases') . ' (' . count($lng_module) . ')</a>';
                if (!empty($lng_edit) && in_array($name_module, array_keys($lng_edit)))
                    echo ' | <a href="?act=languages&amp;mod=reset_module&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;start=' . Vars::$START . '">' . lng('reset') . ' изменений</a>';
                echo '</div></div>';//TODO: Перевести слово
            }
            echo '<div class="phdr">' . lng('total') . ': <b>' . $total . '</b></div>';
            if ($total > Vars::$USER_SET['page_size']) {
                echo '<div class="topmenu">' . Functions::displayPagination('?act=languages&amp;mod=module&amp;language=' . $language . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                    '<p><form action="?act=languages&amp;mod=module&amp;language=' . $language . '&amp;" method="post">' .
                    '<input type="text" name="page" size="2"/>' .
                    '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
            }
        } else {
            echo '<div class="rmenu"><p>' . lng('list_empty') . '!</p></div>' .
                '<div class="phdr"><a href="?act=languages"><b>' . lng('back') . '</b></a></div>';
        }
        echo '<p><a href="index.php">' . lng('admin_panel') . '</a></p>';
        break;

    case 'info_module':
        /*
        -----------------------------------------------------------------
        Переименовываем языковой модуль
        -----------------------------------------------------------------
        */
        $name_module = isset($_GET['module']) ? ini_file::key_filter($_GET['module']) : false;
        $lng_module_standart = ini_file::parser($language, $name_module);
        $lng_edit = ini_file::parser_edit($language);
        if (!$lng_module_standart) {
            header('Location: index.php?act=languages&do=error');
            exit;
        }
        $lng_module = ini_file::update_lng($name_module, $lng_edit, $lng_module_standart);
        echo '<div class="phdr"><b>' . $lng_list[$language] . '</b>: <a href="index.php?act=languages&amp;mod=module&amp;language=' . $language . '"><b>' . lng('modules') . '</b></a> | ' . lng('information') . '</div>' .
            '<div class="menu">' .
            '<p><h3>' . lng('information') . '</h3></p><p><ul>' .
            '<li><span class="gray">' . lng('name') . ':</span> ' . $name_module . '</li>' .
            '<li><span class="gray">' . lng('phras') . ':</span> ' . count($lng_module) . '</li>';
        if (isset($lng_edit[$name_module])) {
            $mass_edit = count(array_intersect_key($lng_module_standart, $lng_edit[$name_module]));
            if (!empty($mass_edit))
                echo '<li><span class="gray">' . lng('edit_phras') . ':</span> ' . $mass_edit . '</li>';
            $mass_add = count(array_diff_key($lng_edit[$name_module], $lng_module_standart));
            if (!empty($mass_add))
                echo '<li><span class="gray">' . lng('add_phras') . ':</span> ' . $mass_add . '</li>';
        }
        echo '</ul></p></div><div class="phdr">' .
            '<a href="?act=languages&amp;mod=module&amp;language=' . $language . '&amp;start=' . Vars::$START . '"><b>' . lng('back') . '</b></a>' .
            '</div><p><a href="index.php">' . lng('admin_panel') . '</a></p>';
        break;

    case 'reset_module':
        /*
        -----------------------------------------------------------------
        Отменяем все изменения в языковом модуле
        -----------------------------------------------------------------
        */
        $name_module = isset($_GET['module']) ? ini_file::key_filter($_GET['module']) : false;
        if (!$name_module) {
            header('Location: index.php?act=languages&do=error');
            exit;
        }
        if (isset($_GET['yes'])) {
            $lng_edit = ini_file::parser_edit($language);
            if (isset($lng_edit[$name_module])) {
                unset($lng_edit[$name_module]);
                ini_file::save_file($language, $lng_edit);
            }
            header('Location: index.php?act=languages&mod=module&language=' . $language . '&start=' . Vars::$START . '&do=reset');
            exit;
        } else {
            echo '<div class="phdr"><b>' . $lng_list[$language] . '</b>: <a href="index.php?act=languages&amp;mod=module&amp;language=' . $language . '"><b>' . lng('modules') . '</b></a> | ' . lng('reset') . '</div>' .
                '<div class="rmenu"><p>' . lng('module_resets') . '</p>' .
                '<p><form name="form" action="?act=languages&amp;mod=reset_module&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;start=' . Vars::$START . '&amp;yes" method="POST">' .
                '<input type="submit" name="submit" value="' . lng('continue') . '"/>&#160;' .
                '</form></p>' .
                '</div>' .
                '<div class="phdr"><a href="?act=languages&amp;mod=module&amp;language=' . $language . '&amp;start=' . Vars::$START . '"><b>' . lng('back') . '</b></a></div>' .
                '<p><a href="index.php">' . lng('admin_panel') . '</a></p>';
        }
        break;

    case 'phrases':
        /*
        -----------------------------------------------------------------
        Выводим список фраз модуля
        -----------------------------------------------------------------
        */
        $name_module = isset($_GET['module']) ? ini_file::key_filter($_GET['module']) : false;
        $symbol = isset($_GET['symbol']) ? trim(mb_substr($_GET['symbol'], 0, 1)) : 0;
        $lng_module_standart = ini_file::parser($language, $name_module);
        $lng_edit = ini_file::parser_edit($language);
        if (!$lng_module_standart) {
            header('Location: index.php?act=languages&do=error');
            exit;
        }
        $lng_module = ini_file::update_lng($name_module, $lng_edit, $lng_module_standart);
        $total = 0;
        $array_symbol = array();
        $array_result = array();
        $array_menu = array();
        $array_menu[] = $symbol ? '<a href="?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '">' . lng('all') . '</a>' : '<b>' . lng('all') . '</b>';
        foreach ($lng_module as $key => $val) {
            $symbol_1 = substr($key, 0, 1);
            if (!in_array($symbol_1, $array_symbol)) {
                $array_symbol[] = $symbol_1;
                $array_menu[] = $symbol && $symbol_1 == $symbol ? '<b>' . $symbol_1 . '</b>'
                    : '<a href="?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;symbol=' . $symbol_1 . '">' . $symbol_1 . '</a>';
            }
            if (!$symbol || $symbol_1 == $symbol) {
                ++$total;
                if ($total > Vars::$START && $total < Vars::$START + Vars::$USER_SET['page_size'])
                    $array_result[$key] = $val;
            }
        }
        $array_menu[] = '<a href="?act=languages&amp;mod=search&amp;language=' . $language . '&amp;module=' . $name_module . '">' . lng('search') . '</a>';
        $lng_module = $array_result;
        echo '<div class="phdr"><b>' . $lng_list[$language] . '</b>: <a href="index.php?act=languages&amp;mod=module&amp;language=' . $language . '"><b>' . lng('modules') . '</b></a> | ' . $name_module . ': ' . lng('phrases') . '</div>';
        echo '<div class="topmenu">' . Functions::displayMenu($array_menu) . '</div>';
        switch ($do) {
            case 'add':
                echo '<div class="gmenu"><b>' . lng('phrase_add') . '!</b></div>';
                break;

            case 'delete':
                echo '<div class="gmenu"><b>' . lng('phrase_delete') . '!</b></div>';
                break;

            case 'reset':
                echo '<div class="gmenu"><b>' . lng('phrase_default') . '!</b></div>';
                break;

            case 'edit':
                echo '<div class="gmenu"><b>' . lng('phrase_edit') . '!</b></div>';
                break;

            case 'error':
                echo '<div class="rmenu"><b>' . lng('error') . '!</b></div>';
                break;

            default :
                echo '';
        }
        if ($total) {
            echo '<form action="?act=languages&amp;mod=massdel_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '" method="post">';
            $i = 0;
            foreach ($lng_module as $key => $val) {
                echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                echo $key . '<br />' . $val . '<div class="sub">';
                if (isset($lng_edit[$name_module]) && in_array($key, array_keys($lng_edit[$name_module])))
                    echo '<input type="checkbox" name="delch[]" value="' . $key . '"/>&#160;';
                echo '<a href="?act=languages&amp;mod=edit_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;key=' . $key . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '">' . lng('edit') . '</a>';
                if (isset($lng_edit[$name_module]) && in_array($key, array_keys($lng_edit[$name_module]))) {
                    if (!in_array($key, array_keys($lng_module_standart)))
                        echo ' | <a href="?act=languages&amp;mod=delete_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;key=' . $key . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '">' . lng('delete') . '</a>';
                    else
                        echo ' | <a href="?act=languages&amp;mod=delete_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;key=' . $key . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '">' . lng('default') . '</a>';
                }
                echo '</div></div>';
                ++$i;
            }
            echo '<div class="rmenu"><input type="submit" value="' . lng('delete') . '"/></div></form>' .
                '<div class="gmenu"><form name="form" action="?act=languages&amp;mod=add_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;start=' . Vars::$START . '" method="POST">' .
                '<p><input type="submit" name="submit" value="' . lng('phrase_adds') . '"/></p>' .
                '</form></div>' .
                '<div class="phdr">' . lng('total') . ': <b>' . $total . '</b></div>';
            if ($total > Vars::$USER_SET['page_size']) {
                echo '<div class="topmenu">' . Functions::displayPagination('?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;symbol=' . $symbol . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                    '<p><form action="?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;symbol=' . $symbol . '" method="post">' .
                    '<input type="text" name="page" size="2"/>' .
                    '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
            }
        } else {
            echo '<div class="rmenu"><p>' . lng('list_empty') . '!</p></div>' .
                '<div class="gmenu"><form name="form" action="?act=languages&amp;mod=add_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;start=' . Vars::$START . '" method="POST">' .
                '<p><input type="submit" name="submit" value="' . lng('phrase_adds') . '"/></p>' .
                '</form></div>' .
                '<div class="phdr"><a href="?act=languages&amp;mod=module&amp;language=' . $language . '"><b>' . lng('back') . '</b></a></div>';
        }
        echo '<p><a href="index.php">' . lng('admin_panel') . '</a></p>';
        break;

    case 'search':
        /*
        -----------------------------------------------------------------
        Поиск по фразам модуля
        -----------------------------------------------------------------
        */
        $name_module = isset($_GET['module']) ? ini_file::key_filter($_GET['module']) : false;
        $symbol = isset($_GET['symbol']) ? trim(mb_substr($_GET['symbol'], 0, 1)) : 0;
        $lng_module_standart = ini_file::parser($language, $name_module);
        if (!$lng_module_standart) {
            header('Location: index.php?act=languages&do=error');
            exit;
        }
        $lng_edit = ini_file::parser_edit($language);
        $lng_module = ini_file::update_lng($name_module, $lng_edit, $lng_module_standart);
        $search_post = isset($_POST['search']) ? trim($_POST['search']) : false;
        $search_get = isset($_GET['search']) ? rawurldecode(trim($_GET['search'])) : false;
        $search = $search_post ? $search_post : $search_get;
        $array_symbol = array();
        $array_result = array();
        $array_menu = array();
        $total = 0;
        $array_menu[] = '<a href="?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '">' . lng('all') . '</a>';
        foreach ($lng_module as $key => $val) {
            $symbol_1 = substr($key, 0, 1);
            if (!in_array($symbol_1, $array_symbol)) {
                $array_symbol[] = $symbol_1;
                $array_menu[] = $symbol && $symbol_1 == $symbol ? '<b>' . $symbol_1 . '</b>'
                    : '<a href="?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;symbol=' . $symbol_1 . '">' . $symbol_1 . '</a>';
            }
            if (isset($search) && (stristr($key, $search) || stristr($val, $search))) {
                ++$total;
                if ($total > Vars::$START && $total < Vars::$START + Vars::$USER_SET['page_size'])
                    $array_result[$key] = $val;
            }
        }
        $array_menu[] = '<b>' . lng('search') . '</b>';
        $lng_module = $array_result;
        echo '<div class="phdr"><b>' . $lng_list[$language] . '</b>: <a href="index.php?act=languages&amp;mod=module&amp;language=' . $language . '"><b>' . lng('modules') . '</b></a> | ' . $name_module . ': ' . lng('search') . '</div>';
        echo '<div class="topmenu">' . Functions::displayMenu($array_menu) . '</div>';
        echo '<div class="gmenu"><form action="?act=languages&amp;mod=search&amp;language=' . $language . '&amp;module=' . $name_module . '" method="post">' .
            '<p><input type="text" value="' . ($search ? Validate::filterString($search) : '') . '" name="search" />' .
            '<input type="submit" value="' . lng('search') . '" name="submit" />' .
            '</p></form></div>';
        $i = 0;
        if ($total) {
            echo '<form action="?act=languages&amp;mod=massdel_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '" method="post">';
            foreach ($lng_module as $key => $val) {
                $search = str_replace('*', '', $search);
                $search_key = mb_strlen($search) < 3 ? $key : preg_replace('|(' . preg_quote($search, '/') . ')|siu', '<span style="background-color: #FFFF33">$1</span>', $key);
                $search_val = mb_strlen($search) < 3 ? $val : preg_replace('|(' . preg_quote($search, '/') . ')|siu', '<span style="background-color: #FFFF33">$1</span>', $val);
                echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                echo $search_key . '<br />' . $search_val . '<div class="sub">';
                if (isset($lng_edit[$name_module]) && in_array($key, array_keys($lng_edit[$name_module])))
                    echo '<input type="checkbox" name="delch[]" value="' . $key . '"/>&#160;';
                echo '<a href="?act=languages&amp;mod=edit_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;key=' . $key . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '">' . lng('edit') . '</a>';

                if (isset($lng_edit[$name_module]) && in_array($key, array_keys($lng_edit[$name_module]))) {
                    if (!in_array($key, array_keys($lng_module_standart)))
                        echo ' | <a href="?act=languages&amp;mod=delete_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;key=' . $key . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '">' . lng('delete') . '</a>';
                    else
                        echo ' | <a href="?act=languages&amp;mod=delete_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;key=' . $key . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '">' . lng('default') . '</a>';
                }
                echo '</div></div>';
                ++$i;
            }
            echo '<div class="rmenu"><input type="submit" value="' . lng('delete') . '"/></div></form>' .
                '<div class="phdr">' . lng('total') . ': <b>' . $total . '</b></div>';
            if ($total > Vars::$USER_SET['page_size']) {
                echo '<div class="topmenu">' . Functions::displayPagination('?act=languages&amp;mod=search&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                    '<p><form action="?act=languages&amp;mod=search&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;search=' . urlencode($search) . '" method="post">' .
                    '<input type="text" name="page" size="2"/>' .
                    '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
            }
        } else {
            echo '<div class="rmenu"><p>' . lng('list_empty') . '!</p></div>' .
                '<div class="phdr"><a href="?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '"><b>' . lng('back') . '</b></a></div>';
        }
        echo '<p><a href="index.php">' . lng('admin_panel') . '</a></p>';
        break;

    case 'edit_phrase':
        /*
        -----------------------------------------------------------------
        Редактируем отдельную фразу языкового модуля
        -----------------------------------------------------------------
        */
        $name_module = isset($_GET['module']) ? ini_file::key_filter($_GET['module']) : false;
        $symbol = isset($_GET['symbol']) ? trim(mb_substr($_GET['symbol'], 0, 1)) : 0;
        $key = isset($_GET['key']) ? ini_file::key_filter($_GET['key']) : 0;
        $lng_module_standart = ini_file::parser($language, $name_module);
        $lng_edit = ini_file::parser_edit($language);
        if (!$lng_module_standart) {
            header('Location: index.php?act=languages&do=error');
            exit;
        }
        $lng_module = ini_file::update_lng($name_module, $lng_edit, $lng_module_standart);
        if (!in_array($key, array_keys($lng_module))) {
            header('Location: index.php?act=languages&mod=phrases&language=' . $language . '&module=' . $module . '&symbol=' . $symbol . '&start=' . Vars::$START . '&do=error');
            exit;
        }
        if (isset($_POST['submit']) && !empty($_POST['value'])) {
            $value_edit = ini_file::value_filter($_POST['value']);
            if (!$value_edit) {
                header('Location: index.php?act=languages&mod=edit_phrase&language=' . $language . '&module=' . $name_module . '&symbol=' . $symbol . '&key=' . $key . '&start=' . Vars::$START);
                exit;
            }
            if ($lng_module[$key] != $value_edit) {
                if (!$lng_module_standart[$key] || $lng_module_standart[$key] != $value_edit) {
                    $lng_edit[$name_module][$key] = $value_edit;
                } else {
                    if (count($lng_edit[$name_module]) > 1)
                        unset($lng_edit[$name_module][$key]);
                    elseif (count($lng_edit[$name_module]))
                        unset($lng_edit[$name_module]);
                }
                ini_file::save_file($language, $lng_edit);
            }
            header('Location: index.php?act=languages&mod=phrases&language=' . $language . '&module=' . $name_module . '&symbol=' . $symbol . '&start=' . Vars::$START . '&do=edit');
            exit;
        } else {
            echo '<div class="phdr"><b>' . $lng_list[$language] . '</b>: <a href="index.php?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;symbol=' . $symbol . '"><b>' . lng('phrases') . '</b></a> | ' . lng('edit') . '</div>' .
                '<form name="form" action="?act=languages&amp;mod=edit_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;key=' . $key . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '" method="POST"><div class="menu">' .
                '<p><h3>' . lng('value') . '</h3>' .
                '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="value">' . htmlentities($lng_module[$key], ENT_QUOTES, 'UTF-8') . '</textarea></p></div>' .
                '<div class="gmenu"><input type="submit" name="submit" value="' . lng('save') . '"/>' .
                '</div></form>' .
                '<div class="phdr"><a href="?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '"><b>' . lng('back') . '</b></a></div>' .
                '<p><a href="index.php">' . lng('admin_panel') . '</a></p>';
        }
        break;

    case 'add_phrase':
        /*
        -----------------------------------------------------------------
        Добавляем фразу в языковой модуль
        -----------------------------------------------------------------
        */
        $name_module = isset($_GET['module']) ? ini_file::key_filter($_GET['module']) : false;
        $lng_module_standart = ini_file::parser($language, $name_module);
        if (!$lng_module_standart) {
            header('Location: index.php?act=languages&do=error');
            exit;
        }
        if (isset($_POST['submit']) && !empty($_POST['key']) && !empty($_POST['value'])) {
            $key = ini_file::key_filter($_POST['key']);
            $value = ini_file::value_filter($_POST['value']);
            $lng_edit = ini_file::parser_edit($language);
            $lng_module = ini_file::update_lng($name_module, $lng_edit, $lng_module_standart);
            if (!$key || !$value || in_array($key, array_keys($lng_module))) {
                header('Location: index.php?act=languages&mod=add_phrase&language=' . $language . '&module=' . $name_module . '&start=' . Vars::$START . '&do=no_key');
                exit;
            }
            // Добавляем ключ и его значение
            $lng_edit[$name_module][$key] = $value;
            ini_file::save_file($language, $lng_edit);
            header('Location: index.php?act=languages&mod=phrases&language=' . $language . '&module=' . $name_module . '&start=' . Vars::$START . '&do=add');
            exit;
        } else {
            echo '<div class="phdr"><b>' . $lng_list[$language] . '</b>: <a href="index.php?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '"><b>' . lng('phrases') . '</b></a> | ' . lng('add') . '</div>';
            if ($do == 'no_key')
                echo '<div class="rmenu"><b>' . lng('no_key') . '</b></div>';
            echo '<form name="form" action="?act=languages&amp;mod=add_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;start=' . Vars::$START . '" method="POST">' .
                '<div class="menu"><p><h3>' . lng('key') . '</h3>' .
                '<input type="text" name="key" maxlength="27"/></p>' .
                '<p><h3>' . lng('value') . '</h3>' .
                '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="value"></textarea></p></div>' .
                '<div class="gmenu"><input type="submit" name="submit" value="' . lng('save') . '"/>' .
                '</div></form>' .
                '<div class="phdr"><a href="?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;start=' . Vars::$START . '"><b>' . lng('back') . '</b></a></div>' .
                '<p><a href="index.php">' . lng('admin_panel') . '</a></p>';
        }
        break;

    case 'delete_phrase':
        /*
        -----------------------------------------------------------------
        Удаляем отдельную фразу из языкового модуля
        -----------------------------------------------------------------
        */
        $name_module = isset($_GET['module']) ? ini_file::key_filter($_GET['module']) : false;
        $symbol = isset($_GET['symbol']) ? trim(mb_substr($_GET['symbol'], 0, 1)) : 0;
        $key = isset($_GET['key']) ? ini_file::key_filter($_GET['key']) : 0;
        $lng_module_standart = ini_file::parser($language, $name_module);
        $lng_edit = ini_file::parser_edit($language);
        if (!$lng_module_standart) {
            header('Location: index.php?act=languages&do=error');
            exit;
        }
        $lng_module = ini_file::update_lng($name_module, $lng_edit, $lng_module_standart);
        if (!in_array($key, array_keys($lng_module))) {
            header('Location: index.php?act=languages&mod=phrases&language=' . $language . '&module=' . $module . '&symbol=' . $symbol . '&start=' . Vars::$START . '&do=error');
            exit;
        }
        if (isset($_GET['yes'])) {
            if (count($lng_edit[$name_module]) > 1)
                unset($lng_edit[$name_module][$key]);
            elseif (count($lng_edit[$name_module]))
                unset($lng_edit[$name_module]);
            ini_file::save_file($language, $lng_edit);
            $referer = isset($lng_module_standart[$key]) ? 'reset' : 'delete';
            header('Location: index.php?act=languages&mod=phrases&language=' . $language . '&module=' . $name_module . '&symbol=' . $symbol . '&start=' . Vars::$START . '&do=' . $referer);
            exit;
        } else {
            echo '<div class="phdr"><b>' . $lng_list[$language] . '</b>: <a href="index.php?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;symbol=' . $symbol . '"><b>' . lng('phrases') . '</b></a> | ' . (isset($lng_module_standart[$key])
                ? '' . lng('reset') . '' : lng('delete')) . '</div>' .
                '<div class="rmenu"><p>';
            if (isset($lng_module_standart[$key]))
                echo lng('phrase_resets');
            else
                echo lng('phrase_deletes');
            echo '</p><p><form name="form" action="?act=languages&amp;mod=delete_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;key=' . $key . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '&amp;yes" method="POST">' .
                '<input type="submit" name="submit" value="' . lng('continue') . '"/>&#160;' .
                '</form></p>' .
                '</div>' .
                '<div class="phdr"><a href="?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '"><b>' . lng('back') . '</b></a></div>' .
                '<p><a href="index.php">' . lng('admin_panel') . '</a></p>';
        }
        break;

    case 'massdel_phrase':
        /*
        -----------------------------------------------------------------
        Удаляем список фраз из языкового модуля
        -----------------------------------------------------------------
        */
        $name_module = isset($_GET['module']) ? ini_file::key_filter($_GET['module']) : false;
        $symbol = isset($_GET['symbol']) ? trim(mb_substr($_GET['symbol'], 0, 1)) : 0;
        $lng_edit = ini_file::parser_edit($language);
        if (!$name_module) {
            header('Location: index.php?act=languages&do=error');
            exit;
        }
        if (isset($_GET['yes'])) {
            $mass_dell = $_SESSION['mass_dell'];
            foreach ($mass_dell as $key) {
                if (isset($lng_edit[$name_module][$key]))
                    unset($lng_edit[$name_module][$key]);
            }
            if (!count($lng_edit[$name_module]))
                unset($lng_edit[$name_module]);
            ini_file::save_file($language, $lng_edit);
            header('Location: index.php?act=languages&mod=phrases&language=' . $language . '&module=' . $name_module . '&symbol=' . $symbol . '&start=' . Vars::$START . '&do=massdel');
            exit;
        } else {
            if (!$_POST['delch']) {
                header('Location: index.php?act=languages&mod=phrases&language=' . $language . '&module=' . $name_module . '&symbol=' . $symbol . '&start=' . Vars::$START);
                exit;
            }
            foreach ($_POST['delch'] as $key) {
                $mass_dell[] = ini_file::key_filter($key);
            }
            $_SESSION['mass_dell'] = $mass_dell;
            echo'<div class="phdr"><b>' . $lng_list[$language] . '</b>: <a href="index.php?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;symbol=' . $symbol . '"><b>' . lng('phrases') . '</b></a> | ' . lng('mass_delete') . '</div>' .
                '<div class="rmenu"><p>' . lng('mass_deletes') . '</p>' .
                '<p><form name="form" action="?act=languages&amp;mod=massdel_phrase&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;symbol=' . $symbol . '&amp;start=' . Vars::$START . '&amp;yes" method="POST">' .
                '<input type="submit" name="submit" value="' . lng('continue') . '"/>&#160;' .
                '</form></p>' .
                '</div>' .
                '<div class="phdr"><a href="?act=languages&amp;mod=phrases&amp;language=' . $language . '&amp;module=' . $name_module . '&amp;start=' . Vars::$START . '"><b>' . lng('back') . '</b></a></div>' .
                '<p><a href="index.php">' . lng('admin_panel') . '</a></p>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Выводим список доступных языков
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . lng('admin_panel') . '</b></a> | ' . lng('language_default') . '</div>';
        if (isset($_GET['refresh'])) {
            echo '<div class="gmenu"><p>' . lng('refresh_descriptions_ok') . '</p></div>';
        }
        echo'<div class="menu">' .
            '<form action="' . Vars::$URI . '?act=set" method="post"><p>' .
            '<table><tr><td>&nbsp;</td><td style="padding-bottom:4px"><h3>' . lng('language_system') . '</h3></td></tr>';
        foreach ($lng_desc as $key => $val) {
            $lng_menu = array(
                (!empty($val['author']) ? '<span class="gray">' . lng('author') . ':</span> ' . $val['author'] : ''),
                (!empty($val['author_email']) ? '<span class="gray">E-mail:</span> ' . $val['author_email'] : ''),
                (!empty($val['author_url']) ? '<span class="gray">URL:</span> ' . $val['author_url'] : ''),
                (!empty($val['description']) ? '<span class="gray">' . lng('description') . ':</span> ' . $val['description'] : '')
            );
            echo'<tr>' .
                '<td valign="top"><input type="radio" value="' . $key . '" name="iso" ' . ($key == Vars::$SYSTEM_SET['lng'] ? 'checked="checked"' : '') . '/></td>' .
                '<td style="padding-bottom:6px">' .
                (file_exists(ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'flags' . DIRECTORY_SEPARATOR . $key . '.gif')
                    ? '<img src="' . Vars::$HOME_URL . '/images/flags/' . $key . '.gif" alt=""/>&#160;'
                    : ''
                ) .
                '<a href="index.php?act=languages&amp;mod=module&amp;language=' . $key . '"><b>' . $val['name'] . '</b></a>&#160;<span class="green">[' . $key . ']</span>' .
                '<div class="sub">' . Functions::displayMenu($lng_menu, '<br />') . '</div></td>' .
                '</tr>';
        }
        echo'<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="' . lng('save') . '" /></td></tr>' .
            '</table></p>' .
            '</form></div>' .
            '<div class="phdr">' . lng('total') . ': <b>' . count($lng_desc) . '</b></div>' .
            '<p><a href="' . Vars::$URI . '?refresh">' . lng('refresh_descriptions') . '</a><br />' .
            '<a href="' . Vars::$MODULE_URI . '">' . lng('admin_panel') . '</a></p>';
}