--
-- Удаляем ненужные таблицы
--
DROP TABLE IF EXISTS `ban`;
DROP TABLE IF EXISTS `moder`;


--
-- Создаем таблицу настроек
--
DROP TABLE IF EXISTS `cms_settings`;
CREATE TABLE `cms_settings` (
  `key` tinytext character set utf8 NOT NULL,
  `val` text character set utf8 NOT NULL,
  PRIMARY KEY  (`key`(30))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Создаем таблицу `ban_ip`
--
DROP TABLE IF EXISTS `ban_ip`;
CREATE TABLE `ban_ip` (
  `ip` int(11) NOT NULL default '0',
  `ban_type` tinyint(4) NOT NULL default '0',
  `link` varchar(100) NOT NULL,
  `who` varchar(25) NOT NULL,
  `reason` text NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Модифицируем таблицу "settings"
--
ALTER TABLE `settings` CHANGE `gb` `gb` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `fmod` `fmod` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `rmod` `rmod` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `gzip` `gzip` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `sdvigclock` `sdvigclock` TINYINT NOT NULL DEFAULT '0';

--
-- Модифицируем таблицу "guest"
--
ALTER TABLE `guest` ADD `adm` BOOL NOT NULL DEFAULT '0' AFTER `id` ;
ALTER TABLE `guest` ADD INDEX ( `adm` ) ;

--
-- Модифицируем таблицу "lib"
--
ALTER TABLE `lib` CHANGE `moder` `moder` BOOL NOT NULL DEFAULT '0';

--
-- Модифицируем таблицу "users"
--
ALTER TABLE `users` DROP `ban`;
ALTER TABLE `users` DROP `why`;
ALTER TABLE `users` DROP `who`;
ALTER TABLE `users` DROP `bantime`;
ALTER TABLE `users` DROP `fban`;
ALTER TABLE `users` DROP `fwhy`;
ALTER TABLE `users` DROP `fwho`;
ALTER TABLE `users` DROP `ftime`;
ALTER TABLE `users` DROP `chban`;
ALTER TABLE `users` DROP `chwhy`;
ALTER TABLE `users` DROP `chwho`;
ALTER TABLE `users` DROP `chtime`;