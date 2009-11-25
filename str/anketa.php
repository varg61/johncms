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

$headmod = 'anketa';
require_once ('../incfiles/core.php');

if (!$user_id)
{
    require_once ('../incfiles/head.php');
    display_error('Только для зарегистрированных посетителей');
    require_once ('../incfiles/end.php');
    exit;
}

if ($id && $id != $user_id)
{
    // Если был запрос на юзера, то получаем его данные
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
    if (mysql_num_rows($req))
    {
        $user = mysql_fetch_assoc($req);
        $textl = 'Анкета: ' . $user['name'];
    } else
    {
        require_once ('../incfiles/head.php');
        echo display_error('Такого пользователя не существует');
        require_once ("../incfiles/end.php");
        exit;
    }
} else
{
    $textl = 'Личная анкета';
    $user = $datauser;
}

require_once ('../incfiles/head.php');

////////////////////////////////////////////////////////////
// Выводим анкету пользователя                            //
////////////////////////////////////////////////////////////
echo '<div class="phdr"><b>' . ($id ? 'Анкета пользователя' : 'Моя анкета') . '</b></div>';
if ($user['dayb'] == $day && $user['monthb'] == $mon)
{
    echo '<div class="gmenu">ИМЕНИНЫ!!!</div>';
}
echo '<div class="gmenu"><p><h3><img src="../theme/' . $set_user['skin'] . '/images/' . ($user['sex'] == 'm' ? 'm' : 'f') . ($user['datereg'] > $realtime - 86400 ? '_new.gif" width="20"' : '.gif" width="16"') .
    ' height="16" class="left"/>&nbsp;';
echo '<b>' . $user['name'] . '</b> (id: ' . $user['id'] . ')';
if ($realtime > $user['lastdate'] + 300)
{
    echo '<span class="red"> [Off]</span>';
    $lastvisit = date("d.m.Y (H:i)", $user['lastdate']);
} else
{
    echo '<span class="green"> [ON]</span>';
}
echo '</h3><ul>';
if (!empty($user['status']))
    echo '<li><span class="gray"><u>Статус</u>: </span>' . $user['status'] . '</li>';
echo '<li><span class="gray"><u>Логин</u>:</span> <b>' . $user['name_lat'] . '</b></li>';
if ($user['rights'] != 0)
{
    echo '<li><span class="gray"><u>Должность</u>:</span> ';
    switch ($datauser['rights'])
    {
        case 1:
            echo 'Киллер';
            break;
        case 2:
            echo 'Модер чата';
            break;
        case 3:
            echo 'Модер форума';
            break;
        case 4:
            echo 'Зам. админа по загрузкам';
            break;
        case 5:
            echo 'Зам. админа по библиотеке';
            break;
        case 6:
            echo 'Супермодератор';
            break;
        case 7:
            echo 'Админ';
            break;
    }
    echo '</li>';
}
if (isset($lastvisit))
    echo '<li><span class="gray"><u>Последний визит</u>:</span> ' . $lastvisit . '</li>';
if ($dostmod)
{
    echo '<li><span class="gray"><u>UserAgent</u>:</span> ' . $user['browser'] . '</li>';
    echo '<li><span class="gray"><u>Адрес IP</u>:</span> ' . long2ip($user['ip']) . '</li>';
    if ($user['immunity'])
        echo '<li><span class="green"><b>ИММУНИТЕТ</b></span></li>';
}
echo '</ul></p></div><div class="menu">';
// Личные данные
$out = '';
$req = mysql_query("select * from `gallery` where `type`='al' and `user`=1 and `avtor`='" . $user['name'] . "' LIMIT 1;");
if (mysql_num_rows($req))
{
    $res = mysql_fetch_array($req);
    $out .= '<li><a href="../gallery/index.php?id=' . $res['id'] . '">Личный альбом</a></li>';
}
if (!empty($user['imname']))
    $out .= '<li><span class="gray"><u>Имя</u>:</span> ' . $user['imname'] . '</li>';
if (!empty($user['dayb']))
    $out .= '<li><span class="gray"><u>Дата рождения</u>:</span> ' . $user['dayb'] . '&nbsp;' . $mesyac[$user['monthb']] . '&nbsp;' . $user['yearofbirth'] . '</li>';
if (!empty($user['live']))
    $out .= '<li><span class="gray"><u>Город</u>:</span> ' . $user['live'] . '</li>';
if (!empty($user['about']))
    $out .= '<li><span class="gray"><u>О себе</u>:<br /></span> ' . smileys(tags($user['about'])) . '</li>';
if (!empty($out))
{
    echo '<p><h3><img src="../images/contacts.png" width="16" height="16" class="left" />&nbsp;Личные данные</h3><ul>';
    echo $out;
    echo '</ul></p>';
}
// Связь
$out = '';
if (!empty($user['mibile']))
    $out .= '<li><span class="gray"><u>Тел. номер</u>:</span> ' . $user['mibile'] . '</li>';
