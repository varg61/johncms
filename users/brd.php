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
$headmod = 'birth';
require('../incfiles/core.php');
$textl = $lng['birthday_men'];
require('../incfiles/head.php');

/*
-----------------------------------------------------------------
Выводим список именинников
-----------------------------------------------------------------
*/
echo '<div class="phdr"><b>' . $lng['birthday_men'] . '</b></div>';
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `dayb` = '$day' AND `monthb` = '$mon' AND `preg` = '1'"), 0);
if ($total) {
    $req = mysql_query("SELECT * FROM `users` WHERE `dayb` = '$day' AND `monthb` = '$mon' AND `preg` = '1' LIMIT $start, $kmess");
    while ($res = mysql_fetch_assoc($req)) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        echo display_user($res) . '</div>';
        ++$i;
    }
    echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
    if ($total > $kmess) {
        echo '<p>' . display_pagination('brd.php?', $start, $total, $kmess) . '</p>';
        echo '<p><form action="brd.php" method="post">' .
            '<input type="text" name="page" size="2"/>' .
            '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
            '</form></p>';
    }
} else {
    echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
}

require('../incfiles/end.php');
?>