--
-- Структура таблицы `cms_sessions`
--
DROP TABLE IF EXISTS `cms_session`;
DROP TABLE IF EXISTS `cms_sessions`;
CREATE TABLE `cms_sessions` (
  `session_id` char(40) NOT NULL DEFAULT '',
  `session_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `session_data` text NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Структура таблицы `cms_user_ip`
--
DROP TABLE IF EXISTS `cms_users_iphistory`;
DROP TABLE IF EXISTS `cms_user_ip`;
CREATE TABLE `cms_user_ip` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_via_proxy` int(10) unsigned NOT NULL DEFAULT '0',
  `useragent` varchar(150) NOT NULL DEFAULT '',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ip` (`ip`),
  KEY `ip_via_proxy` (`ip_via_proxy`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Структура таблицы `cms_user_settings`
--
DROP TABLE IF EXISTS `cms_users_data`;
DROP TABLE IF EXISTS `cms_user_settings`;
CREATE TABLE `cms_user_settings` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `key` varchar(32) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`user_id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Структура таблицы `cms_ip_bwlist`
--
DROP TABLE IF EXISTS `cms_ban_ip`;
DROP TABLE IF EXISTS `cms_ip_bwlist`;
CREATE TABLE `cms_ip_bwlist` (
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_upto` int(10) unsigned NOT NULL DEFAULT '0',
  `mode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `reason` text NOT NULL,
  PRIMARY KEY (`ip`),
  UNIQUE KEY `ip_upto` (`ip_upto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Изменения таблицы `users`
--
ALTER TABLE `users` ENGINE = InnoDB;
ALTER TABLE `users` ADD `nickname` VARCHAR( 32 ) NOT NULL DEFAULT '' AFTER `id`;
ALTER TABLE `users` CHANGE `password` `password` CHAR( 32 ) NOT NULL DEFAULT '';
ALTER TABLE `users` ADD `token` CHAR( 32 ) NOT NULL DEFAULT '' AFTER `password`;
ALTER TABLE `users` ADD `salt` VARCHAR( 10 ) NOT NULL DEFAULT '' AFTER `token`;
ALTER TABLE `users` ADD `email` VARCHAR( 50 ) NOT NULL DEFAULT '' AFTER `salt`;
ALTER TABLE `users` CHANGE `sex` `sex_old` VARCHAR( 2 ) NOT NULL DEFAULT '';
ALTER TABLE `users` CHANGE `failed_login` `failed_login` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `rights` `rights` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` ADD `level` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '1' AFTER `rights`;
ALTER TABLE `users` ADD `sex` ENUM( 'm', 'w' ) NOT NULL DEFAULT 'm' AFTER `failed_login`;
ALTER TABLE `users` ADD `birth` DATE NOT NULL AFTER `imname`;
ALTER TABLE `users` CHANGE `komm` `count_comments` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `postforum` `count_forum` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `datereg` `join_date` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `lastdate` `last_visit` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `status` `status` VARCHAR( 100 ) NOT NULL DEFAULT '';
ALTER TABLE `users`
DROP `ip` ,
DROP `ip_via_proxy` ,
DROP `browser`;
ALTER TABLE `users` ADD `ip` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` ADD `ip_via_proxy` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` ADD `user_agent` VARCHAR( 200 ) NOT NULL DEFAULT '';
ALTER TABLE `users` ADD `login_try` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `salt`;
ALTER TABLE `users` CHANGE `lastpost` `lastpost` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `rest_time` `rest_time` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `karma_plus` `karma_plus` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `karma_minus` `karma_minus` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `karma_time` `karma_time` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `karma_off` `karma_off` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `mailvis` `mailvis` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` CHANGE `icq` `icq` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `users` ADD `ban` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `level`;

--
-- КОНВЕРТАЦИЯ ДАННЫХ
--
-- Если `name_lat` совпадает с транслитерированным `name`, то записываем значение `name` в поле `nickname`
-- Если `name_lat` не совпадает с транслитерированным `name`, то записываем значение `name_lat` в поле `nickname`
-- Конвертируем данные из поля `sex_old` в поле `sex`
-- Конвертируем дату рождения из полей `yearofbirth`, `monthb`, `dayb` в поле `birth`
-- Для тех юзеров, у кого есть активные баны, поставить флаг 1 в поле `ban`
--
--
--
--

--
-- После конвертации данных
--
ALTER TABLE `users`
DROP `name`,
DROP `name_lat`,
DROP `sex_old`,
DROP `set_user`,
DROP `set_forum`,
DROP `smileys`,
DROP `movings`,
DROP `place`,
DROP `total_on_site`,
DROP `sestime`,
DROP `postguest`,
DROP `mail`,
DROP `yearofbirth`,
DROP `monthb`,
DROP `dayb`,
DROP `preg`,
DROP `regadm`,
DROP `time`,
DROP `failed_login`;

--
-- Удаляем ненужные таблицы
--
DROP TABLE IF EXISTS `privat`;








--
-- Переименовываем таблицу `cms_user_guestbook`
--
RENAME TABLE `cms_users_guestbook` TO `cms_user_guestbook`;