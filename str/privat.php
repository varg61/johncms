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
session_name('SESID');
session_start();
$headmod = 'privat';
$textl = 'Почта';
require_once ("../incfiles/core.php");
require_once ("../incfiles/head.php");

if (!empty($_SESSION['uid']))
{
    $newl = mysql_query("select * from `privat` where `user` LIKE '" . $login . "' and `type` LIKE 'in' and `chit` LIKE 'no';");
    $countnew = mysql_num_rows($newl);
    if ($countnew > 0)
    {
        $newlet = "|<span style='color: red'>$countnew</span>";
    }
    $messages = mysql_query("select * from `privat` where user='$login' and type='in' ;");
    $count = mysql_num_rows($messages);
    echo "<p><a href='pradd.php?act=in'>Входящих</a> ($count$newlet)<br/>";
    $messages = mysql_query("select * from `privat` where author='$login' and type='out' ;");
    $count = mysql_num_rows($messages);
    $newo = mysql_query("select * from `privat` where `author` LIKE '" . $login . "' and `type` LIKE 'out' and `chit` LIKE 'no';");
    $countnewo = mysql_num_rows($newo);
    if ($countnewo > 0)
    {
        $newleto = "|<span style='color: red'>$countnewo</span>";
    }
    $contacts = mysql_query("select * from `privat` where me='$login' and cont!='';");
    $vscon = mysql_num_rows($contacts);
    $g = 0;
    $onltime = $realtime - 300;
    while ($itog = mysql_fetch_array($contacts))
    {
        $uson = mysql_query("select * from `users` where name='$itog[cont]' and pvrem>='" . intval($onltime) . "';");
        $uson1 = mysql_num_rows($uson);
        if ($uson1 == 1)
        {
            $g = $g + 1;
        }
    }
    $ign = mysql_query("select * from `privat` where me='$login' and ignor!='';");
    $ign1 = mysql_num_rows($ign);

    echo "<a href='pradd.php?act=out'>Исходящие</a> ($count$newleto)<br/>";
    echo "<a href='cont.php?'>Контакты($g/$vscon)</a><br/>";
    echo "<a href='ignor.php?'>Игнор($ign1)</a><br/>";
    echo "<a href='pradd.php?act=write'>Написать</a></p>";
}

require_once ('../incfiles/end.php');

?>