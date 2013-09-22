<?php

/**
 * Очистка почты и контактов от несуществующих (удаленных) юзеров
 * Поместить скрипт в корень сайта и вызвать по ссылке http://ваш_сайт/cleanup.php
 * После завершения работы скрипта, файл удалить!
 */

define('_IN_JOHNCMS', 1);

require('incfiles/core.php');

$req1 = mysql_query("
    SELECT `cms_mail`.`id`
    FROM `cms_mail` LEFT JOIN `users` ON `cms_mail`.`from_id` = `users`.`id`
    WHERE `users`.`id` IS NULL
");

while ($res1 = mysql_fetch_assoc($req1)) {
    mysql_query("
        DELETE FROM `cms_mail`
        WHERE `id` = '" . $res1['id'] . "'
    ");
}

$req2 = mysql_query("
    SELECT `cms_mail`.`id`
    FROM `cms_mail` LEFT JOIN `users` ON `cms_mail`.`user_id` = `users`.`id`
    WHERE `users`.`id` IS NULL
");

while ($res2 = mysql_fetch_assoc($req2)) {
    mysql_query("
        DELETE FROM `cms_mail`
        WHERE `id` = '" . $res2['id'] . "'
    ");
}

mysql_query("OPTIMIZE TABLE `cms_mail`");
echo '<div>Mail cleanup completed successfully!</div>';

$req1 = mysql_query("
    SELECT `cms_contact`.`id`
    FROM `cms_contact` LEFT JOIN `users` ON `cms_contact`.`from_id` = `users`.`id`
    WHERE `users`.`id` IS NULL
");

while ($res1 = mysql_fetch_assoc($req1)) {
    mysql_query("
        DELETE FROM `cms_contact`
        WHERE `id` = '" . $res1['id'] . "'
    ");
}

$req2 = mysql_query("
    SELECT `cms_contact`.`id`
    FROM `cms_contact` LEFT JOIN `users` ON `cms_contact`.`user_id` = `users`.`id`
    WHERE `users`.`id` IS NULL
");

while ($res2 = mysql_fetch_assoc($req2)) {
    mysql_query("
        DELETE FROM `cms_contact`
        WHERE `id` = '" . $res2['id'] . "'
    ");
}

mysql_query("OPTIMIZE TABLE `cms_contact`");
echo '<div>Contacts cleanup completed successfully!</div>';