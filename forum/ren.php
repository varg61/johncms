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

defined('_IN_JOHNCMS') or die('Error: restricted access');

if ($rights == 3 || $rights >= 6) {
    if (!$id) {
        require_once('../incfiles/head.php');
        echo "Ошибка!<br/><a href='index.php?'>В форум</a><br/>";
        require_once('../incfiles/end.php');
        exit;
    }
    $typ = mysql_query("SELECT * FROM `forum` WHERE `id` = '$id'");
    $ms = mysql_fetch_array($typ);
    if ($ms[type] != "t") {
        require_once('../incfiles/head.php');
        echo "Ошибка!<br/><a href='index.php?'>В форум</a><br/>";
        require_once('../incfiles/end.php');
        exit;
    }
    if (isset($_POST['submit'])) {
        if (empty($_POST['nn'])) {
            require_once('../incfiles/head.php');
            echo "Ошибка!<br/><a href='index.php?'>В форум</a><br/>";
            require_once('../incfiles/end.php');
            exit;
        }
        $nn = check(trim($_POST['nn']));
        $pt = mysql_query("select * from `forum` where type='t' and refid='" . $ms[refid] . "' and text='" . $nn . "';");
        if (mysql_num_rows($pt) != 0) {
            require_once('../incfiles/head.php');
            echo "Ошибка!Тема с таким названием уже есть в этом разделе<br/><a href='index.php?act=ren&amp;id=" . $id . "'>Повторить</a><br/>";
            require_once("../incfiles/end.php");
            exit;
        }
        mysql_query("update `forum` set  text='" . $nn . "' where id='" . $id . "';");
        header("Location: index.php?id=$id");
    } else {
        require_once("../incfiles/head.php");
        echo "<form action='index.php?act=ren&amp;id=" . $id . "' method='post'>Переименование темы:<br/><input type='text' name='nn' value='" . $ms[text] . "'/><br/><input type='submit' name='submit' value='Ok!'/></form>";
    }
} else {
    require_once("../incfiles/head.php");
    echo "Доступ закрыт!!!<br>";
}
echo "<a href='index.php?'>В форум</a><br/>";

?>