<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

$rootpath = '';
require('incfiles/core.php');
$lng_reg = core::load_lng('registration');
$textl = $lng['registration'];
require('incfiles/head.php');
echo '<div class="phdr"><b>' . $lng['registration'] . '</b></div>';

$reg_data['login'] = isset($_POST['login']) ? trim($_POST['login']) : '';
$reg_data['password'] = isset($_POST['password']) ? trim($_POST['password']) : '';
$reg_data['password_confirm'] = isset($_POST['password_confirm']) ? trim($_POST['password_confirm']) : '';
$reg_data['captcha'] = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
$reg_data['email'] = isset($_POST['email']) ? trim($_POST['email']) : '';
$reg_data['about'] = isset($_POST['about']) ? trim($_POST['about']) : '';
$reg_data['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
$reg_data['sex'] = isset($_POST['sex']) ? intval($_POST['sex']) : 0;

switch (login::registration($reg_data)) {
    case 'login':
        /*
        -----------------------------------------------------------------
        Форма регистрации новых пользователей
        -----------------------------------------------------------------
        */
        $error_style = 'style="background-color: #FFCCCC"';

        // Показываем ошибки (если есть)
        if (!empty(login::$error)) echo'<div class="rmenu"><p>' . core::$lng['errors_occurred'] . '</p></div>';

        echo'<form action="registration.php" method="post">' .
            '<div class="gmenu">' .

            // Логин
            '<p><h3>' . $lng_reg['login'] . '</h3>' .
            (isset(login::$error['login']) ? '<small class="red"><b>' . core::$lng['error'] . '</b>: ' . login::$error['login'] . '<br /></small>' : '') .
            (isset($_POST['check_login']) && empty(login::$error) ? '<small>' . $lng_reg['nick_available'] . '<br /></small>' : '') .
            '<input type="text" name="login" maxlength="20" value="' . htmlspecialchars($reg_data['login']) . '" ' . (isset(login::$error['login']) ? $error_style : '') . '/>' .
            '<input type="submit" name="check_login" value="?"/></p>' .

            // Пароль
            '<p><h3>' . $lng_reg['password'] . '</h3>' .
            (isset(login::$error['password']) ? '<small class="red"><b>' . core::$lng['error'] . '</b>: ' . login::$error['password'] . '<br /></small>' : '') .
            '<input type="password" name="password" maxlength="20" value="' . htmlspecialchars($reg_data['password']) . '" ' . (isset(login::$error['password']) ? $error_style : '') . '/><br />' .
            '<small>' . $lng_reg['repeat_password'] . '</small><br />' .
            (isset(login::$error['password_confirm']) ? '<small class="red"><b>' . core::$lng['error'] . '</b>: ' . login::$error['password_confirm'] . '<br /></small>' : '') .
            '<input type="password" name="password_confirm" maxlength="20" value="' . htmlspecialchars($reg_data['password_confirm']) . '" ' . (isset(login::$error['password_confirm']) ? $error_style : '') . '/></p>' .

            // E-mail
            '<p><h3>E-mail</h3>' .
            (isset(login::$error['email']) ? '<small class="red"><b>' . core::$lng['error'] . '</b>: ' . login::$error['email'] . '<br /></small>' : '') .
            '<input type="text" name="email" maxlength="50" value="' . htmlspecialchars($reg_data['email']) . '" ' . (isset(login::$error['email']) ? $error_style : '') . '/></p>' .

            // Пол
            '<p><h3>' . $lng_reg['sex'] . '</h3>' .
            (isset(login::$error['sex']) ? '<small class="red"><b>' . core::$lng['error'] . '</b>: ' . login::$error['sex'] . '<br /></small>' : '') .
            '<input type="radio" value="1" name="sex" ' . ($reg_data['sex'] == 1 ? 'checked="checked"' : '') . '/>&#160;' .
            functions::get_image('usr_m.png', '', 'align="middle"') . '&#160;' . $lng_reg['sex_m'] . '<br />' .
            '<input type="radio" value="2" name="sex" ' . ($reg_data['sex'] == 2 ? 'checked="checked"' : '') . '/>&#160;' .
            functions::get_image('usr_w.png', '', 'align="middle"') . '&#160;' . $lng_reg['sex_w'] . '</p>' .

            // CAPTCHA
            '<p><h3>' . core::$lng['captcha'] . '</h3>' .
            '<img src="captcha.php?r=' . rand(1000, 9999) . '" alt="' . $lng_reg['captcha_help'] . '" border="2"/><br />' .
            (isset(login::$error['captcha']) ? '<small class="red"><b>' . core::$lng['error'] . '</b>: ' . login::$error['captcha'] . '<br /></small>' : '') .
            '<input type="text" size="5" maxlength="5"  name="captcha" ' . (isset(login::$error['captcha']) ? $error_style : '') . '/></p>' .

            // Кнопка регистрации
            '<p><input type="submit" name="submit" value="' . $lng_reg['registration'] . '"/></p>' .
            '</div></form>';

        // Справка по заполнению полей
        echo'<div class="phdr"><small>' .
            '<p><b>' . $lng_reg['mandatory_fields'] . '</b></p>' .
            '<p><b>' . mb_strtoupper($lng_reg['login']) . '</b>: ' . $lng_reg['login_help'] . '</p>' .
            '<p><b>' . mb_strtoupper($lng_reg['password']) . '</b>: ' . $lng_reg['password_help'] . '</p>' .
            (!empty($lng_reg['registration_terms']) ? '<p>' . $lng_reg['registration_terms'] . '</p>' : '') .
            '</small></div>';

        // Предупреждение о включенной модерации
        if ($set['mod_reg'] == 1) echo'<div class="topmenu"><small class="red"><p>' . $lng_reg['moderation_warning'] . '</p></small></div>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Если регистрация закрыта
        -----------------------------------------------------------------
        */
        echo'<div class="topmenu"><p class="red">' .
            (core::$user_id ? $lng_reg['already_registered'] : $lng_reg['registration_closed']) .
            '</p></div>';
}

require('incfiles/end.php');






//    if (empty($error)) {
//        $preg = $set['mod_reg'] > 1 ? 1 : 0;
//        mysql_query("INSERT INTO `users` SET
//            `name` = '" . mysql_real_escape_string($reg_nick) . "',
//            `name_lat` = '" . mysql_real_escape_string($lat_nick) . "',
//            `password` = '" . mysql_real_escape_string($pass) . "',
//            `imname` = '$reg_name',
//            `about` = '$reg_about',
//            `sex` = '$reg_sex',
//            `rights` = '0',
//            `ip` = '" . core::$ip . "',
//            `ip_via_proxy` = '" . core::$ip_via_proxy . "',
//            `browser` = '" . mysql_real_escape_string($agn) . "',
//            `datereg` = '" . time() . "',
//            `lastdate` = '" . time() . "',
//            `sestime` = '" . time() . "',
//            `preg` = '$preg'
//        ");
//        $usid = mysql_insert_id();
//        echo '<div class="menu"><p><h3>' . $lng_reg['you_registered'] . '</h3>' . $lng_reg['your_id'] . ': <b>' . $usid . '</b><br/>' . $lng_reg['your_login'] . ': <b>' . $reg_nick . '</b><br/>' . $lng_reg['your_password'] . ': <b>' . $reg_pass . '</b></p>' .
//            '<p><h3>' . $lng_reg['your_link'] . '</h3><input type="text" value="' . $set['homeurl'] . '/login.php?id=' . $usid . '&amp;p=' . $reg_pass . '" /><br/>';
//        if ($set['mod_reg'] == 1) {
//            echo '<p><span class="red"><b>' . $lng_reg['moderation_note'] . '</b></span></p>';
//        } else {
//            echo '<br /><a href="login.php?id=' . $usid . '&amp;p=' . $reg_pass . '">' . $lng_reg['enter'] . '</a><br/><br/>';
//        }
//        echo '</p></div>';
//        require('incfiles/end.php');
//        exit;