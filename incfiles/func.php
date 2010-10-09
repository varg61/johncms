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

defined('_IN_JOHNCMS') or die('Error: restricted access');

/*
-----------------------------------------------------------------
Рекламная сеть mobileads.ru
-----------------------------------------------------------------
*/
function mobileads($mad_siteId = NULL) {
    $out = '';
    $mad_socketTimeout = 2;      // таймаут соединения с сервером mobileads.ru
    ini_set("default_socket_timeout", $mad_socketTimeout);
    $mad_pageEncoding = "UTF-8"; // устанавливаем кодировку страницы
    $mad_ua = urlencode(@$_SERVER['HTTP_USER_AGENT']);
    $mad_ip = urlencode(@$_SERVER['REMOTE_ADDR']);
    $mad_xip = urlencode(@$_SERVER['HTTP_X_FORWARDED_FOR']);
    $mad_ref = urlencode(@$_SERVER['SERVER_NAME'] . @$_SERVER['REQUEST_URI']);
    $mad_lines = "";
    $mad_fp = @fsockopen("mobileads.ru", 80, $mad_errno, $mad_errstr, $mad_socketTimeout);

    if ($mad_fp) {
        // переменная $mad_lines будет содержать массив, непарные элементы которого будут ссылками, парные - названием
        $mad_lines = @file("http://mobileads.ru/links?id=$mad_siteId&ip=$mad_ip&xip=$mad_xip&ua=$mad_ua&ref=$mad_ref");
    }
    @fclose($mad_fp); // вывод ссылок

    for ($malCount = 0; $malCount < count($mad_lines); $malCount += 2) {
        $linkURL = trim($mad_lines[$malCount]);
        $linkName = iconv("Windows-1251", $mad_pageEncoding, $mad_lines[$malCount + 1]);
        $out .= '<a href="' . $linkURL . '">' . $linkName . '</a><br />';
    }
    $_SESSION['mad_links'] = $out;
    $_SESSION['mad_time'] = $realtime;
    return $out;
}

?>