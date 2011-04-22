<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Restricted access');

class core {
    public $ip;                                 // IP адрес
    public $ip_via_proxy;                       // IP адрес за прокси-сервером
    public $user_agent;                         // User Agent (Browser)
    public $system_settings = array();          // Системные настройки
    public $system_time;                        // Системное время
    public $lng;                                // Двухбуквенный ISO код языка
    public $lng_list;                           // Список имеющихся языков
    public $regban = false;                     // Запрет регистрации пользователей

    public $user_id = false;                    // Идентификатор пользователя
    public $user_rights = 0;                    // Права доступа
    public $user_data = array();                // Все данные пользователя
    public $user_set = array();                 // Пользовательские настройки
    public $user_ban = array();                 // Бан

    private $flood_chk = 1;                     // Включение - выключение функции IP антифлуда
    private $flood_interval = '120';            // Интервал времени в секундах
    private $flood_limit = '30';                // Число разрешенных запросов за интервал

    /*
    -----------------------------------------------------------------
    Конструктор класса, выполняем основную последовательность
    -----------------------------------------------------------------
    */
    function __construct() {
        // Получаем реальный адрес IP
        $this->ip = ip2long($_SERVER['REMOTE_ADDR']) or die('Invalid IP');
        $this->ip_via_proxy = isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $this->ip_valid($_SERVER['HTTP_X_FORWARDED_FOR']) ? ip2long($_SERVER['HTTP_X_FORWARDED_FOR']) : false;

        // Проверка адреса IP на флуд
        if ($this->flood_chk && !$this->ip_whitelist($this->ip)) {
            if ($this->ip_reqcount() > $this->flood_limit) die('Error: exceeded limit of allowed requests (FLOOD)');
        }

        // Удаляем слэши
        if (get_magic_quotes_gpc()) $this->del_slashes();

        // Получаем User Agent
        $this->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities(substr($_SERVER['HTTP_USER_AGENT'], 0, 150), ENT_QUOTES) : 'Not Recognised';

        // Стартуем сессию
        session_name('SESID');
        session_start();

        // Соединяемся с базой данных
        $this->db_connect();

        // Проверяем адрес IP на бан
        $this->ip_ban();

        // Получаем системные настройки
        $this->system_settings();

        // Автоочистка системы
        $this->clean();

        // Авторизация пользователей
        $this->authorize();

        // Определяем язык системы
        $this->lng_detect();
    }

    /*
    -----------------------------------------------------------------
    Подключаемся к базе данных
    -----------------------------------------------------------------
    */
    private function db_connect() {
        global $rootpath;
        require($rootpath . 'incfiles/db.php');
        $db_host = isset($db_host) ? $db_host : 'localhost';
        $db_user = isset($db_user) ? $db_user : '';
        $db_pass = isset($db_pass) ? $db_pass : '';
        $db_name = isset($db_name) ? $db_name : '';
        $connect = @mysql_connect($db_host, $db_user, $db_pass) or die('Error: cannot connect to database server');
        @mysql_select_db($db_name) or die('Error: specified database does not exist');
        @mysql_query("SET NAMES 'utf8'", $connect);
    }

    /*
    -----------------------------------------------------------------
    Счетчик числа обращений с заданного IP
    -----------------------------------------------------------------
    */
    private function ip_reqcount() {
        global $rootpath;
        $file = $rootpath . 'files/cache/ip_flood.dat';
        $tmp = array();
        $requests = 1;
        if (!file_exists($file)) $in = fopen($file, "w+");
        else $in = fopen($file, "r+");
        flock($in, LOCK_EX) or die("Cannot flock ANTIFLOOD file.");
        $now = time();
        while ($block = fread($in, 8)) {
            $arr = unpack("Lip/Ltime", $block);
            if (($now - $arr['time']) > $this->flood_interval) {
                continue;
            }
            if ($arr['ip'] == $this->ip) {
                $requests++;
            }
            $tmp[] = $arr;
        }
        fseek($in, 0);
        ftruncate($in, 0);
        for ($i = 0; $i < count($tmp); $i++) {
            fwrite($in, pack('LL', $tmp[$i]['ip'], $tmp[$i]['time']));
        }
        fwrite($in, pack('LL', $this->ip, $now));
        fclose($in);
        return $requests;
    }

    /*
    -----------------------------------------------------------------
    Валидация IP адреса
    -----------------------------------------------------------------
    */
    public function ip_valid($ip = '') {
        if (empty($ip)) return false;
        $d = explode('.', $ip);
        for ($x = 0; $x < 4; $x++) if (!is_numeric($d[$x]) || ($d[$x] < 0) || ($d[$x] > 255)) return false;
        return $ip;
    }

