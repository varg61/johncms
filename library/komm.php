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

defined('_IN_JOHNCMS') or die('Error: restricted access');

if (!$id)
{
    echo '<p>Не выбрана статья<br/><a href="index.php">К категориям</a></p>';
    require_once ('../incfiles/end.php');
    exit;
}
// Запрос имени статьи
$req = mysql_query("SELECT `name` FROM `lib` WHERE `type` = 'bk' AND `id` = '" . $id . "' LIMIT 1");
if (mysql_num_rows($req) != 1)
{
    // если статья не существует, останавливаем скрипт
	echo '<p>Не выбрана статья<br/><a href="index.php">К категориям</a></p>';
    require_once ('../incfiles/end.php');
    exit;
}
$article = mysql_fetch_array($req);
// Запрос числа каментов
$req = mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'komm' AND `refid` = '" . $id . "'");
$countm = mysql_result($req, 0);
echo '<div class="phdr">Комментируем статью:<br /><b>' . $article['name'] . '</b></div>';
if ($user_id && !$ban['1'] && !$ban['10'])
{
    echo '<div class="gmenu"><a href="index.php?act=addkomm&amp;id=' . $id . '">Написать</a></div>';
}
// Запрос списка коментариев
$mess = mysql_query("SELECT * FROM `lib` WHERE `type` = 'komm' AND `refid` = '" . $id . "' ORDER BY `time` DESC LIMIT " . $start . "," . $kmess);
while ($mass = mysql_fetch_array($mess))
{
    echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
    $uz = mysql_query("select * from `users` where name='" . check($mass['avtor']) . "';");
    $mass1 = mysql_fetch_array($uz);
    if ((!empty($_SESSION['uid'])) && ($_SESSION['uid'] != $mass1['id']))
    {
        echo "<a href='anketa.php?user=" . $mass1['id'] . "'>$mass[avtor]</a>";
    } else
    {
        echo $mass['avtor'];
    }
    $vr = $mass['time'] + $sdvig * 3600;
    $vr1 = date("d.m.Y / H:i", $vr);
    switch ($mass1['rights'])
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
        case 1:
            echo ' Kil ';
            break;
    }
    $ontime = $mass1['lastdate'];
    $ontime2 = $ontime + 300;
    if ($realtime > $ontime2)
    {
        echo '<font color="#FF0000"> [Off]</font>';
    } else
    {
        echo '<font color="#00AA00"> [ON]</font>';
    }
    echo "($vr1)<br/>";
    if ($offsm != 1 && $offgr != 1)
    {
        $tekst = smiles($mass['text']);
        $tekst = smilescat($tekst);
        if ($mass['from'] == nickadmina || $mass['from'] == nickadmina2 || $mass1['rights'] >= 1)
        {
            $tekst = smilesadm($tekst);
        }
    } else
    {
        $tekst = $mass['text'];
    }
    echo "$tekst<br/>";
    if ($dostlmod == 1)
    {
        echo long2ip($mass['ip']) . " - $mass[soft]<br/><a href='index.php?act=del&amp;id=" . $mass['id'] . "'>(Удалить)</a>";
    }
    echo '</div>';
    ++$i;
}
echo '<div class="phdr">Всего каментов: ' . $countm . '</div>';
// Навигация по страницам
if ($countm > $kmess)
{
    echo '<p>' . pagenav('index.php?act=komm&amp;id=' . $id . '&amp;', $start, $countm, $kmess) . '</p>';
    echo '<p><form action="index.php" method="get"><input type="hidden" name="act" value="komm"/><input type="hidden" name="id" value="' . $id .
        '"/><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
}

echo '<p><a href="?id=' . $id . '">К статье</a></p>';

?>