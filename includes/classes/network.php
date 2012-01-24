<?php

/**
 * mobiCMS
 *
 * @copyright  Copyright (C) 2007-2012 mobiCMS team
 * @link       http://mobicms.org
 * @license    http://mobicms.org/about/LICENSE.txt
 * @author     Oleg Kasyanov aka AlkatraZ
 * @package    mobiCMS
 * @subpackage core
 *
 * Данный модуль заимствован из mobiCMS исключительно для JohnCMS
 * Использовать в других проектах без согласия автора запрещено!
 */

/**
 * Network protocols handler
 *
 * Obtain an IP, IP via Proxy, User Agent,
 * processing of black / white lists of IP addresses,
 * check for HTTP Flood attack
 *
 * @version 1.0.1
 * @since 5.0.0
 */

class Network extends Vars
{
    /**
     * @var int Set the maximum number of allowed requests per time period
     */
    private $_floodLimit = 40;

    /**
     * @var int The time period for calculating number of allowed requests [sec]
     */
    private $_floodInterval = 120;

    /**
     * @var bool Enable / Disable HTTP flood check
     */
    private $_floodCheck = true;

    /**
     * Perform basic processing sequence
     *
     * Obtain an IP address, matches the IP with the black/white lists,
     * check for HTTP flood, obtain an IP via PROXY address, obtain an User Agent.
     *
     * @throws Exception if an invalid IP address
     * @throws Exception if an IP is found in the black list
     * @throws Exception if the requests reached the limit
     */
    public function __construct()
    {
        try {
            // Obtain an IP address
            if ((parent::$IP = $this->_getIp()) == false) {
                throw new Exception('invalid IP address');
            }

            // Matches the IP with the black/white lists
            switch ($this->_ipBWlist(parent::$IP)) {
                case 2:
                    // IP is found in the white list
                    $this->_floodCheck = false;
                    break;

                case 1:
                    // IP is found in the black list
                    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
                    throw new Exception('Access denied');
                    break;

                default:
            }

            // Check for HTTP flood
            if ($this->_checkIpFlood(parent::$IP) > $this->_floodLimit) {
                throw new Exception('You have reached the limit of allowed requests<br />Please wait a few minutes');
            }

            // Obtain an IP via PROXY address
            parent::$IP_VIA_PROXY = $this->_getIpViaProxy(parent::$IP);

            // Obtain an User Agent
            parent::$USER_AGENT = $this->_getUserAgent();
        } catch (Exception $e) {
            die('<center><h2>NETWORK ERROR</h2>' . $e->getMessage() . '</center>');
        }
    }

    /**
     * Obtain an IP address
     *
     * @return int|false unsigned IP address,
     *                   false if the address is not valid
     */
    private function _getIp()
    {
        $ip = ip2long($_SERVER['REMOTE_ADDR']);
        if ($ip) {
            return sprintf("%u", $ip);
        }
        return false;
    }

    /**
     * Trying to get an IP via Proxy [HTTP_X_FORWARDED_FOR]
     *
     * @param int $ip unsigned IP address
     * @return int|false unsigned IP via Proxy address,
     *                   false if unable to get the address
     */
    private function _getIpViaProxy($ip)
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $vars)
        ) {
            foreach ($vars[0] AS $var)
            {
                $ip_via_proxy = ip2long($var);
                if ($ip_via_proxy
                    && $ip_via_proxy != $ip
                    && !preg_match('#^(10|172\.16|192\.168)\.#', $var)
                ) {
                    return sprintf("%u", $ip_via_proxy);
                }
            }
        }
        return 0;
    }

    /**
     * Obtain an User Agent
     *
     * @return string User Agent
     */
    private function _getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])
            ? htmlspecialchars((string)substr($_SERVER['HTTP_USER_AGENT'], 0, 150))
            : 'Not Recognised';
    }

    /**
     * Processing the cache of white / black lists of IP
     *
     * @param int $ip unsigned IP address
     * @return int 0 [not found],
     *             1 [found in the black list],
     *             2 [found in the white list]
     */
    private function _ipBWlist($ip)
    {
        $file = CACHEPATH . 'ip_list.dat';
        if (file_exists($file)) {
            $in = fopen($file, 'r');
            while ($block = fread($in, 18)) {
                $arr = unpack('dip/dip_upto/Smode', $block);
                if ($ip >= $arr['ip'] && $ip <= $arr['ip_upto']) {
                    fclose($in);
                    return $arr['mode'];
                }
            }
            fclose($in);
        }
        return false;
    }

    /**
     * Check for HTTP Flood attack
     *
     * @param int $ip
     * @return int The number of requests from the current IP, for a period of time
     * @uses $ipRequestsList
     */
    private function _checkIpFlood($ip)
    {
        $file = CACHEPATH . 'ip_flood.dat';
        $tmp = array();
        $requests = array();
        $count = 1;
        if (!file_exists($file)) {
            $in = fopen($file, 'w+');
        } else {
            $in = fopen($file, 'r+');
        }
        flock($in, LOCK_EX) or die('ERROR: antiflood file');
        $now = time();
        while ($block = fread($in, 8)) {
            $arr = unpack('Lip/Ltime', $block);
            if (($now - $arr['time']) > $this->_floodInterval) {
                continue;
            }
            if ($this->_floodCheck && $arr['ip'] == $ip) {
                $count++;
            }
            $tmp[] = $arr;
            $requests[] = $arr['ip'];
        }
        fseek($in, 0);
        ftruncate($in, 0);
        for ($i = 0; $i < count($tmp); $i++) {
            fwrite($in, pack('LL', $tmp[$i]['ip'], $tmp[$i]['time']));
        }
        fwrite($in, pack('LL', $ip, $now));
        fclose($in);
        parent::$IP_REQUESTS_LIST = array_count_values($requests);
        return $count;
    }
}