    /*
    -----------------------------------------------------------------
    Обрабатываем "белый" список IP адресов
    -----------------------------------------------------------------
    */
    private function ip_whitelist($ip) {
        global $rootpath;
        $file = $rootpath . 'files/cache/ip_wlist.dat';
        if (file_exists($file)) {
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $val) {
                $tmp = explode(':', $val);
                if (!$tmp[1]) $tmp[1] = $tmp[0];
                if ($ip >= $tmp[0] && $ip <= $tmp[1]) return true;
            }
        }
        return false;
    }

    /*
    -----------------------------------------------------------------
    Удаляем слэши из глобальных переменных
    -----------------------------------------------------------------
    */
    private function del_slashes() {
        $in = array(
            &$_GET,
            &$_POST,
            &$_COOKIE
        );
        while ((list($k, $v) = each($in)) !== false) {
            foreach ($v as $key => $val) {
                if (!is_array($val)) {
                    $in[$k][$key] = stripslashes($val);
                    continue;
                }
                $in[] = &$in[$k][$key];
            }
        }
        unset($in);
        if (!empty($_FILES)) foreach ($_FILES as $k => $v) $_FILES[$k]['name'] = stripslashes((string)$v['name']);
    }

    /*
    -----------------------------------------------------------------
    Проверяем адрес IP на Бан
    -----------------------------------------------------------------
    */
    public function ip_ban() {
        $req = mysql_query("SELECT `ban_type`, `link` FROM `cms_ban_ip` WHERE '" . $this->ip . "' BETWEEN `ip1` AND `ip2` LIMIT 1") or die('Error: table "cms_ban_ip"');
        if (mysql_num_rows($req)) {
            $res = mysql_fetch_array($req);
            switch ($res['ban_type']) {
                case 2:
                    if (!empty($res['link'])) header('Location: ' . $res['link']);
                    else header('Location: http://johncms.com');
                    exit;
                    break;

                case 3:
                    $this->regban = true;
                    break;

                default :
                    header("HTTP/1.0 404 Not Found");
                    exit;
            }
        }
    }

    /*
    -----------------------------------------------------------------
    Получаем системные настройки
    -----------------------------------------------------------------
    */
    private function system_settings() {
        $set = array();
        $req = mysql_query("SELECT * FROM `cms_settings`");
        while (($res = mysql_fetch_row($req)) !== false) $set[$res[0]] = $res[1];
        $this->lng = isset($set['lng']) && !empty($set['lng']) ? $set['lng'] : 'en';
        $this->lng_list = isset($set['lng_list']) ? unserialize($set['lng_list']) : array();
        $this->system_time = time() + $set['timeshift'] * 3600;
        $this->system_settings = $set;
    }

    /*
    -----------------------------------------------------------------
    Определяем язык
    -----------------------------------------------------------------
    */
    private function lng_detect() {
        $setlng = isset($_POST['setlng']) ? substr(trim($_POST['setlng']), 0, 2) : '';
        if (!empty($setlng) && array_key_exists($setlng, $this->lng_list)) {
            // Переключатель языка
            $_SESSION['lng'] = $setlng;
        }
        if (isset($_SESSION['lng']) && array_key_exists($_SESSION['lng'], $this->lng_list)) {
            // По сессии
            $this->lng = $_SESSION['lng'];
        } elseif ($this->user_id && isset($this->user_set['lng']) && array_key_exists($this->user_set['lng'], $this->lng_list)) {
            // По настройкам пользователя
            $this->lng = $this->user_set['lng'];
        } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // По браузеру
            foreach (explode(',', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE']))) as $var) {
                $lng = substr($var, 0, 2);
                if (array_key_exists($lng, $this->lng_list)) {
                    $this->lng = $lng;
                    break;
                }
            }
        }
    }

    /*
    -----------------------------------------------------------------
    Загружаем фразы языка из файла
    -----------------------------------------------------------------
    */
    public function load_lng($module = '_core') {
        global $rootpath;
        $lng_file = $rootpath . 'incfiles/languages/' . $this->lng . '/' . $module . '.lng';
        if (file_exists($lng_file)) {
            $out = parse_ini_file($lng_file) or die('ERROR: language file');
            return $out;
        }
        die('ERROR: Language file is missing');
    }

    /*
    -----------------------------------------------------------------
    Авторизация пользователя и получение его данных из базы
    -----------------------------------------------------------------
    */
    private function authorize() {
        $user_id = false;
        $user_ps = false;
        if (isset($_SESSION['uid']) && isset($_SESSION['ups'])) {
            // Авторизация по сессии
            $user_id = abs(intval($_SESSION['uid']));
            $user_ps = $_SESSION['ups'];
        } elseif (isset($_COOKIE['cuid']) && isset($_COOKIE['cups'])) {
            // Авторизация по COOKIE
            $user_id = abs(intval(base64_decode(trim($_COOKIE['cuid']))));
            $_SESSION['uid'] = $user_id;
            $user_ps = md5(trim($_COOKIE['cups']));
            $_SESSION['ups'] = $user_ps;
        }
        if ($user_id && $user_ps) {
            $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$user_id'");
            if (mysql_num_rows($req)) {
                $user_data = mysql_fetch_assoc($req);
                $permit = $user_data['failed_login'] < 3 || $user_data['failed_login'] > 2 && $user_data['ip'] == $this->ip && $user_data['browser'] == $this->user_agent ? true : false;
                if ($permit && $user_ps === $user_data['password']) {
                    // Если авторизация прошла успешно
                    $this->user_id = $user_data['id'];
                    $this->user_data = $user_data;
                    $this->user_rights = $user_data['rights'];
                    $this->user_set = !empty($this->user_data['set_user']) ? unserialize($this->user_data['set_user']) : $this->user_setings_default();
                    $this->user_ip_history();
                    $this->user_ban_check();
                } else {
                    // Если авторизация не прошла
                    mysql_query("UPDATE `users` SET `failed_login` = '" . ($user_data['failed_login'] + 1) . "' WHERE `id` = '" . $user_data['id'] . "'");
                    $this->user_unset();
                }
            } else {
                // Если пользователь не существует
                $this->user_unset();
            }
        } else {
            // Для неавторизованных, загружаем настройки по-умолчанию
            $this->user_set = $this->user_setings_default();
        }
    }

    /*
    -----------------------------------------------------------------
    Проверка пользователя на Бан
    -----------------------------------------------------------------
    */
    private function user_ban_check() {
        $req = mysql_query("SELECT * FROM `cms_ban_users` WHERE `user_id` = '" . $this->user_id . "' AND `ban_time` > '" . $this->system_time . "'");
        if (mysql_num_rows($req)) {
            $this->user_rights = 0;
            while (($res = mysql_fetch_row($req)) !== false) $this->user_ban[$res[4]] = 1;
        }
    }

    /*
    -----------------------------------------------------------------
    Фиксация истории IP адресов пользователя
    -----------------------------------------------------------------
    */
    private function user_ip_history() {
        if ($this->user_data['ip'] != $this->ip) {
            // Удаляем из истории текущий адрес (если есть)
            mysql_query("DELETE FROM `cms_users_iphistory` WHERE `user_id` = '" . $this->user_id . "' AND `ip` = '" . $this->ip . "' LIMIT 1");
            if (!empty($this->user_data['ip']) && $this->ip_valid(long2ip($this->user_data['ip']))) {
                // Вставляем в историю предыдущий адрес IP
                mysql_query("INSERT INTO `cms_users_iphistory` SET
                    `user_id` = '" . $this->user_id . "',
                    `ip` = '" . $this->user_data['ip'] . "',
                    `time` = '" . $this->user_data['lastdate'] . "'
                ");
            }
            // Обновляем текущий адрес в таблице `users`
            mysql_query("UPDATE `users` SET `ip` = '" . $this->ip . "' WHERE `id` = '" . $this->user_id . "'");
        }
    }

    /*
    -----------------------------------------------------------------
    Пользовательские настройки по умолчанию
    -----------------------------------------------------------------
    */
    private function user_setings_default() {
        $settings = array(
            'avatar' => 1, // Показывать аватары
            'digest' => 0, // Показывать Дайджест
            'field_h' => 3, // Высота текстового поля ввода
            'field_w' => 20, // Ширина текстового поля ввода
            'gzip' => 1, // Отображать коэффициент сжатия
            'kmess' => 10, // Число сообщений на страницу
            'movings' => 1, // Отображать число перемещений по сайту
            'online' => 1, // Время, проведенное Онлайн
            'quick_go' => 1, // Быстрый переход
            'sdvig' => 0, // Временной сдвиг
            'skin' => $this->system_settings['skindef'], // Тема оформления
            'smileys' => 1, // Включить(1) выключить(0) смайлы
            'translit' => 0 // Транслит
        );
        return $settings;
    }

    /*
    -----------------------------------------------------------------
    Уничтожаем данные авторизации юзера
    -----------------------------------------------------------------
    */
    private function user_unset() {
        $this->user_id = false;
        $this->user_rights = 0;
        $this->user_set = $this->user_setings_default();
        $this->user_data = array();
        unset($_SESSION['uid']);
        unset($_SESSION['ups']);
        setcookie('cuid', '');
        setcookie('cups', '');
    }

    /*
    -----------------------------------------------------------------
    Автоочистка системы
    -----------------------------------------------------------------
    */
    private function clean() {
        if (!isset($this->system_settings['clean_time'])) mysql_query("INSERT INTO `cms_settings` SET `key` = 'clean_time', `val` = '0'");
        if ($this->system_settings['clean_time'] < $this->system_time - 86400) {
            // Очищаем таблицу статистики гостей (удаляем записи старше 1 дня)
            mysql_query("DELETE FROM `cms_guests` WHERE `time` < '" . ($this->system_time - 86400) . "'");
            mysql_query("OPTIMIZE TABLE `cms_guests`");
            // Очищаем таблицу истории IP адресов (удаляем записи старше 1 месяца)
            mysql_query("DELETE FROM `cms_users_iphistory` WHERE `time` < '" . ($this->system_time - 2592000) . "'");
            mysql_query("OPTIMIZE TABLE `cms_users_iphistory`");
            // Обновляем метку времени
            mysql_query("UPDATE `cms_settings` SET  `val` = '" . $this->system_time . "' WHERE `key` = 'clean_time' LIMIT 1");
        }
    }
}

?>