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

defined('_IN_JOHNCMS') or die('Error: restricted access');

$headmod = isset($headmod) ? mysql_real_escape_string($headmod) : '';
if ($headmod == 'mainpage')
    $textl = $copyright;

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
header((stristr($agn, "msie") && stristr($agn, "windows")) ? 'Content-type: text/html; charset=UTF-8' : 'Content-type: application/xhtml+xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
echo "\n" . '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">';
echo "\n" . '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">';
echo "\n" . '<head><meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>';
echo "\n" . '<link rel="shortcut icon" href="' . $home . '/favicon.ico" />';
echo "\n" . '<meta name="copyright" content="Powered by JohnCMS" />'; // ВНИМАНИЕ!!! Данный копирайт удалять нельзя
echo "\n" . '<link rel="alternate" type="application/rss+xml" title="RSS | Новости ресурса" href="' . $home . '/rss/rss.php" />';
echo "\n" . '<title>' . $textl . '</title>';
echo "\n" . '<link rel="stylesheet" href="' . $home . '/theme/' . $set_user['skin'] . '/style.css" type="text/css" />';
echo "\n" . '</head><body>';

// Выводим логотип
echo '<div><img src="' . $home . '/theme/' . $set_user['skin'] . '/images/logo.gif" alt=""/></div>';

// Выводим верхний блок с приветствием
echo '<div class="header">Привет ' . ($user_id ? '<b> ' . $login . '</b>!' : 'прохожий!') . '</div>';

// Выводим меню пользователя
echo '<div class="tmn">';
echo ($headmod != "mainpage" || ($headmod == 'mainpage' && $act)) ? '<a href=\'' . $home . '\'>На главную</a> | ' : '';
echo ($user_id && $_GET['mod'] != 'cab') ? '<a href="' . $home . '/index.php?act=cab">Личное</a> | ' : '';
echo $user_id ? '<a href="' . $home . '/exit.php">Выход</a>' : '<a href="' . $home . '/in.php">Вход</a> | <a href="' . $home . '/registration.php">Регистрация</a>';
echo '</div>';
echo '<div class="maintxt">';

////////////////////////////////////////////////////////////
// Фиксация местоположений посетителей                    //
////////////////////////////////////////////////////////////
$sql = '';
if ($user_id)
{
    // Фиксируем местоположение авторизованных
    $movings = $datauser['movings'];
    if ($datauser['lastdate'] < ($realtime - 300))
    {
        $movings = 0;
        $sql .= "`sestime` = '$realtime',";
    }
    if ($datauser['place'] != $headmod)
    {
        $movings = $movings + 1;
        $sql .= "`movings` = '$movings', `place` = '$headmod',";
    }
    if ($datauser['ip'] != $ipl)
        $sql .= "`ip` = '$ipl',";
    if ($datauser['browser'] != $agn)
        $sql .= "`browser` = '" . mysql_real_escape_string($agn) . "',";
    $totalonsite = $datauser['total_on_site'];
    if ($datauser['lastdate'] > ($realtime - 300))
        $totalonsite = $totalonsite + $realtime - $datauser['lastdate'];
    mysql_query("UPDATE `users` SET $sql
    `total_on_site` = '$totalonsite',
    `lastdate` = '$realtime'
    WHERE `id` = '$user_id'");
} else
{
    // Фиксируем местоположение гостей
    $sid = md5($ipl . $agn);
    $movings = 0;
    $req = mysql_query("SELECT * FROM `cms_guests` WHERE `session_id` = '$sid' LIMIT 1");
    if (mysql_num_rows($req))
    {
        // Если есть в базе, то обновляем данные
        $res = mysql_fetch_assoc($req);
        $movings = $res['movings'];
        if ($res['sestime'] < ($realtime - 300))
        {
            $movings = 0;
            $sql .= "`sestime` = '$realtime',";
        }
        if ($res['ip'] != $ipl)
            $sql .= "`ip` = '$ipl',";
        if ($res['browser'] != $agn)
            $sql .= "`browser` = '" . mysql_real_escape_string($agn) . "',";
        if ($res['place'] != $headmod)
        {
            $movings = $movings + 1;
            $sql .= "`movings` = '$movings', `place` = '$headmod',";
        }
        mysql_query("UPDATE `cms_guests` SET $sql
        `lastdate` = '$realtime'
        WHERE `session_id` = '$sid'");
    } else
    {
        // Если еще небыло в базе, то добавляем запись
        mysql_query("INSERT INTO `cms_guests` SET
        `session_id` = '$sid',
        `ip` = '$ipl',
        `browser` = '" . mysql_real_escape_string($agn) . "',
        `lastdate` = '$realtime',
        `sestime` = '$realtime',
        `place` = '$headmod'");
    }
}

// Выводим сообщение о Бане
if (isset($ban))
    echo '<div class="alarm">БАН&nbsp;<a href="' . $home . '/index.php?act=ban">Подробно</a></div>';

// Проверяем, есть ли новые письма
if ($headmod != "pradd")
{
    $newl = mysql_query("SELECT COUNT(*) FROM `privat` WHERE `user` = '$login' AND `type` = 'in' AND `chit` = 'no'");
    $countnew = mysql_result(mysql_query("SELECT COUNT(*) FROM `privat` WHERE `user` = '$login' AND `type` = 'in' AND `chit` = 'no'"), 0);
    if ($countnew > 0)
    {
        echo "<div class=\"rmenu\" style='text-align: center'><a href='$home/str/pradd.php?act=in&amp;new'><b><font color='red'>Вам письмо: $countnew</font></b></a></div>";
    }
}

?>