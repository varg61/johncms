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

require_once("../incfiles/core.php");

$req = mysql_query("SELECT * FROM `cms_ads` WHERE `id` = '$id' LIMIT 1");
if (mysql_num_rows($req)) {
    $res = mysql_fetch_assoc($req);
    $count_link = $res['count'] + 1;
    mysql_query("UPDATE `cms_ads` SET `count` = '$count_link'  WHERE `id` = '$id'");
    header('Location: ' . $res['link']);
} else {
    header("Location: http://gazenwagen.com/index.php?act=404");
}

?>