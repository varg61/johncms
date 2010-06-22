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

defined('_IN_JOHNADM') or die('Error: restricted access');

echo '<div class="phdr"><a href="index.php"><b>' . $lng['admin_panel'] . '</b></a> | Список должностных лиц</div>';
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '9' ORDER BY `name` ASC");
if (mysql_num_rows) {
    echo '<div class="bmenu">Супервайзоры</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo $sw % 2 ? '<div class="list2">' : '<div class="list1">';
        echo display_user($res, array('header' => ('<b>ID:' . $res['id'] . '</b>')));
        echo '</div>';
        ++$sw;
    }
}
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '7' ORDER BY `name` ASC");
if (mysql_num_rows) {
    echo '<div class="bmenu">Администраторы</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo $adm % 2 ? '<div class="list2">' : '<div class="list1">';
        echo display_user($res, array('header' => ('<b>ID:' . $res['id'] . '</b>')));
        echo '</div>';
        ++$adm;
    }
}
$req = mysql_query("SELECT * FROM `users` WHERE `rights` = '6' ORDER BY `name` ASC");
if (mysql_num_rows) {
    echo '<div class="bmenu">Старшие модераторы</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo $smd % 2 ? '<div class="list2">' : '<div class="list1">';
        echo display_user($res, array('header' => ('<b>ID:' . $res['id'] . '</b>')));
        echo '</div>';
        ++$smd;
    }
}
$req = mysql_query("SELECT * FROM `users` WHERE `rights` BETWEEN '1' AND '5' ORDER BY `name` ASC");
if (mysql_num_rows) {
    echo '<div class="bmenu">Модераторы</div>';
    while ($res = mysql_fetch_assoc($req)) {
        echo $mod % 2 ? '<div class="list2">' : '<div class="list1">';
        echo display_user($res, array('header' => ('<b>ID:' . $res['id'] . '</b>')));
        echo '</div>';
        ++$mod;
    }
}
echo '<div class="phdr">' . $lng['total'] . ': ' . ($sw + $adm + $smd + $mod) . '</div>';
echo '<p><a href="index.php?act=usr_list">Список пользователей</a><br /><a href="index.php">' . $lng['admin_panel'] . '</a></p>';

?>