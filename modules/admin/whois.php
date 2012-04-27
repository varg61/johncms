<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 *
 * IP Whois module
 */

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 1) {
    echo Functions::displayError(lng('access_forbidden'));
    exit;
}

$tpl = Template::getInstance();

if (Vars::$USER_RIGHTS
    && isset($_GET['ip'])
    && ip2long($_GET['ip']) !== FALSE
) {
    $ipwhois = '';
    $ip = trim($_GET['ip']);
    if (($fsk = @fsockopen('whois.arin.net.', 43))) {
        fputs($fsk, "$ip\r\n");
        while (!feof($fsk)) $ipwhois .= fgets($fsk, 1024);
        @fclose($fsk);
    }
    $match = array();
    if (preg_match('#ReferralServer: whois://(.+)#im', $ipwhois, $match)) {
        if (strpos($match[1], ':') !== FALSE) {
            $pos = strrpos($match[1], ':');
            $server = substr($match[1], 0, $pos);
            $port = (int)substr($match[1], $pos + 1);
            unset($pos);
        } else {
            $server = $match[1];
            $port = 43;
        }
        $buffer = '';
        if (($fsk = @fsockopen($server, $port))) {
            fputs($fsk, "$ip\r\n");
            while (!feof($fsk)) $buffer .= fgets($fsk, 1024);
            @fclose($fsk);
        }
        $ipwhois = (empty($buffer)) ? $ipwhois : $buffer;
    }
    $ipwhois = trim(TextParser::highlightUrl(htmlspecialchars($ipwhois)));
} else {
    $ipwhois = lng('error_wrong_data');
}
$tpl->ipWhois = nl2br($ipwhois);
$tpl->contents = $tpl->includeTpl('whois');