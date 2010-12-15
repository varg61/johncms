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

/*
-----------------------------------------------------------------
Загрузка выбранного языка для инсталлятора
-----------------------------------------------------------------
*/
$language = 'ru';
$lng_array = array ();
foreach (glob('languages/*/language.ini') as $var) {
    $ini = parse_ini_file($var, true);
    $lng_array[$ini['description']['iso']] = $ini['description']['name'];
}
if (isset($_POST['lng'])) {
    $lang = substr(trim($_POST['lng']), 0, 2);
    if (array_key_exists($lang, $lng_array))
        $language = $lang;
} elseif (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $accept_lang = explode(',', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
    foreach ($accept_lang as $var) {
        $lang = substr($var, 0, 2);
        if (array_key_exists($lang, $lng_array)) {
            $language = $lang;
            break;
        }
    }
}
if ($lang = parse_ini_file('languages/' . $language . '/language.ini', true)) {
    $lng = $lang['install'];
} else {
    echo 'ERROR: Language';
    exit;
}

/*
-----------------------------------------------------------------
HTML Пролог
-----------------------------------------------------------------
*/
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' .
    '<html xmlns="http://www.w3.org/1999/xhtml">' .
    '<title>JohnCMS 4.0.0 - Установка</title>' .
    '<style type="text/css">' .
    'body {font-family: Arial, Helvetica, sans-serif; font-size: small; color: #000000; background-color: #FFFFFF}' .
    'h2{margin: 0; padding: 0; padding-bottom: 4px;}' .
    'h3{margin: 0; padding: 0; padding-bottom: 2px; color: #0000EE}' .
    'ul{margin:0; padding-left:20px; }' .
    'li{padding-bottom: 6px; }' .
    '.red{color: #FF0000; font-weight: bold;}' .
    '.green{color: #009933; font-weight: bold;}' .
    '.gray{color: #999999; font: small;}' .
    '</style>' .
    '</head><body>' .
    '<h2 class="green">JohnCMS 4.0.0</h2><hr />';

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
switch ($_REQUEST['act']) {
    case 'mode':
        echo '<h3>Инсталляция</h3>';
        echo 'Новая установка JohnCMS';
        echo '<form action="index.php?act=install" method="post"><input type="submit" name="submit" value="Инсталляция" /></form>';
        echo '<hr />';
        echo '<h3>Обновление</h3>';
        echo 'Обновление с версии JohnCMS 3.2.2';
        echo '<form action="index.php?act=update" method="post"><input type="submit" name="submit" value="Обновление" /></form>';
        break;

    case 'install':
        /*
        -----------------------------------------------------------------
        Новая инсталляция
        -----------------------------------------------------------------
        */
        require('includes/check.php');
        break;

    case 'update':
        /*
        -----------------------------------------------------------------
        Обновление с предыдущей версии
        -----------------------------------------------------------------
        */
        echo 'UPDATE';
        break;

    case 'languages':
        /*
        -----------------------------------------------------------------
        Установка языковых пакетов
        -----------------------------------------------------------------
        */
        echo 'LANGUAGES';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Главное меню инсталлятора
        -----------------------------------------------------------------
        */
        echo '<form action="index.php" method="post">' .
            '<table>' .
            '<tr><td valign="top"><input type="radio" name="act" value="install" checked="checked" /></td><td style="padding-bottom:6px"><h3>' . $lng['install'] . '</h3><small>' . $lng['install_note'] . '</small></td></tr>' .
            '<tr><td valign="top"><input type="radio" name="act" value="update" /></td><td><h3 style="padding-bottom:6px">' . $lng['update'] . '</h3><small>' . $lng['update_note'] . '</small></td></tr>' .
            '<tr><td valign="top"><input type="radio" name="act" value="languages" /></td><td><h3>' . $lng['install_languages'] . '</h3><small>' . $lng['install_languages_note'] . '</small></td></tr>' .
            '<tr><td>&nbsp;</td><td style="padding-top:6px"><input type="submit" name="submit" value="' . $lng['continue'] . '" /></td></tr>' .
            '</table>' .
            '</form>' .
            '<hr />' .
            '<form action="index.php" method="post"><table>' .
            '<tr><td>&nbsp;</td><td><h3>' . $lng['change_language'] . '</h3></td></tr>';
        foreach ($lng_array as $key => $val) {
            echo '<tr><td valign="top"><input type="radio" name="lng" value="' . $key . '" ' . ($key == $language ? 'checked="checked"' : '') . ' /></td>' .
                '<td>' . $val . '</td></tr>';
        }
        echo '<tr><td>&nbsp;</td><td style="padding-top:6px"><input type="submit" name="submit" value="' . $lng['change'] . '" /></td></tr>' .
            '</table></form>';
}
echo '<hr />&copy;&#160;Powered by <a href="http://johncms.com">JohnCMS</a></body></html>';
?>