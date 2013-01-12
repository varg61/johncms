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
 */
class Session extends Vars
{
    private $sessionLifeTime = 86400;
    private $data;

    function __construct()
    {
        @ini_set('session.use_trans_sid', '0');
        @ini_set('session.use_cookies', TRUE);
        @ini_set('session.use_only_cookies', TRUE);
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

        session_name('SID');
        session_set_cookie_params($this->sessionLifeTime, '/');
        session_start();
        setcookie(session_name(), session_id(), (time() + $this->sessionLifeTime), '/');
    }

    /**
     * Open Session
     *
     * @param $path
     * @param $name
     *
     * @return bool
     */
    public function sessionOpen($path, $name)
    {
        return TRUE;
    }

    /**
     * Close Session
     *
     * @return bool true
     */
    public function sessionClose()
    {
        return TRUE;
    }

    /**
     * Read session data
     *
     * @param string $sid
     *
     * @return string
     */
    public function sessionRead($sid)
    {
        $STH = DB::PDO()->prepare('
            SELECT *
            FROM `cms_sessions`
            WHERE `session_id` = :sid
            FOR UPDATE
        ');

        $STH->bindParam(':sid', $sid, PDO::PARAM_STR);
        $STH->execute();

        if ($STH->rowCount()) {
            $this->data = $STH->fetch();

            return $this->data['session_data'];
        } else {
            $STH = DB::PDO()->prepare('
                INSERT INTO `cms_sessions` SET
                `session_id`        = :sid,
                `session_timestamp` = :time,
                `session_data`      = :data
            ');

            $STH->bindParam(':sid', $sid, PDO::PARAM_STR);
            $STH->bindValue(':time', time(), PDO::PARAM_INT);
            $STH->bindValue(':data', '', PDO::PARAM_STR);
            $STH->execute();

            return '';
        }
    }

    /**
     * Write session data
     *
     * @param string $sid
     * @param string $data
     *
     * @return bool
     */
    public function sessionWrite($sid, $data)
    {
        if ($this->data['session_timestamp'] > (time() - 300)) {
            $views = ++$this->data['views'];
            $movings = $this->data['place'] == parent::$PLACE
                ? $this->data['movings']
                : ++$this->data['movings'];
        } else {
            $views = 1;
            $movings = 1;
        }

        $STH = DB::PDO()->prepare('
            UPDATE `cms_sessions` SET
            `session_timestamp` = :time,
            `session_data`      = :data,
            `user_id`           = :uid,
            `ip`                = :ip,
            `ip_via_proxy`      = :ipvia,
            `user_agent`        = :ua,
            `place`             = :place,
            `views`             = :views,
            `movings`           = :movings
            WHERE `session_id`  = :sid
        ');

        $STH->bindParam(':sid', $sid, PDO::PARAM_STR);
        $STH->bindValue(':time', time(), PDO::PARAM_INT);
        $STH->bindParam(':data', $data, PDO::PARAM_STR);
        $STH->bindValue(':uid', static::$USER_ID, PDO::PARAM_INT);
        $STH->bindValue(':ip', static::$IP, PDO::PARAM_INT);
        $STH->bindValue(':ipvia', static::$IP_VIA_PROXY, PDO::PARAM_INT);
        $STH->bindValue(':ua', static::$USER_AGENT, PDO::PARAM_STR);
        $STH->bindValue(':place', static::$PLACE, PDO::PARAM_STR);
        $STH->bindParam(':views', $views, PDO::PARAM_INT);
        $STH->bindParam(':movings', $movings, PDO::PARAM_INT);
        $STH->execute();

        return TRUE;
    }

    /**
     * Destroy Session
     *
     * @param string $sid
     *
     * @return bool
     */
    public function sessionDestroy($sid)
    {
        $STH = DB::PDO()->prepare('
            DELETE FROM `cms_sessions`
            WHERE `session_id` = :sid
        ');

        $STH->bindParam(':sid', $sid, PDO::PARAM_STR);
        $STH->execute();

        return TRUE;
    }

    /**
     * Garbage collection
     *
     * @return bool
     */
    public function sessionGc()
    {
        $STH = DB::PDO()->prepare('
            DELETE FROM `cms_sessions`
            WHERE `session_timestamp` < :time
        ');

        $STH->bindValue(':time', (time() - $this->sessionLifeTime), PDO::PARAM_INT);
        $STH->execute();

        return TRUE;
    }

    /**
     * Error message
     *
     * @param string $error
     *
     * @return string
     */
    private function _error($error)
    {
        return '<center><h2>SESSION ERROR</h2>' . $error . '</center>';
    }
}