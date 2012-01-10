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
        session_set_cookie_params($this->sessionLifeTime);
        register_shutdown_function('session_write_close');
        session_start();
        setcookie(session_name(), session_id(), (time() + $this->sessionLifeTime));
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
        $data = '';
        $req = mysql_query("SELECT `session_data`, `user_id`
            FROM `cms_sessions`
            WHERE `session_id` = '" . mysql_real_escape_string($sid) . "'
            FOR UPDATE
        ") or exit ($this->_error(mysql_error()));
        if (mysql_num_rows($req)) {
            $res = mysql_fetch_assoc($req);
            $data = $res['session_data'];
        }
        return $data;
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
        mysql_query("INSERT INTO `cms_sessions` SET
            `session_id` = '" . mysql_real_escape_string($sid) . "',
            `session_timestamp` = '" . time() . "',
            `session_data` = '" . mysql_real_escape_string($data) . "',
            `user_id` = " . parent::$USER_ID . ",
            `ip` = " . parent::$IP . ",
            `ip_via_proxy` = " . parent::$IP_VIA_PROXY . ",
            `user_agent` = '" . mysql_real_escape_string(parent::$USERAGENT) . "'
            ON DUPLICATE KEY UPDATE
            `session_timestamp` = VALUES(`session_timestamp`),
            `session_data` = VALUES(`session_data`),
            `user_id` = VALUES(`user_id`),
            `ip` = VALUES(`ip`),
            `ip_via_proxy` = VALUES(`ip_via_proxy`)
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