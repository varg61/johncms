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
    public static $hosty = FALSE;
    public static $hity = FALSE;
    private $robot = FALSE;
    private $robot_type = FALSE;
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

    /**
     * Сохраняем все данные
     */
    function __destruct()
    {
        if ($this->query_text != FALSE) {
            $STH = DB::PDO()->prepare('
                SELECT * FROM `stat_robots`
                WHERE `query` = :query
                AND `engine`  = :engine
                LIMIT 1
            ');

            $STH->bindValue(':query', $this->query_text, PDO::PARAM_STR);
            $STH->bindValue(':engine', $this->http_site, PDO::PARAM_STR);
            $STH->execute();

            if ($STH->rowCount()) {
                $result = $STH->fetch();
                $time1 = date("d.m.y", $result['date']);
                $time2 = date("d.m.y", time());
                if ($time1 !== $time2) {
                    $today = 1;
                } else {
                    $today = ++$result['today'];
                }

                $STHU = DB::PDO()->prepare('
                    UPDATE `stat_robots` SET
                    `date`        = :date,
                    `url`         = :url,
                    `ip`          = :ip,
                    `ua`          = :ua,
                    `count`       = :count,
                    `today`       = :today
                    WHERE `query` = :query
                    AND `engine`  = :engine
                    LIMIT 1
                ');

                $STHU->bindValue(':date', time(), PDO::PARAM_INT);
                $STHU->bindValue(':url', $this->http_referer, PDO::PARAM_STR);
                $STHU->bindValue(':ip', Vars::$IP, PDO::PARAM_INT);
                $STHU->bindValue(':ua', Vars::$USER_AGENT, PDO::PARAM_STR);
                $STHU->bindValue(':count', ++$result['count'], PDO::PARAM_INT);
                $STHU->bindParam(':today', $today, PDO::PARAM_INT);

                $STHU->bindValue(':query', $this->query_text, PDO::PARAM_STR);
                $STHU->bindValue(':engine', $this->http_site, PDO::PARAM_STR);
                $STHU->execute();
            } else {
                $STHI = DB::PDO()->prepare('
                    INSERT INTO `stat_robots` SET
                    `engine` = :engine,
                    `date`   = :date,
                    `url`    = :url,
                    `query`  = :query,
                    `ua`     = :ua,
                    `ip`     = :ip,
                    `count`  = 1,
                    `today`  = 1
                ');

                $STHI->bindValue(':engine', $this->http_site, PDO::PARAM_STR);
                $STHI->bindValue(':date', time(), PDO::PARAM_INT);
                $STHI->bindValue(':url', $this->http_referer, PDO::PARAM_STR);
                $STHI->bindValue(':query', $this->query_text, PDO::PARAM_STR);
                $STHI->bindValue(':ua', Vars::$USER_AGENT, PDO::PARAM_STR);
                $STHI->bindValue(':ip', Vars::$IP, PDO::PARAM_INT);
                $STHI->execute();
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

        $STHC = DB::PDO()->prepare('
            INSERT INTO `counter` SET
            `date`     = :date,
            `browser`  = :ua,
            `ip`       = :ip,
            `ref`      = :ref,
            `host`     = :host,
            `site`     = :site,
            `pop`      = :pop,
            `head`     = :head,
            `operator` = :op,
            `country`  = :country,
            `robot`    = "",
            `robot_type`    = ""
        ');

        $STHC->bindValue(':date', time(), PDO::PARAM_INT);
        $STHC->bindValue(':ua', Vars::$USER_AGENT, PDO::PARAM_STR);
        $STHC->bindValue(':ip', long2ip(Vars::$IP), PDO::PARAM_STR);
        $STHC->bindValue(':ref', $this->http_referer, PDO::PARAM_STR);
        $STHC->bindValue(':host', $this->new_host, PDO::PARAM_INT);
        $STHC->bindValue(':site', $this->http_site, PDO::PARAM_STR);
        $STHC->bindValue(':pop', $this->request_uri, PDO::PARAM_STR);
        $STHC->bindValue(':head', self::$page_title, PDO::PARAM_STR);
        $STHC->bindValue(':op', $this->operator, PDO::PARAM_STR);
        $STHC->bindValue(':country', $this->country, PDO::PARAM_STR);
        $STHC->execute();
    }

    /**
     * Получаем исходные данные
     */
    private function get_data()
    {
        $request_uri = urldecode(htmlspecialchars($_SERVER['REQUEST_URI']));
        $this->request_uri = strtok($request_uri, '?');
        $this->http_referer = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : $this->http_referer;

        if (isset($_SERVER['HTTP_REFERER'])) {
            $http_site = parse_url($_SERVER['HTTP_REFERER']);
            $this->http_site = isset($http_site['host']) ? htmlspecialchars($http_site['host']) : $this->http_site;
        }

        $this->current_data = DB::PDO()->query('
            SELECT
            MAX(`date`) AS `date`,
            MAX(`host`) AS `host`,
            MAX(`hits`) AS `hity`
            FROM `counter`
        ')->fetch();

        $rob_detect = new RobotsDetect(Vars::$USER_AGENT);
        $this->robot = $rob_detect->getNameBot();
        $this->robot_type = $rob_detect->getTypeBot();
    }

    /**
     * Функция вывода русского названия месяца
     *
     * @param string $str
     *
     * @return string
     */
    public static function month($str)
    {
        $en = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
        $rus = array("Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря");
        $str = str_replace($en, $rus, $str);
        return $str;
    }

    /**
     * Перекодировка запросов из поисковиков
     *
     * @param $zapros
     *
     * @return string
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

    /**
     * Определение оператора и страны
     */
    private function detect_oper_country()
    {
        $STH = DB::PDO()->prepare('
            SELECT `operator`, `country` FROM `counter_ip_base`
            WHERE :ip BETWEEN `start` AND `stop`
            LIMIT 1
        ');

        $STH->bindValue(':ip', (Vars::$IP_VIA_PROXY ? : Vars::$IP));
        $STH->execute();

        if ($STH->rowCount()) {
            $result = $STH->fetch();
            $this->operator = $result['operator'];
            $this->country = $result['country'];
        }
    }

    /**
     * Получаем текст поискового запроса
     */
    private function get_query_text()
    {
        $http_ref = str_replace("&amp;", "&", $this->http_referer);
        if (preg_match("/google./i", $this->http_referer) || preg_match("/bing./i", $this->
            http_referer)
        ) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars(urldecode($query_text['q']));
        } elseif (preg_match("/yandex./i", $this->http_referer)) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars(urldecode($query_text['text']));
        } elseif (preg_match("/nigma./i", $this->http_referer)) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars(urldecode($query_text['s']));
        } elseif (preg_match("/search.qip./i", $this->http_referer) || preg_match("/rambler./i",
            $this->http_referer)
        ) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars(urldecode($query_text['query']));
        } elseif (preg_match("/aport./i", $this->http_referer)) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars(urldecode($query_text['r']));
        } elseif (preg_match("/yahoo./i", $this->http_referer)) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars(urldecode($query_text['p']));
        } elseif (preg_match("/mail.ru/i", $this->http_referer) || preg_match("/gogo./i", $this->
            http_referer)
        ) {
            $url = parse_url($http_ref);
            parse_str($url['query'], $query_text);
            $this->query_text = htmlspecialchars($this->to_utf(urldecode($query_text['q'])));
        }
    }

    /**
     * Проверяем хост
     */
    private function check_host()
    {
        if (!isset($_COOKIE['hosty'])) {
            setcookie('hosty', '1', strtotime(date("d F y", time() + 86400)));

            $sql = (Vars::$IP_VIA_PROXY) ? " AND `ip_via_proxy` = '" . long2ip(Vars::$IP_VIA_PROXY) . "'" : '';
            $ip = (Vars::$IP_VIA_PROXY) ? long2ip(Vars::$IP_VIA_PROXY) : long2ip(Vars::$IP);
            $ip_time = time() - 900; // Время в течении которого считать 1 ip одним юзером.

            $ip_check = DB::PDO()->query("
                SELECT COUNT(*) FROM `counter`
                WHERE (`ip` = '" . $ip . "'
                OR `ip_via_proxy` = '" . $ip . "')
                AND `date` > '" . $ip_time . "'
            ")->fetchColumn();

            if ($ip_check == 0) {
                $db_check = DB::PDO()->query("
                SELECT COUNT(*) FROM `counter`
                WHERE `browser` = '" . Vars::$USER_AGENT . "'
                AND `ip` = '" . long2ip(Vars::$IP) . "'" . $sql
                )->fetchColumn();

                if ($db_check == 0 && !$this->robot) {
                    $this->new_host = self::$hosty + 1;
                }
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

        $query = DB::PDO()->query($sql);
        $count_query = array();
        while ($result_array = $query->fetch(PDO::FETCH_BOTH)) {
            $count_query[] = $result_array[0];
        }

//TODO: Разобраться
//        DB::PDO()->exec("insert into `countersall` values('" . $this->current_data['date'] .
//            "','" . self::$hity . "','" . self::$hosty . "','" . $count_query[0] . "','" . $count_query[2] .
//            "', '" . $count_query[3] . "', '" . $count_query[1] . "', '" . $count_query[4] .
//            "', '" . $count_query[5] . "', '" . $count_query[6] . "', '" . $count_query[7] .
//            "', '" . $count_query[8] . "', '" . $count_query[9] . "')");

        DB::PDO()->exec("TRUNCATE TABLE `counter`");

        self::$hity = 0;
        self::$hosty = 0;
        $_SESSION["host"] = 0;
        $_SESSION["hity"] = 0;
        setcookie('hosty', '');

    }


}

