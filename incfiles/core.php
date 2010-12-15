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

defined('_IN_JOHNCMS') or die('Error: restricted access');
Error_Reporting(E_ALL & ~E_NOTICE);
@ini_set('session.use_trans_sid', '0');
@ini_set('arg_separator.output', '&amp;');
date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');
if (!isset($rootpath))
    $rootpath = '../';

/*
-----------------------------------------------------------------
Автозагрузка Классов
-----------------------------------------------------------------
*/
spl_autoload_register('autoload');
function autoload($name) {
    global $rootpath;
    $file = $rootpath . 'incfiles/classes/' . $name . '.php';

    if (file_exists($file))
        require_once($file);
}

/*
-----------------------------------------------------------------
Инициализируем Ядро системы
-----------------------------------------------------------------
*/
$core = new core() or die('Error: Core System');

/*
-----------------------------------------------------------------
Получаем системные переменные
-----------------------------------------------------------------
*/
$ip = $core->ip;                         // Адрес IP
$agn = $core->user_agent;                // User Agent
$set = $core->system_settings;           // Системные настройки
$realtime = $core->system_time;          // Системное время с учетом сдвига
$language = $core->system_language;      // Язык системы
$lng = $core->language_phrases;          // Фразы выбранного языка
$is_mobile = functions::mobile_detect(); // Определение мобильного браузера
$home = $set['homeurl'];                 // Домашняя страница

/*
-----------------------------------------------------------------
Получаем пользовательские переменные
-----------------------------------------------------------------
*/
$user_id = $core->user_id;        // Идентификатор пользователя
$rights = $core->user_rights;     // Права доступа
$datauser = $core->user_data;     // Все данные пользователя
$login = $datauser['name'];       // Ник пользователя
$set_user = $core->user_settings; // Пользовательские настройки
$ban = $core->user_ban;           // Бан
$kmess = $set_user['kmess'] > 4 && $set_user['kmess'] < 99 ? $set_user['kmess'] : 10;

/*
-----------------------------------------------------------------
Получаем и фильтруем основные переменные для системы
-----------------------------------------------------------------
*/
$id = isset($_REQUEST['id']) ? abs(intval($_REQUEST['id'])) : false;
$user = isset($_REQUEST['user']) ? abs(intval($_REQUEST['user'])) : false;
$act = isset($_GET['act']) ? trim($_GET['act']) : '';
$mod = isset($_GET['mod']) ? trim($_GET['mod']) : '';
$do = isset($_GET['do']) ? trim($_GET['do']) : '';
$page = isset($_REQUEST['page']) && $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
$start = isset($_REQUEST['page']) ? $page * $kmess - $kmess : (isset($_GET['start']) ? abs(intval($_GET['start'])) : 0);

/*
-----------------------------------------------------------------
Показываем Дайджест
-----------------------------------------------------------------
*/
if ($user_id && $datauser['lastdate'] < ($realtime - 3600) && $set_user['digest'] && $headmod == 'mainpage')
    header('Location: ' . $set['homeurl'] . '/index.php?act=digest&last=' . $datauser['lastdate']);

/*
-----------------------------------------------------------------
Подключаем служебные файлы
-----------------------------------------------------------------
*/
require($rootpath . 'incfiles/func.php');

/*
-----------------------------------------------------------------
Буфферизация вывода
-----------------------------------------------------------------
*/
if ($set['gzip'] && @extension_loaded('zlib')) {
    @ini_set('zlib.output_compression_level', 3);
    ob_start('ob_gzhandler');
} else {
    ob_start();
}
?>