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
mb_internal_encoding('UTF-8');
@ini_set('arg_separator.output', '&amp;');
@ini_set('session.use_trans_sid', '0');

if (!isset($rootpath))
    $rootpath = '../';
if (get_magic_quotes_gpc()) {
    // Удаляем слэши, если открыт magic_quotes_gpc
    $in = array (
        &$_GET,
        &$_POST,
        &$_COOKIE
    );
    while (list($k, $v) = each($in)) {
        foreach ($v as $key => $val) {
            if (!is_array($val)) {
                $in[$k][$key] = stripslashes($val);
                continue;
            }
            $in[] = &$in[$k][$key];
        }
    }
    unset($in);
    if (!empty($_FILES)) {
        foreach ($_FILES as $k => $v) {
            $_FILES[$k]['name'] = stripslashes((string)$v['name']);
        }
    }
}

/*
-----------------------------------------------------------------
Получаем и фильтруем основные переменные для системы
-----------------------------------------------------------------
*/
$id = isset($_REQUEST['id']) ? abs(intval($_REQUEST['id'])) : false;
$page = isset($_REQUEST['page']) && $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
$start = isset($_GET['start']) ? abs(intval($_GET['start'])) : 0;
$act = isset($_GET['act']) ? trim($_GET['act']) : '';
$mod = isset($_GET['mod']) ? trim($_GET['mod']) : '';
$do = isset($_GET['do']) ? trim($_GET['do']) : '';
$agn = htmlentities(substr($_SERVER['HTTP_USER_AGENT'], 0, 100), ENT_QUOTES);

/*
-----------------------------------------------------------------
1) Получаем реальный IP
2) Проверяем на попытку HTTP флуда
-----------------------------------------------------------------
*/
require($rootpath . 'incfiles/class_ipinit.php');
$ipinit = new ipinit();
$ipl = $ipinit->ip;
$ipp = long2ip($ipl);
unset($ipinit);

/*
-----------------------------------------------------------------
Стартуем сессию
-----------------------------------------------------------------
*/
session_name('SESID');
session_start();

/*
-----------------------------------------------------------------
Подключаемся к базе данных,
устанавливаем кодировку подключения UTF8
-----------------------------------------------------------------
*/
require($rootpath . 'incfiles/db.php');
$connect = @mysql_connect($db_host, $db_user, $db_pass) or die('Error: cannot connect to DB server');
@mysql_select_db($db_name) or die('Error: cannot select DB');
@mysql_query("SET NAMES 'utf8'", $connect);

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
                header("Location: http://gazenwagen.com");
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
Получаем и задаем основные настройки системы
-----------------------------------------------------------------
*/
$req = mysql_query("SELECT * FROM `cms_settings`");
$set = array ();
while ($res = mysql_fetch_row($req)) $set[$res[0]] = $res[1];
mysql_free_result($req);
$emailadmina = $set['emailadmina']; // E-mail администратора
$sdvigclock = $set['sdvigclock'];   // Временной сдвиг по умолчанию для системы
$copyright = $set['copyright'];     // Коприайт сайта
$home = $set['homeurl'];            // Домашняя страница
$ras_pages = 'txt';                 // Расширение текстовых страниц
$admp = $set['admp'];               // Папка с Админкой
$flsz = $set['flsz'];               // Максимальный размер файлов

/*
-----------------------------------------------------------------
Задаем настройки по-умолчанию для гостей
-----------------------------------------------------------------
*/
$set_user = array ();
$set_user['sdvig'] = 0;              // Временной сдвиг
$set_user['smileys'] = 1;            // Включить(1) выключить(0) смайлы
$set_user['quick_go'] = 1;           // Быстрый переход
$set_user['avatar'] = 1;             // Аватары
$set_user['field_w'] = 20;           // Ширина текстового поля ввода
$set_user['field_h'] = 3;            // Высота текстового поля ввода
$set_user['skin'] = $set['skindef']; // Тема оформления
$set_user['kmess'] = 10;             // Число сообщений на страницу
$kmess = $set_user['kmess'];         // Число сообщений на страницу для SQL запросов
$user_id = false;
$user_ps = false;
$rights = 0;

/*
-----------------------------------------------------------------
Обрабатываем дату и время
-----------------------------------------------------------------
*/
date_default_timezone_set('Europe/Moscow');
$realtime = time() + $sdvigclock * 3600;
$mon = date("m", $realtime);
if (substr($mon, 0, 1) == 0) {
    $mon = str_replace("0", "", $mon);
}
$day = date("d", $realtime);
if (substr($day, 0, 1) == 0) {
    $day = str_replace("0", "", $day);
}
$mesyac = array (
    1 => "января",
    "февраля",
    "марта",
    "апреля",
    "мая",
    "июня",
    "июля",
    "августа",
    "сентября",
    "октября",
    "ноября",
    "декабря"
);

/*
-----------------------------------------------------------------
Автоочистка системы
-----------------------------------------------------------------
*/
if ($set['clean_time'] <= ($realtime - 43200)) {
    // Очищаем таблицу `cms_guests`
    mysql_query("DELETE FROM `cms_guests` WHERE `time` < '" . ($realtime - 600) . "'");
    mysql_query("OPTIMIZE TABLE `cms_guests`");
    mysql_query("UPDATE `cms_settings` SET  `val`='" . $realtime . "' WHERE `key`='clean_time'");
}

