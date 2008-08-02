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

define('_IN_JOHNCMS', 1);

$textl = 'Управление';
require_once ("../incfiles/core.php");
require_once ("../incfiles/head.php");
if ($dostmod == 1)
{
    $do = isset($_GET['do']) ? $_GET['do'] : '';
    switch ($do)
    {
        case "users":
            if (empty($_POST['user']))
            {
                echo '<br /><b>Вы не заполнили поле!</b><br/><br/><a href="main.php?do=search">Назад</a><br/><br/>';
                require_once ("../incfiles/end.php");
                exit;
            }
            // Поиск по ID
            if ($_POST['term'] == '1')
            {
                $search = intval($_POST['user']);
                $req = mysql_query("select * from `users` where `id`='" . $search . "';");
            }
            // Поиск по Нику
            else
            {
                $search = rus_lat(mb_strtolower($_POST['user']));
                $req = mysql_query("select * from `users` where `name_lat`='" . mysql_real_escape_string($search) . "';");
            }
            if (mysql_num_rows($req) == 0)
            {
                echo "Нет такого юзера!<br/><a href='main.php'>Назад</a><br/>";
                require_once ("../incfiles/end.php");
                exit;
            }
            $res = mysql_fetch_array($req);
            header("location: editusers.php?act=edit&user=$res[id]");
            break;

        case "search":
            echo '<div class="phdr">Поиск юзера</div>';
			echo '<br />Кого ищем?:<br/>';
            echo '<form action="main.php?do=users" method="post">';
            echo '<input type="text" name="user"/><br/>';
            echo '<input name="term" type="radio" value="0" checked="checked" />Поиск по Нику<br />';
            echo '<input name="term" type="radio" value="1" />Поиск по ID<br /><br />';
            echo '<input type="submit" value="Поиск"/>';
            echo '</form><br/>';
            echo '<a href="main.php">В админку</a><br/><br/>';
            break;

        default:
            echo '<div class="phdr">Админ Панель</div>';
			echo '<div class="bmenu">Пользователи</div>';
            echo '<div class="menu"><a href="main.php?do=search">Поиск</a></div>';
            echo '<div class="bmenu">Модули</div>';
            echo '<div class="menu"><a href="news.php">Новости</a></div>';
            echo '<div class="menu"><a href="forum.php">Форум</a></div>';
            echo '<div class="menu"><a href="chat.php">Чат</a></div>';
            if ($dostadm == 1)
            {
                echo '<div class="bmenu">Система</div>';
                echo '<div class="menu"><a href="set.php">Настройки</a></div>';
            }
    }
} else
{
    header("Location: ../index.php?err");
}
require_once ("../incfiles/end.php");

?>