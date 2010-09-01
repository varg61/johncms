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
Russian Language Pack
*/

/*
-----------------------------------------------------------------
Задаем язык перевода
-----------------------------------------------------------------
Внимание! Значение переменной $install_language указывать
в соответствии с двухбуквенной классификацией ISO 639-1
http://www.sil.org/iso639-3/codes.asp?order=639_1

Пример:
Русский     $install_language = 'ru';
Английский  $install_language = 'en';

Обязательно указать имя языка в $lng['main']['language_name']
-----------------------------------------------------------------
*/
$install_language = 'ru';

define('_IN_JOHNCMS', 1);
$rootpath = '../../../';
require('../../../incfiles/core.php');

/*
-----------------------------------------------------------------
Предварительная очистка таблицы
-----------------------------------------------------------------
*/
mysql_query("DELETE FROM `cms_languages` WHERE `iso` = '$install_language'");
mysql_query("OPTIMIZE TABLE `cms_languages`");

/*
-----------------------------------------------------------------
Читаем языковые файлы и заносим информацию в базу
-----------------------------------------------------------------
*/
foreach(glob('lng_*.txt') as $var){
    $module = strtr($var, array('lng_' => '', '.txt' => ''));
    foreach(file($var) as $lng){
        $lng = explode(':::', $lng);
        mysql_query("INSERT INTO `cms_languages` SET
            `iso` = '$install_language',
            `module` = '$module',
            `var` = '" . mysql_real_escape_string(trim($lng[0])) . "',
            `default` = '" . mysql_real_escape_string(trim($lng[1])) . "'
        ");
    }
}

/*
-----------------------------------------------------------------
Загружаем справочные файлы в базу
-----------------------------------------------------------------
*/
$lng = array();
$lng['faq'] = array(
    'forum_rules_text'              => file_get_contents('faq_forum.txt'),
    'tags_faq_text'                 => file_get_contents('faq_tags.txt'),
    'translit_help_text'            => file_get_contents('faq_translit.txt')
);

foreach ($lng as $module => $array) {
    foreach ($array as $key => $val) {
        mysql_query("INSERT INTO `cms_languages` SET
            `iso` = '$install_language',
            `module` = '$module',
            `var` = '" . mysql_real_escape_string($key) . "',
            `default` = '" . mysql_real_escape_string($val) . "'
        ");
    }
}

?>