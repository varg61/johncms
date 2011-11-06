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
        if (login::$error) echo functions::display_error(login::$error);
        echo '<form action="login.php" method="post">' .
            '<div class="menu"><p><img src="captcha.php?r=' . rand(1000, 9999) . '" alt="' . $lng['verifying_code'] . '"/><br />' .
            $lng['enter_code'] . ':<br/><input type="text" size="5" maxlength="5"  name="captcha"/>';
        if (isset($login_data['id'])) echo '<input type="hidden" name="id" value="' . intval($login_data['id']) . '"/>';
        else echo '<input type="hidden" name="login" value="' . htmlspecialchars($login_data['login']) . '"/>';
        echo '<input type="hidden" name="password" value="' . htmlspecialchars($login_data['password']) . '"/>' .
            '<input type="hidden" name="remember" value="' . $login_data['remember'] . '"/>' .
            '<input type="submit" name="submit" value="' . $lng['continue'] . '"/></p></div></form>';
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
            '<h3>' . core::$lng['login_caption'] . '</h3>' .
            '<input type="text" name="login" value="' . htmlspecialchars($login_data['login']) . '" maxlength="20" ' . $login_style . '/></p>' .
            '<p><h3>' . $lng['password'] . '</h3>' .
            '<input type="password" name="password" maxlength="20" ' . $pass_style . '/></p>' .
            '<p><input type="checkbox" name="remember" value="1" checked="checked"/>' . $lng['remember'] . '</p>' .
            '<p><input type="submit" value="' . $lng['login'] . '"/></p>' .
            '</div></form>' .
            '<div class="phdr"><a href="users/skl.php?continue">' . $lng['forgotten_password'] . '?</a></div>';
}

require('incfiles/end.php');