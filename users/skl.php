<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                             Content Management System              //
// Официальный сайт сайт проекта:      http://johncms.com                     //
// Дополнительный сайт поддержки:      http://gazenwagen.com                  //
////////////////////////////////////////////////////////////////////////////////
// JohnCMS core team:                                                         //
// Евгений Рябинин aka john77          john77@gazenwagen.com                  //
// Олег Касьянов aka AlkatraZ          alkatraz@gazenwagen.com                //
//                                                                            //
// Информацию о версиях смотрите в прилагаемом файле version.txt              //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);

require('../incfiles/core.php');
$lng_pass = load_lng('pass');
$textl = $lng_pass['password_restore'];
require('../incfiles/head.php');

function passgen($length) {
    $vals = "abcdefghijklmnopqrstuvwxyz0123456789";
    for ($i = 1; $i <= $length; $i++) {
        $result .= $vals{rand(0, strlen($vals))};
    }
    return $result;
}

switch ($act) {
    case 'sent':
        /*
        -----------------------------------------------------------------
        Отправляем E-mail с инструкциями по восстановлению пароля
        -----------------------------------------------------------------
        */
        $nick = isset($_POST['nick']) ? rus_lat(mb_strtolower(check($_POST['nick']))) : '';
        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
        $code = isset($_POST['code']) ? trim($_POST['code']) : '';
        $error = false;
        if (!$nick || !$email || !$code)
            $error = $lng['error_mandatory_fields'];
        elseif (!isset($_SESSION['code']) || mb_strlen($code) < 4 || $code != $_SESSION['code'])
            $error = $lng_pass['error_code'];
        unset($_SESSION['code']);
        if (!$error) {
            // Проверяем данные по базе
            $req = mysql_query("SELECT * FROM `users` WHERE `name_lat` = '$nick' LIMIT 1");
            if (mysql_num_rows($req) == 1) {
                $res = mysql_fetch_array($req);
                if (empty($res['mail']) || $res['mail'] != $email)
                    $error = $lng_pass['error_email'];
                if ($res['rest_time'] > $realtime - 86400)
                    $error = $lng_pass['restore_timelimit'];
            } else {
                $error = $lng['error_user_not_exist'];
            }
        }
        if (!$error) {
            // Высылаем инструкции на E-mail
            $subject = $lng_pass['password_restore'];
            $mail = $lng_pass['restore_help1'] . ', ' . $res['name'] . "\r\n" . $lng_pass['restore_help2'] . ' ' . $home . "\r\n";
            $mail .= $lng_pass['restore_help3'] . ": \r\n$home/users/skl.php?act=set&id=" . $res['id'] . "&code=" . session_id() . "\n\n";
            $mail .= $lng_pass['restore_help4'] . "\r\n";
            $mail .= $lng_pass['restore_help5'];
            $adds = "From: <" . $emailadmina . ">\r\n";
            $adds .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
            if (mail($res['mail'], $subject, $mail, $adds)) {
                mysql_query("UPDATE `users` SET `rest_code` = '" . session_id() . "', `rest_time` = '$realtime' WHERE `id` = '" . $res['id'] . "'");
                echo '<div class="gmenu"><p>' . $lng_pass['restore_help6'] . '</p></div>';
            } else {
                echo '<div class="rmenu"><p>' . $lng_pass['error_email_sent'] . '</p></div>';
            }
        } else {
            // Выводим сообщение об ошибке
            echo display_error($error, '<a href="skl.php">' . $lng['back'] . '</a>');
        }
        break;

    case 'set':
        /*
        -----------------------------------------------------------------
        Устанавливаем новый пароль
        -----------------------------------------------------------------
        */
        $code = isset($_GET['code']) ? trim($_GET['code']) : '';
        $error = false;
        if (!$id || !$code)
            $error = $lng['error_wrong_data'];
        $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
        if (mysql_num_rows($req) == 1) {
            $res = mysql_fetch_array($req);
            if (empty($res['rest_code']) || empty($res['rest_time']) || $code != $res['rest_code']) {
                $error = $lng_pass['error_fatal'];
            }
            if (!$error && $res['rest_time'] < $realtime - 3600) {
                $error = $lng_pass['error_timelimit'];
                mysql_query("UPDATE `users` SET `rest_code` = '', `rest_time` = '' WHERE `id` = '$id' LIMIT 1");
            }
        } else {
            $error = $lng['error_user_not_exist'];
        }
        if (!$error) {
            // Высылаем пароль на E-mail
            $pass = passgen(4);
            $subject = $lng_pass['your_new_password'];
            $mail = $lng_pass['restore_help1'] . ', ' . $res['name'] . "\r\n" . $lng_pass['restore_help8'] . ' ' . $home . "\r\n";
            $mail .= $lng_pass['your_new_password'] . ": $pass\r\n";
            $mail .= $lng_pass['restore_help7'];
            $adds = "From: <" . $emailadmina . ">\n";
            $adds .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
            if (mail($res['mail'], $subject, $mail, $adds)) {
                mysql_query("UPDATE `users` SET `rest_code` = '', `password` = '" . md5(md5($pass)) . "' WHERE `id` = '$id'");
                echo '<div class="phdr">' . $lng_pass['change_password'] . '</div>';
                echo '<div class="gmenu"><p>' . $lng_pass['change_password_conf'] . '</p></div>';
            } else {
                echo '<div class="rmenu"><p>' . $lng_pass['error_email_sent'] . '</p></div>';
            }
        } else {
            // Выводим сообщение об ошибке
            echo display_error($error);
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Форма для восстановления пароля
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>' . $lng_pass['password_restore'] . '</b></div>';
        echo '<div class="menu"><form action="skl.php?act=sent" method="post">';
        echo '<p>' . $lng_pass['your_login'] . ':<br/><input type="text" name="nick" /><br/>';
        echo $lng_pass['your_email'] . ':<br/><input type="text" name="email" /></p>';
        echo '<p><img src="../captcha.php?r=' . rand(1000, 9999) . '" alt="' . $lng_pass['captcha'] . '"/><br />';
        echo '<input type="text" size="4" maxlength="4"  name="code"/>&#160;' . $lng_pass['enter_code'] . '</p>';
        echo '<p><input type="submit" value="' . $lng_pass['sent'] . '"/></p></form></div>';
        echo '<div class="phdr"><small>' . $lng_pass['restore_help'] . '</small></div>';
        break;
}

require('../incfiles/end.php');
?>