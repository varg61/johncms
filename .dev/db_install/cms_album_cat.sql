-- Dumping structure for table dev_johncms.cms_album_cat
DROP TABLE IF EXISTS `cms_album_cat`;
CREATE TABLE IF NOT EXISTS `cms_album_cat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  `name` varchar(40) NOT NULL,
  `description` text NOT NULL,
  `password` varchar(20) NOT NULL,
  `access` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `access` (`access`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;