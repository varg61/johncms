-- Dumping structure for table dev_johncms.lib
DROP TABLE IF EXISTS `lib`;
CREATE TABLE IF NOT EXISTS `lib` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `type` varchar(4) NOT NULL DEFAULT '',
  `name` tinytext NOT NULL,
  `announce` text NOT NULL,
  `avtor` varchar(25) NOT NULL DEFAULT '',
  `text` mediumtext NOT NULL,
  `ip` int(11) NOT NULL DEFAULT '0',
  `soft` text NOT NULL,
  `moder` tinyint(1) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `moder` (`moder`),
  KEY `time` (`time`),
  KEY `refid` (`refid`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;