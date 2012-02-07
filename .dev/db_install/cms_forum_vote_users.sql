-- Dumping structure for table dev_johncms.cms_forum_vote_users
DROP TABLE IF EXISTS `cms_forum_vote_users`;
CREATE TABLE IF NOT EXISTS `cms_forum_vote_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT '0',
  `topic` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `topic` (`topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;