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

defined('_IN_ADMIN') or die('Error: restricted access');

$tpl = Template::getInstance();
$form = new Form(Vars::$URI . '?act=whois');

$ip = isset($_GET['ip']) ? trim($_GET['ip']) : FALSE;

$form
    ->fieldsetStart(__('ip_information'))

    ->addHtml('<br/>')

    ->add('text', 'ip', array(
    'value' => ($ip ? $ip : '')))

    ->add('submit', 'submit', array(
    'value' => __('search'),
    'class' => 'btn btn-primary'))

    ->addHtml('<a class="btn" href="' . (isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : Vars::$URI) . '">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted || $ip) {
    $ip = $form->isSubmitted ? trim($form->validOutput['ip']) : $ip;
    if ($ip && empty($ip) || !Validate::ip($ip)) {
        $tpl->errormsg = __('error_ip');
    } else {
        $ipwhois = '';
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

        $ipwhois = strtr($ipwhois, array(
            '%'              => '#',
            'inetnum:'       => '<span style="color: #c81237">inetnum:</span>',
            'netname:'       => '<span style="color: #c81237">netname:</span>',
            'descr:'         => '<span style="color: #c81237">descr:</span>',
            'country:'       => '<span style="color: #c81237">country:</span>',
            'address:'       => '<span style="color: #c81237">address:</span>',
            'e-mail:'        => '<span style="color: #c81237">e-mail:</span>',
            'route:'         => '<span style="color: #c81237">route:</span>',
            'org-name:'      => '<span style="color: #c81237">org-name:</span>',
            'abuse-mailbox:' => '<span style="color: #c81237">abuse-mailbox:</span>',
        ));

        $tpl->whois = nl2br($ipwhois);
    }
}

$tpl->contents = $tpl->includeTpl('whois');