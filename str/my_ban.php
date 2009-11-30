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

$headmod = 'userban';
$textl = 'Список нарушений';
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
        $textl = 'Список нарушений: ' . $user['name'];
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

if ($act == "ban")
{
    ////////////////////////////////////////////////////////////
    // Список нарушений                                       //
    ////////////////////////////////////////////////////////////
    require_once ('../incfiles/ban.php');
    echo '<div class="phdr"><b>История нарушений</b></div>';
    echo '<div class="gmenu"><img src="../images/' . ($user['sex'] == 'm' ? 'm' : 'f') . '.gif" alt=""/>&nbsp;<b>' . $user['name'] . '</b> (id: ' . $user['id'] . ')';
    $ontime = $user['lastdate'];
    $ontime2 = $ontime + 300;
    $preg = $user['preg'];
    $regadm = $user['regadm'];
    if ($realtime > $ontime2)
    {
        echo '<font color="#FF0000"> [Off]</font>';
        if ($user['sex'] == "m")
        {
            $lastvisit = 'был: ';
        }
        if ($user['sex'] == "zh")
        {
            $lastvisit = 'была: ';
        }
        $lastvisit = $lastvisit . date("d.m.Y (H:i)", $user['lastdate']);
    } else
    {
        echo '<font color="#00AA00"> [ON]</font>';
    }
    echo '</div>';
    $req = mysql_query("SELECT * FROM `cms_ban_users` WHERE `user_id`='" . $user['id'] . "' ORDER BY `ban_while` DESC;");
    $total = mysql_num_rows($req);
    while ($res = mysql_fetch_array($req))
    {
        echo '<div class="' . ($res['ban_time'] > $realtime ? 'rmenu' : 'menu') . '">';
        echo '<a href="my_ban.php?act=details&amp;id=' . $res['id'] . '">' . date("d.m.Y", $res['ban_while']) . '</a> <b>' . $ban_term[$res['ban_type']] . '</b>';
        echo '</div>';
    }
    echo '<div class="phdr">Всего нарушений: ' . $total . '</div>';
    echo '<p><a href="anketa.php?id=' . $user['id'] . '">В анкету</a></p>';
    require_once ("../incfiles/end.php");
    exit;
}
if ($act == "details")
{
    ////////////////////////////////////////////////////////////
    // Детали отдельного бана                                 //
    ////////////////////////////////////////////////////////////
    require_once ('../incfiles/ban.php');
    $req = mysql_query("SELECT `cms_ban_users`.*, `users`.`name`, `users`.`name_lat`
    FROM `cms_ban_users` LEFT JOIN `users` ON `cms_ban_users`.`user_id` = `users`.`id`
    WHERE `cms_ban_users`.`id`='" . $id . "'");
    if (mysql_num_rows($req) != 0)
    {
        $res = mysql_fetch_array($req);
        echo '<div class="phdr"><b>Бан детально</b></div>';
        if (isset($_GET['ok']))
            echo '<div class="rmenu">Юзер забанен</div>';
        echo '<div class="menu">Ник: <a href="../str/anketa.php?id=' . $res['user_id'] . '"><b>' . $res['name'] . '</b></a></div>';
        echo '<div class="menu">Тип бана: <b>' . $ban_term[$res['ban_type']] . '</b><br />';
        echo $ban_desc[$res['ban_type']] . '</div>';
        echo '<div class="menu">Забанил: ' . $res['ban_who'] . '</div>';
        echo '<div class="menu">Когда: ' . gmdate('d.m.Y, H:i:s', $res['ban_while']) . '</div>';
        echo '<div class="menu">Срок: ' . timecount($res['ban_time'] - $res['ban_while']) . '</div>';
        echo '<div class="bmenu">Причина</div>';
        if (!empty($res['ban_ref']))
            echo '<div class="menu">Нарушение <a href="' . $home . '/forum/index.php?act=post&amp;id=' . $res['ban_ref'] . '">на форуме</a></div>';
        if (!empty($res['ban_reason']))
            echo '<div class="menu">' . $res['ban_reason'] . '</div>';
        echo '<div class="phdr">Осталось: ' . timecount($res['ban_time'] - $realtime) . '</div><p>';
        if ($dostkmod == 1 && $res['ban_time'] > $realtime)
            echo '<div><a href="../' . $admp . '/zaban.php?do=razban&amp;id=' . $id . '">Разбанить</a></div>';
        if ($dostadm == 1)
            echo '<div><a href="../' . $admp . '/zaban.php?do=delban&amp;id=' . $id . '">Удалить бан</a></div>';
        echo '</p><p><a href="my_ban.php?act=ban&amp;id=' . $res['user_id'] . '">Назад</a></p>';
    } else
    {
        echo 'Ошибка';
    }
    require_once ("../incfiles/end.php");
    exit;
}

require_once ('../incfiles/end.php');

?>