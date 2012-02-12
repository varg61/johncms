-- Dumping structure for table dev_johncms.cms_modules
DROP TABLE IF EXISTS `cms_modules`;
CREATE TABLE IF NOT EXISTS `cms_modules` (
  `module` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table dev_johncms.cms_modules: 13 rows
INSERT INTO `cms_modules` (`module`) VALUES
	('admin'),
	('album'),
	('avatars'),
	('exit'),
	('help'),
	('language'),
	('login'),
	('mail'),
	('mainmenu'),
	('online'),
	('registration'),
	('rss'),
	('smileys'),
	('users');