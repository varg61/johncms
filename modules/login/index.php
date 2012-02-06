<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

echo '<div class="phdr"><b>' . Vars::$LNG['login'] . '</b></div>';

$error_style = 'style="background-color: #FFCCCC"';

$login = new Login;
switch ($login->userLogin()) {
    case 'homepage':
        /*
        -----------------------------------------------------------------
        Редирект на главную
        -----------------------------------------------------------------
        */
        header('Location: ' . Vars::$HOME_URL);
        echo'<div class="gmenu"><p><h3><a href="index.php">' . Vars::$LNG['enter_on_site'] . '</a></h3></p></div>';
        break;

    case 'captcha':
        /*
        -----------------------------------------------------------------
        Показываем CAPTCHA
        -----------------------------------------------------------------
        */
        if (!empty($login->error)) {
            echo'<div class="rmenu"><p>' . Vars::$LNG['errors_occurred'] . '</p></div>';
        }
        echo'<form action="' . Vars::$URI . '" method="post">' .
            '<div class="menu">' .
            '<p><h3>' . Vars::$LNG['captcha'] . '</h3>' .
            Captcha::display(0) . '<br />' .
            (isset($login->error['captcha']) ? '<small class="red"><b>' . Vars::$LNG['error'] . '</b>: ' . $login->error['captcha'] . '<br /></small>' : '') .
            '<input type="text" size="5" maxlength="5"  name="captcha" ' . (isset($login->error['captcha']) ? $error_style : '') . '/></p>';
        if (isset($_REQUEST['id']) && isset($_REQUEST['token'])) {
            echo'<input type="hidden" name="id" value="' . intval($_REQUEST['id']) . '"/>' .
                '<input type="hidden" name="token" value="' . htmlspecialchars($_REQUEST['token']) . '"/>';
        } else {
            echo'<input type="hidden" name="login" value="' . htmlspecialchars($_POST['login']) . '"/>' .
                '<input type="hidden" name="password" value="' . htmlspecialchars($_POST['password']) . '"/>' .
                '<input type="hidden" name="remember" value="' . $_POST['remember'] . '"/>';
        }
        echo'<p><input type="submit" name="submit" value="' . Vars::$LNG['continue'] . '"/></p></div></form>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Показываем LOGIN форму
        -----------------------------------------------------------------
        */
        $login_style = isset($login->error['login']) ? 'style="background-color: #FFCCCC"' : '';
        $id_style = isset($login->error['id']) ? 'style="background-color: #FFCCCC"' : '';
        $pass_style = isset($login->error['password']) ? 'style="background-color: #FFCCCC"' : '';

        // Показываем сообщение об ошибке
        if (!empty($login->error)) {
            echo'<div class="rmenu">' . Vars::$LNG['errors_occurred'] . '</div>';
        }

        echo'<form action="' . Vars::$URI . '" method="post">' .
            '<div class="gmenu"><p>' .

            // Логин
            '<h3>' . Vars::$LNG['login_caption'] . '</h3>' .
            (isset($login->error['login']) ? '<small class="red"><b>' . Vars::$LNG['error'] . '</b>: ' . $login->error['login'] . '<br /></small>' : '') .
            '<input type="text" name="login" value="' . (isset($_POST['login']) ? htmlspecialchars(trim($_POST['login'])) : '') . '" maxlength="20" ' . $login_style . '/></p>' .

            // Пароль
            '<p><h3>' . Vars::$LNG['password'] . '</h3>' .
            (isset($login->error['password']) ? '<small class="red"><b>' . Vars::$LNG['error'] . '</b>: ' . $login->error['password'] . '<br /></small>' : '') .
            '<input type="password" name="password" maxlength="20" ' . $pass_style . '/></p>' .

            // Запомнить
            '<p><input type="checkbox" name="remember" value="1" checked="checked"/>' . Vars::$LNG['remember'] . '</p>' .

            // Кнопка входа
            '<p><input type="submit" value="' . Vars::$LNG['login'] . '"/></p>' .
            '</div></form>' .
            '<div class="phdr"><a href="users/skl.php?continue">' . Vars::$LNG['forgotten_password'] . '?</a></div>';
}