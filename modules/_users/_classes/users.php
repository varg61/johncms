<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Users
{
    public static $data;

    public static function get($id)
    {
        if ($id == Vars::$USER_ID) {
            self::$data = Vars::$USER_DATA;
            return TRUE;
        }
        //TODO: Перечислить нужные поля для запроса
        $req = mysql_query("SELECT * FROM `users` WHERE `id` = " . intval($id));
        if (mysql_num_rows($req)) {
            self::$data = mysql_fetch_assoc($req);
            return TRUE;
        }
        return FALSE;
    }
}
