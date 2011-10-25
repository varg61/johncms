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
require('incfiles/head.php');
echo '<div class="phdr"><b>' . $lng['login'] . '</b></div>';

$login_data['mode'] = isset($_POST['mode']) ? intval($_POST['mode']) : 1;
$login_data['captcha'] = isset($_POST['captcha']) ? intval($_POST['captcha']) : false;
if (isset($_GET['id']) && isset($_GET['p'])) {
    $login_data['login'] = trim($_GET['id']);
    $login_data['password'] = trim($_GET['p']);
    $login_data['mode'] = 2;
} elseif (isset($_POST['mode']) && isset($_POST['login']) && isset($_POST['password'])) {
    $login_data['login'] = trim($_POST['login']);
    $login_data['password'] = trim($_POST['password']);
    if (isset($_POST['remember'])) $login_data['remember'] = true;
}

$login = new login($login_data);

switch ($login->display_mode) {
    case 'digest':
        /*
        -----------------------------------------------------------------
        Редирект на Дайджест
        -----------------------------------------------------------------
        */
        header('Location: index.php?act=digest&last=' . $user['lastdate']);
        echo '<div class="gmenu"><p><h3>Дайджест <a href="index.php?act=digest">' . $lng['enter_on_site'] . '</a></h3></p></div>';
        break;

    case 'homepage':
        /*
        -----------------------------------------------------------------
        Редирект на главную
        -----------------------------------------------------------------
        */
        header('Location: index.php');
        echo '<div class="gmenu"><p><h3>Главная <a href="index.php">' . $lng['enter_on_site'] . '</a></h3></p></div>';
        break;

    case 'captcha':
        /*
        -----------------------------------------------------------------
        Показываем CAPTCHA
        -----------------------------------------------------------------
        */
        break;

    default:
        /*
        -----------------------------------------------------------------
        Показываем LOGIN форму
        -----------------------------------------------------------------
        */
        $login_style = isset(login::$error['login']) ? 'style="background-color: #FFCCCC"' : '';
        $id_style = isset(login::$error['id']) ? 'style="background-color: #FFCCCC"' : '';
        $pass_style = isset(login::$error['password']) ? 'style="background-color: #FFCCCC"' : '';

        if (login::$error) echo functions::display_error(login::$error);
        echo '<form action="login.php" method="post">' .
             '<div class="gmenu"><p>' .
             '<h3>' . core::$lng['login_name'] . '</h3>' .
             '<input type="text" name="login" value="' . htmlentities($login_data['login'], ENT_QUOTES, 'UTF-8') . '" maxlength="20" ' . $login_style . '/><br />' .
             '<input type="radio" name="mode" value="1" ' . ($login_data['mode'] == 1 ? 'checked="checked"' : '') . '/>&#160;' . core::$lng['nick'] . '<br />' .
             '<input type="radio" name="mode" value="2" ' . ($login_data['mode'] == 2 ? 'checked="checked"' : '') . '/>&#160;User ID<br />' .
             '<input type="radio" name="mode" value="3" ' . ($login_data['mode'] == 3 ? 'checked="checked"' : '') . '/>&#160;E-mail</p>' .
             '<p><h3>' . $lng['password'] . '</h3>' .
             '<input type="password" name="password" maxlength="20" ' . $pass_style . '/></p>' .
             '<p><input type="checkbox" name="remember" value="1" checked="checked"/>' . $lng['remember'] . '</p>' .
             '<p><input type="submit" value="' . $lng['login'] . '"/></p>' .
             '</div></form>' .
             '<div class="phdr"><a href="users/skl.php?continue">' . $lng['forgotten_password'] . '?</a></div>';
}


$error = array();
$captcha = false;
$display_form = 1;
$user_login = isset($_POST['n']) ? functions::check($_POST['n']) : NULL;
$user_pass = isset($_REQUEST['p']) ? functions::check($_REQUEST['p']) : NULL;
$user_mem = isset($_POST['mem']) ? 1 : 0;
$user_code = isset($_POST['code']) ? trim($_POST['code']) : NULL;
if ($user_pass && !$user_login && !$id)
    $error[] = $lng['error_login_empty'];
