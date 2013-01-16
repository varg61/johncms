<?php

if (Vars::$USER_RIGHTS >= 7) {
    if (isset($_POST['submit'])) {
        // Проводим очистку Гостевой, согласно заданным параметрам
        $adm = isset($_SESSION['ga']) ? 1 : 0;
        $cl = isset($_POST['cl']) ? intval($_POST['cl']) : '';
        switch ($cl) {
            case '1':
                // Чистим сообщения, старше 1 дня
                mysql_query("DELETE FROM `guest` WHERE `adm`='$adm' AND `time` < '" . (time() - 86400) . "'");
                echo '<p>' . __('clear_day_ok') . '</p>';
                break;

            case '2':
                // Проводим полную очистку
                mysql_query("DELETE FROM `guest` WHERE `adm`='$adm'");
                echo '<p>' . __('clear_full_ok') . '</p>';
                break;
            default :
                // Чистим сообщения, старше 1 недели
                mysql_query("DELETE FROM `guest` WHERE `adm`='$adm' AND `time`<='" . (time() - 604800) . "';");
                echo '<p>' . __('clear_week_ok') . '</p>';
        }
        mysql_query("OPTIMIZE TABLE `guest`");
        echo '<p><a href="' . $url . '">' . __('guestbook') . '</a></p>';
    } else {
        // Запрос параметров очистки
        echo '<div class="phdr"><a href="' . $url . '"><b>' . __('guestbook') . '</b></a> | ' . __('clear') . '</div>' .
            '<div class="menu">' .
            '<form id="clean" method="post" action="' . $url . '?act=clean">' .
            '<p><h3>' . __('clear_param') . '</h3>' .
            '<input type="radio" name="cl" value="0" checked="checked" />' . __('clear_param_week') . '<br />' .
            '<input type="radio" name="cl" value="1" />' . __('clear_param_day') . '<br />' .
            '<input type="radio" name="cl" value="2" />' . __('clear_param_all') . '</p>' .
            '<p><input type="submit" name="submit" value="' . __('clear') . '" /></p>' .
            '</form></div>' .
            '<div class="phdr"><a href="' . $url . '">' . __('cancel') . '</a></div>';
    }
}