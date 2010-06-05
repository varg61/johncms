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
$headmod = 'anketa';
require_once('../incfiles/core.php');
if (!$user_id) {
    require_once('../incfiles/head.php');
    display_error('Только для зарегистрированных посетителей');
    require_once('../incfiles/end.php');
    exit;
}
if ($id && $id != $user_id) {
    // Если был запрос на юзера, то получаем его данные
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
    if (mysql_num_rows($req)) {
        $user = mysql_fetch_assoc($req);
        $textl = 'Анкета: ' . $user['name'];
    } else {
        require_once('../incfiles/head.php');
        echo display_error('Такого пользователя не существует');
        require_once('../incfiles/end.php');
        exit;
    }
} else {
    $id = false;
    $textl = 'Личная анкета';
    $user = $datauser;
}

require_once('../incfiles/head.php');

////////////////////////////////////////////////////////////
// Выводим анкету пользователя                            //
////////////////////////////////////////////////////////////
echo '<div class="phdr">' . ($id ? '<b>Анкета пользователя</b>' : '<a href="../index.php?act=cab"><b>Кабинет</b></a> | Моя анкета') . '</div>';
// Меню анкеты
$menu = array ();
if ($user['id'] == $user_id || ($rights >= 7 && $rights > $user['rights']))
    $menu[] = '<a href="my_data.php?id=' . $user['id'] . '">Редактировать</a>';
if($user['id'] != $user_id && $rights >= 7 && $rights > $user['rights'])
    $menu[] = '<a href="../' . $admp . '/index.php?act=usr_del&amp;id=' . $user['id'] . '">Удалить</a>';
if ($user['id'] != $user_id && $rights > $user['rights'])
    $menu[] = '<a href="users_ban.php?act=ban&amp;id=' . $user['id'] . '">Банить</a>';
if (!empty($menu))
    echo '<div class="topmenu">' . show_menu($menu) . '</div>';
// Уведомление о дне рожденья
if ($user['dayb'] == $day && $user['monthb'] == $mon) {
    echo '<div class="gmenu">ИМЕНИНЫ!!!</div>';
}
// Выводим данные пользователя
echo '<div class="user"><p>';
$arg = array (
    'lastvisit' => 1,
    'iphist' => 1,
    'header' => '<b>ID:' . $user['id'] . '</b>'
);
echo show_user($user, $arg);
echo '</p></div>';

// Блок Кармы
if ($set_karma['on']) {
    if ($user['karma'])
        $exp = explode('|', $user['plus_minus']);
    if ($exp[0] > $exp[1]) {
        $karma = $exp[1] ? ceil($exp[0] / $exp[1]) : $exp[0];
        $images = $karma > 10 ? '2' : '1';
        echo '<div class="gmenu">';
    } else if ($exp[1] > $exp[0]) {
        $karma = $exp[0] ? ceil($exp[1] / $exp[0]) : $exp[1];
        $images = $karma > 10 ? '-2' : '-1';
        echo '<div class="rmenu">';
    } else {
        $images = 0;
        echo '<div class="menu">';
    }
    echo '<table  width="100%"><tr><td width="22" valign="top"><img src="../images/k_' . $images . '.gif"/></td><td>';
    echo '<b>Карма (' . $user['karma'] . ')</b><div class="sub">
   <span class="green"><a href="karma.php?id=' . $id . '&amp;type=1">За (' . $exp[0] . ')</a></span> | <span class="red"><a href="karma.php?id=' . $id . '&amp;type=2">Против (' . $exp[1] . ')</a></span>';
    if ($id) {
        if (!$datauser['karma_off'] && (!$user['rights'] || ($user['rights'] && !$set_karma['adm'])) && $user['ip'] != $datauser['ip']) {
            $sum = mysql_result(mysql_query("SELECT SUM(`points`) FROM `karma_users` WHERE `user_id` = '$user_id' AND `time` >= '" . $datauser['karma_time'] . "'"), 0);
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `user_id` = '$user_id' AND `karma_user` = '$id' AND `time` > '" . ($realtime - 86400) . "'"), 0);
            if ($datauser['postforum'] >= $set_karma['forum'] && $datauser['total_on_site'] >= $set_karma['karma_time'] && ($set_karma['karma_points'] - $sum) > 0 && !$count) {
                echo '<br /><a href="karma.php?act=user&amp;id=' . $id . '">Отдать голос</a>';
            }
        }
    } else {
        $total_karma = mysql_result(mysql_query("SELECT COUNT(*) FROM `karma_users` WHERE `karma_user` = '$user_id' AND `time` > " . ($realtime - 86400)), 0);
        if ($total_karma > 0)
            echo '<br /><a href="karma.php?act=new">Новые отзывы</a> (' . $total_karma . ')';
    }
    echo '</div></td></tr></table></div>';
}

// Личные данные
echo '<div class="list2">';
$out = '';
if (file_exists('../files/users/photo/' . $user['id'] . '_small.jpg'))
    $out .= '<li><a href="../files/users/photo/' . $user['id'] . '.jpg"><img src="../files/users/photo/' . $user['id'] . '_small.jpg" alt="' . $user['name'] . '" border="0" /></a></li>';
