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

if ($rights == 5 || $rights >= 6) {
    if ($_GET['id'] == "") {
        echo "Ошибка<br/><a href='index.php?'>В библиотеку</a><br/>";
        require_once('../incfiles/end.php');
        exit;
    }
    $typ = mysql_query("select * from `lib` where id='" . $id . "';");
    $ms = mysql_fetch_array($typ);
    if ($id != 0 && ($ms['type'] == "bk" || $ms['type'] == "komm")) {
        echo "Ошибка<br/><a href='index.php?'>В библиотеку</a><br/>";
        require_once('../incfiles/end.php');
        exit;
    }
    if (isset($_POST['submit'])) {
        if (empty($_POST['text'])) {
            echo "Вы не ввели название!<br/><a href='index.php?act=mkcat&amp;id=" . $id . "'>Повторить</a><br/>";
            require_once('../incfiles/end.php');
            exit;
        }
        $text = functions::check($_POST['text']);
        $user = intval($_POST['user']);
        $typs = intval($_POST['typs']);
        mysql_query("INSERT INTO `lib` (
                refid,
                time,
                type,
                text,
                ip,
                soft
                ) VALUES(
                '" . $id . "',
                '" . $realtime . "',
                'cat',
                '" . $text . "',
                '" . $typs . "',
                '" . $user . "');");
        $cid = mysql_insert_id();
        echo "Категория создана<br/><a href='index.php?id=" . $cid . "'>В категорию</a><br/>";
    } else {
        echo "Добавление категории<br/><form action='index.php?act=mkcat&amp;id=" . $id .
            "' method='post'>Введите название:<br/><input type='text' name='text'/><br/>Тип категории(для статей или вложенных категорий)<br/><select name='typs'><option value='1'>Категории</option><option value='0'>Статьи</option></select><hr/><input type='checkbox' name='user' value='1'/>Если тип-Статьи,разрешить юзерам добавлять свои статьи?<hr/><input type='submit' name='submit' value='Ok!'/><br/></form><a href ='index.php?id="
            . $id . "'>Назад</a><br/>";
    }
} else {
    header("location: index.php");
}

?>