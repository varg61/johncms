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


class statistic
{
    private $query_text = FALSE;
    private $http_referer = 'Not detect';
    private $request_uri = '';
    private $http_site = 'Not detect';
    private $operator = 'Not detect';
    private $country = 'Not detect';
    private $current_data = array();
    public static $hosty = false;
    public static $hity = false;
    private $robot = false;
    private $robot_type = false;
    private $new_host = 0;
    public static $page_title;
    

    function __construct()
    {
        $this->get_data();
        $this->get_query_text();
        $this->detect_oper_country();
        self::$hosty = $this->current_data['host'];
        self::$hity = $this->current_data['hity'];
        $_SESSION["host"] = $this->current_data['host'];
        $_SESSION["hity"] = $this->current_data['hity'];
        $time1 = date("d.m.y", $this->current_data['date']);
        $time2 = date("d.m.y", time());
        if ($time1 !== $time2)
            $this->close_day();
        $this->check_host();
    }
    
    
    /*
    -----------------------------------------------------------------
    Сохраняем все данные
    -----------------------------------------------------------------
    */
    function __destruct()
    {
        if ($this->query_text != FALSE) {
            $req = mysql_query("SELECT * FROM `stat_robots` WHERE `query` = '" . $this->
                query_text . "' AND `engine` = '" . $this->http_site . "' LIMIT 1");
            if (mysql_num_rows($req)) {
                $quer = mysql_fetch_array($req);
                $time1 = date("d.m.y", $quer['date']);
                $time2 = date("d.m.y", time());
                if ($time1 !== $time2) {
                    $today = 1;
                } else {
                    $today = $quer['today'] + 1;
                }
                $count = $quer['count'] + 1;
                mysql_query("UPDATE `stat_robots` SET `date` = '" . time() .
                    "', `url` = '" . $this->http_referer . "', `ua` = '" . Vars::$USER_AGENT .
                    "', `ip` = '" . Vars::$IP . "', `count` = '" . $count . "', `today` = '" . $today .
                    "' WHERE `query` = '" . mysql_real_escape_string($this->query_text) . "' AND `engine` = '" . $this->
                    http_site . "'");
            } else {
                mysql_query("INSERT INTO `stat_robots` SET `engine` = '" . $this->http_site .
                    "', `date` = '" . time() . "', `url` = '" . $this->http_referer .
                    "', `query` = '" . mysql_real_escape_string($this->query_text) . "', `ua` = '" . Vars::$USER_AGENT .
                    "', `ip` = '" . Vars::$IP . "', `count` = '1', `today` = '1'");
            }
        }
        
        $sql = '';
        if (Vars::$IP_VIA_PROXY)
            $sql = ', `ip_via_proxy` = "' . long2ip(Vars::$IP_VIA_PROXY) . '"';
        if (Vars::$USER_ID)
            $sql = ', `user` = "' . Vars::$USER_ID . '"';
        if ($this->robot)
            $sql .= ', `robot` = "' . $this->robot . '", `robot_type` = "' . $this->
                robot_type . '"';

        mysql_query("INSERT INTO `counter` SET `date` = '" . time() .
            "', `browser` = '" . Vars::$USER_AGENT . "', `ip` = '" . long2ip(Vars::$IP) .
            "', `ref` = '" . $this->http_referer . "', `host` = '" . $this->new_host .
            "', `site` = '" . $this->http_site . "', `pop` = '" . $this->request_uri .
            "', `head` = '" . self::$page_title . "', `operator` = '" . $this->operator .
            "', `country` = '" . $this->country . "' " . $sql . ";");

    }
    
    
    
    /*
    -----------------------------------------------------------------
    Получаем исходные данные
    -----------------------------------------------------------------
    */
    private function get_data()
    {
        $request_uri = urldecode(htmlspecialchars((string)$_SERVER['REQUEST_URI']));
        $this->request_uri = mysql_real_escape_string(strtok($request_uri, '?'));
        $this->http_referer = isset($_SERVER['HTTP_REFERER']) ? mysql_real_escape_string(htmlspecialchars((string)$_SERVER['HTTP_REFERER'])) :
            $this->http_referer;

        if (isset($_SERVER['HTTP_REFERER'])) {
            $http_site = parse_url($_SERVER['HTTP_REFERER']);
            $this->http_site = isset($http_site['host']) ? mysql_real_escape_string(htmlspecialchars((string)$http_site['host'])) :
                $this->http_site;
        }

        $this->current_data = mysql_fetch_assoc(mysql_query("SELECT MAX(`date`) AS date, MAX(`host`) AS host, MAX(hits) AS hity FROM `counter`"));

        $rob_detect = new RobotsDetect(Vars::$USER_AGENT);
        $this->robot = $rob_detect->getNameBot();
        $this->robot_type = $rob_detect->getTypeBot();

    }
    

