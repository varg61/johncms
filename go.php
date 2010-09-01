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

define('_IN_JOHNCMS', 1);
$rootpath = '';
require('incfiles/core.php');
if ($id) {
    /*
    -----------------------------------------------------------------
    Редирект по рекламной ссылке
    -----------------------------------------------------------------
    */
    $req = mysql_query("SELECT * FROM `cms_ads` WHERE `id` = '$id' LIMIT 1");
    if (mysql_num_rows($req)) {
        $res = mysql_fetch_assoc($req);
        $count_link = $res['count'] + 1;
        mysql_query("UPDATE `cms_ads` SET `count` = '$count_link'  WHERE `id` = '$id'");
        header('Location: ' . $res['link']);
    } else {
        header("Location: http://johncms.com/index.php?act=404");
    }
} else {
    /*
    -----------------------------------------------------------------
    Редирект по "быстрому переходу"
    -----------------------------------------------------------------
    */
    $adres = trim($_POST['adres']);
    switch ($adres) {
        case 'chat':
            header("location: $home/chat/index.php");
            break;

        case 'forum':
            header("location: $home/forum/index.php");
            break;

        case 'lib':
            header("location: $home/library/index.php");
            break;

        case 'down':
            header("location: $home/download/index.php");
            break;

        case 'gallery':
            header("location: $home/gallery/index.php");
            break;

        case 'news':
            header("location: $home/news/index.php");
            break;

        case 'guest':
            header("location: $home/guestbook/index.php");
            break;

        default :
            header("location: http://johncms.com");
            break;
    }
}
?>