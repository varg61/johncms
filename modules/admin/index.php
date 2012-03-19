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
            $ipwhois = trim(TextParser::highlightUrl(htmlspecialchars($ipwhois)));
        } else {
            $ipwhois = lng('error_wrong_data');
        }
        $tpl->ipWhois = nl2br($ipwhois);
        $tpl->contents = $tpl->includeTpl('whois');
        break;

    case'users_settings':
        /*
        -----------------------------------------------------------------
        Настройки для пользователей
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 7) {
            $setUsers = isset(Vars::$SYSTEM_SET['users']) && !empty(Vars::$SYSTEM_SET['users'])
                ? unserialize(Vars::$SYSTEM_SET['users'])
                : Vars::$USER_SET_SYS;

            if (isset($_POST['submit'])) {
                $setUsers['reg_mode'] = isset($_POST['reg_mode']) && $_POST['reg_mode'] > 0 && $_POST['reg_mode'] < 4 ? intval($_POST['reg_mode']) : 3;
                $setUsers['flood_mode'] = isset($_POST['flood_mode']) && $_POST['flood_mode'] > 0 && $_POST['flood_mode'] < 5 ? intval($_POST['flood_mode']) : 1;
                $setUsers['flood_day'] = isset($_POST['flood_day']) ? intval($_POST['flood_day']) : 10;
                $setUsers['flood_night'] = isset($_POST['flood_night']) ? intval($_POST['flood_night']) : 30;
                $setUsers['upload_avatars'] = isset($_POST['upload_avatars']);
                $setUsers['upload_animation'] = isset($_POST['upload_animation']);
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
                $tpl->save = 1;
            } elseif (isset($_POST['reset'])) {
                @mysql_query("DELETE FROM `cms_settings` WHERE `key` = 'users'");
                $setUsers = Vars::$USER_SET_SYS;
                $tpl->reset = 1;
            } elseif (isset($_GET['reset'])) {
                $tpl->contents = $tpl->includeTpl('users_settings_reset');
                exit;
            }
            $tpl->setUsers = $setUsers;
            $tpl->contents = $tpl->includeTpl('users_settings');
        } else {
            echo Functions::displayError(lng('access_forbidden'));
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Главное меню Админ панели
        -----------------------------------------------------------------
        */
        $tpl->usrTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users`"), 0);
        //$tpl->regTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `level`='0'"), 0);
        //$tpl->banTotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time` > '" . time() . "'"), 0);
        $tpl->contents = $tpl->includeTpl('index');
}