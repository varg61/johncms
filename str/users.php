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

$headmod = 'users';
$textl = 'Юзеры';
require_once("../incfiles/core.php");
require_once('../incfiles/head.php');
echo '<div class="phdr"><b>Список пользователей</b></div>';
$req = mysql_query("SELECT COUNT(*) FROM `users`");
$total = mysql_result($req, 0);
$req = mysql_query("SELECT `id`, `name`, `sex`, `lastdate`, `datereg`, `status`, `rights`, `ip`, `browser`, `rights` FROM `users` WHERE `preg` = 1 ORDER BY `datereg` DESC LIMIT $start, $kmess");
while ($res = mysql_fetch_assoc($req)) {
    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
    echo show_user($res) . '</div>';
    ++$i;
}
echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div><p>';
if ($total > $kmess) {
    echo '<p>' . pagenav('users.php?', $start, $total, $kmess) . '</p>';
    echo '<p><form action="users.php" method="post"><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
}
echo '<a href="users_search.php">Поиск пользователя</a><br /><a href="' . $_SESSION['refsm'] . '">Назад</a></p>';

require_once('../incfiles/end.php');

?>