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
date_default_timezone_set('Europe/Moscow');
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

$core = new core() or die('Error: Core System');
// Системные переменные
$ipl = $core->ip;                   // Адрес IP
$set = $core->system_settings;      // Системные настройки
$realtime = $core->system_time;     // Системное время с учетом сдвига
$language = $core->system_language; // Язык системы
$lng = $core->language_phrases;     // Фразы выбранного языка
// Пользовательские переменные
$user_id = $core->user_id;        // Идентификатор пользователя
$rights = $core->user_rights;     // Права доступа
$datauser = $core->user_data;     // Все данные пользователя
$set_user = $core->user_settings; // Пользовательские настройки
$ban = $core->user_ban;           // Бан

// Число сообщений на страницу для SQL запросов
$kmess = $set_user['kmess'] > 4 && $set_user['kmess'] < 99 ? $set_user['kmess'] : 10;
// Логин (Ник) пользователя
$login = $datauser['name'];

/*
-----------------------------------------------------------------
Получаем и фильтруем основные переменные для системы
-----------------------------------------------------------------
*/
$id = isset($_REQUEST['id']) ? abs(intval($_REQUEST['id'])) : false;
$user = isset($_REQUEST['user']) ? abs(intval($_REQUEST['user'])) : false;
$page = isset($_REQUEST['page']) && $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
$start = isset($_GET['start']) ? abs(intval($_GET['start'])) : 0;
$act = isset($_GET['act']) ? trim($_GET['act']) : '';
$mod = isset($_GET['mod']) ? trim($_GET['mod']) : '';
$do = isset($_GET['do']) ? trim($_GET['do']) : '';
$agn = htmlentities(substr($_SERVER['HTTP_USER_AGENT'], 0, 100), ENT_QUOTES);

/*
-----------------------------------------------------------------
Проверяем адрес IP на Бан
и обрабатываем действие в случае Бана
-----------------------------------------------------------------
*/
$req = mysql_query("SELECT `ban_type`, `link` FROM `cms_ban_ip` WHERE '$ipl' BETWEEN `ip1` AND `ip2` LIMIT 1") or die('Error: table "cms_ban_ip"');
if (mysql_num_rows($req) > 0) {
    $res = mysql_fetch_array($req);
    switch ($res['ban_type']) {
        case 2:
            if (!empty($res['link'])) {
                // Редирект по ссылке
                header("Location: " . $res['link']);
                exit;
            } else {
                header("Location: http://johncms.com");
                exit;
            }
            break;

        case 3:
            // Закрытие регистрации
            $regban = true;
            break;
            default :
            // Полный запрет доступа к сайту
            header("HTTP/1.0 404 Not Found");
            exit;
    }
}

/*
-----------------------------------------------------------------
Обрабатываем дату и время
-----------------------------------------------------------------
*/
//TODO: Убрать
$mon = date("m", $realtime);
if (substr($mon, 0, 1) == 0) {
    $mon = str_replace("0", "", $mon);
}
//TODO: Убрать
$day = date("d", $realtime);
if (substr($day, 0, 1) == 0) {
    $day = str_replace("0", "", $day);
}

/*
-----------------------------------------------------------------
Запрос в базу данных по юзеру
-----------------------------------------------------------------
*/
if ($user_id && $user_ps) {
    // Если юзера не было на сайте более 1-го часа , показываем дайджест
    if ($datauser['lastdate'] < ($realtime - 3600) && $set_user['digest'] && $headmod == 'mainpage')
        header('Location: ' . $set['homeurl'] . '/index.php?act=digest&last=' . $datauser['lastdate']);
}

/*
-----------------------------------------------------------------
Подключаем служебные файлы
-----------------------------------------------------------------
*/
require($rootpath . 'incfiles/func.php');

/*
-----------------------------------------------------------------
Актуализация переменных
-----------------------------------------------------------------
*/
$start = isset($_REQUEST['page']) ? $page * $kmess - $kmess : $start;

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