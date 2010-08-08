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
require('../incfiles/core.php');
$textl = $lng['users'];
require('../incfiles/head.php');

/*
-----------------------------------------------------------------
Выводим список пользователей
-----------------------------------------------------------------
*/
echo '<div class="phdr"><a href="../index.php?act=users"><b>' . $lng['community'] . '</b></a> | ' . $lng['users_list'] . '</div>';
$req = mysql_query("SELECT COUNT(*) FROM `users`");
$total = mysql_result($req, 0);
$req = mysql_query("SELECT `id`, `name`, `sex`, `lastdate`, `datereg`, `status`, `rights`, `ip`, `browser`, `rights` FROM `users` WHERE `preg` = 1 ORDER BY `datereg` DESC LIMIT $start, $kmess");
while ($res = mysql_fetch_assoc($req)) {
    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
    echo display_user($res) . '</div>';
    ++$i;
}
echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
if ($total > $kmess) {
    echo '<p>' . display_pagination('users.php?', $start, $total, $kmess) . '</p>';
    echo '<p><form action="users.php" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
        '</form></p>';
}
echo '<p><a href="users_search.php">' . $lng['search_user'] . '</a><br />' .
    '<a href="../index.php?act=users">' . $lng['back'] . '</a></p>';

require('../incfiles/end.php');
?>