    /*
    -----------------------------------------------------------------
    Функция вывода русского названия месяца
    -----------------------------------------------------------------
    */
    public static function month($str)
    {
        $en = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct",
            "Nov", "Dec");
        $rus = array("Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля",
            "Августа", "Сентября", "Октября", "Ноября", "Декабря");
        $str = str_replace($en, $rus, $str);
        return $str;
    }


    /*
    -----------------------------------------------------------------
    Перекодировка запросов из поисковиков
    -----------------------------------------------------------------
    */
    private function to_utf($zapros)
    {
        if (mb_check_encoding($zapros, 'UTF-8')) {
        } elseif (mb_check_encoding($zapros, 'windows-1251')) {
            $zapros = iconv("windows-1251", "UTF-8", $zapros);
        } elseif (mb_check_encoding($zapros, 'KOI8-R')) {
            $zapros = iconv("KOI8-R", "UTF-8", $zapros);
        }
        return $zapros;
    }

    /*
    -----------------------------------------------------------------
    Определение оператора и страны
    -----------------------------------------------------------------
    */
    private function detect_oper_country()
    {
        $ip_check = Vars::$IP_VIA_PROXY !== false ? Vars::$IP_VIA_PROXY : Vars::$IP;
        $ip_base = mysql_query("SELECT `operator`, `country` FROM `counter_ip_base` WHERE '" .
            $ip_check . "' BETWEEN `start` AND `stop` LIMIT 1;");
        if (mysql_num_rows($ip_base) > 0) {
            $oper = mysql_fetch_array($ip_base);
            $this->operator = $oper['operator'];
            $this->country = $oper['country'];
        }
    }


    /*
    -----------------------------------------------------------------
    Получаем текст поискового запроса
    -----------------------------------------------------------------
    */
    private function get_query_text()
    {
        $http_ref = str_replace("&amp;", "&", $this->http_referer);
        if (preg_match("/google./i", $this->http_referer) || preg_match("/bing./i", $this->
            http_referer)) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars((string)urldecode($query_text['q']));
        } elseif (preg_match("/yandex./i", $this->http_referer)) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars((string)urldecode($query_text['text']));
        } elseif (preg_match("/nigma./i", $this->http_referer)) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars((string)urldecode($query_text['s']));
        } elseif (preg_match("/search.qip./i", $this->http_referer) || preg_match("/rambler./i",
        $this->http_referer)) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars((string)urldecode($query_text['query']));
        } elseif (preg_match("/aport./i", $this->http_referer)) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars((string)urldecode($query_text['r']));
        } elseif (preg_match("/yahoo./i", $this->http_referer)) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars((string)urldecode($query_text['p']));
        } elseif (preg_match("/mail.ru/i", $this->http_referer) || preg_match("/gogo./i", $this->
        http_referer)) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars((string)$this->to_utf(urldecode($query_text['q'])));
        }
    }


    /*
    -----------------------------------------------------------------
    Проверяем хост
    -----------------------------------------------------------------
    */
    private function check_host()
    {
        if (!isset($_COOKIE['hosty'])) {
            setcookie('hosty', '1', strtotime(date("d F y", time() + 86400)));

            $sql = (Vars::$IP_VIA_PROXY) ? " AND `ip_via_proxy` = '" . long2ip(Vars::$IP_VIA_PROXY) . "'" : '';
            $ip = (Vars::$IP_VIA_PROXY) ? long2ip(Vars::$IP_VIA_PROXY) : long2ip(Vars::$IP);
            $ip_time = time() - 900; // Время в течении которого считать 1 ip одним юзером.
            $ip_check = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE (`ip` = '" .
                $ip . "' OR `ip_via_proxy` = '" . $ip . "') AND `date` > '" . $ip_time . "';"),
                0);
            if($ip_check == 0){
            $db_check = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `browser` = '" .
                Vars::$USER_AGENT . "' AND `ip` = '" . long2ip(Vars::$IP) . "'" . $sql .
                ";"), 0);
                
            if ($db_check == 0 && !$this->robot)
                $this->new_host = self::$hosty + 1;
            }
        }
    }


    /*
    -----------------------------------------------------------------
    Закрываем прошедший день
    -----------------------------------------------------------------
    */
    private function close_day()
    {
        $where_time = strtotime(date("d F y", time()));
        $where_time2 = $where_time - 86400;
        $sql = "(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '" . $where_time2 .
            "' AND `date` < '" . $where_time .
            "' AND `engine` LIKE '%yandex%') UNION ALL (SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '" .
            $where_time2 . "' AND `date` < '" . $where_time .
            "' AND `engine` LIKE '%mail%') UNION ALL (SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '" .
            $where_time2 . "' AND `date` < '" . $where_time .
            "' AND `engine` LIKE '%rambler%') UNION ALL (SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '" .
            $where_time2 . "' AND `date` < '" . $where_time .
            "' AND `engine` LIKE '%google%') UNION ALL (SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '" .
            $where_time2 . "' AND `date` < '" . $where_time .
            "' AND `engine` LIKE '%gogo%') UNION ALL (SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '" .
            $where_time2 . "' AND `date` < '" . $where_time .
            "' AND `engine` LIKE '%yahoo%') UNION ALL (SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '" .
            $where_time2 . "' AND `date` < '" . $where_time .
            "' AND `engine` LIKE '%bing%') UNION ALL (SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '" .
            $where_time2 . "' AND `date` < '" . $where_time .
            "' AND `engine` LIKE '%nigma%') UNION ALL (SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '" .
            $where_time2 . "' AND `date` < '" . $where_time .
            "' AND `engine` LIKE '%qip%') UNION ALL (SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '" .
            $where_time2 . "' AND `date` < '" . $where_time .
            "' AND `engine` LIKE '%aport%')";

        $query = mysql_query($sql);
        $count_query = array();
        while ($result_array = mysql_fetch_array($query)) {
            $count_query[] = $result_array[0];
        }

        mysql_query("insert into `countersall` values('" . $this->current_data['date'] .
            "','" . self::$hity . "','" . self::$hosty . "','" . $count_query[0] . "','" . $count_query[2] .
            "', '" . $count_query[3] . "', '" . $count_query[1] . "', '" . $count_query[4] .
            "', '" . $count_query[5] . "', '" . $count_query[6] . "', '" . $count_query[7] .
            "', '" . $count_query[8] . "', '" . $count_query[9] . "');");

        mysql_query("TRUNCATE TABLE `counter`;");

        self::$hity = 0;
        self::$hosty = 0;
        $_SESSION["host"] = 0;
        $_SESSION["hity"] = 0;
        setcookie('hosty', '');

    }


}

