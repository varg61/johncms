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

if (empty($_GET['id']))
{
    require_once ("../incfiles/head.php");
    echo "Ошибка!<br/><a href='index.php'>В форум</a><br/>";
    require_once ("../incfiles/end.php");
    exit;
}
if (!$user_id || $ban['1'] || $ban['11'])
{
    require_once ("../incfiles/head.php");
    echo "Вы не авторизованы!<br/>";
    require_once ("../incfiles/end.php");
    exit;
}

// Проверка на спам
$old = ($rights > 0 || $dostsadm = 1) ? 10 : 30;
if ($lastpost > ($realtime - $old))
{
    require_once ("../incfiles/head.php");
    echo '<p><b>Антифлуд!</b><br />Вы не можете так часто писать<br/>Порог ' . $old . ' секунд<br/><br/><a href="?id=' . $id . '&amp;start=' . $start . '">Назад</a></p>';
    require_once ("../incfiles/end.php");
    exit;
}

$type = mysql_query("SELECT * FROM `forum` WHERE `id` = '$id'");
$type1 = mysql_fetch_array($type);
$tip = $type1['type'];
if ($tip != "r")
{
    require_once ("../incfiles/head.php");
    echo "Ошибка!<br/><a href='?'>В форум</a><br/>";
    require_once ("../incfiles/end.php");
    exit;
}
if (isset($_POST['submit']))
{
    if (empty($_POST['th']))
    {
        require_once ("../incfiles/head.php");
        echo "Вы не ввели название темы!<br/><a href='index.php?act=nt&amp;id=" . $id . "'>Повторить</a><br/>";
        require_once ("../incfiles/end.php");
        exit;
    }
    if (empty($_POST['msg']))
    {
        require_once ("../incfiles/head.php");
        echo "Вы не ввели сообщение!<br/><a href='index.php?act=nt&amp;id=" . $id . "'>Повторить</a><br/>";
        require_once ("../incfiles/end.php");
        exit;
    }
    $th = mb_substr($th, 0, 100);
    $th = check(trim($_POST['th']));
    $msg = mysql_real_escape_string(trim($_POST['msg']));
    if ($_POST['msgtrans'] == 1)
    {
        $th = trans($th);
        $msg = trans($msg);
    }
    $pt = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `refid` = '$id' AND `text` = '$th'");
    if (mysql_result($pt, 0) > 0)
    {
        require_once ("../incfiles/head.php");
        echo '<p>ОШИБКА!<br />Тема с таким названием уже есть в этом разделе<br/><a href="index.php?act=nt&amp;id=' . $id . '">Повторить</a></p>';
        require_once ("../incfiles/end.php");
        exit;
    }
    if ($set['fmod'] != 1)
    {
        $fmd = 1;
    } else
    {
        $fmd = 0;
    }
    // Добавляем заголовок темы
    mysql_query("INSERT INTO `forum` SET
	`refid` = '$id',
	`type` = 't',
	`time` = '$realtime',
	`from` = '$login',
	`text` = '$th',
	`moder` = '$fmd'");
    $rid = mysql_insert_id();
    $agn = strtok($agn, ' ');
    // Добавляем текст поста
    mysql_query("INSERT INTO `forum` SET
	`refid` = '$rid',
	`type` = 'm',
	`time` = '$realtime',
	`from` = '$login',
	`ip` = '$ipp',
	`soft` = '$agn',
	`text` = '$msg'");
    $postid = mysql_insert_id();
    // Записываем счетчик постов юзера
    $fpst = $datauser['postforum'] + 1;
    mysql_query("UPDATE `users` SET  `postforum` = '$fpst', `lastpost` = '$realtime' WHERE `id` = '$user_id'");
    // Ставим метку о прочтении
    mysql_query("INSERT INTO `cms_forum_rdm` SET  `topic_id`='$rid', `user_id`='$user_id', `time`='$realtime'");
    $addfiles = intval($_POST['addfiles']);
    if ($addfiles == 1)
    {
        header("Location: index.php?id=$postid&act=addfile");
    } else
    {
        header("Location: index.php?id=" . ($set['fmod'] != 1 ? $rid : $id));
    }
} else
{
    require_once ("../incfiles/head.php");
    if ($datauser['postforum'] == 0)
    {
        if (!isset($_GET['yes']))
        {
            include ("../pages/forum.txt");
            echo "<a href='index.php?act=nt&amp;id=" . $id . "&amp;yes'>Согласен</a>|<a href='index.php?id=" . $id . "'>Не согласен</a><br/>";
            require_once ("../incfiles/end.php");
            exit;
        }
    }
    if ($set['fmod'] == 1)
    {
        echo "Внимание!В данный момент в форуме включена премодерация тем,то есть Ваша тема будет открыта для общего доступа только после проверки модератором.<br/>";
    }
    echo "Добавление темы в раздел <font color='" . $cntem . "'>$type1[text]</font>:<br/><form action='index.php?act=nt&amp;id=" . $id .
        "' method='post' enctype='multipart/form-data'>Название(max. 100):<br/><input type='text' size='20' maxlength='100' title='Введите название темы' name='th'/><br/>Сообщение(max. 500):<br/><textarea cols='20' rows='3' title='Введите сообщение' name='msg'></textarea><br/><input type='checkbox' name='addfiles' value='1' /> Добавить файл<br/>";
    if ($offtr != 1)
    {
        echo "<input type='checkbox' name='msgtrans' value='1' /> Транслит сообщения
      <br/>";
    }
    echo "<input type='submit' name='submit' title='Нажмите для отправки' value='Отправить'/><br/></form>";
    echo "<a href='index.php?act=trans'>Транслит</a><br /><a href='../str/smile.php'>Смайлы</a><br/>";
    echo "<a href='?id=" . $id . "'>Назад</a><br/>";
}

?>