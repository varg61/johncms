<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Forum
{
    public static function settings()
    {
        if (!Vars::$USER_ID || ($set_forum = Vars::getUserData('set_forum')) === FALSE) {
            return array(
                'farea'    => 0,
                'upfp'     => 0,
                'preview'  => 1,
                'postclip' => 1,
                'postcut'  => 2
            );
        }

        return $set_forum;
    }
}
