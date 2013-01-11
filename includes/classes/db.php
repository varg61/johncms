<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */


/**
 * Работа с базой данных
 */
class DB
{
    /**
     * @var PDO
     */
    private static $instance = NULL;

    /**
     * @return PDO
     */
    public static function PDO()
    {
        if (is_null(self::$instance)) {
            require(CONFIGPATH . 'db.php');
            $db_host = isset($db_host) ? $db_host : 'localhost';
            $db_user = isset($db_user) ? $db_user : 'root';
            $db_pass = isset($db_pass) ? $db_pass : '';
            $db_name = isset($db_name) ? $db_name : 'johncms';
            try {
                self::$instance = new PDO('mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pass,
                    array(
                        PDO::ATTR_PERSISTENT         => TRUE,
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                    )
                );
            } catch (PDOException $e) {
                die('<p>DB Error: ' . $e->getMessage() . '</p>');
            }
        }

        return self::$instance;
    }
}
