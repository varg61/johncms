-- Dumping structure for table dev_johncms.cms_ip_bwlist
DROP TABLE IF EXISTS `cms_ip_bwlist`;
CREATE TABLE IF NOT EXISTS `cms_ip_bwlist` (
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_upto` int(10) unsigned NOT NULL DEFAULT '0',
  `mode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `reason` text NOT NULL,
  PRIMARY KEY (`ip`),
  UNIQUE KEY `ip_upto` (`ip_upto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;