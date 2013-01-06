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
 * Session handler
 *
 * @version 1.0.0
 * @since 5.0.0
 */
class Session extends Vars
{
    /**
     * @var string Session name
     */
    private $sessionName = 'SID';

    /**
     * @var int Session lifetime [in seconds]
     */
    private $sessionLifeTime = 86400;

    private $session_id;
    private $session_data;

    /**
     * Session handler
     */
    function __construct()
    {
        @ini_set('session.use_trans_sid', '0');
        @ini_set('session.use_cookies', true);
        @ini_set('session.use_only_cookies', true);
        @ini_set('session.gc_maxlifetime', $this->sessionLifeTime);
        @ini_set('session.gc_probability', '1');
        @ini_set('session.gc_divisor', '100');

        session_set_save_handler(
            array($this, 'sessionOpen'),
            array($this, 'sessionClose'),
            array($this, 'sessionRead'),
            array($this, 'sessionWrite'),
            array($this, 'sessionDestroy'),
            array($this, 'sessionGc')
        );

        session_name($this->sessionName);
        session_set_cookie_params($this->sessionLifeTime, '/');
        session_start();
        setcookie(session_name(), session_id(), (time() + $this->sessionLifeTime), '/');
    }

    /**
     * Open Session
     *
     * @param $path
     * @param $name
     * @return bool
     */
    public function sessionOpen($path, $name)
    {
        return true;
    }

    /**
     * Close Session
     *
     * @return bool true
     */
    public function sessionClose()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $sid Session ID
     * @return mixed Session data
     * @uses $userId
     */
    public function sessionRead($sid)
    {
        $this->session_id = $sid;
        $req = mysql_query("SELECT *
            FROM `cms_sessions`
            WHERE `session_id` = '" . mysql_real_escape_string($sid) . "'
            FOR UPDATE
        ") or exit ($this->_error(mysql_error()));
        if (mysql_num_rows($req)) {
            $res = mysql_fetch_assoc($req);
            $this->session_data = $res;
            return $res['session_data'];
        } else {
            mysql_query("INSERT INTO `cms_sessions` SET
                `session_id` = '" . mysql_real_escape_string($sid) . "',
                `session_timestamp` = " . time() . ",
                `session_data` = ''
            ") or exit ($this->_error(mysql_error()));
            return '';
        }
    }

    /**
     * Write session data
     *
     * @param $sid string $sid Session ID
     * @param $data mixed Session data
     * @return bool true
     */
    public function sessionWrite($sid, $data)
    {
        $movings = false;
        $sql[] = "`session_timestamp` = " . time();
        $sql[] = "`session_data` = '" . mysql_real_escape_string($data) . "'";

        if ($this->session_data['user_id'] != parent::$USER_ID) {
            $sql[] = "`user_id` = " . parent::$USER_ID;
        }

        if ($this->session_data['ip'] != parent::$IP) {
            $sql[] = "`ip` = " . parent::$IP;
        }

        if ($this->session_data['ip_via_proxy'] != parent::$IP_VIA_PROXY) {
            $sql[] = "`ip_via_proxy` = " . parent::$IP_VIA_PROXY;
        }

        if ($this->session_data['user_agent'] != parent::$USER_AGENT) {
            $sql[] = "`user_agent` = '" . mysql_real_escape_string(parent::$USER_AGENT) . "'";
        }

        if ($this->session_data['place'] != parent::$PLACE) {
            $sql[] = "`place` = '" . mysql_real_escape_string(parent::$PLACE) . "'";
            $movings = TRUE;
        }

        if ($this->session_data['session_timestamp'] > (time() - 300)) {
            $sql[] = "`views` = " . ++$this->session_data['views'];
            if ($movings) {
                $sql[] = "`movings` = " . ++$this->session_data['movings'];
            }
        } else {
            $sql[] = "`views` = 1";
            $sql[] = "`movings` = 1";
            $sql[] = "`start_time` = " . time();
        }

        mysql_query("UPDATE `cms_sessions` SET " . implode(', ', $sql) . "
            WHERE `session_id` = '" . mysql_real_escape_string($sid) . "'
        ") or exit ($this->_error(mysql_error()));
        return true;
    }

    /**
     * Destroy Session
     *
     * @param $sid
     * @return bool true
     */
    public function sessionDestroy($sid)
    {
        mysql_query("DELETE FROM `cms_sessions`
            WHERE `session_id` = '" . mysql_real_escape_string($sid) . "'
        ") or exit ($this->_error(mysql_error()));
        return true;
    }

    /**
     * Garbage collection
     *
     * @return bool true
     */
    public function sessionGc()
    {
        $time = time() - $this->sessionLifeTime;
        mysql_query("DELETE FROM `cms_sessions`
            WHERE `session_timestamp` < $time
        ") or exit ($this->_error(mysql_error()));
        return true;
    }

    /**
     * Error message
     *
     * @param string $error error message
     * @return string formatted error message
     */
    private function _error($error)
    {
        return '<center><h2>SESSION ERROR</h2>' . $error . '</center>';
    }
}