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

require_once ('../incfiles/head.php');

// Выводим сообщение Админу при закрытом чате
if (!$set['mod_chat'])
    echo '<p><font color="#FF0000"><b>Чат закрыт!</b></font></p>';

echo '<div class="phdr"><b>' . $lng['chat'] . '</b></div>';
$_SESSION['intim'] = '';
$q = mysql_query("select * from `chat` where type='r' order by realid ;");
while ($mass = mysql_fetch_array($q))
{
    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
    echo '<a href="index.php?id=' . $mass['id'] . '">' . $mass['text'] . '</a> (' . wch($mass['id']) . ')</div>';
    ++$i;
}
echo '<div class="phdr"><a href="who.php">Кто в чате</a> (' . wch() . ')</div>';
echo '<p><a href="index.php?act=moders&amp;id=' . $id . '">Модераторы</a><br/>';
echo '<a href="../str/usset.php?act=chat">Настройки чата</a></p>';
require_once ('../incfiles/end.php');

?>