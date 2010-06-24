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

$headmod = 'info';
$textl = 'Информация';
$rootpath = '';
require_once ('incfiles/core.php');
require_once ('incfiles/head.php');

$do = isset ($_GET['do']) ? $_GET['do'] : '';
switch ($do) {
        case 'actmail' :
            include_once ('pages/actmail.txt');
            break;

        case 'forum' :
        include_once ('pages/forum.txt');
        break;

    case 'forumfaq' :
        include_once ('pages/forumfaq.txt');
        break;

    case 'trans' :
        include_once ('pages/trans.txt');
        break;

    default :
        echo '<div class="menu"><a href="read.php?do=actmail">Активация e-mail</a></div>';
        echo '<div class="menu"><a href="read.php?do=forum">Правила форума</a></div>';
        echo '<div class="menu"><a href="read.php?do=forumfaq">FAQ по тэгам</a></div>';
        echo '<div class="menu"><a href="read.php?do=trans">Справка по транслиту</a></div>';
        echo '<div class="menu"><a href="str/smile.php?">' . $lng['smileys'] . '</a></div>';
}

if ($do)
        echo '<a href="read.php">В FAQ</a><br /><br />';

require_once ('incfiles/end.php');

?>