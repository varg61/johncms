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

if (!empty($_SESSION['uid']))
{
    if ($_GET['id'] == "")
    {
        echo "Не выбрано фото<br/><a href='index.php'>В галерею</a><br/>";
        require_once ('../incfiles/end.php');
        exit;
    }
    $id = intval($_GET['id']);
    if (isset($_POST['submit']))
    {
        $flt = $realtime - 30;
        $af = mysql_query("select * from `gallery` where type='km' and time>'" . $flt . "' and avtor= '" . $login . "';");
        $af1 = mysql_num_rows($af);
        if ($af1 != 0)
        {
            echo "Антифлуд!Вы не можете так часто добавлять сообщения<br/>Порог 30 секунд<br/><a href='index.php?act=komm&amp;id=" . $id . "'>К комментариям</a><br/>";
            require_once ("../incfiles/end.php");
            exit;
        }
        if ($_POST['msg'] == "")
        {
            echo "Вы не ввели сообщение!<br/><a href='index.php?act=komm&amp;id=" . $id . "'>К комментариям</a><br/>";
            require_once ('../incfiles/end.php');
            exit;
        }
        $msg = check(trim($_POST['msg']));
        if ($_POST['msgtrans'] == 1)
        {
            $msg = trans($msg);
        }
        $msg = mb_substr($msg, 0, 500);
        $agn = strtok($agn, ' ');
        mysql_query("insert into `gallery` values(0,'" . $id . "','" . $realtime . "','km','" . $login . "','" . $msg . "','','','" . $ipp . "','" . $agn . "');");
        if (empty($datauser['komm']))
        {
            $fpst = 1;
        } else
        {
            $fpst = $datauser['komm'] + 1;
        }
        mysql_query("update `users` set  komm='" . $fpst . "' where id='" . intval($_SESSION['uid']) . "';");
        header("Location: index.php?act=komm&id=$id");
    } else
    {
        echo "Напишите комментарий(max.500)<br/><br/><form action='index.php?act=addkomm&amp;id=" . $id . "' method='post'>
Cообщение<br/>
<textarea rows='3' name='msg'></textarea><br/><br/>
<input type='checkbox' name='msgtrans' value='1' /> Транслит<br/>
<input type='submit' name='submit' value='добавить' />  
  </form><br/>";
        echo "<a href='index.php?act=trans'>Транслит</a><br /><a href='../str/smile.php'>Смайлы</a><br/>";
    }
} else
{
    echo "Вы не авторизованы!<br/>";
}
echo '<br/><br/><a href="?act=komm&amp;id=' . $id . '">К комментариям</a><br/><a href="index.php?id=' . $id . '">К фото</a><br/>';
echo "<a href='index.php'>В галерею</a><br/>";

?>