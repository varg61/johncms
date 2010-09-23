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

// Проверяем права доступа
if ($rights < 1) {
    header('Location: http://johncms.com/?err');
    exit;
}

require_once('../incfiles/head.php');
$array = array (
    'ads' => 'modules',
    'chat' => 'modules',
    'counters' => 'modules',
    'forum' => 'modules',
    'karma' => 'modules',
    'news' => 'modules',
    'languages' => 'system',
    'seo' => 'system',
    'settings' => 'system',
    'smileys' => 'system',
    'access' => 'security',
    'antiflood' => 'security',
    'antispy' => 'security',
    'ipban' => 'ip',
    'search_ip' => 'ip',
    'administrators' => 'users',
    'ban_panel' => 'users',
    'reg' => 'users',
    'search_user' => 'users',
    'users' => 'users',
    'usr_del' => 'users'
);
$path = !empty($array[$act]) ? $array[$act] . '/' : '';
if (array_key_exists($act, $array) && file_exists($path . $act . '.php')) {
    require_once($path . $act . '.php');
} else {
    /*
    -----------------------------------------------------------------
    Главное меню Админ панели
    -----------------------------------------------------------------
    */
    $regtotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `preg`='0'"), 0);
    $bantotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time` > '$realtime'"), 0);
    echo '<div class="phdr"><b>' . $lng['admin_panel'] . '</b></div>' .
        '<div class="user"><p><h3><img src="../images/users.png" width="16" height="16" class="left" />&#160;' . $lng['users'] . '</h3><ul>';
    if ($regtotal && $rights >= 6)
        echo '<li><span class="red"><b><a href="index.php?act=reg">' . $lng['users_reg'] . '</a>&#160;(' . $regtotal . ')</b></span></li>';
    echo '<li><a href="index.php?act=administrators">' . $lng['users_administration'] . '</a>&#160;(' . mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `rights` >= '1'"), 0) . ')</li>' .
        '<li><a href="index.php?act=users">' . $lng['users'] . '</a>&#160;(' . stat_countusers() . ')</li>' .
        '<li><a href="index.php?act=ban_panel">' . $lng['ban_panel'] . '</a>&#160;(' . $bantotal . ')</li>' .
        '<li><a href="index.php?act=search_user"><b>' . $lng['search'] . '</b></a></li>' .
        '</ul></p></div>' .
        '<div class="list2">';
    // Блок модулей
    if ($rights >= 7) {
        echo '<p><h3><img src="../images/modules.png" width="16" height="16" class="left" />&#160;' . $lng['modules'] . '</h3><ul>' .
            '<li><a href="index.php?act=ads">' . $lng['advertisement'] . '</a></li>';
        if ($rights == 9)
            echo '<li><a href="index.php?act=counters">' . $lng['counters'] . '</a></li>';
        echo '<li><a href="index.php?act=news">' . $lng['news'] . '</a></li>' .
            '<li><a href="index.php?act=forum">' . $lng['forum'] . '</a></li>' .
            '<li><a href="index.php?act=chat">' . $lng['chat'] . '</a></li>' .
            '<li><a href="index.php?act=karma">' . $lng['karma'] . '</a></li>' .
            '</ul></p>';
    }
    // Работа с IP адресами
    echo '<p><h3><img src="../images/network.png" width="16" height="16" class="left" />&#160;' . $lng['ip_settings'] . '</h3><ul>' .
        '<li><a href="index.php?act=search_ip">' . $lng['ip_search'] . '</a></li>';
    if ($rights == 9) {
        echo '<li><a href="index.php?act=ipban">' . $lng['ip_ban'] . '</a></li>';
    }
    echo '</ul></p>';
    if ($rights >= 7) {
        // Блок системных настроек
        echo '<p><h3><img src="../images/settings.png" width="16" height="16" class="left" />&#160;' . $lng['system'] . '</h3><ul>';
        if ($rights == 9) {
            echo '<li><a href="index.php?act=settings">' . $lng['site_settings'] . '</a></li>' .
                '<li><a href="index.php?act=languages">' . $lng['language_settings'] . '</a></li>';
        }
        echo '<li><a href="index.php?act=smileys">' . $lng['refresh_smileys'] . '</a></li>' .
            '<li><a href="index.php?act=seo">SEO Tools</a></li>' .
            '</ul></p></div>';
        // Блок безопасности
        echo '<div class="bmenu">' .
            '<p><h3><img src="../images/admin.png" width="16" height="16" class="left" />&#160;' . $lng['security'] . '</h3><ul>' .
            '<li><a href="index.php?act=antiflood">' . $lng['antiflood'] . '</a></li>' .
            '<li><a href="index.php?act=access">' . $lng['access_rights'] . '</a></li>' .
            '<li><a href="index.php?act=antispy">' . $lng['antispy'] . '</a></li>' .
            '</ul></p>';
    }
    echo '</div>';
}

require('../incfiles/end.php');
?>