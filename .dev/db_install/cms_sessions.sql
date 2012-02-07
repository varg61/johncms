-- Dumping structure for table dev_johncms.cms_sessions
DROP TABLE IF EXISTS `cms_sessions`;
CREATE TABLE IF NOT EXISTS `cms_sessions` (
  `session_id` char(32) NOT NULL DEFAULT '',
  `session_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `session_data` text NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_via_proxy` int(10) unsigned NOT NULL DEFAULT '0',
  `user_agent` varchar(200) NOT NULL DEFAULT '',
  `place` varchar(200) NOT NULL DEFAULT '',
  `views` smallint(5) unsigned NOT NULL DEFAULT '0',
  `movings` smallint(5) unsigned NOT NULL DEFAULT '0',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`),
  KEY `online` (`user_id`,`session_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;