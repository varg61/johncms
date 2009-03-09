<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                                                                    //
// Официальный сайт сайт проекта:      http://johncms.com                     //
// Дополнительный сайт поддержки:      http://gazenwagen.com                  //
////////////////////////////////////////////////////////////////////////////////
// JohnCMS core team:                                                         //
// Евгений Рябинин aka john77          john77@johncms.com                     //
// Олег Касьянов aka AlkatraZ          alkatraz@johncms.com                   //
//                                                                            //
// Информацию о версиях смотрите в прилагаемом файле version.txt              //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);

$textl = 'Новости ресурса';
$headmod = "news";
require_once ("../incfiles/core.php");
require_once ("../incfiles/head.php");

echo '<div class="phdr">Новости ресурса</div>';
$req = mysql_query("SELECT COUNT(*) FROM `news`");
$total = mysql_result($req, 0);
$req = mysql_query("SELECT * FROM `news` ORDER BY `time` DESC LIMIT " . $start . "," . $kmess . ";");
while ($nw1 = mysql_fetch_array($req))
{
    echo ceil(ceil($i / 2) - ($i / 2)) == 0 ? '<div class="list1">' : '<div class="list2">';
    $text = $nw1['text'];
    $text = tags($text);
    if ($offsm != 1 && $offgr != 1)
    {
        $text = smiles($text);
        $text = smilescat($text);
        $text = smilesadm($text);
    }
    $vr = $nw1['time'] + $sdvig * 3600;
    $vr1 = date("d.m.y / H:i", $vr);
    echo '<b>' . $nw1['name'] . '</b><br/>' . $text . '<div class="func"><font color="#999999">Добавил: ' . $nw1['avt'] . ' (' . $vr1 . ')</font><br/>';
    if ($nw1['kom'] != 0 && $nw1['kom'] != "")
    {
        $mes = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '" . $nw1['kom'] . "'");
        $komm = mysql_result($mes, 0) - 1;
        echo '<a href="../forum/?id=' . $nw1['kom'] . '">Обсудить на форуме (' . $komm . ')</a><br/>';
    }
    echo '</div></div>';
    ++$i;
}
echo '<div class="phdr">Всего:&nbsp;' . $total . '</div>';
echo '<p>';
if ($total > $kmess)
{
    echo '<p>' . pagenav('news.php?', $start, $total, $kmess) . '</p>';
    echo '<p><form action="index.php" method="get"><input type="hidden" name="act" value="new"/><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
}
echo '</p>';
require_once ("../incfiles/end.php");

?>