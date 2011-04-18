<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('INSTALL', 1);
define('_IN_JOHNCMS', 1);
@ini_set("max_execution_time", "600");

class install {
    public $sql_errors = array(); // Ошибки парсинга SQL

    function __construct() {
        // Конструктор
    }

    /*
    -----------------------------------------------------------------
    Проверяем настройки PHP
    -----------------------------------------------------------------
    */
    public function check_php() {
    }

    /*
    -----------------------------------------------------------------
    Парсинг SQL файла
    -----------------------------------------------------------------
    */
    public function parse_sql($file = false) {
        if ($file && file_exists($file)) {
            $query = fread(fopen($file, 'r'), filesize($file));
            $query = trim($query);
            $query = ereg_replace("\n#[^\n]*\n", "\n", $query);
            $buffer = array();
            $ret = array();
            $in_string = false;
            for ($i = 0; $i < strlen($query) - 1; $i++) {
                if ($query[$i] == ";" && !$in_string) {
                    $ret[] = substr($query, 0, $i);
                    $query = substr($query, $i + 1);
                    $i = 0;
                }
                if ($in_string && ($query[$i] == $in_string) && $buffer[1] != "\\") {
                    $in_string = false;
                } elseif (!$in_string && ($query[$i] == '"' || $query[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\")) {
                    $in_string = $query[$i];
                }
                if (isset($buffer[1])) {
                    $buffer[0] = $buffer[1];
                }
                $buffer[1] = $query[$i];
            }
            if (!empty($query)) {
                $ret[] = $query;
            }
            for ($i = 0; $i < count($ret); $i++) {
                $ret[$i] = trim($ret[$i]);
                if (!empty($ret[$i]) && $ret[$i] != "#") {
                    if (!mysql_query($ret[$i])) {
                        $this->sql_errors[] = mysql_error();
                    }
                }
            }
        } else {
            $this->sql_errors[] = 'Fatal error!';
            return false;
        }
    }
}

$install = new install();


// Служебные переменные
$install = false;
$update = false;
$lng_install = false;
$lng_iso = 'ru';
$system_build = 710; // Версия системы

/*
-----------------------------------------------------------------
Проверка, инсталлирована система, или нет
-----------------------------------------------------------------
*/
if (file_exists('../incfiles/db.php') && file_exists('../incfiles/core.php')) {
    // Если система инсталлирована
    require('../incfiles/core.php');
    if (!$core->system_build)
        $update = true;
} else {
    // Если система не инсталлирована
    $act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
    $mod = isset($_REQUEST['mod']) ? trim($_REQUEST['mod']) : '';
    $install = true;
    session_name('SESID');
    session_start();
}

/*
-----------------------------------------------------------------
Получаем список доступных языков
-----------------------------------------------------------------
*/
$lng_list = array();
$lng_phrases = array();
foreach (glob('languages/*.lng') as $file) {
    $lng_list[] = basename($file, ".lng");
    $lng_phrases[basename($file, ".lng")] = parse_ini_file($file);
}
if (empty($lng_list) || empty($lng_phrases))
    die('ERROR: there are no languages for installation');

/*
-----------------------------------------------------------------
Переключаем язык интерфейса Инсталлятора
-----------------------------------------------------------------
*/
if (isset($_REQUEST['lng_id']) && in_array($_REQUEST['lng_id'], $lng_key)) {
    // Меняем язык по запросу из формы
    echo 'Из формы';
    $lng_id = intval($_REQUEST['lng_id']);
} elseif (isset($core->lng) && in_array($core->lng, $lng_list)) {
    // Если система проинсталлирована, то используем ее язык
    echo 'В системе';
    $lng_iso = $core->lng;
} elseif (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    // Устанавливаем язык по браузеру
    echo 'По браузеру';
    $browser_lang = explode(',', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
    foreach ($browser_lang as $var) {
        $lang = substr($var, 0, 2);
        if (in_array($lang, $lng_list)) {
            $lng_iso = $lang;
            break;
        }
    }
}
$lng = $lng_phrases[$lng_iso];

/*
-----------------------------------------------------------------
HTML Пролог и заголовки страниц
-----------------------------------------------------------------
*/
switch ($act) {
    case 'install':
        $pagetitle = $lng['install'];
        $pagedesc = '';
        break;

    case 'update':
        $pagetitle = $lng['update'];
        $pagedesc = 'Обновление с версии 3.2.2';
        break;

    case 'languages':
        $pagetitle = $lng['install_languages'];
        $pagedesc = '';
        break;

    default:
        $pagetitle = $lng['install'];
        $pagedesc = false;
}
ob_start();
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' .
     '<html xmlns="http://www.w3.org/1999/xhtml">' .
     '<title>JohnCMS 4.1.0 - ' . $pagetitle . '</title>' .
     '<style type="text/css">' .
     'body {font-family: Arial, Helvetica, sans-serif; font-size: small; color: #000000; background-color: #FFFFFF}' .
     'h2{margin: 0; padding: 0; padding-bottom: 4px;}' .
     'h3{margin: 0; padding: 0; padding-bottom: 2px;}' .
     'ul{margin:0; padding-left:20px; }' .
     'li{padding-bottom: 6px; }' .
     '.red{color: #FF0000;}' .
     '.green{color: #009933;}' .
     '.blue{color: #0000EE;}' .
     '.gray{color: #888888;}' .
     '.small{font-size: x-small}' .
     '</style>' .
     '</head><body>' .
     '<h2 class="green">JohnCMS 4.1.0</h2>' . $pagedesc . '<hr />';

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$actions = array(
    'install',
    'update',
    'languages'
);
if (in_array($act, $actions) && file_exists('includes/' . $act . '.php')) {
    require_once('includes/' . $act . '.php');
} else {
    /*
    -----------------------------------------------------------------
    Главное меню инсталлятора
    -----------------------------------------------------------------
    */
    echo '<form action="index.php" method="post">' .
         '<table>' .
         '<tr><td valign="top"><input type="radio" name="act" value="install" ' . ($install ? 'checked="checked"' : 'disabled="disabled"') . '/></td><td style="padding-bottom:6px"><h3 class="' . ($install ? 'blue' : 'gray') . '">'
         . $lng['install'] . '</h3><small>'
         . ($install ? $lng['install_note'] : '<span class="gray">' . $lng['alredy_installed'] . '</span>') . '</small></td></tr>' .
         '<tr><td valign="top"><input type="radio" name="act" value="update" ' . ($update ? 'checked="checked"' : 'disabled="disabled"') . '/></td><td style="padding-bottom:6px"><h3 class="' . ($update ? 'blue' : 'gray') . '">'
         . $lng['update'] . '</h3><small>'
         . ($update ? $lng['update_note'] : '<span class="gray">' . $lng['update_not_required'] . '</span>') . '</small></td></tr>' .
         '<tr><td valign="top"><input type="radio" name="act" value="languages" ' . (!$install && !$update ? 'checked="checked"' : 'disabled="disabled"') . '/></td><td style="padding-bottom:6px"><h3 class="'
         . (!$install && !$update ? 'blue' : 'gray') . '">' . $lng['install_languages'] . '</h3><small>'
         . (!$install && !$update ? $lng['install_languages_note'] : '<span class="gray">' . $lng['install_languages_impossible'] . '</span>') . '</small></td></tr>' .
         '<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="' . $lng['continue'] . '" /></td></tr>' .
         '</table>' .
         '<input type="hidden" name="lng_id" value="' . $lng_id . '" />' .
         '</form><hr />' .
         '<form action="index.php" method="post"><table>' .
         '<tr><td>&nbsp;</td><td><h3>' . $lng['change_language'] . '</h3></td></tr>';
    foreach ($lng_set as $key => $val) {
        echo '<tr>' .
             '<td valign="top"><input type="radio" name="lng_id" value="' . $key . '" ' . ($key == $lng_id ? 'checked="checked"' : '') . ' /></td>' .
             '<td>' . $val['name'] . (isset($core->lng) && $core->lng == $val['iso'] ? ' <small class="red">[' . $lng['system'] . ']</small>' : '') . '</td>' .
             '</tr>';
    }
    echo '<tr><td>&nbsp;</td><td style="padding-top:6px"><input type="submit" name="submit" value="' . $lng['change'] . '" /></td></tr>' .
         '</table></form>';
}
echo '<hr />&copy;&#160;Powered by <a href="http://johncms.com">JohnCMS</a></body></html>';
?>