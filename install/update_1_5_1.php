<?php

// Обновление с версии 1.5.0 до 1.5.1
mysql_query("ALTER TABLE `cms_ban_users` ADD `ban_ref` INT NOT NULL AFTER `ban_who` ;") or die('Error');

?>