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
require('../incfiles/core.php');  
require('../incfiles/head.php');

/*
-----------------------------------------------------------------
Список тем форума, для Карты сайта
-----------------------------------------------------------------
*/
$links_count = 140;
$p = isset($_GET['p']) ? abs(intval($_GET['p'])) : 0;
$page = $links_count * $p;

if($id){
    // Запрашиваем раздел
    $req_s = mysql_query("SELECT * FROM `forum` WHERE `id` = '$id' AND `type` = 'r' LIMIT 1");
    if(mysql_num_rows($req_s)){
        $res_s = mysql_fetch_assoc($req_s);
        echo '<div class="phdr"><b>Карта Форума</b> | ' . $res_s['text'] . '</div>';
        echo '<div class="menu">';
        $req_t = mysql_query("SELECT * FROM `forum` WHERE `refid` = '$id' AND `type` = 't' AND `close` != '1' LIMIT $page, $links_count");
        while($res_t = mysql_fetch_assoc($req_t)){
            echo '<div><a href="' . $home . '/forum/index.php?id=' . $res_t['id'] . '">' . $res_t['text'] . '</a></div>';
        }
        echo '</div>';
    } else {
        echo display_error($lng['error_wrong_data']);
    }
} else {
    echo display_error($lng['error_wrong_data']);
}

require('../incfiles/end.php');
?>