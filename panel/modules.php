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

define('_IN_JOHNCMS', 1);

$textl = 'Админка';
require_once ('../incfiles/core.php');
require_once ('../incfiles/head.php');

if ($dostadm)
{
    ////////////////////////////////////////////////////////////
    // Установка прав доступа к подсистемам                   //
    ////////////////////////////////////////////////////////////
    echo '<div class="phdr">Доступ к подсистемам</div>';
    if (isset($_POST['submit']))
    {
        // Записываем настройки в базу
        mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['reg']) ? intval($_POST['reg']) : 0) . "' WHERE `key`='mod_reg';");
        mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['forum']) ? intval($_POST['forum']) : 0) . "' WHERE `key`='mod_forum';");
        mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['chat']) ? intval($_POST['chat']) : 0) . "' WHERE `key`='mod_chat';");
        mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['guest']) ? intval($_POST['guest']) : 0) . "' WHERE `key`='mod_guest';");
        mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['lib']) ? intval($_POST['lib']) : 0) . "' WHERE `key`='mod_lib';");
        mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['gal']) ? intval($_POST['gal']) : 0) . "' WHERE `key`='mod_gal';");
        mysql_query("UPDATE `cms_settings` SET `val`='" . (isset($_POST['down']) ? intval($_POST['down']) : 0) . "' WHERE `key`='mod_down';");
        $req = mysql_query("SELECT * FROM `cms_settings`;");
        $set = array();
        while ($res = mysql_fetch_row($req))
            $set[$res[0]] = $res[1];
        mysql_free_result($req);
        echo '<div class="rmenu">Сайт настроен</div>';
    }
    // Выводим форму
    echo '<form id="form1" method="post" action="modules.php">';
    echo '<p><input name="reg" type="checkbox" value="1" ' . ($set['mod_reg'] ? 'checked="checked"' : '') . ' />&nbsp;регистрация<br />';
    echo '<input name="forum" type="checkbox" value="1" ' . ($set['mod_forum'] ? 'checked="checked"' : '') . ' />&nbsp;форум<br />';
    echo '<input name="chat" type="checkbox" value="1" ' . ($set['mod_chat'] ? 'checked="checked"' : '') . ' />&nbsp;чат<br />';
    echo '<input name="guest" type="checkbox" value="1" ' . ($set['mod_guest'] ? 'checked="checked"' : '') . ' />&nbsp;гостевая<br />';
    echo '<input name="lib" type="checkbox" value="1" ' . ($set['mod_lib'] ? 'checked="checked"' : '') . ' />&nbsp;библиотека<br />';
    echo '<input name="gal" type="checkbox" value="1" ' . ($set['mod_gal'] ? 'checked="checked"' : '') . ' />&nbsp;галерея<br />';
    echo '<input name="down" type="checkbox" value="1" ' . ($set['mod_down'] ? 'checked="checked"' : '') . ' />&nbsp;загрузки<br />';
    echo '<br /><input type="submit" name="submit" id="button" value="Запомнить" /></p>';
    echo '<p><a href="main.php">В админку</a></p>';
    echo '</form>';
} else
{
    header("Location: ../index.php?err");
}

require_once ("../incfiles/end.php");

?>