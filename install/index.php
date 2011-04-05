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
@ini_set("max_execution_time", "600");

// Служебные переменные
$lng_iso = 'ru';
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
$mod = isset($_REQUEST['mod']) ? trim($_REQUEST['mod']) : '';
session_name('SESID');
session_start();

/*
-----------------------------------------------------------------
Получаем список доступных языков
-----------------------------------------------------------------
*/
$lng_list = array();
$lng_phrases = array();
foreach (glob('languages/*.lng') as $file) {
    $iso = basename($file, ".lng");
    $lng_list[] = $iso;
    $lng_phrases[$iso] = parse_ini_file($file);
}
if (empty($lng_list) || empty($lng_phrases))
    die('ERROR: there are no languages for installation');

/*
-----------------------------------------------------------------
Переключаем язык интерфейса Инсталлятора
-----------------------------------------------------------------
*/
if (isset($_REQUEST['lng']) && in_array($_REQUEST['lng'], $lng_list)) {
    // Меняем язык по запросу из формы
    $lng_iso = substr($_REQUEST['lng'], 0, 2);
} elseif (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    // Устанавливаем язык по браузеру
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
ob_start();
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' .
     '<html xmlns="http://www.w3.org/1999/xhtml">' .
     '<title>JohnCMS 5.0.0 - ' . $lng['install'] . '</title>' .
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
     '<h2 class="green">JohnCMS 5.0.0</h2>' . $pagedesc . '<hr />';

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$actions = array(
    'install'
);
if (in_array($act, $actions) && file_exists('includes/' . $act . '.php')) {
    require_once('includes/' . $act . '.php');
} else {
    /*
    -----------------------------------------------------------------
    Главное меню инсталлятора
    -----------------------------------------------------------------
    */
    echo '<h3 class="blue">' . $lng['install_note'] . '</h3>' .
         '<a href="index.php?act=install&amp;lng=' . $lng_iso . '"><b>' . $lng['start_installation'] . '</b></a>' .
         '<hr />' .
         '<h3 class="blue">' . $lng['change_language'] . '</h3>' .
         '<form action="index.php" method="post">';
    foreach ($lng_list as $val) {
        echo '<div><input type="radio" name="lng" value="' . $val . '" ' . ($val == $lng_iso ? 'checked="checked"' : '') . ' />' .
             '&#160;' . $lng_phrases[$val]['language_name'] . '</div>';
    }
    echo '<div style="padding-top:6px"><input type="submit" name="submit" value="' . $lng['change'] . '" /></div></form>';
}
echo '<hr />&copy;&#160;Powered by <a href="http://johncms.com">JohnCMS</a></body></html>';
?>