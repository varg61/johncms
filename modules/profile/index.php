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
//TODO: Возможность из админки открывать просмотр профилей для гостей

defined('_IN_JOHNCMS') or die('Error: restricted access');

/*
-----------------------------------------------------------------
Получаем данные пользователя
-----------------------------------------------------------------
*/
if (Vars::$USER_ID) {
    if (($user = Vars::getUser()) === false) {
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
    'activity' => 'activity.php',
    'edit' => 'edit.php',
);

if (isset($actions[Vars::$ACT])
    && is_file(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT])
) {
    require_once(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT]);
} else {
    switch (Vars::$ACT) {
        case 'reset':
            /*
            -----------------------------------------------------------------
            Сброс пользовательских настроек системы (настройки по-умолчанию)
            -----------------------------------------------------------------
            */
            if (!Vars::$USER_ID
                || ($user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS < 7)
                || ($user['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS != 9 && Vars::$USER_RIGHTS <= $user['rights'])
            ) {
                // Закрываем настройки от посторонних
                echo Functions::displayError(lng('access_forbidden'));
                exit;
            }

            if (isset($_POST['submit'])
                && isset($_POST['token'])
                && isset($_SESSION['form_token'])
                && $_POST['token'] == $_SESSION['form_token']
            ) {
                unset($_SESSION['user_set']);
                Vars::setUserData('user_set');
                header('Location: ' . Vars::$URI . '?act=settings&reset');
                exit;
            }
            $tpl->token = mt_rand(100, 10000);
            $_SESSION['form_token'] = $tpl->token;
            $tpl->contents = $tpl->includeTpl('settings_reset');
            break;

        case 'settings':
            /*
            -----------------------------------------------------------------
            Пользовательские настройки системы
            -----------------------------------------------------------------
            */
            if (!Vars::$USER_ID || $user['id'] != Vars::$USER_ID) {
                // Закрываем настройки от посторонних
                echo Functions::displayError(lng('access_forbidden'));
                exit;
            }

            $tpl_list = array();
            $templates = glob(TPLPATH . '*' . DIRECTORY_SEPARATOR . '*.css');
            foreach ($templates as $val) {
                $dir = explode(DIRECTORY_SEPARATOR, dirname($val));
                $tpl_list[] = array_pop($dir);
            }
            sort($tpl_list);
            $tpl->tpl_list = $tpl_list;

            if (isset($_POST['submit'])
                && isset($_POST['token'])
                && isset($_SESSION['form_token'])
                && $_POST['token'] == $_SESSION['form_token']
            ) {
                // Принимаем данные из формы
                if (isset($_POST['timeshift']) && $_POST['timeshift'] > -13 && $_POST['timeshift'] < 13) {
                    Vars::$USER_SET['timeshift'] = intval($_POST['timeshift']);
                }
                if (isset($_POST['field_h']) && $_POST['field_h'] > 0 && $_POST['field_h'] < 10) {
                    Vars::$USER_SET['field_h'] = intval($_POST['field_h']);
                }
                if (isset($_POST['page_size']) && $_POST['page_size'] > 4 && $_POST['page_size'] < 100) {
                    Vars::$USER_SET['page_size'] = intval($_POST['page_size']);
                }
                if (isset($_POST['skin']) && in_array($_POST['skin'], $tpl_list)) {
                    Vars::$USER_SET['skin'] = trim($_POST['skin']);
                }
                $lng_select = isset($_POST['iso']) ? trim($_POST['iso']) : false;
                if ($lng_select && array_key_exists($lng_select, Vars::$LNG_LIST)) {
                    Vars::$USER_SET['lng'] = $lng_select;
                    unset($_SESSION['lng']);
                }
                Vars::$USER_SET['avatar'] = isset($_POST['avatar']);
                Vars::$USER_SET['smileys'] = isset($_POST['smileys']);
                Vars::$USER_SET['translit'] = isset($_POST['translit']);
                Vars::$USER_SET['digest'] = isset($_POST['digest']);
                Vars::$USER_SET['direct_url'] = isset($_POST['direct_url']);
                Vars::$USER_SET['quick_go'] = isset($_POST['quick_go']);

                // Записываем настройки
                unset($_SESSION['user_set']);
                Vars::setUserData('user_set', Vars::$USER_SET);
                header('Location: ' . Vars::$URI . '?act=settings&save');
                exit;
            } elseif (isset($_POST['reset'])) {

            } elseif (isset($_GET['reset'])) {

            }

            if (isset($_GET['save'])) {
                $tpl->save = 1;
            } elseif (isset($_GET['reset'])) {
                $tpl->reset = 1;
            }

            $tpl->token = mt_rand(100, 10000);
            $_SESSION['form_token'] = $tpl->token;
            $tpl->contents = $tpl->includeTpl('settings');
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
                'iphist' => 1,
                'header' => '<b>ID:' . $user['id'] . '</b>',
                'footer' => ($user['id'] != Vars::$USER_ID
                    ? '<span class="gray">' . lng('where') . ':</span> ' . Functions::displayPlace($user['id'], $user['place'])
                    : false)
            );

            //Управление контактами
            $contacts = mysql_query("SELECT * FROM `cms_mail_contacts` WHERE `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='" . $user['id'] . "'");
            $num_cont = mysql_num_rows($contacts);
            if ($num_cont) {
                $rows = mysql_fetch_assoc($contacts);
                $tpl->banned = $rows['banned'];
                if ($rows['delete'] == 1) $num_cont = 0;
            }
            $tpl->textbanned = $num_cont && $rows['banned'] == 1 ? lng('contact_delete_ignor') : lng('contact_add_ignor');
            $tpl->textcontact = $num_cont ? lng('contact_delete') : lng('contact_add');
            //Подключаем шаблон profile
            $tpl->contents = $tpl->includeTpl('profile');
    }
}