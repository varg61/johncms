<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

//TODO: Доработать для новых настроек!

if (Vars::$USER_RIGHTS >= 7 && Vars::$USER_RIGHTS > $user['rights']) {
    /*
    -----------------------------------------------------------------
    Сброс настроек пользователя
    -----------------------------------------------------------------
    */
    mysql_query("UPDATE `users` SET `set_user` = '', `set_forum` = '', `set_chat` = '' WHERE `id` = '" . $user['user_id'] . "'");
    echo '<div class="gmenu"><p>' . lng('reset1') . ' <b>' . $user['name'] . '</b> ' . lng('reset2') . '<br />' .
         '<a href="profile.php?user=' . $user['user_id'] . '">' . lng('profile') . '</a></p></div>';
    exit;
}