/*
-----------------------------------------------------------------
Авторизация по сессии
-----------------------------------------------------------------
*/
if (isset($_SESSION['uid']) && isset($_SESSION['ups'])) {
    $user_id = intval($_SESSION['uid']);
    $user_ps = $_SESSION['ups'];
}

/*
-----------------------------------------------------------------
Авторизация по COOKIE
-----------------------------------------------------------------
*/
elseif (isset($_COOKIE['cuid']) && isset($_COOKIE['cups'])) {
    $user_id = intval(base64_decode($_COOKIE['cuid']));
    $_SESSION['uid'] = $user_id;
    $user_ps = md5($_COOKIE['cups']);
    $_SESSION['ups'] = $user_ps;
    $cookauth = true;
}

/*
-----------------------------------------------------------------
Запрос в базу данных по юзеру
-----------------------------------------------------------------
*/
if ($user_id && $user_ps) {
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$user_id' LIMIT 1");
    if (mysql_num_rows($req)) {
        $datauser = mysql_fetch_assoc($req);
        if ($user_ps === $datauser['password']) {
            // Получаем общие настройки пользователя
            $set_user = array ();
            $set_user = unserialize($datauser['set_user']);
            if (empty($set_user)) {
                // Задаем пользовательские настройки по-умолчанию
                $set_user['avatar'] = 1;
                $set_user['smileys'] = 1;
                $set_user['translit'] = 1;
                $set_user['quick_go'] = 1;
                $set_user['gzip'] = 1;
                $set_user['online'] = 1;
                $set_user['movings'] = 1;
                $set_user['digest'] = 1;
                $set_user['field_w'] = 20;
                $set_user['field_h'] = 3;
                $set_user['sdvig'] = 0;
                $set_user['kmess'] = 10;
                $set_user['skin'] = 'default';
            }
            $kmess = (int)$set_user['kmess']; // Число сообщений на страницу
            // Получаем данные пользователя
            $login = $datauser['name']; // Логин (Ник) пользователя
            $rights = $datauser['rights'];
            // Проверка IP адреса, если менялся, то фиксируем новый
            if ($datauser['ip'] != $ipl){
                // Обновляем время на предыдущем адресе
                $req = mysql_query("SELECT * FROM `cms_users_iphistory` WHERE `user_id` = '$user_id' ORDER BY `time` DESC LIMIT 1");
                if(mysql_num_rows($req)){
                    $res = mysql_fetch_row($req);
                    mysql_query("UPDATE `cms_users_iphistory` SET `time` = '" . $datauser['lastdate'] . "' WHERE `id` = '" . $res[0] . "' LIMIT 1");
                }
                // Обрабатываем текущий адрес
                $req = mysql_query("SELECT * FROM `cms_users_iphistory` WHERE `user_id` = '$user_id' AND `user_ip` = '$ipl' LIMIT 1");
                if(mysql_num_rows($req)){
                    // Если адрес в истории был, то обновляем время
                    $res = mysql_fetch_row($req);
                    mysql_query("UPDATE `cms_users_iphistory` SET `time` = '$realtime' WHERE `id` = '" . $res[0] . "' LIMIT 1");
                } else {
                    // Если адреса в истории небыло, то вставляем запись
                    mysql_query("INSERT INTO `cms_users_iphistory` SET `user_id` = '$user_id', `user_ip` = '$ipl', `time` = '$realtime'");
                }
                // Обновляем текущий адрес в анкете
                mysql_query("UPDATE `users` SET `ip` = '$ipl' WHERE `id` = '$user_id' LIMIT 1");
            }
            
            // Проверка юзера на бан
            $req = mysql_query("SELECT * FROM `cms_ban_users` WHERE `user_id` = '$user_id' AND `ban_time` > '$realtime'") or die('Error: table "cms_ban_users"');
            if (mysql_num_rows($req)) {
                $rights = 0;
                $ban = array ();
                while ($res = mysql_fetch_row($req)) $ban[$res[4]] = 1;
                mysql_free_result($req);
            }
            // Если юзера не было на сайте более 1-го часа , показываем дайджест
            if ($datauser['lastdate'] < ($realtime - 3600) && $set_user['digest'] && $headmod == 'mainpage')
                header('Location: ' . $home . '/index.php?act=digest&last=' . $datauser['lastdate']);
        } else {
            // Если пароль не совпадает, уничтожаем переменные сессии и чистим куки
            unset($_SESSION['uid']);
            unset($_SESSION['ups']);
            setcookie('cuid', '');
            setcookie('cups', '');
            $user_id = false;
            $user_ps = false;
        }
    } else {
        // Если юзер не найден, уничтожаем переменные сессии и чистим куки
        unset($_SESSION['uid']);
        unset($_SESSION['ups']);
        setcookie('cuid', '');
        setcookie('cups', '');
        $user_id = false;
        $user_ps = false;
    }
}

/*
-----------------------------------------------------------------
Подключаем служебные файлы
-----------------------------------------------------------------
*/
if(!include($rootpath . 'incfiles/languages/' . $set['language'] . '/default.php'))
    echo '<p>Error: cannot include the main language file</p>';
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