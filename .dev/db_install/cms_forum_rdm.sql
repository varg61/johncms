-- Dumping structure for table dev_johncms.cms_forum_rdm
DROP TABLE IF EXISTS `cms_forum_rdm`;
CREATE TABLE IF NOT EXISTS `cms_forum_rdm` (
  `topic_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`,`user_id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;