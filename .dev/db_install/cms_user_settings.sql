-- Dumping structure for table dev_johncms.cms_user_settings
DROP TABLE IF EXISTS `cms_user_settings`;
CREATE TABLE IF NOT EXISTS `cms_user_settings` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `key` varchar(32) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`user_id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;