$req = mysql_query("select * from `gallery` where `type`='al' and `user`=1 and `avtor`='" . $user['name'] . "' LIMIT 1");
if (mysql_num_rows($req)) {
    $res = mysql_fetch_array($req);
    $out .= '<li><a href="../gallery/index.php?id=' . $res['id'] . '">Личный альбом</a></li>';
}
if (!empty($user['imname']))
    $out .= '<li><span class="gray">Имя:</span> ' . $user['imname'] . '</li>';
if (!empty($user['dayb']))
    $out .= '<li><span class="gray">Дата рождения:</span> ' . $user['dayb'] . '&nbsp;' . $mesyac[$user['monthb']] . '&nbsp;' . $user['yearofbirth'] . '</li>';
if (!empty($user['live']))
    $out .= '<li><span class="gray">Город:</span> ' . $user['live'] . '</li>';
if (!empty($user['about']))
    $out .= '<li><span class="gray">О себе:<br /></span> ' . smileys(tags($user['about'])) . '</li>';
if (!empty($out))
    echo '<p><h3><img src="../images/contacts.png" width="16" height="16" class="left" />&nbsp;Личные данные</h3><ul>' . $out . '</ul></p>';

// Связь
$out = '';
if (!empty($user['mibile']))
    $out .= '<li><span class="gray">Тел. номер:</span> ' . $user['mibile'] . '</li>';
if (!empty($user['mail']) && (($id && $user['mailvis']) || !$id || $rights >= 7)) {
    $out .= '<li><span class="gray">E-mail:</span> ' . $user['mail'];
    $out .= ($user['mailvis'] ? '' : '<span class="gray"> [скрыт]</span>') . '</li>';
}
if (!empty($user['icq']))
    $out .= '<li><span class="gray">ICQ:</span>&nbsp;<img src="http://web.icq.com/whitepages/online?icq=' . $user['icq'] . '&amp;img=5" width="18" height="18" alt="icq" align="middle"/>&nbsp;' . $user['icq'] . '</li>';
if (!empty($user['skype']))
    $out .= '<li><span class="gray">Skype:</span>&nbsp;' . $user['skype'] . '</li>';
if (!empty($user['jabber']))
    $out .= '<li><span class="gray">Jabber:</span>&nbsp;' . $user['jabber'] . '</li>';
if (!empty($user['www']))
    $out .= '<li><span class="gray">Сайт:</span> ' . tags($user['www']) . '</li>';
if (!empty($out)) {
    echo '<p><h3><img src="../images/mail.png" width="16" height="16" class="left" />&nbsp;Связь</h3><ul>';
    echo $out;
    echo '</ul></p>';
}
// Статистика
echo '<p><h3><img src="../images/rate.gif" width="16" height="16" class="left" />&nbsp;Статистика</h3><ul>';
if ($rights >= 7) {
    if (!$user['preg'] && empty($user['regadm']))
        echo '<li>Ожидает подтверждения регистрации</li>';
    elseif (!$user['preg'] && !empty($user['regadm']))
        echo '<li>Регистрацию отклонил ' . $user['regadm'] . '</li>';
    elseif ($user['preg'] && !empty($user['regadm']))
        echo '<li>Регистрацию подтвердил ' . $user['regadm'] . '</li>';
    else
        echo '<li>Свободная регистрация</li>';
}
echo '<li><span class="gray">' . ($user['sex'] == 'm' ? 'Зарегистрирован' : 'Зарегистрирована') . ':</span> ' . date("d.m.Y", $user['datereg']) . '</li>';
echo '<li><span class="gray">' . ($user['sex'] == 'm' ? 'Пробыл' : 'Пробыла') . ' на сайте:</span> ' . timecount($user['total_on_site']) . '</li>';
echo '<li><a href="my_stat.php?id=' . $user['id'] . '">Статистика активности</a></li>';
echo '<li><a href="my_stat.php?act=forum' . ($id ? '&amp;id=' . $id : '') . '">Последние записи</a></li>';
// Если были нарушения, показываем ссылку на их историю
$ban = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $user['id'] . "'"), 0);
if ($ban)
    echo '<li><a href="users_ban.php' . ($id && $id != $user_id ? '?id=' . $user['id'] : '') . '">Нарушения</a>&nbsp;<span class="red">(' . $ban . ')</span></li>';
echo '</ul></p></div>';
$menu = array ();
if ($id && $id != $user_id) {
    $menu[] = '<a href="pradd.php?act=write&amp;adr=' . $user['id'] . '">Написать</a>';
    // Контакты
    $contacts = mysql_query("select * from `privat` where me='" . $login . "' and cont='" . $user['name'] . "'");
    $conts = mysql_num_rows($contacts);
    if ($conts != 1)
        $menu[] = '<a href="cont.php?act=edit&amp;id=' . $id . '&amp;add=1">В контакты</a>';
    // Игнор
    $igns = mysql_query("select * from `privat` where me='" . $login . "' and ignor='" . $user['name'] . "'");
    $ignss = mysql_num_rows($igns);
    if ($igns != 1) {
        if ($user['rights'] == 0 && $user['name'] != $nickadmina && $user['name'] != $nickadmina) {
            $menu[] = '<a href="ignor.php?act=edit&amp;id=' . $id . '&amp;add=1">В игнор</a>';
        }
    }
}
echo '<div class="phdr">' . show_menu($menu) . '</div>';
require_once('../incfiles/end.php');

?>