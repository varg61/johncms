<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);
$rootpath = '';
require('incfiles/core.php');
require('incfiles/head.php');
echo '<div class="phdr"><b>' . $lng['login'] . '</b></div>';

$error = array ();
$captcha = false;
$display_form = 1;
$user_login = isset($_POST['n']) ? check($_POST['n']) : NULL;
$user_pass = isset($_REQUEST['p']) ? check($_REQUEST['p']) : NULL;
$user_mem = isset($_POST['mem']) ? 1 : 0;
$user_code = isset($_POST['code']) ? trim($_POST['code']) : NULL;
if ($user_pass && !$user_login && !$id)
    $error[] = $lng['error_login_empty'];
if (($user_login || $id) && !$user_pass)
    $error[] = $lng['error_password_empty'];
if ($user_login && (mb_strlen($user_login) < 2 || mb_strlen($user_login) > 20))
    $error[] = $lng['error_nicklenght'];
if ($user_pass && (mb_strlen($user_pass) < 3 || mb_strlen($user_pass) > 15))
    $error[] = $lng['error_password_lenght'];
if (!$error && $user_pass && ($user_login || $id)) {
    // Запрос в базу на юзера
    $sql = $id ? "`id` = '$id'" : "`name_lat`='" . rus_lat(mb_strtolower($user_login)) . "'";
    $req = mysql_query("SELECT * FROM `users` WHERE $sql LIMIT 1");
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
                    $error[] = $lng['error_verifying_code'];
                }
            } else {
                // Показываем CAPTCHA
                $display_form = 0;
                echo '<form action="login.php" method="post">' .
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
                mysql_query("UPDATE `users` SET `failed_login` = '0' WHERE `id` = '" . $user['id'] . "' LIMIT 1");
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
                    mysql_query("UPDATE `users` SET `sestime` = '$realtime' WHERE `id` = '" . $user['id'] . "'");
                    $set_user = unserialize($user['set_user']);
                    if ($user['lastdate'] < ($realtime - 3600) && $set_user['digest'])
                        header('Location: ' . $home . '/index.php?act=digest&last=' . $user['lastdate']);
                    else
                        header('Location: ' . $home . '/index.php');
                    echo '<div class="gmenu"><p><b><a href="index.php?act=digest">' . $lng['enter_on_site'] . '</a></b></p></div>';
                }
            } else {
                // Если логин неудачный
                if ($user['failed_login'] < 3) {
                    // Прибавляем к счетчику неудачных логинов
                    $failed_login = $user['failed_login'] + 1;
                    mysql_query("UPDATE `users` SET `failed_login` = '$failed_login' WHERE `id` = '" . $user['id'] . "' LIMIT 1");
                }
                $error[] = $lng['authorisation_not_passed'];
            }
        }
    } else {
        $error[] = $lng['authorisation_not_passed'];
    }
}
if ($display_form) {
    if ($error)
        echo display_error($error);
    echo '<div class="gmenu"><form action="login.php" method="post"><p>' . $lng['login_name'] . ':<br/>' .
        '<input type="text" name="n" value="' . htmlentities($user_login, ENT_QUOTES, 'UTF-8') . '" maxlength="20"/>' .
        '<br/>' . $lng['password'] . ':<br/>' .
        '<input type="password" name="p" maxlength="20"/></p>' .
        '<p><input type="checkbox" name="mem" value="1" checked="checked"/>' . $lng['remember'] . '</p>' .
        '<p><input type="submit" value="' . $lng['login'] . '"/></p>' .
        '</form></div>' .
        '<div class="phdr"><a href="str/skl.php?continue">' . $lng['forgotten_password'] . '?</a></div>';
}

require('incfiles/end.php');
?>