-- Dumping structure for table dev_johncms.karma_users
DROP TABLE IF EXISTS `karma_users`;
CREATE TABLE IF NOT EXISTS `karma_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `karma_user` int(11) NOT NULL,
  `points` int(2) NOT NULL,
  `type` int(1) NOT NULL,
  `time` int(11) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `karma_user` (`karma_user`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;