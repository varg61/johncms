<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

//TODO: Добавить информацию о дне рождении
//TODO: Добавить информацию о подтверждении регистрации

defined('_IN_JOHNCMS') or die('Error: restricted access');
define('_IN_PROFILE', 1);

/*
-----------------------------------------------------------------
Получаем данные пользователя
-----------------------------------------------------------------
*/
if (Vars::$USER_ID || Vars::$USER_SYS['view_profiles']) {
    if (($user = Vars::getUser()) === FALSE) {
        echo Functions::displayError(lng('user_does_not_exist'));
        exit;
    }
} else {
    echo Functions::displayError(lng('access_guest_forbidden'));
    exit;
}

$tpl = Template::getInstance();
$tpl->user = $user;

$actions = array(
    'activity'      => 'activity.php',
    'edit'          => 'profile_edit.php',
    'guestbook'     => 'guestbook.php',
    'ip'            => 'ip.php',
    'password'      => 'profile_password.php',
    'settings'      => 'profile_settings.php',
);

if (isset($actions[Vars::$ACT]) && is_file(MODPATH . Vars::$MODULE_PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT])) {
    require_once(MODPATH . Vars::$MODULE_PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT]);
} else {
    if (empty($user['relationship'])) {
        $tpl->rel_count = 0;
        $tpl->rel = $tpl->bar = array(
            'a' => 0,
            'b' => 0,
            'c' => 0,
            'd' => 0,
            'e' => 0,
        );
    } else {
        $tpl->rel = unserialize($user['relationship']);
        $tpl->rel_count = array_sum($tpl->rel);
        $tpl->bar = array(
            'a' => round(100 / $tpl->rel_count * $tpl->rel['a']),
            'b' => round(100 / $tpl->rel_count * $tpl->rel['b']),
            'c' => round(100 / $tpl->rel_count * $tpl->rel['c']),
            'd' => round(100 / $tpl->rel_count * $tpl->rel['d']),
            'e' => round(100 / $tpl->rel_count * $tpl->rel['e']),
        );
    }

    if (Vars::$USER_ID) {
        // Получаем данные своего голосования
        $req = mysql_query("SELECT `value` FROM `cms_user_relationship` WHERE `from` = '" . Vars::$USER_ID . "' AND `to` = '" . $user['id'] . "'");
        if (mysql_num_rows($req)) {
            $res = mysql_fetch_assoc($req);
            $tpl->my_rel = $res['value'];
        } else {
            $tpl->my_rel = 10;
        }
    }

    switch (Vars::$ACT) {
        case 'reputation':
            /*
            -----------------------------------------------------------------
            Система Отношений
            -----------------------------------------------------------------
            */
            if (Vars::$USER_ID
                && isset($_POST['submit'])
                && isset($_POST['form_token'])
                && isset($_SESSION['form_token'])
                && $_POST['form_token'] == $_SESSION['form_token']
                && isset($_POST['vote'])
                && $_POST['vote'] >= -2
                && $_POST['vote'] <= 2
            ) {
                // Записываем голос в базу данных
                mysql_query("INSERT INTO `cms_user_relationship` SET
                    `from` = " . Vars::$USER_ID . ",
                    `to` = " . $tpl->user['id'] . ",
                    `value` = '" . intval($_POST['vote']) . "'
                    ON DUPLICATE KEY UPDATE
                    `value` = '" . intval($_POST['vote']) . "'
                ");

                // Обновляем статистику пользователя, за которого голосовали
                $rel['a'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_user_relationship` WHERE `to` = " . $user['id'] . " AND `value` = '2'"), 0);
                $rel['b'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_user_relationship` WHERE `to` = " . $user['id'] . " AND `value` = '1'"), 0);
                $rel['c'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_user_relationship` WHERE `to` = " . $user['id'] . " AND `value` = '0'"), 0);
                $rel['d'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_user_relationship` WHERE `to` = " . $user['id'] . " AND `value` = '-1'"), 0);
                $rel['e'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_user_relationship` WHERE `to` = " . $user['id'] . " AND `value` = '-2'"), 0);
                mysql_query("UPDATE `users` SET `relationship` = '" . mysql_real_escape_string(serialize($rel)) . "' WHERE `id` = " . $user['id']);

                header('Location: ' . Vars::$HOME_URL . '/profile?act=reputation&user=' . $user['id']);
                exit;
            } else {
                $tpl->form_token = mt_rand(100, 10000);
                $_SESSION['form_token'] = $tpl->form_token;
                $tpl->contents = $tpl->includeTpl('reputation');
            }
            break;

        case 'info':
            /*
            -----------------------------------------------------------------
            Подробная информация о пользователе
            -----------------------------------------------------------------
            */
            //TODO: Добавить вывод даты рожденья
            $tpl->contents = $tpl->includeTpl('info');
            break;

        default:
            /*
            -----------------------------------------------------------------
            Анкета пользователя
            -----------------------------------------------------------------
            */
            $tpl->total_photo = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . $user['id'] . "'"), 0);
            $tpl->bancount = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $user['id'] . "'"), 0);

            if ($user['id'] == Vars::$USER_ID || Vars::$USER_RIGHTS == 9 || (Vars::$USER_RIGHTS == 7 && Vars::$USER_RIGHTS > $user['rights'])) {
                $menu[] = '<a href="' . Vars::$HOME_URL . '/profile?act=edit&amp;user=' . $user['id'] . '">' . lng('edit') . '</a>';
            }
            if ($user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS >= 7 && Vars::$USER_RIGHTS > $user['rights']) {
                $menu[] = '<a href="' . Vars::$HOME_URL . '/admin?act=usr_del&amp;id=' . $user['id'] . '">' . lng('delete') . '</a>';
            }
            if ($user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS > $user['rights']) {
                $menu[] = '<a href="profile.php?act=ban&amp;mod=do&amp;user=' . $user['id'] . '">' . lng('ban_do') . '</a>';
            }
            if (isset($menu)) {
                $tpl->menu = Functions::displayMenu($menu);
            }

            $tpl->userarg = array(
                'lastvisit' => 1,
                'iphist'    => 1,
                'header'    => '<b>ID:' . $user['id'] . '</b>',
                'footer'    => ($user['id'] != Vars::$USER_ID
                    ? '<span class="gray">' . lng('where') . ':</span> '
                    : FALSE)
            );

            if ($user['id'] != Vars::$USER_ID) {
                //Управление друзьями
                $tpl->friend = Functions::checkFriend($user['id']);

                //Управление контактами
                $contacts = mysql_query("SELECT * FROM `cms_mail_contacts` WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='" . $user['id'] . "'");
                $tpl->num_cont = mysql_num_rows($contacts);
                if ($tpl->num_cont) {
                    $rows = mysql_fetch_assoc($contacts);
                    $tpl->banned = $rows['banned'];
                    if ($rows['delete'] == 1) $tpl->num_cont = 0;
                }
            }
            //Подключаем шаблон profile
            $tpl->contents = $tpl->includeTpl('profile');
    }
}