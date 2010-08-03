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

defined('_IN_JOHNADM') or die('Error: restricted access');
if ($rights < 7)
    die('Error: restricted access');
switch ($mod) {
    case 'del':
        /*
        -----------------------------------------------------------------
        Удаляем комнату
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php?act=mod_chat"><b>' . $lng['chat_management'] . '</b></a> | ' . $lng['chat_room_delete'] . '</div>';
        if (!$id) {
            echo display_error($lng['error_wrong_data'], '<a href="index.php?act=mod_chat">' . $lng['back'] . '</a>');
            require('../incfiles/end.php');
            exit;
        }
        if (isset($_POST['submit'])) {
            // Удаляем сообщения комнаты
            mysql_query("DELETE FROM `chat` WHERE `refid` = '$id'");
            // Удаляем комнату
            mysql_query("DELETE FROM `chat` WHERE `id` = '$id' AND `type` = 'r' LIMIT 1");
            header("Location: index.php?act=mod_chat");
        } else {
            // Подтверждение удаления
            $req = mysql_query("SELECT * FROM `chat` WHERE `type` = 'r' AND `id` = '$id'");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                echo '<div class="rmenu"><form action="index.php?act=mod_chat&amp;mod=del&amp;id=' . $id . '" method="post">' .
                    '<p>' . $lng['chat_room_delete_warning'] . '<b>' . $res['text'] . '</b>?</p>' .
                    '<p><input type="submit" name="submit" value="' . $lng['delete'] . '" /></p>' .
                    '</form></div>' .
                    '<div class="phdr"><a href="index.php?act=mod_chat">' . $lng['cancel'] . '</a></div>';
            } else {
                // Если комната не существует, выводим ошибку
                echo display_error($lng['error_wrong_data'], '<a href="index.php?act=mod_chat">' . $lng['back'] . '</a>');
                require('../incfiles/end.php');
                exit;
            }
        }
        break;

    case 'edit':
        /*
        -----------------------------------------------------------------
        Добавляем / Редактируем комнату
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php?act=mod_chat"><b>' . $lng['chat_management'] . '</b></a> | ' . ($id ? $lng['chat_room_edit'] : $lng['chat_room_add']) . '</div>';
        if ($id) {
            // Если комната редактироется, запрашиваем ее данные в базе
            $req = mysql_query("SELECT * FROM `chat` WHERE `id` = '$id' AND `type` = 'r' LIMIT 1");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
            } else {
                echo display_error($lng['error_wrong_data'], '<a href="index.php?act=mod_chat">' . $lng['chat_management'] . '</a>');
                require('../incfiles/end.php');
                exit;
            }
        } else {
            $res = array ();
        }
        if (isset($_POST['submit'])) {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            if (empty($name)) {
                echo display_error($lng['error_nameto_empty'], '<a href="chat.php?act=edit&amp;id=' . $id . '">' . $lng['repeat'] . '</a>');
                require('../incfiles/end.php');
                exit;
            }
            switch ($_POST['tr']) {
                case 'vik':
                    $room_type = 'vik';
                    break;

                case 'in':
                    $room_type = 'in';
                    break;

                case 'adm':
                    $room_type = 'adm';
                    break;
                    default:
                    $room_type = '';
            }
            if ($id) {
                // Обновляем данные комнаты в базе
                mysql_query("UPDATE `chat` SET
                    `dpar` = '$room_type',
                    `text` = '" . mysql_real_escape_string($name) . "'
                    WHERE `id` = '$id'");
            } else {
                // Добавляем комнату в базу
                mysql_query("INSERT INTO `chat` SET
                    `type` = 'r',
                    `dpar` = '$room_type',
                    `text` = '" . mysql_real_escape_string($name) . "'");
            }
            header("Location: index.php?act=mod_chat");
        } else {
            $quiz_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `chat` WHERE `type` = 'r' AND `dpar` = 'vik'"), 0);
            $intim_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `chat` WHERE `type` = 'r' AND `dpar` = 'in'"), 0);
            $adm_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `chat` WHERE `type` = 'r' AND `dpar` = 'adm'"), 0);
            echo '<form action="index.php?act=mod_chat&amp;mod=edit' . ($id ? '&amp;id=' . $id : '') . '" method="post">' .
                '<div class="menu"><p><h3>' . $lng['name_the'] . '</h3>' .
                '<input type="text" name="name" value="' . $res['text'] . '"/></p>' .
                '<p><h3>' . $lng['chat_room_type'] . '</h3>' .
                '<input type="radio" value="0" name="tr" ' . (empty($res['dpar']) == 1 ? 'checked="checked"' : '') . '/>&#160;' . $lng['chat_room_simply'] . '<br />';
            if (!$quiz_count || $res['dpar'] == 'vik')
                echo '<input type="radio" value="vik" name="tr" ' . ($res['dpar'] == 'vik' ? 'checked="checked"' : '') . '/>&#160;' . $lng['chat_quiz'] . '<br />';
            if (!$intim_count || $res['dpar'] == 'in')
                echo '<input type="radio" value="in" name="tr" ' . ($res['dpar'] == 'in' ? 'checked="checked"' : '') . '/>&#160;' . $lng['chat_intim'] . '<br />';
            if (!$adm_count || $res['dpar'] == 'adm')
                echo '<input type="radio" value="adm" name="tr" ' . ($res['dpar'] == 'adm' ? 'checked="checked"' : '') . '/>&#160;' . $lng['admin_club'];
            echo '</p><p><input type="submit" name="submit" value="' . $lng['save'] . '"/></p></div></form>' .
                '<div class="phdr"><a href="index.php?act=mod_chat">' . $lng['cancel'] . '</a></div>';
        }
        break;

    case 'up':
        /*
        -----------------------------------------------------------------
        Перемещение комнаты на одну позицию вверх
        -----------------------------------------------------------------
        */
        if ($id) {
            $req = mysql_query("SELECT `realid` FROM `chat` WHERE `type` = 'r' AND `id` = '$id' LIMIT 1");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                $sort = $res['realid'];
                $req = mysql_query("SELECT * FROM `chat` WHERE `type` = 'r' AND `realid` < '$sort' ORDER BY `realid` DESC LIMIT 1");
                if (mysql_num_rows($req)) {
                    $res = mysql_fetch_assoc($req);
                    $id2 = $res['id'];
                    $sort2 = $res['realid'];
                    mysql_query("UPDATE `chat` SET `realid` = '$sort2' WHERE `id` = '$id'");
                    mysql_query("UPDATE `chat` SET `realid` = '$sort' WHERE `id` = '$id2'");
                }
            }
        }
        header('Location: index.php?act=mod_chat');
        break;

    case 'down':
        /*
        -----------------------------------------------------------------
        Перемещение комнаты на одну позицию вниз
        -----------------------------------------------------------------
        */
        if ($id) {
            $req = mysql_query("SELECT `realid` FROM `chat` WHERE `type` = 'r' AND `id` = '$id' LIMIT 1");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                $sort = $res['realid'];
                $req = mysql_query("SELECT * FROM `chat` WHERE `type` = 'r' AND `realid` > '$sort' ORDER BY `realid` ASC LIMIT 1");
                if (mysql_num_rows($req)) {
                    $res = mysql_fetch_assoc($req);
                    $id2 = $res['id'];
                    $sort2 = $res['realid'];
                    mysql_query("UPDATE `chat` SET `realid` = '$sort2' WHERE `id` = '$id'");
                    mysql_query("UPDATE `chat` SET `realid` = '$sort' WHERE `id` = '$id2'");
                }
            }
        }
        header('Location: index.php?act=mod_chat');
        break;

    default:
        /*
        -----------------------------------------------------------------
        Список комнат Чата
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>' . $lng['admin_panel'] . '</b></a> | ' . $lng['chat_management'] . '</div>';
        $req = mysql_query("SELECT * FROM `chat` WHERE `type` = 'r' ORDER BY `realid`");
        while ($res = mysql_fetch_assoc($req)) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            echo '<b>' . $res['text'] . '</b><br />' .
                '<div class="sub"><a href="index.php?act=mod_chat&amp;mod=up&amp;id=' . $res['id'] . '">' . $lng['up'] . '</a> | ' .
                '<a href="index.php?act=mod_chat&amp;mod=down&amp;id=' . $res['id'] . '">' . $lng['down'] . '</a> | ' .
                '<a href="index.php?act=mod_chat&amp;mod=edit&amp;id=' . $res['id'] . '">' . $lng['edit'] . '</a> | ' .
                '<a href="index.php?act=mod_chat&amp;mod=del&amp;id=' . $res['id'] . '">' . $lng['delete'] . '</a>' .
                '</div></div>';
            ++$i;
        }
        echo '<div class="gmenu"><form action="index.php?act=mod_chat&amp;mod=edit" method="post">' .
            '<input type="submit" value="' . $lng['chat_room_add'] . '" /></form></div>' .
            '<div class="phdr"><a href="../chat/index.php">' . $lng['to_chat'] . '</a></div>';
}
echo '<p>' . ($mod ? '<a href="index.php?act=mod_chat">' . $lng['chat_management'] . '</a><br />' : '') .
    '<a href="index.php">' . $lng['admin_panel'] . '</a></p>';
?>