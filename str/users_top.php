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

$headmod = 'sitetop';
$textl = 'Топ активности сайта';
require_once ('../incfiles/core.php');
require_once ('../incfiles/head.php');

function get_top($mod = 'forum')
{
    global $set_user, $realtime;
    $i = 1;
    switch ($mod)
    {
        case 'forum':
            $order = 'postforum';
            break;
        case 'guest':
            $order = 'postguest';
            break;
        case 'chat':
            $order = 'postchat';
            break;
        case 'vic':
            $order = 'otvetov';
            break;
        case 'bal':
            $order = 'balans';
            break;
        case 'kom':
            $order = 'komm';
            break;
    }
    $req = mysql_query("SELECT * FROM `users` WHERE `$order` > 0 ORDER BY `$order` DESC LIMIT 9");
    if (mysql_num_rows($req))
    {
        $out = '';
        while ($res = mysql_fetch_assoc($req))
        {
            $out .= (($i % 2) ? '<div class="list2">' : '<div class="list1">') . ($i > 3 ? '<span class="gray">' : '<span class="green">') . '<b>' . $i . '</b></span>&nbsp;';
            if ($res['sex'])
                $out .= '<img src="../theme/' . $set_user['skin'] . '/images/' . ($res['sex'] == 'm' ? 'm' : 'f') . ($res['datereg'] > $realtime - 86400 ? '_new.gif" width="20"' : '.gif" width="16"') . ' height="16"/>&nbsp;';
            else
                $out .= '<img src="../images/del.png" width="12" height="12" />&nbsp;';
            $out .= '<a href="../str/anketa.php?user=' . $res['id'] . '"><b>' . $res['name'] . '</b></a> ';
            $user_rights = array(1 => 'Kil', 3 => 'Mod', 6 => 'Smd', 7 => 'Adm', 8 => 'SV');
            $out .= $user_rights[$res['rights']];
            $out .= ($realtime > $res['lastdate'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>');
            $out .= ' (' . $res[$order] . ')</div>';
            ++$i;
        }
        return $out;
    } else
    {
        return '<div class="menu"><p>Список пуст</p></div>';
    }
}

////////////////////////////////////////////////////////////
// Показываем топ                                         //
////////////////////////////////////////////////////////////
switch ($act)
{
    case 'guest':
        echo '<p><a href="users_top.php?act=forum">Форум</a> | Гостевая | <a href="users_top.php?act=chat">Чат</a> | <a href="users_top.php?act=vic">Викторина</a> | <a href="users_top.php?act=bal">Баланс</a> | <a href="users_top.php?act=kom">Комментарии</a></p>';
        echo '<div class="phdr"><b>Самые активные в Гостевой</b></div>';
        echo get_top('guest');
        echo '<div class="phdr"><a href="../str/guest.php">В Гостевую</a></div>';
        break;
    case 'chat':
        echo '<p><a href="users_top.php?act=forum">Форум</a> | <a href="users_top.php?act=guest">Гостевая</a> | Чат | <a href="users_top.php?act=vic">Викторина</a> | <a href="users_top.php?act=bal">Баланс</a> | <a href="users_top.php?act=kom">Комментарии</a></p>';
        echo '<div class="phdr"><b>Самые активные в Чате</b></div>';
        echo get_top('chat');
        echo '<div class="phdr"><a href="../chat/index.php">В Чат</a></div>';
        break;
    case 'vic':
        echo '<p><a href="users_top.php?act=forum">Форум</a> | <a href="users_top.php?act=guest">Гостевая</a> | <a href="users_top.php?act=chat">Чат</a> | Викторина | <a href="users_top.php?act=bal">Баланс</a> | <a href="users_top.php?act=kom">Комментарии</a></p>';
        echo '<div class="phdr"><b>Лучшие &quot;умники&quot; Викторины</b></div>';
        echo get_top('vic');
        echo '<div class="phdr"><a href="../chat/index.php">В Чат</a></div>';
        break;
    case 'bal':
        echo '<p><a href="users_top.php?act=forum">Форум</a> | <a href="users_top.php?act=guest">Гостевая</a> | <a href="users_top.php?act=chat">Чат</a> | <a href="users_top.php?act=vic">Викторина</a> | Баланс | <a href="users_top.php?act=kom">Комментарии</a></p>';
        echo '<div class="phdr"><b>Самые большие игровые Балансы</b></div>';
        echo get_top('bal');
        echo '<div class="phdr"><a href="../index.php">На Главную</a></div>';
        break;
    case 'kom':
        echo '<p><a href="users_top.php?act=forum">Форум</a> | <a href="users_top.php?act=guest">Гостевая</a> | <a href="users_top.php?act=chat">Чат</a> | <a href="users_top.php?act=vic">Викторина</a> | <a href="users_top.php?act=bal">Баланс</a> | Комментарии</p>';
        echo '<div class="phdr"><b>Больше всего комментировали</b></div>';
        echo get_top('kom');
        echo '<div class="phdr"><a href="../index.php">На Главную</a></div>';
        break;
    default:
        echo '<p>Форум | <a href="users_top.php?act=guest">Гостевая</a> | <a href="users_top.php?act=chat">Чат</a> | <a href="users_top.php?act=vic">Викторина</a> | <a href="users_top.php?act=bal">Баланс</a> | <a href="users_top.php?act=kom">Комментарии</a></p>';
        echo '<div class="phdr"><b>Самые активные на Форуме</b></div>';
        echo get_top('forum');
        echo '<div class="phdr"><a href="../forum/index.php">В Форум</a></div>';
}

echo '<p><a href="../index.php?act=users">Актив Сайта</a></p>';
require_once ('../incfiles/end.php');

?>