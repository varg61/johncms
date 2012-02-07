-- Dumping structure for table dev_johncms.cms_forum_files
DROP TABLE IF EXISTS `cms_forum_files`;
CREATE TABLE IF NOT EXISTS `cms_forum_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat` int(10) NOT NULL,
  `subcat` int(10) NOT NULL,
  `topic` int(10) NOT NULL,
  `post` int(10) NOT NULL,
  `time` int(11) NOT NULL,
  `filename` text NOT NULL,
  `filetype` tinyint(4) NOT NULL,
  `dlcount` int(10) NOT NULL,
  `del` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat` (`cat`),
  KEY `subcat` (`subcat`),
  KEY `topic` (`topic`),
  KEY `post` (`post`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;