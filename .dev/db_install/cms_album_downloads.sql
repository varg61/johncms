-- Dumping structure for table dev_johncms.cms_album_downloads
DROP TABLE IF EXISTS `cms_album_downloads`;
CREATE TABLE IF NOT EXISTS `cms_album_downloads` (
  `user_id` int(10) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;