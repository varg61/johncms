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

$headmod = 'users';
$textl = 'Юзеры';
require_once ("../incfiles/core.php");
require_once ("../incfiles/head.php");

echo '<div class="phdr"><b>Список пользователей</b></div>';
$req = mysql_query("SELECT COUNT(*) FROM `users`");
$total = mysql_result($req, 0);
$req = mysql_query("SELECT `id`, `name`, `sex`, `lastdate`, `datereg` FROM `users` WHERE `preg` = 1 ORDER BY `datereg` DESC LIMIT " . $start . "," . $kmess);
while ($res = mysql_fetch_array($req))
{
    echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
    if ($res['sex'])
        echo '<img src="../theme/' . $set_user['skin'] . '/images/' . ($res['sex'] == 'm' ? 'm' : 'f') . ($res['datereg'] > $realtime - 86400 ? '_new.gif" width="20"' : '.gif" width="16"') . ' height="16"/>&nbsp;';
    else
        echo '<img src="../images/del.png" width="12" height="12" />&nbsp;';
    if (!$user_id || $user_id == $res['id'])
    {
        print '<b>' . $res['name'] . '</b>';
    } else
    {
        print "<a href='anketa.php?user=" . $res['id'] . "'>$res[name]</a>";
    }
    switch ($res['rights'])
    {
        case 7:
            echo ' Adm ';
            break;
        case 6:
            echo ' Smd ';
            break;
        case 5:
            echo ' Mod ';
            break;
        case 4:
            echo ' Mod ';
            break;
        case 3:
            echo ' Mod ';
            break;
        case 2:
            echo ' Mod ';
            break;
        case 1:
            echo ' Kil ';
            break;
    }
    $ontime = $res['lastdate'];
    $ontime2 = $ontime + 300;
    if ($realtime > $ontime2)
    {
        echo '<span class="red"> [Off]</span><br/>';
    } else
    {
        echo '<span class="green"> [ON]</span><br/>';
    }
    echo '</div>';
    ++$i;
}
echo '<div class="phdr">Всего: ' . $total . '</div><p>';
if ($total > $kmess)
{
    echo '<p>' . pagenav('users.php?', $start, $total, $kmess) . '</p>';
    echo '<p><form action="users.php" method="get"><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
}
echo '<a href="users_search.php">Поиск пользователя</a><br /><a href="' . $_SESSION['refsm'] . '">Назад</a></p>';

require_once ("../incfiles/end.php");

?>