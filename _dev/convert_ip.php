<?php

/*
Конвертация IP адресов Форума
Для запуска конвертера, данный файл нужно поместить в корневую директорию сайта
*/

define('_IN_JOHNCMS', 1);

$rootpath = '';
set_time_limit(600);
require('incfiles/core.php');

// Переименовываем старое поле с IP адресами
mysql_query("ALTER TABLE `forum` CHANGE `ip` `ip_old` TEXT NOT NULL");

// Создаем новые поля для IP адресов
mysql_query("ALTER TABLE `forum` ADD `ip` BIGINT( 11 ) NOT NULL DEFAULT '0' AFTER `text`;");
mysql_query("ALTER TABLE `forum` ADD `ip_via_proxy` BIGINT( 11 ) NOT NULL DEFAULT '0' AFTER `ip`;");

// Переносим сконвертированные IP адреса в новое поле
$req = mysql_query("SELECT `id`, `ip_old` FROM `forum` WHERE `type` = 'm'");
while (($res = mysql_fetch_assoc($req)) !== false) {
    if (!empty($res['ip_old']) && core::ip_valid($res['ip_old'])) {
        mysql_query("UPDATE `forum` SET `ip` = '" . ip2long($res['ip_old']) . "' WHERE `id` = '" . $res['id'] . "' LIMIT 1");
    }
}

// Удаляем старое поле IP
mysql_query("ALTER TABLE `forum` DROP `ip_old`");

echo 'Адреса сконвертированы';

?>