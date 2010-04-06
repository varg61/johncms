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

defined('_IN_JOHNCMS') or die('Error:restricted access');

if ($id && ($rights > 5 || $rights == 2)) {
    $typ = mysql_query("SELECT * FROM `chat` WHERE `id` = '$id' LIMIT 1");
    $ms = mysql_fetch_array($typ);
    if ($ms['type'] != "r") {
        require_once ('../incfiles/head.php');
        echo 'Ошибка!<br/><a href="index.php">В чат</a><br/>';
        require_once ('../incfiles/end.php');
        exit;
    }
    if (isset ($_GET['yes'])) {
        ////////////////////////////////////////////////////////////
        // Очищаем комнату                                        //
        ////////////////////////////////////////////////////////////
        mysql_query("DELETE FROM `chat` WHERE `refid` = '$id'");
        header("Location: $home/chat/index.php?id=$id");
    }
    else {
        require_once ('../incfiles/head.php');
        echo '<div class="rmenu"><p>Вы действительно хотите очистить комнату?<br/><a href="index.php?act=clean&amp;id=' . $id . '&amp;yes">Да</a> | <a href="index.php?id=' . $id . '">Нет</a></p></div>';
        require_once ('../incfiles/end.php');
    }
}

?>