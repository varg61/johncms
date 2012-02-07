-- Dumping structure for table dev_johncms.cms_user_ip
DROP TABLE IF EXISTS `cms_user_ip`;
CREATE TABLE IF NOT EXISTS `cms_user_ip` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_via_proxy` int(10) unsigned NOT NULL DEFAULT '0',
  `useragent` varchar(150) NOT NULL DEFAULT '',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ip` (`ip`),
  KEY `ip_via_proxy` (`ip_via_proxy`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;