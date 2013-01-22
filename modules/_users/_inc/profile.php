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

defined('_IN_USERS') or die('Error: restricted access');
$url = Router::getUri(2);

$tpl = Template::getInstance();
$tpl->url = $url;
$tpl->setUsers = Vars::$USER_SYS;
$tpl->error = array();

if (empty($tpl->user['relationship'])) {
    $tpl->rel_count = 0;
    $tpl->rel = $tpl->bar = array(
        'a' => 0,
        'b' => 0,
        'c' => 0,
        'd' => 0,
        'e' => 0,
    );
} else {
    $tpl->rel = unserialize($tpl->user['relationship']);
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
    $req = DB::PDO()->query("SELECT `value` FROM `cms_user_relationship` WHERE `from` = '" . Vars::$USER_ID . "' AND `to` = '" . $tpl->user['id'] . "'");
    if ($req->rowCount()) {
        $res = $req->fetch();
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
            DB::PDO()->exec("INSERT INTO `cms_user_relationship` SET
                `from` = " . Vars::$USER_ID . ",
                `to` = " . $tpl->user['id'] . ",
                `value` = '" . intval($_POST['vote']) . "'
                ON DUPLICATE KEY UPDATE
                `value` = '" . intval($_POST['vote']) . "'
            ");

            // Обновляем статистику пользователя, за которого голосовали
            $rel['a'] = DB::PDO()->query("SELECT COUNT(*) FROM `cms_user_relationship` WHERE `to` = " . $tpl->user['id'] . " AND `value` = '2'")->fetchColumn();
            $rel['b'] = DB::PDO()->query("SELECT COUNT(*) FROM `cms_user_relationship` WHERE `to` = " . $tpl->user['id'] . " AND `value` = '1'")->fetchColumn();
            $rel['c'] = DB::PDO()->query("SELECT COUNT(*) FROM `cms_user_relationship` WHERE `to` = " . $tpl->user['id'] . " AND `value` = '0'")->fetchColumn();
            $rel['d'] = DB::PDO()->query("SELECT COUNT(*) FROM `cms_user_relationship` WHERE `to` = " . $tpl->user['id'] . " AND `value` = '-1'")->fetchColumn();
            $rel['e'] = DB::PDO()->query("SELECT COUNT(*) FROM `cms_user_relationship` WHERE `to` = " . $tpl->user['id'] . " AND `value` = '-2'")->fetchColumn();

            $data = DB::PDO()->quote(serialize($rel));

            DB::PDO()->exec("UPDATE `users` SET `relationship` = '" . $data . "' WHERE `id` = " . $tpl->user['id']);

            //TODO: Переделать ссылку
            header('Location: ' . Vars::$HOME_URL . '/profile?act=reputation&user=' . $tpl->user['id']);
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
        $tpl->total_photo = DB::PDO()->query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . $tpl->user['id'] . "'")->fetchColumn();
        $tpl->bancount = DB::PDO()->query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $tpl->user['id'] . "'")->fetchColumn();

        if ($tpl->user['id'] == Vars::$USER_ID || Vars::$USER_RIGHTS == 9 || (Vars::$USER_RIGHTS == 7 && Vars::$USER_RIGHTS > $tpl->user['rights'])) {
            //TODO: Переделать ссылку
            $menu[] = '<a href="' . Vars::$HOME_URL . '/profile?act=edit&amp;user=' . $tpl->user['id'] . '">' . __('edit') . '</a>';
        }
        if ($tpl->user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS >= 7 && Vars::$USER_RIGHTS > $tpl->user['rights']) {
            //TODO: Переделать ссылку
            $menu[] = '<a href="' . Vars::$HOME_URL . '/admin?act=usr_del&amp;id=' . $tpl->user['id'] . '">' . __('delete') . '</a>';
        }
        if ($tpl->user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS > $tpl->user['rights']) {
            $menu[] = '<a href="profile.php?act=ban&amp;mod=do&amp;user=' . $tpl->user['id'] . '">' . __('ban_do') . '</a>';
        }
        if (isset($menu)) {
            $tpl->menu = Functions::displayMenu($menu);
        }

        $tpl->userarg = array(
            'lastvisit' => 1,
            'iphist'    => 1,
            'header'    => '<b>ID:' . $tpl->user['id'] . '</b>',
            'footer'    => ($tpl->user['id'] != Vars::$USER_ID
                ? '<span class="gray">' . __('where') . ':</span> '
                : FALSE)
        );

        if ($tpl->user['id'] != Vars::$USER_ID) {
            //Управление друзьями
            $tpl->friend = Functions::checkFriend($tpl->user['id']);

            //Управление контактами
            $contacts = DB::PDO()->query("SELECT * FROM `cms_mail_contacts` WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='" . $tpl->user['id'] . "'");
            $tpl->num_cont = $contacts->rowCount();
            if ($tpl->num_cont) {
                $rows = $contacts->fetch();
                $tpl->banned = $rows['banned'];
                if ($rows['delete'] == 1) $tpl->num_cont = 0;
            }
        }

        //Подключаем шаблон profile
        $tpl->contents = $tpl->includeTpl('profile');
}