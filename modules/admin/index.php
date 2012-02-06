<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

@ini_set("max_execution_time", "600");
defined('_IN_JOHNCMS') or die('Error: restricted access');
define('_IN_JOHNADM', 1);

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 1) {
    header('Location: http://johncms.com/404.php');
    exit;
}

$lng_adm = Vars::loadLanguage('adm');

$regtotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `level`='0'"), 0);
$bantotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time` > '" . time() . "'"), 0);
echo '<div class="phdr"><b>' . Vars::$LNG['admin_panel'] . '</b></div>';

/*
-----------------------------------------------------------------
Блок пользователей
-----------------------------------------------------------------
*/
echo'<div class="user"><p><h3>' . Functions::getImage('users.png', '', 'class="left"') . '&#160;' . Vars::$LNG['users'] . '</h3><ul>';
if ($regtotal && Vars::$USER_RIGHTS >= 6) echo '<li><span class="red"><b><a href="index.php?act=reg">' . $lng_adm['users_reg'] . '</a>&#160;(' . $regtotal . ')</b></span></li>';
echo'<li><a href="' . Vars::$URI . '/users">' . Vars::$LNG['users'] . '</a>&#160;(' . mysql_result(mysql_query("SELECT COUNT(*) FROM `users`"), 0) . ')</li>' .
    '<li><a href="user_adm.php">' . Vars::$LNG['administration'] . '</a>&#160;(' . mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `rights` >= '1'"), 0) . ')</li>' .
    //TODO: Написать очистку неактивных юзеров
    //TODO: Написать новую систему бана юзеров
    (Vars::$USER_RIGHTS >= 7 ? '<li><a href="user_flood.php">' . $lng_adm['antiflood'] . '</a></li>' : '') .
    //TODO: Написать новую систему Кармы
    '<br />' .
    //'<li><a href="../users/search.php">' . $lng['search_nick'] . '</a></li>' .
    //'<li><a href="index.php?act=search_ip">' . $lng['ip_search'] . '</a></li>' .
    '</ul></p></div>';
if (Vars::$USER_RIGHTS >= 7) {

    /*
    -----------------------------------------------------------------
    Блок модулей
    -----------------------------------------------------------------
    */
    echo'<div class="gmenu"><p>' .
        '<h3>' . Functions::getImage('modules.png', '', 'class="left"') . '&#160;' . $lng_adm['modules'] . '</h3><ul>' .
        //TODO: Написать новый рекламный модуль
        //TODO: Написать новый модуль новостей
        //'<li><a href="index.php?act=forum">' . $lng['forum'] . '</a></li>' .
        '</ul></p></div>';

    /*
    -----------------------------------------------------------------
    Блок системных настроек
    -----------------------------------------------------------------
    */
    echo'<div class="menu"><p>' .
        '<h3>' . Functions::getImage('settings.png', '', 'class="left"') . '&#160;' . $lng_adm['system'] . '</h3>' .
        '<ul>' .
        //(Vars::$USER_RIGHTS == 9 ? '<li><a href="index.php?act=settings"><b>' . $lng['site_settings'] . '</b></a></li>' : '') .
        //'<li><a href="index.php?act=smileys">' . $lng['refresh_smileys'] . '</a></li>' .
        //(Vars::$USER_RIGHTS == 9 ? '<li><a href="index.php?act=languages">' . $lng['language_settings'] . '</a></li>' : '') .
        //'<li><a href="index.php?act=access">' . $lng['access_rights'] . '</a></li><br />' .
        //(Vars::$USER_RIGHTS == 9 ? '<li><a href="index.php?act=sitemap">' . $lng['site_map'] . '</a></li>' : '') .
        //(Vars::$USER_RIGHTS == 9 ? '<li><a href="index.php?act=counters">' . $lng['counters'] . '</a></li>' : '') .
        '</ul>' .
        '</p></div>';

    /*
    -----------------------------------------------------------------
    Блок безопасности
    -----------------------------------------------------------------
    */
    echo'<div class="rmenu"><p>' .
        '<h3>' . Functions::getImage('blocked.png', '', 'class="left"') . '&#160;' . $lng_adm['security'] . '</h3>' .
        '<ul>' .
        //'<li><a href="index.php?act=antispy">' . $lng['antispy'] . '</a></li>' .
        (Vars::$USER_RIGHTS == 9 ? '<li><a href="ip_access.php">' . $lng_adm['ip_accesslist'] . '</a></li>' : '') .
        '</ul>' .
        '</p></div>';
}
echo '<div class="phdr">&#160;</div>';