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

defined('_IN_JOHNADM') or die('Error: restricted access');

$error = array ();
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$search = $search ? $search : rawurldecode(trim($_GET['search']));
if (isset($_GET['ip']))
    $search = long2ip(intval($_GET['ip']));
echo '<div class="phdr"><a href="index.php"><b>' . $lng['admin_panel'] . '</b></a> | ' . $lng['ip_search'] . '</div>';
echo '<form action="index.php?act=usr_search_ip" method="post"><div class="gmenu"><p>';
echo '<input type="text" name="search" value="' . checkout($search) . '" />';
echo '<input type="submit" value="' . $lng['search'] . '" name="submit" /><br />';
echo '</p></div></form>';

if ($search) {
    if (strstr($search, '-')) {
        ////////////////////////////////////////////////////////////
        // Обрабатываем диапазон адресов                          //
        ////////////////////////////////////////////////////////////
        $array = explode('-', $search);
        $ip = trim($array[0]);
        if (!ip_valid($ip))
            $error[] = $lng['error_firstip'];
        else
            $ip1 = ip2long($ip);
        $ip = trim($array[1]);
        if (!ip_valid($ip))
            $error[] = $lng['error_secondip'];
        else
            $ip2 = ip2long($ip);
    } elseif (strstr($search, '*')) {
        ////////////////////////////////////////////////////////////
        // Обрабатываем адреса с маской                           //
        ////////////////////////////////////////////////////////////
        $array = explode('.', $search);
        for ($i = 0; $i < 4; $i++) {
            if (!isset($array[$i]) || $array[$i] == '*') {
                $ipt1[$i] = '0';
                $ipt2[$i] = '255';
            } elseif (is_numeric($array[$i]) && $array[$i] >= 0 && $array[$i] <= 255) {
                $ipt1[$i] = $array[$i];
                $ipt2[$i] = $array[$i];
            } else {
                $error = $lng['error_address'];
            }
            $ip1 = ip2long($ipt1[0] . '.' . $ipt1[1] . '.' . $ipt1[2] . '.' . $ipt1[3]);
            $ip2 = ip2long($ipt2[0] . '.' . $ipt2[1] . '.' . $ipt2[2] . '.' . $ipt2[3]);
        }
    } else {
        ////////////////////////////////////////////////////////////
        // Обрабатываем одиночный адрес                           //
        ////////////////////////////////////////////////////////////
        if (!ip_valid($search)) {
            $error = $lng['error_address'];
        } else {
            $ip1 = ip2long($search);
            $ip2 = $ip1;
        }
    }
}
if ($search && !$error) {
    ////////////////////////////////////////////////////////////
    // Выводим результаты поиска                              //
    ////////////////////////////////////////////////////////////
    echo '<div class="phdr">' . $lng['search_results'] . '</div>';
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_users_iphistory` WHERE `user_ip` BETWEEN $ip1 AND $ip2"), 0);
    if ($total) {
        $req = mysql_query("SELECT `cms_users_iphistory`.`user_ip` AS `ip`, `users`.`name`, `users`.`rights`, `users`.`lastdate`, `users`.`sex`, `users`.`status`, `users`.`datereg`, `users`.`id`, `users`.`browser`
        FROM `cms_users_iphistory` LEFT JOIN `users` ON `cms_users_iphistory`.`user_id` = `users`.`id`
        WHERE `cms_users_iphistory`.`user_ip` BETWEEN $ip1 AND $ip2
        ORDER BY `ip` ASC, `name` ASC LIMIT $start, $kmess");
        while ($res = mysql_fetch_assoc($req)) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            echo show_user($res, array('iphist' => 1));
            echo '</div>';
            ++$i;
        }
    } else {
        echo '<div class="menu"><p>' . $lng['not_found'] . '</p></div>';
    }
    echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
    if ($total > $kmess) {
        // Навигация по страницам
        echo '<p>' . pagenav('index.php?act=usr_search_ip&amp;' . ($search_t ? 't=1&amp;' : '') . 'search=' . rawurlencode($search) . '&amp;', $start, $total, $kmess) . '</p>';
        echo '<p><form action="index.php?act=usr_search_ip" method="post"><input type="hidden" name="search" value="' . checkout($search) .
            '" /><input type="text" name="page" size="2"/><input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
    }
    echo '<p><a href="index.php?act=usr_search_ip">' . $lng['search_new'] . '</a><br /><a href="index.php">' . $lng['admin_panel'] . '</a></p>';
} else {
    // Выводим сообщение об ошибке
    if ($error)
        echo display_error($error);
    // Инструкции для поиска
    echo '<div class="phdr">' . $lng['search_ip_help'] . '</div>';
    echo '<p><a href="index.php">' . $lng['admin_panel'] . '</a></p>';
}

?>