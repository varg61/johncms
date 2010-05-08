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

if (!empty($_SESSION['uid'])) {
    if (empty($_GET['id'])) {
        echo "Ошибка!";
        require_once('../incfiles/end.php');
        exit;
    }
    $type = mysql_query("select * from `gallery` where id='" . $id . "';");
    $ms = mysql_fetch_array($type);
    if ($ms[type] != "rz") {
        echo "Ошибка!";
        require_once('../incfiles/end.php');
        exit;
    }
    mysql_query("insert into `gallery` values(0,'" . $id . "','" . $realtime . "','al','" . $login . "','" . $login . "','','1','','');");
    $al = mysql_insert_id();
    header("location: index.php?id=$al");
} else {
    header("location: index.php");
}

?>