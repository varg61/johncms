<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

@ini_set("max_execution_time", "600");
define('_IN_JOHNCMS', 1);
define('_IN_JOHNADM', 1);

require('../incfiles/core.php');
// Подключаем язык Админ-панели
$lng = array_merge($lng, load_lng('admin'));
$textl = $lng['admin_panel'];
if ($rights < 1) {
    header('Location: http://gazenwagen.com/?err');
    exit;
}

require_once('../incfiles/head.php');
$array = array (
    'mod_ads',
    'mod_chat',
    'mod_counters',
    'mod_karma',
    'mod_forum',
    'mod_news',
    'sys_access',
    'sys_antispy',
    'sys_flood',
    'sys_ipban',
    'sys_ipop',
    'sys_lng',
    'sys_set',
    'sys_smileys',
    'usr_adm',
    'usr_ban',
    'usr_del',
    'usr_list',
    'usr_reg',
    'usr_search_ip',
    'usr_search_nick'
);
if (in_array($act, $array) && file_exists($act . '.php')) {
    require_once($act . '.php');
} else {
    /*
    -----------------------------------------------------------------
    Главное меню Админ панели
    -----------------------------------------------------------------
    */
    echo '<div class="phdr"><b>' . $lng['admin_panel'] . '</b></div>';
    echo '<div class="user"><p><h3><img src="../images/users.png" width="16" height="16" class="left" />&#160;' . $lng['users'] . '</h3><ul>';
    $regtotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `preg`='0'"), 0);
    if ($regtotal)
        echo '<li><span class="red"><b><a href="index.php?act=usr_reg">' . $lng['users_reg'] . '</a>&#160;(' . $regtotal . ')</b></span></li>';
    echo '<li><a href="index.php?act=usr_adm">' . $lng['users_administration'] . '</a>&#160;(' . mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `rights` >= '1'"), 0) . ')</li>';
    //TODO: Написать показ числа новых
    echo '<li><a href="index.php?act=usr_list">' . $lng['users'] . '</a>&#160;(' . mysql_result(mysql_query("SELECT COUNT(*) FROM `users`"), 0) . ')</li>';
    $bantotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time` > '$realtime'"), 0);
    echo '<li><a href="index.php?act=usr_ban">' . $lng['ban_panel'] . '</a>&#160;(' . $bantotal . ')</li>';
    echo '<li><a href="index.php?act=usr_search_nick"><b>' . $lng['search'] . '</b></a></li>';
    echo '</ul></p></div>';
    echo '<div class="list2">';
    // Блок модулей
    if ($rights >= 7) {
        echo '<p><h3><img src="../images/modules.png" width="16" height="16" class="left" />&#160;' . $lng['modules'] . '</h3><ul>';
        echo '<li><a href="index.php?act=mod_ads">' . $lng['advertisement'] . '</a></li>';
        if ($rights == 9)
            echo '<li><a href="index.php?act=mod_counters">' . $lng['counters'] . '</a></li>';
        echo '<li><a href="index.php?act=mod_news">' . $lng['news'] . '</a></li>';
        echo '<li><a href="index.php?act=mod_forum">' . $lng['forum'] . '</a></li>';
        echo '<li><a href="index.php?act=mod_chat">' . $lng['chat'] . '</a></li>';
        echo '<li><a href="index.php?act=mod_karma">' . $lng['karma'] . '</a></li>';
        echo '</ul></p>';
    }
    // Блок системных настроек
    if ($rights >= 7) {
        if ($rights == 9) {
            echo '<p><h3><img src="../images/network.png" width="16" height="16" class="left" />&#160;' . $lng['ip_settings'] . '</h3><ul>';
            //TODO: Разобраться с правами доступа к поиску для модеров
            echo '<li><a href="index.php?act=usr_search_ip">' . $lng['ip_search'] . '</a></li>';
            echo '<li><a href="index.php?act=sys_ipban">' . $lng['ip_ban'] . '</a></li>';
            echo '</ul></p>';
        }
        echo '<p><h3><img src="../images/settings.png" width="16" height="16" class="left" />&#160;' . $lng['system'] . '</h3><ul>';
        if ($rights == 9){
            echo '<li><a href="index.php?act=sys_set">' . $lng['site_settings'] . '</a></li>';
            echo '<li><a href="index.php?act=sys_lng">' . $lng['language_default'] . '</a></li>';
        }
        echo '<li><a href="index.php?act=sys_smileys">' . $lng['refresh_smileys'] . '</a></li>';
        echo '</ul></p>';
        echo '</div><div class="bmenu">';
        echo '<p><h3><img src="../images/admin.png" width="16" height="16" class="left" />&#160;' . $lng['security'] . '</h3><ul>';
        echo '<li><a href="index.php?act=sys_flood">' . $lng['antiflood'] . '</a></li>';
        echo '<li><a href="index.php?act=sys_access">' . $lng['access_rights'] . '</a></li>';
        echo '<li><a href="index.php?act=sys_antispy">' . $lng['antispy'] . '</a></li>';
        echo '</ul></p></div>';
    }
}

require('../incfiles/end.php');

?>