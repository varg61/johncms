<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                             Content Management System              //
// Официальный сайт сайт проекта:      http://johncms.com                     //
// Дополнительный сайт поддержки:      http://gazenwagen.com                  //
////////////////////////////////////////////////////////////////////////////////
// JohnCMS core team:                                                         //
// Евгений Рябинин aka john77          john77@gazenwagen.com                  //
// Олег Касьянов aka AlkatraZ          alkatraz@gazenwagen.com                //
//                                                                            //
// Информацию о версиях смотрите в прилагаемом файле version.txt              //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Error:restricted access');

$req = mysql_query("SELECT * FROM `chat` WHERE `id`= '" . $id . "' AND `type` = 'r' LIMIT 1");
if (mysql_num_rows($req)) {
    $type = mysql_fetch_array($req);
    ////////////////////////////////////////////////////////////
    // Если "Интим", то выводим форму ввода пароля            //
    ////////////////////////////////////////////////////////////
    if ($type['dpar'] == 'in') {
        if (!isset ($_SESSION['intim'])) {
            require_once ('../incfiles/head.php');
            echo '<form action="index.php?act=pass&amp;id=' . $id . '" method="post"><br/>';
            echo 'Пароль (max. 10):<br/><input type="text" name="parol" size="10" maxlength="10"/>';
            echo '<input type="submit" name="submit" value="Ok!"/></form>';
            echo '<p><a href="index.php">Прихожая</a></p>';
            require_once ("../incfiles/end.php");
            exit;
        }
    }
    else {
        if (isset ($_SESSION['intim']))
            unset ($_SESSION['intim']);
    }

    ////////////////////////////////////////////////////////////
    // Выводим сообщения комнаты Чата                         //
    ////////////////////////////////////////////////////////////
    $refr = rand(0, 999);
    $room = TRUE;
    require_once ('../incfiles/head.php');
    echo '<p>';
    if (!$set_chat['carea'])
        echo '[1] <a href="index.php?act=say&amp;id=' . $id . '" accesskey="1">Сказать</a> ';
    echo '[2] <a href="index.php?id=' . $id . '&amp;refr=' . $refr . '" accesskey="2">Обновить</a></p>';
    echo '<div class="phdr"><a href="index.php"><b>Чат</b></a> | ' . $type['text'] . '</div>';
    if ($set_chat['carea']) {
        // Поле "написать сообщение"
        echo '<form action="index.php?act=say&amp;id=' . $id . '" method="post"><div class="gmenu">';
        echo 'Сообщение(max. 500):<br /><textarea cols="' . $set_chat['carea_w'] . '" rows="' . $set_chat['carea_h'] . '" name="msg"></textarea><br/>';
        if ($set_user['translit']) {
            echo '<input type="checkbox" name="msgtrans" value="1" /> Транслит сообщения<br/>';
        }
        echo '<input type="submit" title="Нажмите для отправки" name="submit" value="Сказать"/></div></form>';
    }

    ////////////////////////////////////////////////////////////
    // Для Викторины подключаем Умника и показываем вопрос    //
    ////////////////////////////////////////////////////////////
    if ($type['dpar'] == 'vik') {
        require_once ('quiz.php');
        $quiz_req = mysql_query("SELECT * FROM `chat` WHERE `type` = 'v' ORDER BY `id` DESC LIMIT 1");
        $quiz_res = mysql_fetch_assoc($quiz_req);
        if ($quiz_res['realid'] > 1)
            echo '<div class="gmenu"><p>' . $quiz_res['text'] . '</p></div>';
    }
    $req = mysql_query("SELECT * FROM `chat` WHERE `refid` = '$id' AND `type` = 'm' ORDER BY `id` DESC LIMIT " . $set_chat['chmes']);
    $i = 0;
    while ($res = mysql_fetch_assoc($req)) {
    //TODO: Написать игнор
        if ($res['user_id'])
            echo '<div class="list2">';
        else
            echo '<div class="list1">';
        // Время поста
        echo '<span class="gray">' . date("H:i", ($res['time'] + $sdvig * 3600)) . '</span>&nbsp;';
        if ($res['user_id'] && $res['user_id'] != $user_id) {
            echo '<b><a href="index.php?act=say&amp;id=' . $res['id'] . '">' . $res['from'] . '</a></b> &gt;&gt; ';
        }
        elseif ($res['user_id']) {
            echo '<b>' . $res['from'] . '</b> &gt;&gt; ';
        }
        $text = tags($res['text']);
        if ($offsm != 1) {
            $text = smileys($text, ($mass1['rights'] >= 1) ? 1 : 0);
        }
        echo $text . '</div>';
        ++$i;
    }
    echo '<div class="phdr"><a href="who.php?id=' . $id . '">В чате</a> (' . wch($id) . ')</div>';
    echo '<p>[0] <a href="index.php?" accesskey="0">Прихожая</a><br/>';
    if ($type['dpar'] == "in")
        echo '[3] <a href="index.php?act=chpas&amp;id=' . $id . '" accesskey="3">Сменить пароль</a><br/>';
    if ($total > $set_chat['chmes'])
        echo '[4] <a href="" accesskey="4">История сообщений</a><br/>';
    if ($rights == 2 || $rights >= 6)
        echo '[5] <a href="index.php?act=clean&amp;id=' . $id . '" accesskey="5">Очистить комнату</a><br/>';
    echo '</p>';
    require_once ('../incfiles/end.php');
}
else {
    require_once ('../incfiles/head.php');
    echo '<div class="rmenu"><p>ОШИБКА!<br/><a href="index.php">В Чат</a></p></div>';
    require_once ('../incfiles/end.php');
}

?>