if ($user['mailact'] == 1)
{
    if (!empty($user['mail']))
    {
        $out .= '<li><span class="gray"><u>E-mail</u>:</span> ';
        if ($user['mailvis'] == 1)
        {
            $out .= $user['mail'] . '</li>';
        } else
        {
            $out .= 'скрыт</li>';
        }
    }
}
if (!empty($user['icq']))
    $out .= '<li><span class="gray"><u>ICQ</u>:</span>&nbsp;<img src="http://web.icq.com/whitepages/online?icq='.$user['icq'].'&amp;img=5" width="18" height="18" alt="icq" align="middle"/>&nbsp;' . $user['icq'] . '</li>';
if (!empty($user['skype']))
    $out .= '<li><span class="gray"><u>Skype</u>:</span>&nbsp;' . $user['skype'] . '</li>';
if (!empty($user['jabber']))
    $out .= '<li><span class="gray"><u>Jabber</u>:</span>&nbsp;' . $user['jabber'] . '</li>';
if (!empty($user['www']))
    $out .= '<li><span class="gray"><u>Сайт</u>:</span> ' . tags($user['www']) . '</li>';
if (!empty($out))
{
    echo '<p><h3><img src="../images/mail.png" width="16" height="16" class="left" />&nbsp;Связь</h3><ul>';
    echo $out;
    echo '</ul></p>';
}
// Статистика
echo '<p><h3><img src="../images/rate.gif" width="16" height="16" class="left" />&nbsp;Статистика</h3><ul>';
if ($dostadm)
{
    if (!$user['preg'] && empty($user['regadm']))
        echo '<li>Ожидает подтверждения регистрации</li>';
    elseif (!$user['preg'] && !empty($user['regadm']))
        echo '<li>Регистрацию отклонил ' . $user['regadm'] . '</li>';
    elseif ($user['preg'] && !empty($user['regadm']))
        echo '<li>Регистрацию подтвердил ' . $user['regadm'] . '</li>';
    else
        echo '<li>Свободная регистрация</li>';
}
echo '<li><span class="gray"><u>' . ($user['sex'] == 'm' ? 'Зарегистрирован' : 'Зарегистрирована') . '</u>:</span> ' . date("d.m.Y", $user['datereg']) . '</li>';
echo '<li><span class="gray"><u>' . ($user['sex'] == 'm' ? 'Пробыл' : 'Пробыла') . ' на сайте</u>:</span> ' . timecount($user['total_on_site']) . '</li>';
echo '<li><a href="my_stat.php?id=' . $user['id'] . '">Статистика активности</a></li>';
echo '<li><a href="my_stat.php?act=forum' . ($id ? '&amp;id=' . $id : '') . '">Последние записи</a></li>';
// Если были нарушения, показываем ссылку на их историю
$ban = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '" . $user['id'] . "'"), 0);
if ($ban)
    echo '<li><a href="my_ban.php?act=ban">Нарушения</a>&nbsp;<span class="red">(' . $ban . ')</span></li>';
echo '</ul></p></div>';
echo '<div class="phdr">' . (!$id || $id == $user_id || $dostadm ? '<a href="my_data.php' . ($id ? '?id=' . $id : '') . '">Редактировать</a>' : '&nbsp;');
if ($dostmod)
{
    if ($id && !$user['immunity'] && $id != $user_id)
    {
        if ($dostsmod == 1)
        {
            echo ' | <a href="../' . $admp . '/zaban.php?do=ban&amp;id=' . $user['id'] . '">Банить</a>';
        } elseif ($dostfmod == 1 && isset($_GET['fid']))
        {
            $fid = intval($_GET['fid']);
            echo '<a href="../' . $admp . '/zaban.php?do=ban&amp;id=' . $user['id'] . '&amp;fid=' . $fid . '">Пнуть из форума</a><br/>';
        }
        if ($dostadm)
        {
            echo ' | <a href="../' . $admp . '/editusers.php?act=del&amp;user=' . $user['id'] . '">Удалить</a><br/>';
        }
    }
}
echo '</div>';
if ($id && $id != $user_id)
{
    echo '<p>';
    // Контакты
    $contacts = mysql_query("select * from `privat` where me='" . $login . "' and cont='" . $user['name'] . "'");
    $conts = mysql_num_rows($contacts);
    if ($conts != 1)
        echo "<a href='cont.php?act=edit&amp;id=" . $id . "&amp;add=1'>Добавить в контакты</a><br/>";
    else
        echo "<a href='cont.php?act=edit&amp;id=" . $id . "'>Удалить из контактов</a><br/>";
    // Игнор
    $igns = mysql_query("select * from `privat` where me='" . $login . "' and ignor='" . $user['name'] . "'");
    $ignss = mysql_num_rows($igns);
    if ($igns != 1)
    {
        if ($user['rights'] == 0 && $user['name'] != $nickadmina && $user['name'] != $nickadmina)
        {
            echo "<a href='ignor.php?act=edit&amp;id=" . $id . "&amp;add=1'>Добавить в игнор</a><br/>";
        }
    } else
    {
        echo "<a href='ignor.php?act=edit&amp;id=" . $id . "'>Удалить из игнора</a><br/>";
    }
    echo "<a href='pradd.php?act=write&amp;adr=" . $user['id'] . "'>Написать в приват</a></p>";
}

require_once ('../incfiles/end.php');

?>