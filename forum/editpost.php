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

defined('_IN_JOHNCMS') or die('Error: restricted access');

require_once ("../incfiles/head.php");
if (!$user_id || !$id)
{
    header('Location: index.php');
    exit;
}

$typ = mysql_query("select * from `forum` where id='" . $id . "';");
$ms = mysql_fetch_array($typ);
if ($ms['type'] != "m")
{
    echo "Ошибка!<br/><a href='?'>В форум</a><br/>";
    require_once ("../incfiles/end.php");
    exit;
}

$lp = mysql_query("select * from `forum` where type='m' and refid='" . $ms['refid'] . "'  order by time desc ;");
while ($arr = mysql_fetch_array($lp))
{
    $idpp[] = $arr['id'];
}
$idpr = $idpp[0];
$tpp = $realtime - 300;
$lp1 = mysql_query("select * from `forum` where id='" . $idpr . "';");
$arr1 = mysql_fetch_array($lp1);
if (($dostfmod != 1) && (($ms['from'] != $login) || ($arr1['id'] != $ms['id']) || ($ms['time'] < $tpp)))
{
    echo "Ошибка!Вероятно,прошло более 5 минут со времени написания поста,или он уже не последний<br/><a href='?id=" . $ms['refid'] . "'>В тему</a><br/>";
    require_once ("../incfiles/end.php");
    exit;
}
if (($dostfmod == 1) || (($arr1['from'] == $login) && ($arr1['id'] == $ms['id']) && ($ms['time'] > $tpp)))
{
    if (isset($_POST['submit']))
    {
        if (empty($_POST['msg']))
        {
            echo "Вы не ввели сообщение!<br/><a href='?act=editpost&amp;id=" . $id . "'>Повторить</a><br/>";
            require_once ("../incfiles/end.php");
            exit;
        }
        $msg = mysql_real_escape_string(trim($_POST['msg']));
        if ($_POST['msgtrans'] == 1)
        {
            $msg = trans($msg);
        }
        mysql_query("UPDATE `forum` SET
        `tedit` = '$realtime',
        `edit` = '$login',
        `kedit` = '" . ($ms['kedit'] + 1) . "',
        `text` = '$msg'
        WHERE `id` = '$id'");
        $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $ms['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '$id'"), 0) / $kmess);
        header('Location: index.php?id=' . $ms['refid'] . '&page=' . $page);
    } else
    {
        echo '<div class="phdr"><b>Изменить сообщение</b></div>';
        echo '<div class="rmenu"><form action="?act=editpost&amp;id=' . $id . '&amp;start=' . $start . '" method="post">';
        echo '<textarea cols="' . $set_forum['farea_w'] . '" rows="' . $set_forum['farea_h'] . '" name="msg">' . htmlentities($ms['text'], ENT_QUOTES, 'UTF-8') . '</textarea><br/>';
        if ($set_user['translit'])
            echo '<input type="checkbox" name="msgtrans" value="1" /> Транслит сообщения<br/>';
        echo "<input type='submit' title='Нажмите для отправки' name='submit' value='Отправить'/></form></div>";
        echo '<div class="phdr"><a href="index.php?act=trans">Транслит</a> | <a href="../str/smile.php">Смайлы</a></div>';
        echo '<p><a href="index.php?id=' . $ms['refid'] . '&amp;start=' . $start . '">Назад</a></p>';
    }
}

?>