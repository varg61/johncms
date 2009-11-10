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
require_once ('../incfiles/head.php');

if ($act == "ban")
{
    ////////////////////////////////////////////////////////////
    // Список нарушений                                       //
    ////////////////////////////////////////////////////////////
    require_once ('../incfiles/ban.php');
    echo '<div class="phdr">История нарушений</div>';
    echo '<div class="gmenu"><img src="../images/' . ($arr['sex'] == 'm' ? 'm' : 'f') . '.gif" alt=""/>&nbsp;<b>' . $arr['name'] . '</b> (id: ' . $arr['id'] . ')';
    $ontime = $arr['lastdate'];
    $ontime2 = $ontime + 300;
    $preg = $arr['preg'];
    $regadm = $arr['regadm'];
    if ($realtime > $ontime2)
    {
        echo '<font color="#FF0000"> [Off]</font>';
        if ($arr['sex'] == "m")
        {
            $lastvisit = 'был: ';
        }
        if ($arr['sex'] == "zh")
        {
            $lastvisit = 'была: ';
        }
        $lastvisit = $lastvisit . date("d.m.Y (H:i)", $arr['lastdate']);
    } else
    {
        echo '<font color="#00AA00"> [ON]</font>';
    }
    echo '</div>';
    $req = mysql_query("SELECT * FROM `cms_ban_users` WHERE `user_id`='" . $user . "' ORDER BY `ban_while` DESC;");
    $total = mysql_num_rows($req);
    while ($res = mysql_fetch_array($req))
    {
        echo '<div class="' . ($res['ban_time'] > $realtime ? 'rmenu' : 'menu') . '">';
        echo '<a href="anketa.php?act=bandet&amp;id=' . $res['id'] . '">' . date("d.m.Y", $res['ban_while']) . '</a> <b>' . $ban_term[$res['ban_type']] . '</b>';
        echo '</div>';
    }
    echo '<div class="bmenu">Всего нарушений: ' . $total . '</div>';
    echo '<p><a href="anketa.php?user=' . $user . '">В анкету</a></p>';
    require_once ("../incfiles/end.php");
    exit;
}
if ($act == "bandet")
{
    ////////////////////////////////////////////////////////////
    // Детали отдельного бана                                 //
    ////////////////////////////////////////////////////////////
    require_once ('../incfiles/ban.php');
    $id = isset($_GET['id']) ? intval($_GET['id']) : '';
    $req = mysql_query("SELECT `cms_ban_users`.*, `users`.`name`, `users`.`name_lat`
			FROM `cms_ban_users` LEFT JOIN `users` ON `cms_ban_users`.`user_id` = `users`.`id`
			WHERE `cms_ban_users`.`id`='" . $id . "';");
    if (mysql_num_rows($req) != 0)
    {
        $res = mysql_fetch_array($req);
        echo '<div class="phdr">Бан детально</div>';
        if (isset($_GET['ok']))
            echo '<div class="rmenu">Юзер забанен</div>';
        echo '<div class="menu">Ник: <a href="../str/anketa.php?user=' . $res['user_id'] . '"><b>' . $res['name'] . '</b></a></div>';
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
        echo '<div class="bmenu">Осталось: ' . timecount($res['ban_time'] - $realtime) . '</div><p>';
        if ($dostkmod == 1 && $res['ban_time'] > $realtime)
            echo '<div><a href="../' . $admp . '/zaban.php?do=razban&amp;id=' . $id . '">Разбанить</a></div>';
        if ($dostadm == 1)
            echo '<div><a href="../' . $admp . '/zaban.php?do=delban&amp;id=' . $id . '">Удалить бан</a></div>';
        echo '</p><p><a href="anketa.php?act=ban&amp;user=' . $res['user_id'] . '">Назад</a></p>';
    } else
    {
        echo 'Ошибка';
    }
    require_once ("../incfiles/end.php");
    exit;
}

require_once ('../incfiles/end.php');

?>