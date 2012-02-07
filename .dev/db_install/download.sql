-- Dumping structure for table dev_johncms.download
DROP TABLE IF EXISTS `download`;
CREATE TABLE IF NOT EXISTS `download` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL DEFAULT '0',
  `adres` text NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `name` text NOT NULL,
  `type` varchar(4) NOT NULL DEFAULT '',
  `avtor` varchar(25) NOT NULL DEFAULT '',
  `ip` text NOT NULL,
  `soft` text NOT NULL,
  `text` text NOT NULL,
  `screen` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `refid` (`refid`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;