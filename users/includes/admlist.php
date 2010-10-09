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

defined('_IN_JOHNCMS') or die('Error: restricted access');
$textl = $lng['administration'];
$headmod = "admlist";
require('../incfiles/head.php');

/*
-----------------------------------------------------------------
Выводим список администрации
-----------------------------------------------------------------
*/
echo '<div class="phdr"><a href="index.php"><b>' . $lng['community'] . '</b></a> | ' . $lng['administration'] . '</div>';
$req = mysql_query("SELECT COUNT(*) FROM `users` WHERE `rights` >= 1");
$total = mysql_result($req, 0);
$req = mysql_query("SELECT `id`, `name`, `sex`, `lastdate`, `datereg`, `status`, `rights`, `ip`, `browser`, `rights` FROM `users` WHERE `rights` >= 1 ORDER BY `rights` DESC LIMIT $start, $kmess");
while ($res = mysql_fetch_assoc($req)) {
    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
    echo functions::display_user($res) . '</div>';
    ++$i;
}
echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
if ($total > $kmess) {
    echo '<p>' . functions::display_pagination('index.php?act=admlist&amp;', $start, $total, $kmess) . '</p>' .
        '<p><form action="index.php?act=admlist" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
        '</form></p>';
}
echo '<p><a href="index.php?act=search">' . $lng['search_user'] . '</a><br />' .
    '<a href="index.php">' . $lng['back'] . '</a></p>';
?>