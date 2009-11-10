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

if ($dostfmod == 1)
{
    if (empty($_GET['id']))
    {
        require_once ("../incfiles/head.php");
        echo "Ошибка!<br/><a href='?'>В форум</a><br/>";
        require_once ("../incfiles/end.php");
        exit;
    }
    $typ = mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $id . "'");
    $ms = mysql_fetch_array($typ);
    if ($ms['type'] != "m")
    {
        require_once ("../incfiles/head.php");
        echo "Ошибка!<br/><a href='?'>В форум</a><br/>";
        require_once ("../incfiles/end.php");
        exit;
    }
    if (isset($_GET['yes']))
    {
        if ($dostsadm == 1)
        {
            $req1 = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = '$id'");
            if (mysql_num_rows($req1) > 0)
            {
                $res1 = mysql_fetch_array($req1);
                unlink('files/' . $res1['filename']);
                mysql_query("DELETE FROM `cms_forum_files` WHERE `post` = '$id' LIMIT 1");
            }
            mysql_query("DELETE FROM `forum` WHERE `id`='$id' LIMIT 1");
        } else
        {
            // Скрываем пост
            mysql_query("UPDATE `forum` SET `close` = '1' WHERE `id` = '$id'");
            // Скрываем файл
            $req1 = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = '$id'");
            if (mysql_num_rows($req1) > 0)
            {
                mysql_query("UPDATE `cms_forum_files` SET `del` = '1' WHERE `post` = '$id'");
            }
        }
        header("Location: index.php?id=" . $ms['refid'] . "&start=" . $start);
    }
    if (isset($_GET['hid']))
    {
        if ($dostsadm)
        {
            mysql_query("update `forum` set  close='1' where id='$id'");
            // Скрываем файл
            $req1 = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = '$id'");
            if (mysql_num_rows($req1) > 0)
            {
                mysql_query("UPDATE `cms_forum_files` SET `del` = '1' WHERE `post` = '$id'");
            }
        }
        $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $ms['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '$id'"), 0) / $kmess);
        header('Location: index.php?id=' . $ms['refid'] . '&page=' . $page);
    }
    require_once ("../incfiles/head.php");
    echo '<p>Вы действительно хотите удалить пост?</p>';
    echo '<p><a href="?act=delpost&amp;id=' . $id . '&amp;start=' . $start . '&amp;yes">Удалить</a><br />';
    if (($dostsadm == 1) && ($ms['close'] != 1))
    {
        echo '<a href="index.php?act=delpost&amp;id=' . $id . '&amp;start=' . $start . '&amp;hid">Скрыть</a><br />';
    }
    echo '<a href="index.php?id=' . $ms['refid'] . '">Отмена</a></p>';
} else
{
    echo 'Доступ закрыт!!!<br>';
}

?>