-- Dumping structure for table dev_johncms.gallery
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `type` varchar(2) NOT NULL DEFAULT '',
  `avtor` varchar(25) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `name` text NOT NULL,
  `user` binary(1) NOT NULL DEFAULT '\0',
  `ip` text NOT NULL,
  `soft` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `refid` (`refid`),
  KEY `type` (`type`),
  KEY `time` (`time`),
  KEY `avtor` (`avtor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;