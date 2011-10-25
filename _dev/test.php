<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Oleg Kasyanov
 * Date: 01.05.11
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */

define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');

/*
-----------------------------------------------------------------
Удаляем дубликаты E-mail
-----------------------------------------------------------------
*/
$req = mysql_query("SELECT * FROM `users` WHERE `mail` != '' GROUP BY `mail` HAVING COUNT(*) > 1");
while ($res = mysql_fetch_assoc($req)) {
    $req_m = mysql_query("SELECT * FROM `users` WHERE `mail` = '" . mysql_real_escape_string($res['test']) . "'");
    while ($res_m = mysql_fetch_assoc($req_m)) {
        mysql_query("UPDATE `users` SET `mail` = '' WHERE `id` = " . $res_m['id']);
    }
}

//$req = mysql_query("SELECT * FROM `users`");
//while($res = mysql_fetch_assoc($req)){
//    // Конвертируем Логины и E-mail адреса
//    $out['login'] = login::check_login($res['name']) !== false ? $res['name'] : $res['name_lat'];
//    $out['email'] = login::check_email($res['mail']) !== false ? $res['mail'] : '';
//}

/*
-----------------------------------------------------------------
Вычисление флудерастов.
Рассчет на основе общего числа постов / к-ва символов
-----------------------------------------------------------------
*/
//$req = mysql_query("SELECT `user_id`, COUNT(*) AS `posts`, SUM(CHAR_LENGTH(`text`)) AS `chars` FROM `forum` WHERE `type` = 'm' GROUP BY `user_id`");
//while ($res = mysql_fetch_assoc($req)) {
//    if($res['posts'] > 5 && $res['chars'] / $res['posts'] < 10)
//        echo $res['chars'] . ' - ' . $res['posts'] . ' - [' . $res['user_id'] . ']<br />';
//}