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

defined('_IN_JOHNCMS') or die('Restricted access');

// Запрашиваем (если есть) последний заданный вопрос Умника
$quiz_req = mysql_query("SELECT * FROM `chat` WHERE `type` = 'v' ORDER BY `id` DESC LIMIT 1");
$quiz_num = mysql_num_rows($quiz_req);
$quiz_res = mysql_fetch_array($quiz_req);
if (!$quiz_num || ($quiz_res['realid'] == 1 && $quiz_res['time'] < ($realtime - 15))) {
    // "Умник" задает вопрос Викторины
    $num = rand(1, mysql_result(mysql_query("SELECT COUNT(*) FROM `vik`"), 0));
    $vik = mysql_fetch_array(mysql_query("SELECT * FROM `vik` LIMIT $num, 1"));
    $vopros = functions::checkout($vik['vopros']);
    $len = mb_strlen($vik['otvet']);
    mysql_query("INSERT INTO `chat` SET
    `refid` = '$id',
    `realid` = '2',
    `type` = 'v',
    `time` = '$realtime',
    `dpar` = 'vop',
    `text` = '<b>Вопрос (" . $len . ($len < 5 ? " буквы" : " букв") .
        "): <span class=\"green\">" . $vopros . "</span></b>',
    `soft` = '" . mysql_real_escape_string($vik['otvet']) . "'");
} elseif ($quiz_res['realid'] > 1 && $quiz_res['time'] < ($realtime - 150)) {
    // Если на вопрос никто не ответил
    mysql_query("INSERT INTO `chat` SET
    `refid` = '$id',
    `type` = 'm',
    `time` = '$realtime',
    `text` = '<span class=\"red\"><b>Время истекло! Вопрос не был угадан</b></span>'");
    mysql_query("UPDATE `chat` SET `realid` = '1', `time` = '$realtime' WHERE `id` = '" . $quiz_res['id'] . "'");
} else {
    // Подсказки Умника
    if (($quiz_res['realid'] == 2 && $quiz_res['time'] < ($realtime - 50)) || ($quiz_res['realid'] == 3 && $quiz_res['time'] < ($realtime - 100))) {
        $ans = $quiz_res['soft'];
        $len = mb_strlen($ans);
        $d = round($len / ($quiz_res['realid'] == 3 ? 3 : 4));
        $tip = mb_substr($ans, 0, $d);
        for ($i = $d; $i < $len; ++$i)$tip .= '*';
        mysql_query("INSERT INTO `chat` SET
        `refid` = '$id',
        `type` = 'm',
        `time` = '$realtime',
        `text` = '<b>" . ($quiz_res['realid'] == 3 ? "Вторая п" : "П") .
            "одсказка: <span class=\"gray\">" . $tip . "</span></b>'");
        mysql_query("UPDATE `chat` SET `realid` = '" . (($len > 4 && $quiz_res['realid'] == 2) ? 3 : 4) . "' WHERE `id` = '" . $quiz_res['id'] . "'");
    }
}

?>