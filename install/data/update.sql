--
-- Удаляем ненужные таблицы
--
DROP TABLE IF EXISTS `ban`;
DROP TABLE IF EXISTS `moder`;

ALTER TABLE `settings` CHANGE `gb` `gb` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `fmod` `fmod` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `rmod` `rmod` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `gzip` `gzip` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `sdvigclock` `sdvigclock` TINYINT NOT NULL DEFAULT '0';

ALTER TABLE `guest` ADD `adm` BOOL NOT NULL DEFAULT '0' AFTER `id` ;
ALTER TABLE `guest` ADD INDEX ( `adm` ) ;

ALTER TABLE `users`
DROP `ban` ,
DROP `why` ,
DROP `who` ,
DROP `bantime` ,
DROP `fban` ,
DROP `fwhy` ,
DROP `fwho` ,
DROP `ftime` ,
DROP `chban` ,
DROP `chwhy` ,
DROP `chwho` ,
DROP `chtime` ;