if (($user_login || $id) && !$user_pass)
    $error[] = $lng['error_empty_password'];
if ($user_login && (mb_strlen($user_login) < 2 || mb_strlen($user_login) > 20))
    $error[] = $lng['nick'] . ': ' . $lng['error_wrong_lenght'];
if ($user_pass && (mb_strlen($user_pass) < 3 || mb_strlen($user_pass) > 15))
    $error[] = $lng['password'] . ': ' . $lng['error_wrong_lenght'];
if (!$error && $user_pass && ($user_login || $id)) {
    // Запрос в базу на юзера
    $sql = $id ? "`id` = '$id'" : "`name` = '" . $user_login . "'";
    $req = mysql_query("SELECT * FROM `users` WHERE $sql");
    if (mysql_num_rows($req)) {
        $user = mysql_fetch_assoc($req);
        if ($user['failed_login'] > 2) {
            if ($user_code) {
                if (mb_strlen($user_code) > 3 && $user_code == $_SESSION['code']) {
                    // Если введен правильный проверочный код
                    unset($_SESSION['code']);
                    $captcha = true;
                } else {
                    // Если проверочный код указан неверно
                    unset($_SESSION['code']);
                    $error[] = $lng['error_wrong_captcha'];
                }
            } else {
                // Показываем CAPTCHA
                $display_form = 0;
                echo '<form action="login.php' . ($id ? '?id=' . $id : '') . '" method="post">' .
                     '<div class="menu"><p><img src="captcha.php?r=' . rand(1000, 9999) . '" alt="' . $lng['verifying_code'] . '"/><br />' .
                     $lng['enter_code'] . ':<br/><input type="text" size="5" maxlength="5"  name="code"/>' .
                     '<input type="hidden" name="n" value="' . $user_login . '"/>' .
                     '<input type="hidden" name="p" value="' . $user_pass . '"/>' .
                     '<input type="hidden" name="mem" value="' . $user_mem . '"/>' .
                     '<input type="submit" name="submit" value="' . $lng['continue'] . '"/></p></div></form>';
            }
        }
        if ($user['failed_login'] < 3 || $captcha) {
            if (md5(md5($user_pass)) == $user['password']) {
                // Если логин удачный
                $display_form = 0;
                mysql_query("UPDATE `users` SET `failed_login` = '0' WHERE `id` = '" . $user['id'] . "'");
                if (!$user['preg']) {
                    // Если регистрация не подтверждена
                    echo '<div class="rmenu"><p>' . $lng['registration_not_approved'] . '</p></div>';
                } else {
                    // Если все проверки прошли удачно, подготавливаем вход на сайт
                    if ($_POST['mem'] == 1) {
                        // Установка данных COOKIE
                        $cuid = base64_encode($user['id']);
                        $cups = md5($user_pass);
                        setcookie("cuid", $cuid, time() + 3600 * 24 * 365);
                        setcookie("cups", $cups, time() + 3600 * 24 * 365);
                    }
                    // Установка данных сессии
                    $_SESSION['uid'] = $user['id'];
                    $_SESSION['ups'] = md5(md5($user_pass));
                    mysql_query("UPDATE `users` SET `sestime` = '" . time() . "' WHERE `id` = '" . $user['id'] . "'");
                    $set_user = unserialize($user['set_user']);
                    if ($user['lastdate'] < (time() - 3600) && $set_user['digest'])
                        header('Location: ' . $set['homeurl'] . '/index.php?act=digest&last=' . $user['lastdate']);
                    else
                        header('Location: ' . $set['homeurl'] . '/index.php');
                    echo '<div class="gmenu"><p><b><a href="index.php?act=digest">' . $lng['enter_on_site'] . '</a></b></p></div>';
                }
            } else {
                // Если логин неудачный
                if ($user['failed_login'] < 3) {
                    // Прибавляем к счетчику неудачных логинов
                    mysql_query("UPDATE `users` SET `failed_login` = '" . ($user['failed_login'] + 1) . "' WHERE `id` = '" . $user['id'] . "'");
                }
                $error[] = $lng['authorisation_not_passed'];
            }
        }
    } else {
        $error[] = $lng['authorisation_not_passed'];
    }
}

require('incfiles/end.php');