<?php
/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                             Content Management System              //
// Официальный сайт сайт проекта:      http://johncms.com                     //
// Дополнительный сайт поддержки:      http://gazenwagen.com                  //
////////////////////////////////////////////////////////////////////////////////
// JohnCMS core team:                                                         //
// Евгений Рябинин aka john77          john77@gazenwagen.com                  //
// Олег Касьянов aka AlkatraZ          alkatraz@gazenwagen.com                //
//                                                                            //
// Информацию о версиях смотрите в прилагаемом файле version.txt              //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);

$textl = 'Администрация';
$headmod = "moders";
require_once ('../incfiles/core.php');
require_once ('../incfiles/head.php');

echo '<div class="phdr"><b>Администрация ресурса</b></div>';

// Супервизоры
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '9'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Супервизоры</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($i % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res, 1) . '</div>';
        ++$i;
    }
}
// Администраторы
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '7'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Администраторы</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($i % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res, 1) . '</div>';
        ++$i;
    }
}
// Старшие Модераторы
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '6'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Старшие Модераторы</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($i % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res, 1) . '</div>';
        ++$i;
    }
}
// Модераторы Библиотеки
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '5'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Модераторы Библиотеки</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($i % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res, 1) . '</div>';
        ++$i;
    }
}
// Модераторы Загрузок
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '4'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Модераторы Загрузок</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($i % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res, 1) . '</div>';
        ++$i;
    }
}
// Модераторы Форума
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '3'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Модераторы Форума</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($i % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res, 1) . '</div>';
        ++$i;
    }
}
// Модераторы Чата
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '2'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Модераторы Чата</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($i % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res, 1) . '</div>';
        ++$i;
    }
}
// Киллеры
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '1'");
if (mysql_num_rows($req)) {
    $i = 0;
    echo '<div class="bmenu">Киллеры</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo ($i % 2) ? '<div class="list2">' : '<div class="list1">';
        echo show_user($res, 1) . '</div>';
        ++$i;
    }
}
echo '<div class="phdr">&nbsp;</div>';
echo '<p><a href="../index.php?act=users">Актив сайта</a></p>';
require_once ("../incfiles/end.php");

?>