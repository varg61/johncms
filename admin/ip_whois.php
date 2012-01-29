<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require_once('../includes/core.php');

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 1) {
    header('Location: http://johncms.com/404.php');
    exit;
}

$ip = isset($_GET['ip']) ? trim($_GET['ip']) : false;

echo'<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['admin_panel'] . '</b></a> | IP WHOIS</div>';
if ($ip) {
    $ipwhois = '';
    if (($fsk = @fsockopen('whois.arin.net.', 43))) {
        fputs($fsk, "$ip\r\n");
        while (!feof($fsk)) $ipwhois .= fgets($fsk, 1024);
        @fclose($fsk);
    }
    $match = array();
    if (preg_match('#ReferralServer: whois://(.+)#im', $ipwhois, $match)) {
        if (strpos($match[1], ':') !== false) {
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
    $array = array(
        '%' => '#',
        'NetRange:' => '<strong class="red">NetRange:</strong>',
        'NetName:' => '<strong class="red">NetName:</strong>',
        'OriginAS:' => '<strong class="gray">OriginAS:</strong>',
        'CIDR:' => '<strong class="gray">CIDR:</strong>',
        'NetHandle:' => '<strong class="gray">NetHandle:</strong>',
        'Parent:' => '<strong class="gray">Parent:</strong>',
        'RegDate:' => '<strong class="gray">RegDate:</strong>',
        'Updated:' => '<strong class="gray">Updated:</strong>',
        'inetnum:' => '<strong class="red">inetnum:</strong>',
        'Ref:' => '<strong class="green">Ref:</strong>',
        'OrgName:' => '<strong class="green">OrgName:</strong>',
        'OrgId:' => '<strong class="green">OrgId:</strong>',
        'Address:' => '<strong class="green">Address:</strong>',
        'City:' => '<strong class="green">City:</strong>',
        'StateProv:' => '<strong class="green">StateProv:</strong>',
        'PostalCode:' => '<strong class="green">PostalCode:</strong>',
        'netname:' => '<strong class="green">netname:</strong>',
        'descr:' => '<strong class="red">descr:</strong>',
        'country:' => '<strong class="red">country:</strong>',
        'Country:' => '<strong class="red">Country:</strong>',
        'admin-c:' => '<strong class="gray">admin-c:</strong>',
        'tech-c:' => '<strong class="gray">tech-c:</strong>',
        'status:' => '<strong class="gray">status:</strong>',
        'mnt-by:' => '<strong class="gray">mnt-by:</strong>',
        'mnt-lower:' => '<strong class="gray">mnt-lower:</strong>',
        'mnt-routes:' => '<strong class="gray">mnt-routes:</strong>',
        'OrgAbuseRef:' => '<strong class="gray">OrgAbuseRef:</strong>',
        'OrgTechRef:' => '<strong class="gray">OrgTechRef:</strong>',
        'OrgTechHandle:' => '<strong class="gray">OrgTechHandle:</strong>',
        'OrgTechName:' => '<strong class="gray">OrgTechName:</strong>',
        'OrgTechEmail:' => '<strong class="gray">OrgTechEmail:</strong>',
        'OrgTechPhone:' => '<strong class="gray">OrgTechPhone:</strong>',
        'source:' => '<strong class="gray">source:</strong>',
        'role:' => '<strong class="gray">role:</strong>',
        'address:' => '<strong class="green">address:</strong>',
        'e-mail:' => '<strong class="green">e-mail:</strong>',
        'nic-hdl:' => '<strong class="gray">nic-hdl:</strong>',
        'org:' => '<strong class="gray">org:</strong>',
        'person:' => '<strong class="green">person:</strong>',
        'phone:' => '<strong class="green">phone:</strong>',
        'remarks:' => '<strong class="gray">remarks:</strong>',
        'route:' => '<strong class="red"><b>route:</b></strong>',
        'origin:' => '<strong class="gray">origin:</strong>',
        'organisation:' => '<strong class="gray">organisation:</strong>',
        'org-name:' => '<strong class="red"><b>org-name:</b></strong>',
        'org-type:' => '<strong class="gray">org-type:</strong>',
        'abuse-mailbox:' => '<strong class="red"><b>abuse-mailbox:</b></strong>',
        'mnt-ref:' => '<strong class="gray">mnt-ref:</strong>',
        'fax-no:' => '<strong class="green">fax-no:</strong>',
        'NetType:' => '<strong class="gray">NetType:</strong>',
        'Comment:' => '<strong class="gray">Comment:</strong>'
    );
    $ipwhois = trim(TextParser::highlightUrl(htmlspecialchars($ipwhois)));
    $ipwhois = strtr($ipwhois, $array);
} else {
    $ipwhois = Vars::$LNG['error_wrong_data'];
}
echo'<div class="menu"><small>' . nl2br($ipwhois) . '</small></div>' .
    '<div class="phdr"><a href="' . htmlspecialchars($_SERVER['HTTP_REFERER']) . '">' . Vars::$LNG['back'] . '</a></div>';