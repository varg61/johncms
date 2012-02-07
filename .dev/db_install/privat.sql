-- Dumping structure for table dev_johncms.privat
DROP TABLE IF EXISTS `privat`;
CREATE TABLE IF NOT EXISTS `privat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(25) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `time` varchar(25) NOT NULL DEFAULT '',
  `author` varchar(25) NOT NULL DEFAULT '',
  `type` char(3) NOT NULL DEFAULT '',
  `chit` char(3) NOT NULL DEFAULT '',
  `temka` text NOT NULL,
  `otvet` binary(1) NOT NULL DEFAULT '\0',
  `me` varchar(25) NOT NULL DEFAULT '',
  `cont` varchar(25) NOT NULL DEFAULT '',
  `ignor` varchar(25) NOT NULL DEFAULT '',
  `attach` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `me` (`me`),
  KEY `ignor` (`ignor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;