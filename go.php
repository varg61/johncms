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
require_once('incfiles/core.php');

$adres = trim($_POST['adres']);
switch ($adres) {
    case 'chat':
        header("location: $home/chat/index.php");
        break;

    case 'forum':
        header("location: $home/forum/index.php");
        break;

    case 'set':
        header("location: $home/str/usset.php");
        break;

    case 'privat':
        header("location: $home/index.php?act=cab");
        break;

    case 'prof':
        header("location: $home/str/anketa.php");
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
        header("location: $home/str/news.php");
        break;

    case 'guest':
        header("location: $home/str/guest.php");
        break;
        default :
    header("location: http://gazenwagen.com");
        break;
}

?>