DROP TABLE IF EXISTS `ban`;

ALTER TABLE `settings` CHANGE `gb` `gb` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `fmod` `fmod` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `rmod` `rmod` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `gzip` `gzip` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `sdvigclock` `sdvigclock` TINYINT NOT NULL DEFAULT '0';

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