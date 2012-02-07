-- Dumping structure for table dev_johncms.cms_ads
DROP TABLE IF EXISTS `cms_ads`;
CREATE TABLE IF NOT EXISTS `cms_ads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL,
  `view` int(2) NOT NULL,
  `layout` int(2) NOT NULL,
  `count` int(11) NOT NULL,
  `count_link` int(11) NOT NULL,
  `name` text NOT NULL,
  `link` text NOT NULL,
  `to` int(10) NOT NULL DEFAULT '0',
  `color` varchar(10) NOT NULL,
  `time` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `mesto` int(2) NOT NULL,
  `bold` tinyint(1) NOT NULL DEFAULT '0',
  `italic` tinyint(1) NOT NULL DEFAULT '0',
  `underline` tinyint(1) NOT NULL DEFAULT '0',
  `show` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;