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

$textl = 'Администрация';
$headmod = "moders";
require_once('../incfiles/core.php');
require_once('../incfiles/head.php');
echo '<div class="phdr"><b>Администрация ресурса</b></div>';

// Супервайзоры
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '9'");
if (mysql_num_rows($req)) {
    echo '<div class="bmenu">Супервайзоры</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($sw % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res) . '</div>';
        ++$sw;
    }
}
// Администраторы
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '7'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Администраторы</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($adm % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res) . '</div>';
        ++$adm;
    }
}
// Старшие Модераторы
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '6'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Старшие Модераторы</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($smd % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res) . '</div>';
        ++$smd;
    }
}
// Модераторы Библиотеки
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '5'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Модераторы Библиотеки</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($lmod % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res) . '</div>';
        ++$lmod;
    }
}
// Модераторы Загрузок
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '4'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Модераторы Загрузок</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($dmod % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res) . '</div>';
        ++$dmod;
    }
}
// Модераторы Форума
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '3'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Модераторы Форума</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($fmod % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res) . '</div>';
        ++$fmod;
    }
}
// Модераторы Чата
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '2'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Модераторы Чата</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($cmod % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res) . '</div>';
        ++$cmod;
    }
}
// Киллеры
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '1'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Киллеры</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($kil % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res) . '</div>';
        ++$kil;
    }
}
echo '<div class="phdr">Всего: ' . ($sw + $adm + $smd + $lmod + $dmod + $fmod + $cmod + $kil) . '</div>';
echo '<p><a href="../index.php?act=users">Актив сайта</a></p>';

require_once('../incfiles/end.php');

?>