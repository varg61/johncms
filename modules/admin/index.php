<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

@ini_set("max_execution_time", "600");
defined('_IN_JOHNCMS') or die('Error: restricted access');

//TODO: Распределить права доступа!!!

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 1) {
    header('Location: http://johncms.com/404');
    exit;
}

$tpl = Template::getInstance();

switch (Vars::$ACT) {
    case'whois':
        /*
        -----------------------------------------------------------------
        IP Whois
        -----------------------------------------------------------------
        */
        $ip = isset($_GET['ip']) ? trim($_GET['ip']) : false;
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
                'address:' => '<strong class="green">address:</strong>',
                'e-mail:' => '<strong class="green">e-mail:</strong>',
                'person:' => '<strong class="green">person:</strong>',
                'phone:' => '<strong class="green">phone:</strong>',
                'route:' => '<strong class="red"><b>route:</b></strong>',
                'org-name:' => '<strong class="red"><b>org-name:</b></strong>',
                'abuse-mailbox:' => '<strong class="red"><b>abuse-mailbox:</b></strong>',
                'fax-no:' => '<strong class="green">fax-no:</strong>'
            );
            $ipwhois = trim(TextParser::highlightUrl(htmlspecialchars($ipwhois)));
            $ipwhois = strtr($ipwhois, $array);
        } else {
            $ipwhois = lng('error_wrong_data');
        }
        $tpl->ipWhois = nl2br($ipwhois);
        $tpl->contents = $tpl->includeTpl('whois');
        break;

    case'set_users':
        /*
        -----------------------------------------------------------------
        Настройки для пользователей
        -----------------------------------------------------------------
        */
        $defaults = array(
            'reg_mode'    => 3,
            'flood_mode'  => 2,
            'flood_day'   => 10,
            'flood_night' => 30
        );
        $setUsers = isset(Vars::$SYSTEM_SET['users']) && !empty(Vars::$SYSTEM_SET['users'])
            ? unserialize(Vars::$SYSTEM_SET['users'])
            : $defaults;

        if (isset($_POST['submit'])) {
            $setUsers['reg_mode'] = isset($_POST['reg_mode']) && $_POST['reg_mode'] > 0 && $_POST['reg_mode'] < 4 ? intval($_POST['reg_mode']) : 3;
            $setUsers['flood_mode'] = isset($_POST['flood_mode']) && $_POST['flood_mode'] > 0 && $_POST['flood_mode'] < 5 ? intval($_POST['flood_mode']) : 1;
            $setUsers['flood_day'] = isset($_POST['flood_day']) ? intval($_POST['flood_day']) : 10;
            $setUsers['flood_night'] = isset($_POST['flood_night']) ? intval($_POST['flood_night']) : 30;
            // Проверяем принятые данные
            if ($setUsers['flood_day'] < 5) {
                $setUsers['flood_day'] = 5;
            } elseif ($setUsers['flood_day'] > 300) {
                $setUsers['flood_day'] = 300;
            }
            if ($setUsers['flood_night'] < 4) {
                $setUsers['flood_night'] = 4;
            } elseif ($setUsers['flood_night'] > 300) {
                $setUsers['flood_night'] = 300;
            }
            // Записываем настройки в базу
            mysql_query("REPLACE INTO `cms_settings` SET
                `key` = 'users',
                `val` = '" . mysql_real_escape_string(serialize($setUsers)) . "'
            ");
            // Подтверждение сохранения настроек
            $tpl->saved = 1;
        } elseif (isset($_POST['reset'])) {

        }
        $tpl->setUsers = $setUsers;
        $tpl->contents = $tpl->includeTpl('user_settings');
        break;

    default:
        /*
        -----------------------------------------------------------------
        Главное меню Админ панели
        -----------------------------------------------------------------
        */
        $tpl->usrTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users`"), 0);
        $tpl->regTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `level`='0'"), 0);
        $tpl->banTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time` > '" . time() . "'"), 0);
        $tpl->contents = $tpl->includeTpl('index');
}