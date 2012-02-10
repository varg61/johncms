-- Dumping structure for table dev_johncms.cms_contacts
DROP TABLE IF EXISTS `cms_contacts`;
CREATE TABLE IF NOT EXISTS `cms_contacts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `contact_id` int(10) unsigned NOT NULL DEFAULT '0',
  `count_in` int(10) unsigned NOT NULL DEFAULT '0',
  `count_out` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL,
  `delete` enum('0','1') NOT NULL DEFAULT '0',
  `banned` enum('0','1') NOT NULL DEFAULT '0',
  `archive` enum('0','1') NOT NULL DEFAULT '0',
  `access` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`contact_id`),
  UNIQUE KEY `contact_id` (`contact_id`,`user_id`),
  KEY `time` (`time`),
  KEY `delete` (`delete`),
  KEY `banned` (`banned`),
  KEY `archive` (`archive`),
  KEY `access` (`access`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;