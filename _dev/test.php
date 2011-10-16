<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Oleg Kasyanov
 * Date: 01.05.11
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */

define('_IN_JOHNCMS', 1);
$rootpath = '';
require('incfiles/core.php');

//$req = mysql_query("SELECT `user_id`, COUNT(*) AS `posts`, SUM(CHAR_LENGTH(`text`)) AS `chars` FROM `forum` WHERE `type` = 'm' GROUP BY `user_id` ORDER BY `posts` DESC LIMIT 20");
$req = mysql_query("SELECT `user_id`, COUNT(*) AS `posts`, SUM(CHAR_LENGTH(`text`)) AS `chars` FROM `forum` WHERE `type` = 'm' GROUP BY `user_id`");
while ($res = mysql_fetch_assoc($req)) {
    if($res['posts'] > 5 && $res['chars'] / $res['posts'] < 10)
        echo $res['chars'] . ' - ' . $res['posts'] . ' - [' . $res['user_id'] . ']<br />';
}