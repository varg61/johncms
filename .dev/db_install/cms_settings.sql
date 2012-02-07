-- Dumping structure for table dev_johncms.cms_settings
DROP TABLE IF EXISTS `cms_settings`;
CREATE TABLE IF NOT EXISTS `cms_settings` (
  `key` tinytext NOT NULL,
  `val` text NOT NULL,
  PRIMARY KEY (`key`(30))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table dev_johncms.cms_settings: 26 rows
INSERT INTO `cms_settings` (`key`, `val`) VALUES
	('lng', 'ru'),
	('homeurl', 'http://localhost/johncms'),
	('email', 'oleg@batumi.biz'),
	('timeshift', '0'),
	('copyright', 'Powered by JohnCMS'),
	('admp', 'admin'),
	('flsz', '4000'),
	('gzip', '1'),
	('clean_time', '1328631879'),
	('mod_reg', '2'),
	('mod_forum', '2'),
	('mod_guest', '2'),
	('mod_lib', '2'),
	('mod_gal', '2'),
	('mod_down_comm', '1'),
	('mod_down', '2'),
	('mod_lib_comm', '1'),
	('mod_gal_comm', '1'),
	('meta_key', ''),
	('meta_desc', 'Powered by JohnCMS http://johncms.com'),
	('skindef', 'default'),
	('news', 'a:8:{s:4:"view";i:1;s:4:"size";i:200;s:8:"quantity";i:5;s:4:"days";i:3;s:6:"breaks";i:1;s:7:"smileys";i:1;s:4:"tags";i:1;s:3:"kom";i:1;}'),
	('karma', 'a:6:{s:12:"karma_points";i:5;s:10:"karma_time";i:86400;s:5:"forum";i:20;s:4:"time";i:0;s:2:"on";i:1;s:3:"adm";i:0;}'),
	('antiflood', 'a:5:{s:4:"mode";i:2;s:3:"day";i:10;s:5:"night";i:30;s:7:"dayfrom";i:10;s:5:"dayto";i:22;}'),
	('active', '1'),
	('lng_list', 'a:2:{s:2:"en";s:7:"English";s:2:"ru";s:14:"Русский";}');