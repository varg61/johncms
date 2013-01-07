<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Firewall
{
    public $cacheFile = 'ip_list.dat';

    public function __construct()
    {
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ip_bwlist`"), 0);
        if ($count && !file_exists(CACHEPATH . $this->cacheFile)) {
            $this->updateCache();
        } elseif (!$count && file_exists(CACHEPATH . $this->cacheFile)) {
            unlink(CACHEPATH . $this->cacheFile);
        }
    }

    public function updateCache()
    {
        $req = mysql_query("SELECT * FROM `cms_ip_bwlist`");
        if (mysql_num_rows($req)) {
            $in = fopen(CACHEPATH . $this->cacheFile, "w+");
            flock($in, LOCK_EX);
            ftruncate($in, 0);
            while ($res = mysql_fetch_assoc($req)) {
                $mode = $res['mode'] == 'white' ? 2 : 1;
                fwrite($in, pack('ddS', $res['ip'], $res['ip_upto'], $mode));
            }
            fclose($in);
        }
    }

    public function add($ip)
    {
        if (isset($_POST['submit']) || isset($_POST['confirm'])) {
            $error = array();
            $ip1 = 0;
            $ip2 = 0;
            $get_ip = isset($_POST['ip']) ? trim($_POST['ip']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';

            // Если адрес не введен, выдаем ошибку
            if (empty($get_ip)) {
                $error[] = __('add_ip_address_empty');
            }

            if (empty($error)) {
                if (strstr($get_ip, '-')) {
                    // Обрабатываем диапазон адресов
                    $array = explode('-', $get_ip);
                    $ip1 = trim($array[0]);
                    if (!Validate::ip($ip1)) {
                        $error[] = __('add_ip_firstaddress_error');
                    }
                    $ip2 = trim($array[1]);
                    if (!Validate::ip($ip2)) {
                        $error[] = __('add_ip_secondaddress_error');
                    }
                } elseif (strstr($get_ip, '*')) {
                    // Обрабатываем адреса с маской
                    $ipt1 = array();
                    $ipt2 = array();
                    $array = explode('.', $get_ip);
                    for ($i = 0; $i < 4; $i++) {
                        if (!isset($array[$i]) || $array[$i] == '*') {
                            $ipt1[$i] = '0';
                            $ipt2[$i] = '255';
                        } elseif (is_numeric($array[$i]) && $array[$i] >= 0 && $array[$i] <= 255) {
                            $ipt1[$i] = $array[$i];
                            $ipt2[$i] = $array[$i];
                        } else {
                            $error = __('add_ip_address_error');
                        }
                        $ip1 = $ipt1[0] . '.' . $ipt1[1] . '.' . $ipt1[2] . '.' . $ipt1[3];
                        $ip2 = $ipt2[0] . '.' . $ipt2[1] . '.' . $ipt2[2] . '.' . $ipt2[3];
                    }
                } else {
                    // Обрабатываем одиночный адрес
                    if (!Validate::ip($get_ip)) {
                        $error = __('add_ip_address_error');
                    } else {
                        $ip1 = $get_ip;
                        $ip2 = $get_ip;
                    }
                }
                $ip1 = sprintf("%u", ip2long($ip1));
                $ip2 = sprintf("%u", ip2long($ip2));
                if ($ip1 > $ip2) {
                    $tmp = $ip2;
                    $ip2 = $ip1;
                    $ip1 = $tmp;
                }
            }

            if (!$error) {
                // Проверка на конфликты адресов
                $req = mysql_query("SELECT * FROM `cms_ip_bwlist` WHERE ('$ip1' BETWEEN `ip` AND `ip_upto`) OR ('$ip2' BETWEEN `ip` AND `ip_upto`) OR (`ip` > '$ip1' AND `ip_upto` < '$ip2')");
                $total = mysql_num_rows($req);
                if ($total) {
                    echo Functions::displayError(__('add_ip_address_conflict'));
                    for ($i = 0; ($res = mysql_fetch_array($req)) !== FALSE; ++$i) {
                        echo($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                            ($get_ip = $res['ip'] == $res['ip_upto'] ? long2ip($res['ip']) : long2ip($res['ip']) . ' - ' . long2ip($res['ip_upto'])) .
                            '</div>';
                    }
                    echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
                    echo '<p><a href="' . Router::getUrl(3) . '">' . __('back') . '</a><br /><a href="' . Router::getUrl(2) . '">' . __('admin_panel') . '</a></p>';
                    exit;
                }

                // Проверяем, не попадает ли IP администратора в диапазон
                if (Vars::$MOD == 'black' && (Vars::$IP >= $ip1 && Vars::$IP <= $ip2) || Vars::$IP_VIA_PROXY && (Vars::$IP_VIA_PROXY >= $ip1 && Vars::$IP_VIA_PROXY <= $ip2)) {
                    $error = __('add_ip_myaddress_conflict');
                }
            }

            if (empty($error)) {
                if (isset($_POST['confirm'])) {
                    // Добавляем IP в базу данных
                    mysql_query("INSERT INTO `cms_ip_bwlist` SET
                        `ip` = $ip1,
                        `ip_upto` = $ip2,
                        `mode` = '" . $mod . "',
                        `timestamp` = " . time() . ",
                        `user_id` = " . Vars::$USER_ID . ",
                        `description` = '" . mysql_real_escape_string(base64_decode($description)) . "'
                    ") or exit(mysql_error());
                    update_cache();
                    header('Location: ' . Router::getUrl(3) . '?mod=' . $mod);
                    exit;
                } else {
                    // Выводим окно подтверждения
                    echo'<form action="' . Router::getUrl(3) . '?act=add" method="post"><div class="menu">' .
                        '<input type="hidden" value="' . $mod . '" name="mod" />' .
                        '<input type="hidden" value="' . long2ip($ip1) . ($ip1 == $ip2 ? '' : '-' . long2ip($ip2)) . '" name="ip" />' .
                        '<input type="hidden" value="' . base64_encode($description) . '" name="description" />' .
                        '<p><h3>' . __('ip_address') . ': ' .
                        '<span class="' . $color . '">' . long2ip($ip1) . ($ip1 == $ip2 ? '' : '&#160;-&#160;' . long2ip($ip2)) . '</span></h3>' .
                        ($mod == 'black' ? __('add_ip_confirmation_black') : __('add_ip_confirmation_white')) .
                        '</p><p><input type="submit" name="confirm" value="' . __('save') . '"/></p>' .
                        '</div></form>';
                }
            }

            // Показываем ошибки, если есть
            if (!empty($error)) {
                echo Functions::displayError($error, '<a href="' . Router::getUrl(3) . '?act=add' . (Vars::$MOD == 'black' ? '&amp;mod=black' : '') . '">' . __('back') . '</a>');
            }
        } else {
            /*
            -----------------------------------------------------------------
            Форма ввода IP адреса для Бана
            -----------------------------------------------------------------
            */
            echo'<form action="' . Router::getUrl(3) . '?act=add" method="post">' .
                '<div class="menu"><p><h3>' . __('ip_address') . ':</h3>' .
                '<input type="hidden" value="' . htmlspecialchars(Vars::$MOD) . '" name="mod" />' .
                '<input type="text" name="ip"/></p>' .
                '<p><h3>' . __('description') . '</h3>' .
                '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="description"></textarea>' .
                '<br /><small>&nbsp;' . __('not_mandatory_field') . '</small></p>' .
                '<p><input type="submit" name="submit" value="' . __('add') . '"/></p></div>' .
                '</form>';
        }
    }

    public function delete()
    {
        $del = isset($_POST['del']) && is_array($_POST['del']) ? $_POST['del'] : array();
        echo'<div class="phdr"><a href="' . Router::getUrl(3) . '?mod=' . $mod . '"><b>' . __('ip_accesslist') . '</b></a> | ' . __('delete_ip') . '</div>' .
            ($mod == 'black'
                ? '<div class="rmenu"><p><h3>' . __('black_list') . '</h3></p></div>'
                : '<div class="gmenu"><p><h3>' . __('white_list') . '</h3></p></div>'
            );
        if (!empty($del)) {
            if (isset($_POST['submit'])) {
                foreach ($del as $val) {
                    if (is_numeric($val)) {
                        mysql_query("DELETE FROM `cms_ip_bwlist` WHERE `ip` = " . $val);
                    }
                }
                mysql_query("OPTIMIZE TABLE `cms_ip_bwlist`");
                update_cache();
                header('Location: ' . Router::getUrl(3) . '?mod=' . $mod);
            } else {
                echo'<form action="' . Router::getUrl(3) . '?act=del&amp;mod=' . $mod . '" method="post">';
                foreach ($del as $val) {
                    echo'<input type="hidden" value="' . $val . '" name="del[]" />';
                }
                echo'<div class="rmenu"><p>' . __('delete_ip_warning') . '</p>' .
                    '<p><input type="submit" name="submit" value="' . __('delete') . ' "/></p>' .
                    '</div></form>';
            }
        } else {
            echo Functions::displayError(__('error_not_selected'));
        }
        echo '<div class="phdr"><a href="' . $ref . '">' . __('back') . '</a></div>';
    }

    public function clear()
    {
        echo'<div class="phdr"><a href="' . Router::getUrl(3) . '?mod=' . $mod . '"><b>' . __('ip_accesslist') . '</b></a> | ' . __('clear_list') . '</div>' .
            ($mod == 'black'
                ? '<div class="rmenu"><p><h3>' . __('black_list') . '</h3></p></div>'
                : '<div class="gmenu"><p><h3>' . __('white_list') . '</h3></p></div>'
            );
        if (isset($_POST['submit'])) {
            mysql_query("DELETE FROM `cms_ip_bwlist` WHERE `mode` = '" . $mod . "'");
            mysql_query("OPTIMIZE TABLE `cms_ip_bwlist`");
            update_cache();
            header('Location: ' . Router::getUrl(3) . '?mod=' . $mod);
        } else {
            echo'<form action="' . Router::getUrl(3) . '?act=clear&amp;mod=' . $mod . '" method="post">' .
                '<div class="rmenu"><p>' . __('clear_list_warning') . '</p>' .
                '<p><input type="submit" name="submit" value="' . __('clear') . ' "/></p>' .
                '</div></form>';
        }
        echo '<div class="phdr"><a href="' . $ref . '">' . __('back') . '</a></div>';
    }
}
