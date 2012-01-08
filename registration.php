<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require_once('includes/core.php');
$lng_reg = Vars::loadLanguage('registration');
$textl = Vars::$LNG['registration'];
require_once('includes/head.php');
echo '<div class="phdr"><b>' . Vars::$LNG['registration'] . '</b></div>';

$error_style = 'style="background-color: #FFCCCC"';

$reg_data['login'] = isset($_POST['login']) ? trim($_POST['login']) : '';
$reg_data['password'] = isset($_POST['password']) ? trim($_POST['password']) : '';
$reg_data['password_confirm'] = isset($_POST['password_confirm']) ? trim($_POST['password_confirm']) : '';
$reg_data['captcha'] = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
$reg_data['email'] = isset($_POST['email']) ? trim($_POST['email']) : '';
$reg_data['about'] = isset($_POST['about']) ? trim($_POST['about']) : '';
$reg_data['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
$reg_data['sex'] = isset($_POST['sex']) ? intval($_POST['sex']) : 0;

$login = new Login;
switch ($login->userRegistration($reg_data)) {
    case 'step2';
        /*
        -----------------------------------------------------------------
        Поздравление с регистрацией
        -----------------------------------------------------------------
        */
        echo'<div class="gmenu"><p><h3>' . Vars::$LNG['thanks_for_registration'] . '!</h3></p><p>' .
            Functions::getImage('usr_' . (Vars::$USER_DATA['sex'] == 'm' ? 'm' : 'w') . '.png', '', 'align="middle"') . '&#160;' .
            Vars::$LNG['login'] . ':&#160;<strong>' . htmlspecialchars(Vars::$USER_DATA['nickname']) . '</strong><br />' .
            Functions::getImage('16x16.gif', '', 'align="middle"') . '&#160;' .
            Vars::$LNG['password'] . ':&#160;';

        // Показываем пароль
        if (isset($_GET['pass'])) {
            echo'<strong>' . htmlspecialchars($_SESSION['password']) . '</strong>';
        } else {
            for ($i = 0; $i < strlen($_SESSION['password']); ++$i) echo'*';
            echo'<br /><small><a href="registration.php?pass' . (isset($_GET['auto']) ? '&amp;auto' : '') . '">' . Vars::$LNG['show_password'] . '</a></small>';
        }

        // Показываем ссылку на Автологин
        if (isset($_GET['auto'])) {
            echo'</p><p>' . Vars::$LNG['autologin_link'] .
                '<br /><input type="text" value="' . Vars::$SYSTEM_SET['homeurl'] . '/login.php?id=' . $_SESSION['id'] . '&amp;token=' . htmlspecialchars($_SESSION['token']) . '"/>';
        } else {
            echo'<br /><small><a href="registration.php?auto' . (isset($_GET['pass']) ? '&amp;pass' : '') . '">' . Vars::$LNG['show_autologin_link'] . '</a></small>';
        }
        echo '</p></div>';

        /*
        -----------------------------------------------------------------
        Заполнение анкеты
        -----------------------------------------------------------------
        */
        echo'<div class="rmenu"><p><h3>Не забудьте заполнить свою Анкету</h3></p></div>' .
            '<div class="menu">' . Profile::edit('registration.php') . '</div>';
        echo '<div class="phdr"><p><small>Дополнительная информация не обязательна, форма заполняется по желанию</small></p></div>';
        break;

    case 'step1':
        /*
        -----------------------------------------------------------------
        Форма регистрации новых пользователей
        -----------------------------------------------------------------
        */
        // Показываем ошибки (если есть)
        if (isset($_POST['submit']) && !empty($login->error)) echo'<div class="rmenu"><p>' . Vars::$LNG['errors_occurred'] . '</p></div>';

        echo'<form action="registration.php" method="post">' .
            '<div class="gmenu">' .

            // Логин
            '<p><h3>' . $lng_reg['login'] . '</h3>' .
            (isset(Validate::$error['login']) ? '<small class="red"><b>' . Vars::$LNG['error'] . '</b>: ' . Validate::$error['login'] . '<br /></small>' : '') .
            (isset($_POST['check_login']) && empty(Validate::$error) ? '<small>' . $lng_reg['nick_available'] . '<br /></small>' : '') .
            '<input type="text" name="login" maxlength="20" value="' . htmlspecialchars($reg_data['login']) . '" ' . (isset(Validate::$error['login']) ? $error_style : '') . '/>' .
            '<input type="submit" name="check_login" value="?"/></p>' .

            // Пароль
            '<p><h3>' . $lng_reg['password'] . '</h3>' .
            (isset(Validate::$error['password']) ? '<small class="red"><b>' . Vars::$LNG['error'] . '</b>: ' . Validate::$error['password'] . '<br /></small>' : '') .
            '<input type="password" name="password" maxlength="20" value="' . htmlspecialchars($reg_data['password']) . '" ' . (isset(Validate::$error['password']) ? $error_style : '') . '/><br />' .
            '<small>' . $lng_reg['repeat_password'] . '</small><br />' .
            (isset($login->error['password_confirm']) ? '<small class="red"><b>' . Vars::$LNG['error'] . '</b>: ' . $login->error['password_confirm'] . '<br /></small>' : '') .
            '<input type="password" name="password_confirm" maxlength="20" value="' . htmlspecialchars($reg_data['password_confirm']) . '" ' . (isset($login->error['password_confirm']) ? $error_style : '') . '/></p>' .

            // E-mail
            '<p><h3>E-mail</h3>' .
            (isset(Validate::$error['email']) ? '<small class="red"><b>' . Vars::$LNG['error'] . '</b>: ' . Validate::$error['email'] . '<br /></small>' : '') .
            '<input type="text" name="email" maxlength="50" value="' . htmlspecialchars($reg_data['email']) . '" ' . (isset(Validate::$error['email']) ? $error_style : '') . '/></p>' .

            // Пол
            '<p><h3>' . $lng_reg['sex'] . '</h3>' .
            (isset($login->error['sex']) ? '<small class="red"><b>' . Vars::$LNG['error'] . '</b>: ' . $login->error['sex'] . '<br /></small>' : '') .
            '<input type="radio" value="1" name="sex" ' . ($reg_data['sex'] == 1 ? 'checked="checked"' : '') . '/>&#160;' .
            Functions::getImage('usr_m.png', '', 'align="middle"') . '&#160;' . $lng_reg['sex_m'] . '<br />' .
            '<input type="radio" value="2" name="sex" ' . ($reg_data['sex'] == 2 ? 'checked="checked"' : '') . '/>&#160;' .
            Functions::getImage('usr_w.png', '', 'align="middle"') . '&#160;' . $lng_reg['sex_w'] . '</p>' .

            // CAPTCHA
            '<p><h3>' . Vars::$LNG['captcha'] . '</h3>' .
            Captcha::display(0) . '<br />' .
            (isset($login->error['captcha']) ? '<small class="red"><b>' . Vars::$LNG['error'] . '</b>: ' . $login->error['captcha'] . '<br /></small>' : '') .
            '<input type="text" size="5" maxlength="5"  name="captcha" ' . (isset($login->error['captcha']) ? $error_style : '') . '/></p>' .

            // Кнопка регистрации
            '<p><input type="submit" name="submit" value="' . $lng_reg['registration'] . '"/></p>' .
            '</div></form>' .

            // Справка по заполнению полей
            '<div class="phdr"><small>' .
            '<p><b>' . $lng_reg['mandatory_fields'] . '</b></p>' .
            '<p><b class="green">' . mb_strtoupper($lng_reg['login']) . '</b>: ' . $lng_reg['login_help'] . '</p>' .
            '<p><b class="green">' . mb_strtoupper($lng_reg['password']) . '</b>: ' . $lng_reg['password_help'] . '</p>' .
            (!empty($lng_reg['registration_terms']) ? '<p>' . $lng_reg['registration_terms'] . '</p>' : '') .
            '</small></div>';

        // Предупреждение о включенной модерации
        if (Vars::$SYSTEM_SET['mod_reg'] == 1) echo'<div class="topmenu"><small class="red"><p>' . $lng_reg['moderation_warning'] . '</p></small></div>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Если регистрация закрыта, или пользователь уже авторизован
        -----------------------------------------------------------------
        */
        echo'<div class="topmenu"><p class="red">' .
            (Vars::$USER_ID ? $lng_reg['already_registered'] : $lng_reg['registration_closed']) .
            '</p></div>';
}

require_once('includes/end.php');