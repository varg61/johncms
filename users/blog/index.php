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
$rootpath = '../../';
require('../../incfiles/core.php');
$lng_profile = load_lng('profile');
require('../../incfiles/head.php');

if ($id && $id != $user_id) {
    // Если был запрос на юзера, то получаем его данные
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
    if (mysql_num_rows($req)) {
        $user = mysql_fetch_assoc($req);
        $textl = $lng['profile'] . ': ' . $user['name'];
    } else {
        echo display_error($lng['user_does_not_exist']);
        require('../incfiles/end.php');
        exit;
    }
} else {
    $id = false;
    $textl = $lng['profile'];
    $user = $datauser;
}
echo '<p>Личные Блоги находятся в стадии разработки и планируются позже<br /><a href="../profile/index.php?id=' . $id . '">Назад</a></p>';
require('../../incfiles/end.php');  
?>