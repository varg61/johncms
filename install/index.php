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

define('INSTALL', 1);
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' .
    '<html xmlns="http://www.w3.org/1999/xhtml">' .
    '<title>JohnCMS 4.0.0 - Установка</title>' .
    '<style type="text/css">' .
    'body {font-family: Arial, Helvetica, sans-serif; font-size: small; color: #000000; background-color: #FFFFFF}' .
    'h2{margin: 0; padding: 0; padding-bottom: 4px;}' .
    'h3{margin: 0; padding: 0; padding-bottom: 2px;}' .
    'ul{margin:0; padding-left:20px; }' .
    'li{padding-bottom: 6px; }' .
    '.red{color: #FF0000; font-weight: bold;}' .
    '.green{color: #009933; font-weight: bold;}' .
    '.gray{color: #999999; font: small;}' .
    '</style>' .
    '</head><body>' .
    '<h2 class="green">JohnCMS 4.0.0</h2><hr />';
switch ($_GET['act']) {
    case 'mode':
        echo '<h3>Инсталляция</h3>';
        echo 'Новая установка JohnCMS';
        echo '<form action="index.php?act=install" method="post"><input type="submit" name="submit" value="Инсталляция" /></form>';
        echo '<hr />';
        echo '<h3>Обновление</h3>';
        echo 'Обновление с версии JohnCMS 3.2.2';
        echo '<form action="index.php?act=update" method="post"><input type="submit" name="submit" value="Обновление" /></form>';
        break;

    case 'install': break;

    case 'update':
        break;
        default:
        /*
        -----------------------------------------------------------------
        Шаг 1 - Выбор языка инсталляции
        -----------------------------------------------------------------
        */
        $language = 'en'; // Язык по-умолчанию
        $lng_array = array();
        foreach(glob('languages/*/language.ini') as $var){
            // Считываем список доступных языков
            $ini = parse_ini_file($var);
            $lng_array[$ini['iso']] = $ini['name'];
        }
        // Получаем язык Браузера, заданный по-умолчанию
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $accept_lang = explode(',', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
            foreach ($accept_lang as $lng) {
                $lng = substr($lng, 0, 2);
                if (array_key_exists($lng, $lng_array)) {
                    // Если язык имеется, то устанавливаем его по-умолчанию
                    $language = $lng;
                    break;
                }
            }
        }
        echo '<p class="red">Please select an installation language<br />' .
            'Пожалуйста выберите язык инсталляции</p>';
        echo '<form action="index.php?act=mode" method="post">';
        foreach ($lng_array as $key => $val) {
            // Выбор языка из списка
            echo '<input type="radio" name="lng" value="' . $key . '" ' . ($key == $language ? 'checked="checked"' : '') . ' />&#160;' . $val . '<br />';
        }
        echo '<p><input type="submit" name="submit" value="Продолжить" /></p>';
        echo '</form>';
}
echo '<hr />&copy;&#160;Powered by <a href="http://johncms.com">JohnCMS</a></body></html>';
?>