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

require_once ("../incfiles/head.php");
if (empty($_GET['id']))
{
    echo "Ошибка!<br/><a href='?'>В форум</a><br/>";
    require_once ("../incfiles/end.php");
    exit;
}
$s = intval($_GET['s']);
$typ = mysql_query("SELECT * FROM `forum` WHERE `id`='" . $id . "';");
$ms = mysql_fetch_array($typ);
if ($ms['type'] != "m")
{
    echo 'Ошибка!<br/><a href="?">В форум</a><br/>';
    require_once ('../incfiles/end.php');
    exit;
}
echo '<div class="menu"><b>' . $ms['from'] . '</b><br />';
$text = htmlentities($ms['text'], ENT_QUOTES, 'UTF-8');
$text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
$text = str_replace("\r\n", "<br/>", $text);
$text = tags($text);
$uz = @mysql_query("select `id`, `from`, `rights` FROM `users` where name='" . $ms['from'] . "';");
$mass1 = @mysql_fetch_array($uz);
if ($offsm != 1 && $offgr != 1)
{
    $text = smileys($text, ($ms['from'] == $nickadmina || $ms['from'] == $nickadmina2 || $mass1['rights'] >= 1) ? 1 : 0);
}
echo $text . '</div>';
$q5 = mysql_query("select * from `forum` where type='t' and id='" . $ms['refid'] . "';");
$them = mysql_fetch_array($q5);
$q3 = mysql_query("select `id`, `refid`, `text` from `forum` where type='r' and id='" . $them['refid'] . "';");
$razd = mysql_fetch_array($q3);
$q4 = mysql_query("select `id`, `refid`, `text` from `forum` where type='f' and id='" . $razd['refid'] . "';");
$frm = mysql_fetch_array($q4);
echo "<div>&#187;<a href='index.php?id=" . $ms['refid'] . "&amp;page=" . $s . "'>$them[text]</a><br/>";
echo "&#187;<a href='index.php?id=" . $type1['refid'] . "'>$razd[text]</a><br/>";
echo "&#187;<a href='index.php?id=" . $razd['refid'] . "'>$frm[text]</a><br/>";
echo "&#187;<a href='index.php?'>В форум</a></div>";

?>