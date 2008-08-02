--
-- Удаляем ненужные таблицы
--
DROP TABLE IF EXISTS `ban`;
DROP TABLE IF EXISTS `moder`;

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