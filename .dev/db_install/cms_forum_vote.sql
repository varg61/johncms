-- Dumping structure for table dev_johncms.cms_forum_vote
DROP TABLE IF EXISTS `cms_forum_vote`;
CREATE TABLE IF NOT EXISTS `cms_forum_vote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `topic` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `topic` (`topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;