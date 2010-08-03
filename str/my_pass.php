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
$headmod = 'pass';
require('../incfiles/core.php');
$lng_pass = load_lng('pass');
$textl = $lng_pass['change_password'];
require('../incfiles/head.php');
if (!$user_id) {
    display_error($lng['access_guest_forbidden']);
    require_once('../incfiles/end.php');
    exit;
}
if ($id && $id != $user_id && $rights >= 7) {
    // Если был запрос на юзера, то получаем его данные
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
    if (mysql_num_rows($req)) {
        $user = mysql_fetch_assoc($req);
        if ($user['rights'] > $datauser['rights']) {
            // Если не хватает прав, выводим ошибку
            echo display_error($lng_pass['error_rights']);
            require('../incfiles/end.php');
            exit;
        }
    } else {
        echo display_error($lng['error_user_not_exist']);
        require('../incfiles/end.php');
        exit;
    }
} else {
    $id = false;
    $user = $datauser;
}
switch ($act) {
    case 'change':
        /*
        -----------------------------------------------------------------
        Меняем пароль
        -----------------------------------------------------------------
        */
        $error = array ();
        $oldpass = isset($_POST['oldpass']) ? trim($_POST['oldpass']) : '';
        $newpass = isset($_POST['newpass']) ? trim($_POST['newpass']) : '';
        $newconf = isset($_POST['newconf']) ? trim($_POST['newconf']) : '';
        $autologin = isset($_POST['autologin']) ? 1 : 0;
        if ($rights >= 7) {
            if (!$newpass || !$newconf)
                $error[] = $lng_pass['error_fields'];
        } else {
            if (!$oldpass || !$newpass || !$newconf)
                $error[] = $lng_pass['error_fields'];
        }
        if (!$error && $rights < 7 && md5(md5($oldpass)) !== $user['password'])
            $error[] = $lng_pass['error_old_password'];
        if ($newpass != $newconf)
            $error[] = $lng_pass['error_new_password'];
        if (preg_match("/[^\da-zA-Z_]+/", $newpass) && !$error)
            $error[] = $lng['error_wrongsymbols'];
        if (!$error && (strlen($newpass) < 3 || strlen($newpass) > 10))
            $error[] = $lng_pass['error_lenght'];
        if (!$error) {
            // Записываем в базу
            mysql_query("UPDATE `users` SET `password` = '" . mysql_real_escape_string(md5(md5($newpass))) . "' WHERE `id` = '" . $user['id'] . "' LIMIT 1");
            // Проверяем и записываем COOKIES
            if (isset($_COOKIE['cuid']) && isset($_COOKIE['cups']))
                setcookie('cups', md5($newpass), time() + 3600 * 24 * 365);
            echo '<div class="gmenu"><p><b>' . $lng_pass['password_changed'] . '</b></p>';
            if ($autologin) {
                // Показываем ссылку на Автологин
                echo '<p>' . $lng_pass['autologin_link'] . ':<br />' .
                    '<input type="text" value="' . $home . '/login.php?id=' . $user['id'] . '&amp;p=' . $newpass . '" /></p>' .
                    '<p>' . $lng_pass['autologin_warning'] . '</p>';
            }
            echo '</div>';
        } else {
            $error[] = '<div><a href="my_pass.php">' . $lng['repeat'] . '</a></div>';
            echo display_error($error);
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Форма смены пароля
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . $lng_pass['change_password'] . ':</b> ' . $user['name'] . '</div>';
        echo '<form action="my_pass.php?act=change' . ($id ? '&amp;id=' . $id : '') . '" method="post">';
        if (!$id || $rights < 7)
            echo '<div class="menu"><p>' . $lng_pass['input_old_password'] . ':<br /><input type="password" name="oldpass" /></p></div>';
        echo '<div class="gmenu"><p>' . $lng_pass['input_new_password'] . ':<br />' .
            '<input type="password" name="newpass" /><br />' . $lng_pass['repeat_password'] . ':<br />' .
            '<input type="password" name="newconf" /></p>' .
            '<p><input type="checkbox" value="1" name="autologin" />&#160;' . $lng_pass['show_autologin'] .
            '</p><p><input type="submit" value="' . $lng['save'] . '" name="submit" />' .
            '</p></div></form>' .
            '<div class="phdr"><small>' . $lng_pass['password_change_help'] . '</small></div>' .
            '<p><a href="anketa.php?id=' . $user['id'] . '">' . $lng['profile'] . '</a></p>';
}

require('../incfiles/end.php');
?>