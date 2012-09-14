<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

// Проверяем права доступа
if (Vars::$USER_RIGHTS != 9) {
    echo Functions::displayError(lng('access_forbidden'));
    exit;
}

$mod = Vars::$MOD == 'white' ? 'white' : 'black';
$color = Vars::$MOD == 'white' ? 'green' : 'red';
$ref = htmlspecialchars($_SERVER['HTTP_REFERER']);

function update_cache()
{
    $file = CACHEPATH . 'ip_list.dat';
    $req = mysql_query("SELECT * FROM `cms_ip_bwlist`");
    if (mysql_num_rows($req)) {
        $in = fopen($file, "w+");
        flock($in, LOCK_EX);
        ftruncate($in, 0);
        while ($res = mysql_fetch_assoc($req)) {
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
        echo'<div class="phdr"><a href="' . Vars::$URI . '?mod=' . $mod . '"><b>' . lng('ip_accesslist') . '</b></a> | ' . lng('add_ip') . '</div>' .
            ($mod == 'black'
                ? '<div class="rmenu"><p><h3>' . lng('black_list') . '</h3></p></div>'
                : '<div class="gmenu"><p><h3>' . lng('white_list') . '</h3></p></div>'
            );
        if (isset($_POST['submit']) || isset($_POST['confirm'])) {
            $error = array();
            $ip1 = 0;
            $ip2 = 0;
            $get_ip = isset($_POST['ip']) ? trim($_POST['ip']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';

            // Если адрес не введен, выдаем ошибку
            if (empty($get_ip)) {
                $error[] = lng('add_ip_address_empty');
            }

            if (empty($error)) {
                if (strstr($get_ip, '-')) {
                    // Обрабатываем диапазон адресов
                    $array = explode('-', $get_ip);
                    $ip1 = trim($array[0]);
                    if (!Validate::ip($ip1)) {
                        $error[] = lng('add_ip_firstaddress_error');
                    }
                    $ip2 = trim($array[1]);
                    if (!Validate::ip($ip2)) {
                        $error[] = lng('add_ip_secondaddress_error');
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
                            $error = lng('add_ip_address_error');
                        }
                        $ip1 = $ipt1[0] . '.' . $ipt1[1] . '.' . $ipt1[2] . '.' . $ipt1[3];
                        $ip2 = $ipt2[0] . '.' . $ipt2[1] . '.' . $ipt2[2] . '.' . $ipt2[3];
                    }
                } else {
                    // Обрабатываем одиночный адрес
                    if (!Validate::ip($get_ip)) {
                        $error = lng('add_ip_address_error');
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
                    echo Functions::displayError(lng('add_ip_address_conflict'));
                    for ($i = 0; ($res = mysql_fetch_array($req)) !== false; ++$i) {
                        echo($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                            ($get_ip = $res['ip'] == $res['ip_upto'] ? long2ip($res['ip']) : long2ip($res['ip']) . ' - ' . long2ip($res['ip_upto'])) .
                            '</div>';
                    }
                    echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
                    echo '<p><a href="' . Vars::$URI . '">' . lng('back') . '</a><br /><a href="' . Vars::$MODULE_URI . '">' . lng('admin_panel') . '</a></p>';
                    exit;
                }

                // Проверяем, не попадает ли IP администратора в диапазон
                if (Vars::$MOD == 'black' && (Vars::$IP >= $ip1 && Vars::$IP <= $ip2) || Vars::$IP_VIA_PROXY && (Vars::$IP_VIA_PROXY >= $ip1 && Vars::$IP_VIA_PROXY <= $ip2)) {
                    $error = lng('add_ip_myaddress_conflict');
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
                    header('Location: ' . Vars::$URI . '?mod=' . $mod);
                    exit;
                } else {
                    // Выводим окно подтверждения
                    echo'<form action="' . Vars::$URI . '?act=add" method="post"><div class="menu">' .
                        '<input type="hidden" value="' . $mod . '" name="mod" />' .
                        '<input type="hidden" value="' . long2ip($ip1) . ($ip1 == $ip2 ? '' : '-' . long2ip($ip2)) . '" name="ip" />' .
                        '<input type="hidden" value="' . base64_encode($description) . '" name="description" />' .
                        '<p><h3>' . lng('ip_address') . ': ' .
                        '<span class="' . $color . '">' . long2ip($ip1) . ($ip1 == $ip2 ? '' : '&#160;-&#160;' . long2ip($ip2)) . '</span></h3>' .
                        ($mod == 'black' ? lng('add_ip_confirmation_black') : lng('add_ip_confirmation_white')) .
                        '</p><p><input type="submit" name="confirm" value="' . lng('save') . '"/></p>' .
                        '</div></form>';
                }
            }

            // Показываем ошибки, если есть
            if (!empty($error)) {
                echo Functions::displayError($error, '<a href="' . Vars::$URI . '?act=add' . (Vars::$MOD == 'black' ? '&amp;mod=black' : '') . '">' . lng('back') . '</a>');
            }
        } else {
            /*
            -----------------------------------------------------------------
            Форма ввода IP адреса для Бана
            -----------------------------------------------------------------
            */
            echo'<form action="' . Vars::$URI . '?act=add" method="post">' .
                '<div class="menu"><p><h3>' . lng('ip_address') . ':</h3>' .
                '<input type="hidden" value="' . htmlspecialchars(Vars::$MOD) . '" name="mod" />' .
                '<input type="text" name="ip"/></p>' .
                '<p><h3>' . lng('description') . '</h3>' .
                '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="description"></textarea>' .
                '<br /><small>&nbsp;' . lng('not_mandatory_field') . '</small></p>' .
                '<p><input type="submit" name="submit" value="' . lng('add') . '"/></p></div>' .
                '</form>';
        }
        // Нижний блок с подсказками
        echo'<div class="phdr"><a href="' . Vars::$URI . (Vars::$MOD == 'black' ? '' : '?mod=white') . '">' . lng('back') . '</a></div>' .
            '<div class="topmenu"><p>' .
            (Vars::$MOD == 'black'
                ? '<strong>' . mb_strtoupper(lng('black_list')) . ':</strong> ' . lng('black_list_help')
                : '<strong>' . mb_strtoupper(lng('white_list')) . ':</strong> ' . lng('white_list_help')
            ) .
            '</p>' . (isset($_POST['submit']) ? '' : '<p>' . lng('add_ip_help') . '</p>') .
            '</div>' .
            '<p><a href="' . Vars::$MODULE_URI . '">' . lng('admin_panel') . '</a></p>';
        break;

    case 'clear':
        /*
        -----------------------------------------------------------------
        Очищаем все адреса выбранного списка
        -----------------------------------------------------------------
        */
        echo'<div class="phdr"><a href="' . Vars::$URI . '?mod=' . $mod . '"><b>' . lng('ip_accesslist') . '</b></a> | ' . lng('clear_list') . '</div>' .
            ($mod == 'black'
                ? '<div class="rmenu"><p><h3>' . lng('black_list') . '</h3></p></div>'
                : '<div class="gmenu"><p><h3>' . lng('white_list') . '</h3></p></div>'
            );
        if (isset($_POST['submit'])) {
            mysql_query("DELETE FROM `cms_ip_bwlist` WHERE `mode` = '" . $mod . "'");
            mysql_query("OPTIMIZE TABLE `cms_ip_bwlist`");
            update_cache();
            header('Location: ' . Vars::$URI . '?mod=' . $mod);
        } else {
            echo'<form action="' . Vars::$URI . '?act=clear&amp;mod=' . $mod . '" method="post">' .
                '<div class="rmenu"><p>' . lng('clear_list_warning') . '</p>' .
                '<p><input type="submit" name="submit" value="' . lng('clear') . ' "/></p>' .
                '</div></form>';
        }
        echo '<div class="phdr"><a href="' . $ref . '">' . lng('back') . '</a></div>';
        break;

    case 'del':
        /*
        -----------------------------------------------------------------
        Удаляем выбранные адреса IP
        -----------------------------------------------------------------
        */
        $del = isset($_POST['del']) && is_array($_POST['del']) ? $_POST['del'] : array();
        echo'<div class="phdr"><a href="' . Vars::$URI . '?mod=' . $mod . '"><b>' . lng('ip_accesslist') . '</b></a> | ' . lng('delete_ip') . '</div>' .
            ($mod == 'black'
                ? '<div class="rmenu"><p><h3>' . lng('black_list') . '</h3></p></div>'
                : '<div class="gmenu"><p><h3>' . lng('white_list') . '</h3></p></div>'
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
                header('Location: ' . Vars::$URI . '?mod=' . $mod);
            } else {
                echo'<form action="' . Vars::$URI . '?act=del&amp;mod=' . $mod . '" method="post">';
                foreach ($del as $val) {
                    echo'<input type="hidden" value="' . $val . '" name="del[]" />';
                }
                echo'<div class="rmenu"><p>' . lng('delete_ip_warning') . '</p>' .
                    '<p><input type="submit" name="submit" value="' . lng('delete') . ' "/></p>' .
                    '</div></form>';
            }
        } else {
            echo Functions::displayError(lng('error_not_selected'));
        }
        echo '<div class="phdr"><a href="' . $ref . '">' . lng('back') . '</a></div>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Главное меню модуля
        -----------------------------------------------------------------
        */
        $menu = array(
            ($mod != 'white' ? '<strong>' . lng('black_list') . '</strong>' : '<a href="' . Vars::$URI . '">' . lng('black_list') . '</a>'),
            ($mod == 'white' ? '<strong>' . lng('white_list') . '</strong>' : '<a href="' . Vars::$URI . '?mod=white">' . lng('white_list') . '</a>')
        );
        echo'<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . lng('admin_panel') . '</b></a> | ' . lng('ip_accesslist') . '</div>' .
            '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>';

        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ip_bwlist` WHERE `mode` = '" . $mod . "'"), 0);
        Vars::fixPage($total);

        if ($total > Vars::$USER_SET['page_size']) {
            echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        }

        // Обновляем кэш IP адресов
        if(isset($_GET['update_cache'])){
            update_cache();
            echo'<div class="gmenu">' . lng('cache_updated') . '</div>';
        }

        // Выводим список IP
        echo'<form action="' . Vars::$URI . '?act=add&amp;mod=' . $mod . '" method="post">' .
            '<div class="' . ($mod == 'white' ? 'gmenu' : 'rmenu') . '"><input type="submit" name="delete" value="' . lng('add') . '"/></div></form>';
        if ($total) {
            echo '<form action="' . Vars::$URI . '?act=del&amp;mod=' . $mod . '" method="post">';
            $req = mysql_query("SELECT `cms_ip_bwlist`.*, `users`.`nickname`
                FROM `cms_ip_bwlist` LEFT JOIN `users` ON `cms_ip_bwlist`.`user_id` = `users`.`id`
                WHERE `cms_ip_bwlist`.`mode` = '" . $mod . "'
                ORDER BY `cms_ip_bwlist`.`timestamp` DESC
                " . Vars::db_pagination()
            );
            for ($i = 0; ($res = mysql_fetch_assoc($req)) !== false; ++$i) {
                echo($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                    '<input type="checkbox" name="del[]" value="' . $res['ip'] . '"/>&#160;' .
                    '<strong>IP: <span class="' . $color . '">' . long2ip($res['ip']) . ($res['ip'] != $res['ip_upto'] ? ' - ' . long2ip($res['ip_upto']) : '') . '</span></strong>' .
                    (empty($res['description']) ? '' : '<div class="sub">' . Validate::filterString($res['description'], 1) . '</div>') .
                    '<div class="sub"><span class="gray">' .
                    lng('date') . ':&#160;' . Functions::displayDate($res['timestamp']) .
                    '<br />' . lng('who_added') . ':&#160;' . $res['nickname'] .
                    '</span></div></div>';
            }
            echo '<div class="rmenu"><input type="submit" name="delete" value="' . lng('delete') . ' "/></div></form>';
        } else {
            echo'<div class="menu"><p>' . lng('list_empty') . '</p></div>';
        }

        // Нижний блок с подсказками
        echo'<div class="phdr">' . lng('total') . ': ' . $total . '</div>' .
            '<div class="topmenu"><small><p>' .
            ($mod == 'white'
                ? '<strong>' . mb_strtoupper(lng('white_list')) . ':</strong> ' . lng('white_list_help')
                : '<strong>' . mb_strtoupper(lng('black_list')) . ':</strong> ' . lng('black_list_help')
            ) . '</p></small></div>';

        // Постраничная навигация
        if ($total > Vars::$USER_SET['page_size']) {
            echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                '<p><form action="' . Vars::$URI . '" method="post">' .
                '<input type="text" name="page" size="2"/>' .
                '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/>' .
                '</form></p>';
        }

        // Ссылки внизу
        echo'<p>' . ($total ? '<a href="' . Vars::$URI . '?act=clear&amp;mod=' . $mod . '">' . lng('clear_list') . '</a><br />' : '') .
            '<a href="' . Vars::$URI . '?mod=' . $mod . '&amp;update_cache">' . lng('update_cache') . '</a><br/>' .
            '<a href="' . Vars::$MODULE_URI . '">' . lng('admin_panel') . '</a></p>';
}