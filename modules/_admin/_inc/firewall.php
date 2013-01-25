<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_ADMIN') or die('Error: restricted access');
$uri = Router::getUri(3);

$mod = Vars::$MOD == 'white' ? 'white' : 'black';
$color = Vars::$MOD == 'white' ? 'green' : 'red';
$ref = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : $uri;

function update_cache()
{
    $file = CACHEPATH . 'ip_list.dat';
    $req = DB::PDO()->query("SELECT * FROM `cms_ip_bwlist`");
    if ($req->rowCount()) {
        $in = fopen($file, "w+");
        flock($in, LOCK_EX);
        ftruncate($in, 0);
        foreach ($req as $res) {
            $mode = $res['mode'] == 'white' ? 2 : 1;
            fwrite($in, pack('ddS', $res['ip'], $res['ip_upto'], $mode));
        }
        fclose($in);
    } else {
        unlink($file);
    }
}

switch (Vars::$ACT) {
    case 'add':
        /*
        -----------------------------------------------------------------
        Добавление IP в список
        -----------------------------------------------------------------
        */
        echo'<div class="phdr"><a href="' . $uri . '?mod=' . $mod . '"><b>' . __('ip_accesslist') . '</b></a> | ' . __('add_ip') . '</div>' .
            ($mod == 'black'
                ? '<div class="rmenu"><p><h3>' . __('black_list') . '</h3></p></div>'
                : '<div class="gmenu"><p><h3>' . __('white_list') . '</h3></p></div>'
            );
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
                $req = DB::PDO()->query("SELECT * FROM `cms_ip_bwlist` WHERE ('$ip1' BETWEEN `ip` AND `ip_upto`) OR ('$ip2' BETWEEN `ip` AND `ip_upto`) OR (`ip` > '$ip1' AND `ip_upto` < '$ip2')");
                $total = $req->rowCount();
                if ($total) {
                    echo Functions::displayError(__('add_ip_address_conflict'));
                    for ($i = 0; $res = $req->fetch(); ++$i) {
                        echo($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                            ($get_ip = $res['ip'] == $res['ip_upto'] ? long2ip($res['ip']) : long2ip($res['ip']) . ' - ' . long2ip($res['ip_upto'])) .
                            '</div>';
                    }
                    echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
                    echo '<p><a href="' . $uri . '">' . __('back') . '</a><br /><a href="' . Router::getUri(2) . '">' . __('admin_panel') . '</a></p>';
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
                    DB::PDO()->exec("INSERT INTO `cms_ip_bwlist` SET
                        `ip` = $ip1,
                        `ip_upto` = $ip2,
                        `mode` = '" . $mod . "',
                        `timestamp` = " . time() . ",
                        `user_id` = " . Vars::$USER_ID . ",
                        `description` = " . DB::PDO()->quote(base64_decode($description))
                    );
                    update_cache();
                    header('Location: ' . $uri . '?mod=' . $mod);
                    exit;
                } else {
                    // Выводим окно подтверждения
                    echo'<form action="' . $uri . '?act=add" method="post"><div class="menu">' .
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
                echo Functions::displayError($error, '<a href="' . $uri . '?act=add' . (Vars::$MOD == 'black' ? '&amp;mod=black' : '') . '">' . __('back') . '</a>');
            }
        } else {
            /*
            -----------------------------------------------------------------
            Форма ввода IP адреса для Бана
            -----------------------------------------------------------------
            */
            echo'<form action="' . $uri . '?act=add" method="post">' .
                '<div class="menu"><p><h3>' . __('ip_address') . ':</h3>' .
                '<input type="hidden" value="' . htmlspecialchars(Vars::$MOD) . '" name="mod" />' .
                '<input type="text" name="ip"/></p>' .
                '<p><h3>' . __('description') . '</h3>' .
                '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="description"></textarea>' .
                '<br /><small>&nbsp;' . __('not_mandatory_field') . '</small></p>' .
                '<p><input type="submit" name="submit" value="' . __('add') . '"/></p></div>' .
                '</form>';
        }
        // Нижний блок с подсказками
        echo'<div class="phdr"><a href="' . $uri . (Vars::$MOD == 'black' ? '' : '?mod=white') . '">' . __('back') . '</a></div>' .
            '<div class="topmenu"><p>' .
            (Vars::$MOD == 'black'
                ? '<strong>' . mb_strtoupper(__('black_list')) . ':</strong> ' . __('black_list_help')
                : '<strong>' . mb_strtoupper(__('white_list')) . ':</strong> ' . __('white_list_help')
            ) .
            '</p>' . (isset($_POST['submit']) ? '' : '<p>' . __('add_ip_help') . '</p>') .
            '</div>' .
            '<p><a href="' . Router::getUri(2) . '">' . __('admin_panel') . '</a></p>';
        break;

    case 'clear':
        /*
        -----------------------------------------------------------------
        Очищаем все адреса выбранного списка
        -----------------------------------------------------------------
        */
        echo'<div class="phdr"><a href="' . $uri . '?mod=' . $mod . '"><b>' . __('ip_accesslist') . '</b></a> | ' . __('clear_list') . '</div>' .
            ($mod == 'black'
                ? '<div class="rmenu"><p><h3>' . __('black_list') . '</h3></p></div>'
                : '<div class="gmenu"><p><h3>' . __('white_list') . '</h3></p></div>'
            );
        if (isset($_POST['submit'])) {
            DB::PDO()->exec("DELETE FROM `cms_ip_bwlist` WHERE `mode` = '" . $mod . "'");
            DB::PDO()->query("OPTIMIZE TABLE `cms_ip_bwlist`");
            update_cache();
            header('Location: ' . $uri . '?mod=' . $mod);
        } else {
            echo'<form action="' . $uri . '?act=clear&amp;mod=' . $mod . '" method="post">' .
                '<div class="rmenu"><p>' . __('clear_list_warning') . '</p>' .
                '<p><input type="submit" name="submit" value="' . __('clear') . ' "/></p>' .
                '</div></form>';
        }
        echo '<div class="phdr"><a href="' . $ref . '">' . __('back') . '</a></div>';
        break;

    case 'del':
        /*
        -----------------------------------------------------------------
        Удаляем выбранные адреса IP
        -----------------------------------------------------------------
        */
        $del = isset($_POST['del']) && is_array($_POST['del']) ? $_POST['del'] : array();
        echo'<div class="phdr"><a href="' . $uri . '?mod=' . $mod . '"><b>' . __('ip_accesslist') . '</b></a> | ' . __('delete_ip') . '</div>' .
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
                DB::PDO()->query("OPTIMIZE TABLE `cms_ip_bwlist`");
                update_cache();
                header('Location: ' . $uri . '?mod=' . $mod);
            } else {
                echo'<form action="' . $uri . '?act=del&amp;mod=' . $mod . '" method="post">';
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
        break;

    default:
        /*
        -----------------------------------------------------------------
        Главное меню модуля
        -----------------------------------------------------------------
        */
        $menu = array(
            ($mod != 'white' ? '<strong>' . __('black_list') . '</strong>' : '<a href="' . $uri . '">' . __('black_list') . '</a>'),
            ($mod == 'white' ? '<strong>' . __('white_list') . '</strong>' : '<a href="' . $uri . '?mod=white">' . __('white_list') . '</a>')
        );
        echo'<div class="phdr"><a href="' . Router::getUri(2) . '"><b>' . __('admin_panel') . '</b></a> | ' . __('firewall') . '</div>' .
            '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';

        $total = DB::PDO()->query("SELECT COUNT(*) FROM `cms_ip_bwlist` WHERE `mode` = '" . $mod . "'")->fetchColumn();
        Vars::fixPage($total);

        if ($total > Vars::$USER_SET['page_size']) {
            echo'<div class="topmenu">' . Functions::displayPagination($uri . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        }

        // Обновляем кэш IP адресов
        if(isset($_GET['update_cache'])){
            update_cache();
            echo'<div class="gmenu">' . __('cache_updated') . '</div>';
        }

        // Выводим список IP
        echo'<form action="' . $uri . '?act=add&amp;mod=' . $mod . '" method="post">' .
            '<div class="' . ($mod == 'white' ? 'gmenu' : 'rmenu') . '"><input type="submit" name="delete" value="' . __('add') . '"/></div></form>';
        if ($total) {
            echo '<form action="' . $uri . '?act=del&amp;mod=' . $mod . '" method="post">';
            $req = DB::PDO()->query("SELECT `cms_ip_bwlist`.*, `users`.`nickname`
                FROM `cms_ip_bwlist` LEFT JOIN `users` ON `cms_ip_bwlist`.`user_id` = `users`.`id`
                WHERE `cms_ip_bwlist`.`mode` = '" . $mod . "'
                ORDER BY `cms_ip_bwlist`.`timestamp` DESC
                " . Vars::db_pagination()
            );
            for ($i = 0; $res = $req->fetch(); ++$i) {
                echo($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                    '<input type="checkbox" name="del[]" value="' . $res['ip'] . '"/>&#160;' .
                    '<strong>IP: <span class="' . $color . '">' . long2ip($res['ip']) . ($res['ip'] != $res['ip_upto'] ? ' - ' . long2ip($res['ip_upto']) : '') . '</span></strong>' .
                    (empty($res['description']) ? '' : '<div class="sub">' . Validate::checkout($res['description'], 1) . '</div>') .
                    '<div class="sub"><span class="gray">' .
                    __('date') . ':&#160;' . Functions::displayDate($res['timestamp']) .
                    '<br />' . __('who_added') . ':&#160;' . $res['nickname'] .
                    '</span></div></div>';
            }
            echo '<div class="rmenu"><input type="submit" name="delete" value="' . __('delete') . ' "/></div></form>';
        } else {
            echo'<div class="menu"><p>' . __('list_empty') . '</p></div>';
        }

        // Нижний блок с подсказками
        echo'<div class="phdr">' . __('total') . ': ' . $total . '</div>' .
            '<div class="topmenu"><small><p>' .
            ($mod == 'white'
                ? '<strong>' . mb_strtoupper(__('white_list')) . ':</strong> ' . __('white_list_help')
                : '<strong>' . mb_strtoupper(__('black_list')) . ':</strong> ' . __('black_list_help')
            ) . '</p></small></div>';

        // Постраничная навигация
        if ($total > Vars::$USER_SET['page_size']) {
            echo'<div class="topmenu">' . Functions::displayPagination($uri . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                '<p><form action="' . $uri . '" method="post">' .
                '<input type="text" name="page" size="2"/>' .
                '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
                '</form></p>';
        }

        // Ссылки внизу
        echo'<p>' . ($total ? '<a href="' . $uri . '?act=clear&amp;mod=' . $mod . '">' . __('clear_list') . '</a><br />' : '') .
            '<a href="' . $uri . '?mod=' . $mod . '&amp;update_cache">' . __('update_cache') . '</a><br/>' .
            '<a href="' . Router::getUri(2) . '">' . __('admin_panel') . '</a></p>';
}