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

-- Dumping structure for table dev_johncms.cms_album_comments
DROP TABLE IF EXISTS `cms_album_comments`;
CREATE TABLE IF NOT EXISTS `cms_album_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_id` int(10) unsigned NOT NULL,
  `time` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `reply` text NOT NULL,
  `attributes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_id` (`sub_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.cms_album_downloads
DROP TABLE IF EXISTS `cms_album_downloads`;
CREATE TABLE IF NOT EXISTS `cms_album_downloads` (
  `user_id` int(10) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.cms_album_files
DROP TABLE IF EXISTS `cms_album_files`;
CREATE TABLE IF NOT EXISTS `cms_album_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `album_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `img_name` varchar(100) NOT NULL,
  `tmb_name` varchar(100) NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `comments` tinyint(1) NOT NULL DEFAULT '1',
  `comm_count` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `vote_plus` int(11) NOT NULL,
  `vote_minus` int(11) NOT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `downloads` int(10) unsigned NOT NULL DEFAULT '0',
  `unread_comments` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `album_id` (`album_id`),
  KEY `access` (`access`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.cms_album_views
DROP TABLE IF EXISTS `cms_album_views`;
CREATE TABLE IF NOT EXISTS `cms_album_views` (
  `user_id` int(10) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.cms_album_votes
DROP TABLE IF EXISTS `cms_album_votes`;
CREATE TABLE IF NOT EXISTS `cms_album_votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `vote` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.cms_ban_users
DROP TABLE IF EXISTS `cms_ban_users`;
CREATE TABLE IF NOT EXISTS `cms_ban_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `ban_time` int(11) NOT NULL DEFAULT '0',
  `ban_while` int(11) NOT NULL DEFAULT '0',
  `ban_type` tinyint(4) NOT NULL DEFAULT '1',
  `ban_who` varchar(30) NOT NULL DEFAULT '',
  `ban_ref` int(11) NOT NULL DEFAULT '0',
  `ban_reason` text NOT NULL,
  `ban_raz` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ban_time` (`ban_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.cms_counters
DROP TABLE IF EXISTS `cms_counters`;
CREATE TABLE IF NOT EXISTS `cms_counters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) NOT NULL DEFAULT '1',
  `name` varchar(30) NOT NULL,
  `link1` text NOT NULL,
  `link2` text NOT NULL,
  `mode` tinyint(4) NOT NULL DEFAULT '1',
  `switch` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.cms_forum_files
DROP TABLE IF EXISTS `cms_forum_files`;
CREATE TABLE IF NOT EXISTS `cms_forum_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat` int(10) NOT NULL,
  `subcat` int(10) NOT NULL,
  `topic` int(10) NOT NULL,
  `post` int(10) NOT NULL,
  `time` int(11) NOT NULL,
  `filename` text NOT NULL,
  `filetype` tinyint(4) NOT NULL,
  `dlcount` int(10) NOT NULL,
  `del` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat` (`cat`),
  KEY `subcat` (`subcat`),
  KEY `topic` (`topic`),
  KEY `post` (`post`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.cms_forum_rdm
DROP TABLE IF EXISTS `cms_forum_rdm`;
CREATE TABLE IF NOT EXISTS `cms_forum_rdm` (
  `topic_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`,`user_id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.cms_forum_vote
DROP TABLE IF EXISTS `cms_forum_vote`;
CREATE TABLE IF NOT EXISTS `cms_forum_vote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `topic` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `topic` (`topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.cms_forum_vote_users
DROP TABLE IF EXISTS `cms_forum_vote_users`;
CREATE TABLE IF NOT EXISTS `cms_forum_vote_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT '0',
  `topic` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `topic` (`topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.cms_ip_bwlist
DROP TABLE IF EXISTS `cms_ip_bwlist`;
CREATE TABLE IF NOT EXISTS `cms_ip_bwlist` (
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_upto` int(10) unsigned NOT NULL DEFAULT '0',
  `mode` enum('black','white') NOT NULL DEFAULT 'black',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  PRIMARY KEY (`ip`),
  UNIQUE KEY `ip_upto` (`ip_upto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cms_contacts`;
DROP TABLE IF EXISTS `cms_messages`;

-- Dumping structure for table cms_mail_contacts
DROP TABLE IF EXISTS `cms_mail_contacts`;
CREATE TABLE IF NOT EXISTS `cms_mail_contacts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `contact_id` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL,
  `delete` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `banned` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `archive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `access` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `friends` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`contact_id`),
  UNIQUE KEY `contact_id` (`contact_id`,`user_id`),
  KEY `time` (`time`),
  KEY `delete` (`delete`),
  KEY `banned` (`banned`),
  KEY `archive` (`archive`),
  KEY `access` (`access`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- Dumping structure for table cms_mail_messages
DROP TABLE IF EXISTS `cms_mail_messages`;
CREATE TABLE IF NOT EXISTS `cms_mail_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `delete` int(10) unsigned NOT NULL DEFAULT '0',
  `delete_in` int(10) unsigned NOT NULL,
  `delete_out` int(10) unsigned NOT NULL DEFAULT '0',
  `elected_in` int(10) unsigned NOT NULL DEFAULT '0',
  `elected_out` int(10) unsigned NOT NULL DEFAULT '0',
  `read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sys` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(100) NOT NULL,
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `filecount` int(10) unsigned NOT NULL DEFAULT '0',
  `theme` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `contact_id` (`contact_id`),
  KEY `time` (`time`),
  KEY `delete` (`delete`),
  KEY `delete_in` (`delete_in`),
  KEY `delete_out` (`delete_out`),
  KEY `elected_in` (`elected_in`),
  KEY `elected_out` (`elected_out`),
  KEY `read` (`read`),
  KEY `sys` (`sys`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Dumping structure for table dev_johncms.cms_modules
DROP TABLE IF EXISTS `cms_modules`;
CREATE TABLE IF NOT EXISTS `cms_modules` (
  `module` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table dev_johncms.cms_modules: 16 rows
INSERT INTO `cms_modules` (`module`) VALUES ('admin'),
        ('album'),
        ('avatars'),
        ('cabinet'),
        ('contacts'),
        ('exit'),
        ('friends'),
        ('guestbook'),
        ('help'),
        ('language'),
        ('library'),
        ('login'),
        ('mail'),
        ('online'),
        ('profile'),
        ('redirect'),
        ('registration'),
        ('rss'),
        ('smileys'),
        ('users');

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

-- Dumping structure for table dev_johncms.cms_settings
DROP TABLE IF EXISTS `cms_settings`;
CREATE TABLE IF NOT EXISTS `cms_settings` (
  `key` tinytext NOT NULL,
  `val` text NOT NULL,
  PRIMARY KEY (`key`(30))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table dev_johncms.cms_settings: 26 rows
INSERT INTO `cms_settings` (`key`, `val`) VALUES ('lng', 'ru'),
        ('email', 'oleg@batumi.biz'),
        ('timeshift', '0'),
        ('copyright', 'Powered by JohnCMS'),
        ('flsz', '4000'),
        ('gzip', '1'),
        ('clean_time', '1328631879'),
        ('mod_forum', '2'),
        ('mod_guest', '2'),
        ('mod_lib', '2'),
        ('mod_gal', '2'),
        ('mod_down_comm', '1'),
        ('mod_down', '2'),
        ('mod_lib_comm', '1'),
        ('mod_gal_comm', '1'),
        ('meta_key', ''),
        ('meta_desc',
                'Powered by JohnCMS http://johncms.com'),
        ('skindef', 'default'),
        ('news', 'a:8:{s:4:"view";i:1;s:4:"size";i:200;s:8:"quantity";i:5;s:4:"days";i:3;s:6:"breaks";i:1;s:7:"smileys";i:1;s:4:"tags";i:1;s:3:"kom";i:1;}'),
        ('lng_list', 'a:2:{s:2:"en";s:7:"English";s:2:"ru";s:14:"Русский";}');

-- Dumping structure for table dev_johncms.cms_user_guestbook
DROP TABLE IF EXISTS `cms_user_guestbook`;
CREATE TABLE IF NOT EXISTS `cms_user_guestbook` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_id` int(10) unsigned NOT NULL,
  `time` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `reply` text NOT NULL,
  `attributes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_id` (`sub_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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

-- Dumping structure for table dev_johncms.cms_user_settings
DROP TABLE IF EXISTS `cms_user_settings`;
CREATE TABLE IF NOT EXISTS `cms_user_settings` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `key` varchar(32) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`user_id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.download
DROP TABLE IF EXISTS `download`;
CREATE TABLE IF NOT EXISTS `download` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL DEFAULT '0',
  `adres` text NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `name` text NOT NULL,
  `type` varchar(4) NOT NULL DEFAULT '',
  `avtor` varchar(25) NOT NULL DEFAULT '',
  `ip` text NOT NULL,
  `soft` text NOT NULL,
  `text` text NOT NULL,
  `screen` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `refid` (`refid`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.forum
DROP TABLE IF EXISTS `forum`;
CREATE TABLE IF NOT EXISTS `forum` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `from` varchar(25) NOT NULL DEFAULT '',
  `realid` int(3) NOT NULL DEFAULT '0',
  `ip` bigint(11) NOT NULL DEFAULT '0',
  `ip_via_proxy` bigint(11) NOT NULL DEFAULT '0',
  `soft` text NOT NULL,
  `text` text NOT NULL,
  `close` tinyint(1) NOT NULL DEFAULT '0',
  `close_who` varchar(25) NOT NULL,
  `vip` tinyint(1) NOT NULL DEFAULT '0',
  `edit` text NOT NULL,
  `tedit` int(11) NOT NULL DEFAULT '0',
  `kedit` int(2) NOT NULL DEFAULT '0',
  `curators` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `refid` (`refid`),
  KEY `type` (`type`),
  KEY `time` (`time`),
  KEY `close` (`close`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.gallery
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `type` varchar(2) NOT NULL DEFAULT '',
  `avtor` varchar(25) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `name` text NOT NULL,
  `user` binary(1) NOT NULL DEFAULT '\0',
  `ip` text NOT NULL,
  `soft` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `refid` (`refid`),
  KEY `type` (`type`),
  KEY `time` (`time`),
  KEY `avtor` (`avtor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.guest
DROP TABLE IF EXISTS `guest`;
CREATE TABLE IF NOT EXISTS `guest` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adm` tinyint(1) NOT NULL DEFAULT '0',
  `time` int(15) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(25) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `ip` bigint(11) NOT NULL DEFAULT '0',
  `browser` tinytext NOT NULL,
  `admin` varchar(25) NOT NULL DEFAULT '',
  `otvet` text NOT NULL,
  `otime` int(15) NOT NULL DEFAULT '0',
  `edit_who` varchar(20) NOT NULL DEFAULT '',
  `edit_time` int(11) NOT NULL DEFAULT '0',
  `edit_count` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `ip` (`ip`),
  KEY `adm` (`adm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.lib
DROP TABLE IF EXISTS `lib`;
CREATE TABLE IF NOT EXISTS `lib` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `type` varchar(4) NOT NULL DEFAULT '',
  `name` tinytext NOT NULL,
  `announce` text NOT NULL,
  `avtor` varchar(25) NOT NULL DEFAULT '',
  `text` mediumtext NOT NULL,
  `ip` int(11) NOT NULL DEFAULT '0',
  `soft` text NOT NULL,
  `moder` tinyint(1) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `moder` (`moder`),
  KEY `time` (`time`),
  KEY `refid` (`refid`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.news
DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL DEFAULT '0',
  `avt` varchar(25) NOT NULL DEFAULT '',
  `name` text NOT NULL,
  `text` text NOT NULL,
  `kom` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table dev_johncms.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(32) NOT NULL DEFAULT '',
  `change_time` int(10) unsigned NOT NULL DEFAULT '0',
  `password` char(60) NOT NULL DEFAULT '',
  `token` char(32) NOT NULL DEFAULT '',
  `login_try` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL DEFAULT '',
  `rights` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ban` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sex` enum('m','w') NOT NULL DEFAULT 'm',
  `imname` varchar(50) NOT NULL DEFAULT '',
  `birth` date NOT NULL DEFAULT '0000-00-00',
  `count_comments` int(10) unsigned NOT NULL DEFAULT '0',
  `count_forum` int(10) unsigned NOT NULL DEFAULT '0',
  `join_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_visit` int(10) unsigned NOT NULL DEFAULT '0',
  `icq` int(10) unsigned NOT NULL DEFAULT '0',
  `skype` varchar(50) NOT NULL DEFAULT '',
  `siteurl` varchar(100) NOT NULL DEFAULT '',
  `about` text NOT NULL,
  `live` varchar(100) NOT NULL DEFAULT '',
  `tel` varchar(100) NOT NULL DEFAULT '',
  `status` varchar(50) NOT NULL DEFAULT '',
  `mailvis` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lastpost` int(10) unsigned NOT NULL DEFAULT '0',
  `rest_code` varchar(32) NOT NULL DEFAULT '',
  `rest_time` int(10) unsigned NOT NULL DEFAULT '0',
  `comm_count` int(10) unsigned NOT NULL DEFAULT '0',
  `comm_old` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_via_proxy` int(10) unsigned NOT NULL DEFAULT '0',
  `user_agent` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `lastdate` (`last_visit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Создаем суперпользователя
-- LOGIN:    admin
-- PASSWORD: admin
INSERT INTO `users` SET
        `nickname` = 'admin',
        `password` = '$2a$09$3dc6eee4535ff2912c44fO4djfEMWdsfFM9dw4NKsWCaeLIRyzB6u',
        `email` = 'admin@test.com',
        `rights` = 9,
        `level` = 1,
        `sex` = 'm';