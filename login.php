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

$error_style = 'style="background-color: #FFCCCC"';

if (isset($_GET['id']) && isset($_GET['p'])) {
    // Принимаем данные ссылки AutoLogin
    $login_data['id'] = trim($_GET['id']);
    $login_data['password'] = trim($_GET['p']);
} elseif (isset($_POST['login']) && isset($_POST['password'])) {
    // Принимаем данные формы авторизации
    if (isset($_POST['id'])) $login_data['id'] = trim($_POST['id']);
    if (isset($_POST['captcha'])) $login_data['captcha'] = trim($_POST['captcha']);
    $login_data['login'] = trim($_POST['login']);
    $login_data['password'] = trim($_POST['password']);
    $login_data['remember'] = isset($_POST['remember']);
} else {
    $login_data = array();
}

switch (login::do_login($login_data)) {
    case 'digest':
        /*
        -----------------------------------------------------------------
        Редирект на Дайджест
        -----------------------------------------------------------------
        */
        header('Location: index.php?act=digest&last=' . $user['lastdate']);
        echo'<div class="gmenu"><p><h3>Дайджест <a href="index.php?act=digest">' . $lng['enter_on_site'] . '</a></h3></p></div>';
        break;

    case 'homepage':
        /*
        -----------------------------------------------------------------
        Редирект на главную
        -----------------------------------------------------------------
        */
        header('Location: index.php');
        echo'<div class="gmenu"><p><h3>Главная <a href="index.php">' . $lng['enter_on_site'] . '</a></h3></p></div>';
        break;

    case 'captcha':
        /*
        -----------------------------------------------------------------
        Показываем CAPTCHA
        -----------------------------------------------------------------
        */
        if (!empty(login::$error)) echo'<div class="rmenu"><p>' . core::$lng['errors_occurred'] . '</p></div>';
        echo'<form action="login.php" method="post">' .
            '<div class="menu">' .
            '<p><h3>' . core::$lng['captcha'] . '</h3>' .
            '<img src="captcha.php?r=' . rand(1000, 9999) . '" alt="' . $lng_reg['captcha_help'] . '" border="2"/><br />' .
            (isset(login::$error['captcha']) ? '<small class="red"><b>' . core::$lng['error'] . '</b>: ' . login::$error['captcha'] . '<br /></small>' : '') .
            '<input type="text" size="5" maxlength="5"  name="captcha" ' . (isset(login::$error['captcha']) ? $error_style : '') . '/></p>';
        if (isset($login_data['id'])) echo '<input type="hidden" name="id" value="' . intval($login_data['id']) . '"/>';
        else echo'<input type="hidden" name="login" value="' . htmlspecialchars($login_data['login']) . '"/>';
        echo'<input type="hidden" name="password" value="' . htmlspecialchars($login_data['password']) . '"/>' .
            '<input type="hidden" name="remember" value="' . $login_data['remember'] . '"/>' .
            '<p><input type="submit" name="submit" value="' . $lng['continue'] . '"/></p></div></form>';
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

        if (!empty(login::$error)) echo'<div class="rmenu"><p>' . core::$lng['errors_occurred'] . '</p></div>';
        echo'<form action="login.php" method="post">' .
            '<div class="gmenu"><p>' .

            // Логин
            '<h3>' . core::$lng['login_caption'] . '</h3>' .
            (isset(login::$error['login']) ? '<small class="red"><b>' . core::$lng['error'] . '</b>: ' . login::$error['login'] . '<br /></small>' : '') .
            '<input type="text" name="login" value="' . htmlspecialchars($login_data['login']) . '" maxlength="20" ' . $login_style . '/></p>' .

            // Пароль
            '<p><h3>' . $lng['password'] . '</h3>' .
            (isset(login::$error['password']) ? '<small class="red"><b>' . core::$lng['error'] . '</b>: ' . login::$error['password'] . '<br /></small>' : '') .
            '<input type="password" name="password" maxlength="20" ' . $pass_style . '/></p>' .

            // Запомнить
            '<p><input type="checkbox" name="remember" value="1" checked="checked"/>' . $lng['remember'] . '</p>' .

            // Кнопка входа
            '<p><input type="submit" value="' . $lng['login'] . '"/></p>' .
            '</div></form>' .
            '<div class="phdr"><a href="users/skl.php?continue">' . $lng['forgotten_password'] . '?</a></div>';
}

require('incfiles/end.php');