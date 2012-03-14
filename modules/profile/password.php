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

//TODO: Доработать!

/*
-----------------------------------------------------------------
Проверяем права доступа
-----------------------------------------------------------------
*/
if ($user['user_id'] != Vars::$USER_ID && (Vars::$USER_RIGHTS < 7 || $user['rights'] > Vars::$USER_RIGHTS)) {
    echo Functions::displayError(lng('access_forbidden'));
    exit;
}

switch (Vars::$MOD) {
    case 'change':
        /*
        -----------------------------------------------------------------
        Меняем пароль
        -----------------------------------------------------------------
        */
        $error = array();
        $oldpass = isset($_POST['oldpass']) ? trim($_POST['oldpass']) : '';
        $newpass = isset($_POST['newpass']) ? trim($_POST['newpass']) : '';
        $newconf = isset($_POST['newconf']) ? trim($_POST['newconf']) : '';
        $autologin = isset($_POST['autologin']) ? 1 : 0;
        if ($user['user_id'] != Vars::$USER_ID) {
            if (!$newpass || !$newconf)
                $error[] = lng('error_fields');
        } else {
            if (!$oldpass || !$newpass || !$newconf)
                $error[] = lng('error_fields');
        }
        if (!$error && $user['user_id'] == Vars::$USER_ID && md5(md5($oldpass)) !== $user['password'])
            $error[] = lng('error_old_password');
        if ($newpass != $newconf)
            $error[] = lng('error_new_password');
        if (preg_match("/[^\da-zA-Z_]+/", $newpass) && !$error)
            $error[] = lng('error_wrong_symbols');
        if (!$error && (strlen($newpass) < 3 || strlen($newpass) > 10))
            $error[] = lng('error_lenght');
        if (!$error) {
            // Записываем в базу
            mysql_query("UPDATE `users` SET `password` = '" . mysql_real_escape_string(md5(md5($newpass))) . "' WHERE `id` = '" . $user['user_id'] . "'");
            // Проверяем и записываем COOKIES
            if (isset($_COOKIE['cuid']) && isset($_COOKIE['cups']))
                setcookie('cups', md5($newpass), time() + 3600 * 24 * 365);
            echo '<div class="gmenu"><p><b>' . lng('password_changed') . '</b><br />' .
                 '<a href="' . (Vars::$USER_ID == $user['user_id'] ? '../login.php' : 'profile.php?user=' . $user['user_id']) . '">' . lng('continue') . '</a></p>';
            if ($autologin) {
                // Показываем ссылку на Автологин
                echo '<p>' . lng('autologin_link') . ':<br />' .
                     '<input type="text" value="' . Vars::$HOME_URL . '/login.php?id=' . $user['user_id'] . '&amp;p=' . $newpass . '" /></p>' .
                     '<p>' . lng('autologin_warning') . '</p>';
            }
            echo '</div>';
        } else {
            echo Functions::displayError($error, '<a href="profile.php?act=password&amp;user=' . $user['user_id'] . '">' . lng('repeat') . '</a>');
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Форма смены пароля
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . lng('change_password') . ':</b> ' . $user['name'] . '</div>';
        echo '<form action="profile.php?act=password&amp;mod=change&amp;user=' . $user['user_id'] . '" method="post">';
        if ($user['user_id'] == Vars::$USER_ID)
            echo '<div class="menu"><p>' . lng('input_old_password') . ':<br /><input type="password" name="oldpass" /></p></div>';
        echo '<div class="gmenu"><p>' . lng('input_new_password') . ':<br />' .
             '<input type="password" name="newpass" /><br />' . lng('repeat_password') . ':<br />' .
             '<input type="password" name="newconf" /></p>' .
             '<p><input type="checkbox" value="1" name="autologin" />&#160;' . lng('show_autologin') .
             '</p><p><input type="submit" value="' . lng('save') . '" name="submit" />' .
             '</p></div></form>' .
             '<div class="phdr"><small>' . lng('password_change_help') . '</small></div>' .
             '<p><a href="profile.php?user=' . $user['user_id'] . '">' . lng('profile') . '</a></p>';
}