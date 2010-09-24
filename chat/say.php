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

defined('_IN_JOHNCMS') or die('Error:restricted access');

$chat = 1;
if (empty($id)) {
    require_once('../incfiles/head.php');
    echo 'Ошибка!<br/><a href="index.php">В чат</a><br/>';
    require_once('../incfiles/end.php');
    exit;
}
// Проверка на спам
$old = ($rights > 0) ? 5 : 10;
if ($lastpost > ($realtime - $old)) {
    require_once('../incfiles/head.php');
    echo '<p><b>Антифлуд!</b><br />Вы не можете так часто писать<br/>Порог ' . $old . ' секунд<br/><br/><a href="index.php?id=' . $id . '">Назад</a></p>';
    require_once('../incfiles/end.php');
    exit;
}

$type = mysql_query("SELECT * FROM `chat` WHERE `id` = '$id' LIMIT 1");
$type1 = mysql_fetch_array($type);
$tip = $type1['type'];
switch ($tip) {
    case 'r':
        ////////////////////////////////////////////////////////////
        // Добавление обычного сообщения                          //
        ////////////////////////////////////////////////////////////
        if (isset($_POST['submit'])) {
            if (empty($_POST['msg'])) {
                require_once('../incfiles/head.php');
                echo '<div class="rmenu"><p>Вы не ввели сообщение!<br/><a href="index.php?act=say&amp;id=' . $id . '">Повторить</a></p></div>';
                require_once('../incfiles/end.php');
                exit;
            }
            // Принимаем и проверяем сообщение
            $msg = check(mb_substr($_POST['msg'], 0, 500));
            if ($_POST['msgtrans'])
                $msg = trans($msg);
            // Проверяем на повтор сообщений
            $req = mysql_query("SELECT * FROM `chat` WHERE `refid` = '$id' AND `user_id` = '$user_id' ORDER BY `time` DESC LIMIT 1");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_array($req);
                if (stripslashes($msg) == $res['text']) {
                    header("location: $home/chat/index.php?id=$id");
                    exit;
                }
            }
            // Задаем цвет Ника
            $usr = '<span style="color: black">' . $login . '</span>';
            switch ($rights) {
                case 7:
                    $usr .= ' <b>[Adm]</b>';
                    break;

                case 6:
                    $usr .= ' <b>[Smd]</b>';
                    break;
            }
            // Записываем сообщение в базу
            mysql_query("INSERT INTO `chat` SET
            `refid` = '$id',
            `type` = 'm',
            `time` = '$realtime',
            `user_id` = '$user_id',
            `from` = '" . mysql_real_escape_string($usr) . "',
            `text` = '$msg',
            `ip` = '$ipl',
            `soft` = '" . mysql_real_escape_string(strtok($agn, ' ')) . "'");
            // Обновляем статистику юзера
            mysql_query("UPDATE `users` SET
            `postchat` = '" . ($datauser['postchat'] + 1) . "',
            `lastpost` = '$realtime'
            WHERE `id` = '$user_id'");
            // Если Викторина, то проверяем ответы на вопросы
            if ($type1['dpar'] == 'vik') {
                $quiz_req = mysql_query("SELECT * FROM `chat` WHERE `dpar` = 'vop' ORDER BY `id` DESC LIMIT 1");
                if (mysql_num_rows($quiz_req)) {
                    $quiz_res = mysql_fetch_array($quiz_req);
                    $len = mb_strlen($quiz_res['soft']);
                    if (mb_stristr(stripslashes($msg), $quiz_res['soft']) && $quiz_res['time'] > ($realtime - 150) && $quiz_res['realid'] > 1) {
                        if ($quiz_res['realid'] == 2) {
                            $pods = ' без подсказок';
                            $bls = 3;
                        } elseif ($quiz_res['realid'] == 3 || $len < 5 && $quiz_res['realid'] == 4) {
                            $pods = ', используя одну подсказку';
                            $bls = 2;
                        } else {
                            $pods = ', используя две подсказки';
                            $bls = 1;
                        }
                        $itg = $datauser['otvetov'] + 1;
                        $balans = $datauser['balans'] + $bls;
                        $tx = '<b>ОТЛИЧНО!!!</b> <span class="red">[+' . $bls . ' бал.]</span><br />';
                        $tx .= '<span class="green"><b>' . $login . '</b></span> дал' . ($datauser['sex'] == 'zh' ? 'а' : '') . ' правильный ответ' . $pods . '<br /><small>';
                        //$tx .= 'Потрачено секунд: <b>' . ($realtime - $quiz_res['time']) . '</b><br />';
                        $tx .= 'Всего правильных ответов: <b>' . $itg . '</b><br />';
                        $tx .= 'Игровой баланс: <b>' . $balans . '</b> баллов.';
                        $tx .= '</small>';
                        mysql_query("INSERT INTO `chat` SET
                        `refid` = '$id',
                        `type` = 'm',
                        `time` = '$realtime',
                        `from` = 'Умник',
                        `text` = '$tx'");
                        mysql_query("UPDATE `chat` SET `realid` = '1', `time` = '$realtime' WHERE `id` = '" . $quiz_res['id'] . "'");
                        mysql_query("UPDATE `users` SET `otvetov` = '$itg', `balans` = '$balans' WHERE `id` = '$user_id'");
                    }
                }
            }
            header("location: $home/chat/index.php?id=$id");
        } else {
            require_once('../incfiles/head.php');
            echo '<div class="phdr">Добавляем сообщение</div>';
            echo '<form action="index.php?act=say&amp;id=' . $id . '" method="post"><div class="gmenu"><p>';
            echo '<textarea cols="' . $set_chat['carea_w'] . '" rows="' . $set_chat['carea_h'] . '" name="msg"></textarea><br/>';
            if ($offtr != 1) {
                echo '<input type="checkbox" name="msgtrans" value="1" /> Транслит сообщения<br/>';
            }
            echo '</p><p><input type="submit" name="submit" value="Написать"/></p></div></form>';
            echo '<div class="phdr"><a href="index.php?act=trans">Транслит</a> | <a href="../str/smile.php">' . $lng['smileys'] . '</a></div>';
            echo '<p>[0] <a href="index.php?id=' . $id . '" accesskey="0">Назад</a></p>';
        }
        break;

    case 'm':
        ////////////////////////////////////////////////////////////
        // Добавление сообщения для юзера                         //
        ////////////////////////////////////////////////////////////
        $th = $type1['refid'];
        $th2 = mysql_query("SELECT * FROM `chat` WHERE `id` = '$th'");
        $th1 = mysql_fetch_array($th2);
        if (isset($_POST['submit'])) {
            $flt = $realtime - 10;
            $af = mysql_query("select * from `chat` where type='m' and time>'" . $flt . "' and `from`= '" . $login . "';");
            $af1 = mysql_num_rows($af);
            if ($af1 != 0) {
                require_once('../incfiles/head.php');
                echo "Антифлуд!Вы не можете так часто добавлять сообщения<br/>Порог 10 секунд<br/><a href='index.php?id=" . $th . "'>Назад</a><br/>";
                require_once('../incfiles/end.php');
                exit;
            }
            if (empty($_POST['msg'])) {
                require_once('../incfiles/head.php');
                echo "Вы не ввели сообщение!<br/><a href='index.php?act=say&amp;id=" . $id . "'>Повторить</a><br/>";
                require_once('../incfiles/end.php');
                exit;
            }
            $to = $type1['from'];
            $priv = intval($_POST['priv']);
            $nas = check($_POST['nas']);
            // Принимаем и проверяем сообщение
            $msg = check(mb_substr($_POST['msg'], 0, 500));
            if ($_POST['msgtrans'])
                $msg = trans($msg);
            if (!empty($nas))
                $msg = '<span class="gray">*' . $nas . '*</span> ' . $msg;
            // Проверяем на повтор сообщений
            $req = mysql_query("SELECT * FROM `chat` WHERE `refid` = '$th' AND `user_id` = '$user_id' ORDER BY `time` DESC LIMIT 1");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_array($req);
                if (stripslashes($msg) == $res['text']) {
                    header("location: $home/chat/index.php?id=$id");
                    exit;
                }
            }
            // Записываем сообщение в базу
            mysql_query("INSERT INTO `chat` SET
            `refid` = '$th',
            `type` = 'm',
            `time` = '$realtime',
            `user_id` = '$user_id',
            `from` = '$login',
            `text` = '$msg',
            `ip` = '$ipl',
            `soft` = '"
                . mysql_real_escape_string(strtok($agn, ' ')) . "'");
            // Обновляем статистику юзера
            mysql_query("UPDATE `users` SET
            `postchat` = '" . ($datauser['postchat'] + 1) . "',
            `lastpost` = '$realtime'
            WHERE `id` = '$user_id'");
            header("location: $home/chat/index.php?id=$th");
        } else {
            require_once('../incfiles/head.php');
            echo '<div class="phdr">Написать</div>';
            echo 'Кому: <a href="../users/profile.php?user=' . $type1['user_id'] . '"><b>' . $type1['from'] . '</b></a>';
            $user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '" . $type1['user_id'] . "'"));
            if (!empty($user['nastroy']))
                echo '<br />Настроение: ' . $user['nastroy'];
            echo '<form action="index.php?act=say&amp;id=' . $id . '" method="post"><div class="gmenu">';
            echo '<p><input type="checkbox" value="1" name="priv" checked="checked"/>&#160;Приватно<br />';
            // Список эмоций
            $emo = array (
                'Шёпoтoм',
                'Paдocтнo',
                'Пeчaльнo',
                'Удивлённo',
                'Лacкoвo',
                'Cмyщённo',
                'Oбижeннo',
                'Нacтойчивo',
                'Испуганно',
                'Злобно',
                'Задумчиво',
                'Откровенно'
            );
            echo '<select name="nas"><option value="">Бeз эмoций</option>';
            foreach ($emo as $val)echo '<option value="' . $val . '">' . $val . '</option>';
            echo '</select></p>';
            echo 'Сообщение (макс. 500 символов)<br /><textarea cols="40" rows="3" name="msg"></textarea><br/>';
            if ($offtr != 1) {
                echo '<input type="checkbox" name="msgtrans" value="1" /> Транслит сообщения<br/>';
            }
            echo '<input type="submit" title="Нажмите для отправки" name="submit" value="Отправить"/></div></form>';
            echo '<a href="index.php?act=trans">Транслит</a><br/><a href="../str/smile.php">' . $lng['smileys'] . '</a><br/>';
            if ($ruz != 0) {
                echo "<br/><a href='../str/pradd.php?act=write&amp;adr=" . $udat['id'] . "'>Написать в приват</a><br/>";
                if ($rights == 1) {
                    echo "<a href='../" . $admp . "/zaban.php?do=ban&amp;id=" . $udat['id'] . "&amp;chat'>Пнуть</a><br/>";
                }
            }
            echo "<a href='index.php?id=" . $type1['refid'] . "'>Назад</a><br/>";
        }
        break;

    default:
        require_once('../incfiles/head.php');
        echo "Ошибка!<br/>&#187;<a href='?'>В чат</a><br/>";
        break;
}

require_once('../incfiles/end.php');

?>