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

define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
$lng_cab = load_lng('cab');
$textl = $lng_cab['my_office'];
require('../incfiles/head.php');
if (!$user_id) {
    echo display_error($lng['access_guest_forbidden']);
    require('../incfiles/end.php');
    exit;
}
switch ($act) {
    default:
        /*
        -----------------------------------------------------------------
        Главное меню личного Кабинета
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . $lng_cab['my_office'] . '</b></div>';
        // Блок статистики
        echo '<div class="gmenu"><p><h3><img src="../images/rate.gif" width="16" height="16" class="left" />&#160;' . $lng_cab['my_actives'] . '</h3><ul>' .
            '<li><a href="my_stat.php">' . $lng_cab['my_statistics'] . '</a></li>';
        if ($rights >= 1) {
            $guest = stat_guestbook(2);
            echo '<li><a href="guest.php?act=ga&amp;do=set">' . $lng['admin_club'] . '</a> (<span class="red">' . $guest . '</span>)</li>' .
                '<li><span class="red"><a href="../' . $admp . '/index.php"><b>' . $lng['admin_panel'] . '</b></a></span></li>';
        }
        echo '</ul></p></div>';
        // Блок почты
        $count_mail = mysql_result(mysql_query("SELECT COUNT(*) FROM `privat` WHERE `user` = '$login' AND `type` = 'in'"), 0);
        $count_newmail = mysql_result(mysql_query("SELECT COUNT(*) FROM `privat` WHERE `user` = '" . $login . "' AND `type` = 'in' AND `chit` = 'no'"), 0);
        $count_sentmail = mysql_result(mysql_query("SELECT COUNT(*) FROM `privat` WHERE `author` = '$login' AND `type` = 'out'"), 0);
        $count_sentunread = mysql_result(mysql_query("SELECT COUNT(*) FROM `privat` WHERE `author` = '$login' AND `type` = 'out' AND `chit` = 'no'"), 0);
        $count_files = mysql_result(mysql_query("SELECT COUNT(*) FROM `privat` WHERE `user` = '$login' AND `type` = 'in' AND `attach` != ''"), 0);
        echo '<div class="menu"><p><h3><img src="../images/mail.png" width="16" height="16" class="left" />&#160;' . $lng_cab['my_mail'] . '</h3><ul>' .
            '<li><a href="pradd.php?act=in">' . $lng_cab['received'] . '</a>&#160;(' . $count_mail . ($count_newmail ? '&#160;/&#160;<span class="red"><a href="pradd.php?act=in&amp;new">+' . $count_newmail . '</a></span>' : '') . ')</li>' .
            '<li><a href="pradd.php?act=out">' . $lng_cab['sent'] . '</a>&#160;(' . $count_sentmail . ($count_sentunread ? '&#160;/&#160;<span class="red">' . $count_sentunread . '</span>' : '') . ')</li>';
        if (!$ban['1'] && !$ban['3'])
            echo '<p><form action="pradd.php?act=write" method="post"><input type="submit" value=" ' . $lng['write'] . ' " /></form></p>';
        // Блок контактов
        $count_contacts = mysql_result(mysql_query("SELECT COUNT(*) FROM `privat` WHERE `me` = '$login' AND `cont` != ''"), 0);
        $count_ignor = mysql_result(mysql_query("SELECT COUNT(*) FROM `privat` WHERE `me` = '$login' AND `ignor` != ''"), 0);
        echo '</ul><h3><img src="../images/contacts.png" width="16" height="16" class="left" />&#160;' . $lng['contacts'] . '</h3><ul>' .
            '<li><a href="cont.php">' . $lng['contacts'] . '</a>&#160;(' . $count_contacts . ')</li>' .
            '<li><a href="ignor.php">' . $lng['blocking'] . '</a>&#160;(' . $count_ignor . ')</li>' .
            '</ul></p></div>';
        // Блок настроек
        echo '<div class="bmenu"><p><h3><img src="../images/settings.png" width="16" height="16" class="left" />&#160;' . $lng_cab['my_settings'] . '</h3><ul>' .
            '<li><a href="my_set.php">' . $lng['system_settings'] . '</a></li>' .
            '<li><a href="my_pass.php">' . $lng['change_password'] . '</a></li>' .
            '<li><a href="anketa.php">' . $lng_cab['my_profile'] . '</a></li>' .
            '</ul></p></div>';
}

require('../incfiles/end.php');
?>