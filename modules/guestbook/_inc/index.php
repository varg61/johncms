<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_GUESTBOOK') or die('Error: restricted access');
$url = Router::getUri(2);

if (isset($_SESSION['ref']))
    unset($_SESSION['ref']);

// Если гостевая закрыта, выводим сообщение и закрываем доступ (кроме Админов)
if ((!isset(Vars::$ACL['guestbook']) || !Vars::$ACL['guestbook']) && Vars::$USER_RIGHTS < 7) {
    echo '<div class="rmenu"><p>' . __('guestbook_closed') . '</p></div>';
    exit;
}

// Определяем доступ к Админ-клубу
$mod = Vars::$USER_RIGHTS && Vars::$MOD == 'adm' ? 1 : 0;

$tpl = Template::getInstance();
$form = new Form($url . ($mod ? '?mod=adm' : ''));

if (!Vars::$USER_ID) {
    $form->add('text', 'name', array('label' => __('name')));
}

$form
    ->add('textarea', 'msg', array(
    'toolbar' => (Vars::$USER_ID && !Vars::$IS_MOBILE)))

    ->add('submit', 'submit', array(
    'value' => __('write'),
    'class' => 'btn btn-primary'));

$tpl->form = $form->display();

if ($form->isSubmitted) {
    // Принимаем и обрабатываем данные
    $name = isset($_POST['name']) ? mb_substr(trim($_POST['name']), 0, 20) : '';
    $msg = isset($_POST['msg']) ? mb_substr(trim($_POST['msg']), 0, 5000) : '';
    $from = Vars::$USER_ID ? Vars::$USER_NICKNAME : mysql_real_escape_string($name);
    $trans = isset($_POST['msgtrans']);

    // Транслит сообщения
    if ($trans) {
        $msg = Functions::translit($msg);
    }

    // Проверяем на ошибки
    $error = array();
    $flood = FALSE;
    if (!Vars::$USER_ID && empty($_POST['name'])) {
        $error[] = __('error_empty_name');
    }
    if (empty($msg)) {
        $error[] = __('error_empty_message');
    }
    //TODO: Обработать Бан!
    //if (Vars::$USER_BAN['1'] || Vars::$USER_BAN['13'])
    //    $error[] = Vars::$LNG['access_forbidden'];

    // CAPTCHA для гостей
    if (!Vars::$USER_ID && !Captcha::check()) {
        $error[] = __('error_wrong_captcha');
    }
    unset($_SESSION['captcha']);

    if (Vars::$USER_ID) {
        // Антифлуд для зарегистрированных пользователей
        $flood = Functions::antiFlood();
    } else {
        // Антифлуд для гостей
        $req = mysql_query("SELECT `time` FROM `guest` WHERE `ip` = '" . Vars::$IP . "' AND `user_agent` = '" . mysql_real_escape_string(Vars::$USER_AGENT) . "' AND `time` > '" . (time() - 60) . "'");
        if (mysql_num_rows($req)) {
            $result = mysql_fetch_assoc($req);
            $flood = time() - $result['time'];
        }
    }

    if ($flood) {
        $error = __('error_flood') . ' ' . $flood . '&#160;' . __('seconds');
    }

    if (!$error) {
        // Проверка на одинаковые сообщения
        $req = mysql_query("SELECT * FROM `guest` WHERE `user_id` = " . Vars::$USER_ID . " ORDER BY `time` DESC");
        $result = mysql_fetch_array($req);
        if ($result['text'] == $msg) {
            header("location: ", $url);
            exit;
        }
    }

    if (!$error) {
        // Вставляем сообщение в базу
        mysql_query("INSERT INTO `guest` SET
                `adm` = '$mod',
                `time` = '" . time() . "',
                `user_id` = " . Vars::$USER_ID . ",
                `nickname` = '$from',
                `text` = '" . mysql_real_escape_string($msg) . "',
                `otvet` = '',
                `ip` = '" . Vars::$IP . "',
                `user_agent` = '" . mysql_real_escape_string(Vars::$USER_AGENT) . "'
            ") or exit(mysql_error());
        // Фиксируем статистику и время последнего поста (антиспам)
        if (Vars::$USER_ID) {
            mysql_query("UPDATE `users` SET `count_comments` = '" . ++Vars::$USER_DATA['count_comments'] . "', `lastpost` = '" . time() . "' WHERE `id` = " . Vars::$USER_ID);
        }
        header('location: ' . $url . ($mod ? '?mod=adm' : ''));
    } else {
        echo Functions::displayError($error, '<a href="' . $url . ($mod ? '?mod=adm' : '') . '">' . __('back') . '</a>');
    }
}



// Форма ввода нового сообщения
if ((Vars::$USER_ID || Vars::$SYSTEM_SET['mod_guest'] == 2) && !isset(Vars::$USER_BAN['1']) && !isset(Vars::$USER_BAN['13'])) {
    if (Vars::$USER_SET['translit']) {
        echo '<input type="checkbox" name="msgtrans" value="1" />&nbsp;' . __('translit') . '<br/>';
    }
    echo'<input type="submit" name="submit" value="' . __('sent') . '"/>' .
        '<input type="hidden" name="form_token" value="' . $_SESSION['form_token'] . '"/>' .
        '</form></div>';
} else {
    echo '<div class="rmenu">' . __('access_guest_forbidden') . '</div>';
}

$tpl->total = DB::PDO()->query("SELECT COUNT(*) FROM `guest` WHERE `adm` = '" . ($mod ? 1 : 0) . "'")->fetchColumn();
if ($tpl->total) {
    if ($mod) {
        // Запрос для Админ клуба
        $STH = DB::PDO()->query("
            SELECT `guest`.*, `guest`.`id` AS `gid`, `users`.`rights`, `users`.`last_visit`, `users`.`sex`, `users`.`status`, `users`.`join_date`, `users`.`id`
            FROM `guest` LEFT JOIN `users` ON `guest`.`user_id` = `users`.`id`
            WHERE `guest`.`adm`='1'
            ORDER BY `time` DESC" . Vars::db_pagination()
        );
    } else {
        // Запрос для обычной Гастивухи
        $STH = DB::PDO()->query("
            SELECT `guest`.*, `guest`.`id` AS `gid`, `users`.`rights`, `users`.`last_visit`, `users`.`sex`, `users`.`status`, `users`.`join_date`, `users`.`id`
            FROM `guest` LEFT JOIN `users` ON `guest`.`user_id` = `users`.`id`
            WHERE `guest`.`adm`='0'
            ORDER BY `time` DESC" . Vars::db_pagination()
        );
    }

    for ($i = 0; $result = $STH->fetch(); ++$i) {
        // Время создания поста
        $text = ' <span class="gray">(' . Functions::displayDate($result['time']) . ')</span>';
        if ($result['user_id']) {
            // Для зарегистрированных показываем ссылки и смайлы
            $post = Validate::checkout($result['text'], 1, 1);
            if (Vars::$USER_SET['smilies']) {
                $post = Functions::smilies($post, $result['rights'] >= 1 ? 1 : 0);
            }
        } else {
            // Для гостей обрабатываем имя и фильтруем ссылки
            $result['nickname'] = Validate::checkout($result['nickname']);
            $post = Functions::antiLink(Validate::checkout($result['text'], 0, 2));
        }
        if ($result['edit_count']) {
            // Если пост редактировался, показываем кем и когда
            $post .= '<br /><span class="gray"><small>Изм. <b>' . $result['edit_who'] . '</b> (' . Functions::displayDate($result['edit_time']) . ') <b>[' . $result['edit_count'] . ']</b></small></span>';
        }
        if (!empty($result['otvet'])) {
            // Ответ Администрации
            $reply = Validate::checkout($result['otvet'], 1, 1);
            if (Vars::$USER_SET['smilies']) {
                $reply = Functions::smilies($reply, 1);
            }
            $post .= '<div class="reply"><b>' . $result['admin'] . '</b>: (' . Functions::displayDate($result['otime']) . ')<br/>' . $reply . '</div>';
        }
        $arg = array(
            'header' => $text,
            'body'   => $post
        );
        if (Vars::$USER_RIGHTS >= 6) {
            $menu = array(
                '<a href="' . $url . '?act=reply&amp;id=' . $result['gid'] . ($mod ? '&amp;mod=adm' : '') . '">' . __('reply') . '</a>',
                (Vars::$USER_RIGHTS >= $result['rights'] ? '<a href="' . $url . '?act=edit&amp;id=' . $result['gid'] . ($mod ? '&amp;mod=adm' : '') . '">' . __('edit') . '</a>' : ''),
                (Vars::$USER_RIGHTS >= $result['rights'] ? '<a href="' . $url . '?act=delpost&amp;id=' . $result['gid'] . ($mod ? '&amp;mod=adm' : '') . '">' . __('delete') . '</a>' : '')
            );
            $arg['sub'] = Functions::displayMenu($menu);
        }
        $tpl->list[$i] = Functions::displayUser($result, $arg);
    }
}

$tpl->mod = $mod;
$tpl->form_token = mt_rand(100, 10000);
$_SESSION['form_token'] = $tpl->form_token;

$tpl->url = $url;
$tpl->contents = $tpl->includeTpl('index');