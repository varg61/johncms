DROP TABLE IF EXISTS `cms_ads`;
CREATE TABLE `cms_ads` (
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


DROP TABLE IF EXISTS `cms_album_cat`;
CREATE TABLE `cms_album_cat` (
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


DROP TABLE IF EXISTS `cms_album_comments`;
CREATE TABLE `cms_album_comments` (
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


DROP TABLE IF EXISTS `cms_album_downloads`;
CREATE TABLE `cms_album_downloads` (
  `user_id` int(10) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_album_files`;
CREATE TABLE `cms_album_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `album_id` int(10) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `img_name` varchar(100) NOT NULL DEFAULT '',
  `tmb_name` varchar(100) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `comments` tinyint(1) NOT NULL DEFAULT '1',
  `comm_count` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `vote_plus` int(11) NOT NULL DEFAULT '0',
  `vote_minus` int(11) NOT NULL DEFAULT '0',
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `downloads` int(10) unsigned NOT NULL DEFAULT '0',
  `unread_comments` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `album_id` (`album_id`),
  KEY `access` (`access`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_album_views`;
CREATE TABLE `cms_album_views` (
  `user_id` int(10) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_album_votes`;
CREATE TABLE `cms_album_votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `vote` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_ban_users`;
CREATE TABLE `cms_ban_users` (
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


DROP TABLE IF EXISTS `cms_counters`;
CREATE TABLE `cms_counters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) NOT NULL DEFAULT '1',
  `name` varchar(30) NOT NULL,
  `link1` text NOT NULL,
  `link2` text NOT NULL,
  `mode` tinyint(4) NOT NULL DEFAULT '1',
  `switch` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_forum_files`;
CREATE TABLE `cms_forum_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat` int(10) unsigned NOT NULL DEFAULT '0',
  `subcat` int(10) unsigned NOT NULL DEFAULT '0',
  `topic` int(10) unsigned NOT NULL DEFAULT '0',
  `post` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `filename` text NOT NULL,
  `filetype` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `dlcount` int(10) unsigned NOT NULL DEFAULT '0',
  `del` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat` (`cat`),
  KEY `subcat` (`subcat`),
  KEY `topic` (`topic`),
  KEY `post` (`post`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_forum_rdm`;
CREATE TABLE `cms_forum_rdm` (
  `topic_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`,`user_id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_forum_vote`;
CREATE TABLE `cms_forum_vote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `topic` int(11) NOT NULL DEFAULT '0',
  `name` varchar(200) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `topic` (`topic`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_forum_vote_users`;
CREATE TABLE `cms_forum_vote_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT '0',
  `topic` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `topic` (`topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_ip_bwlist`;
CREATE TABLE `cms_ip_bwlist` (
  `ip` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_upto` int(10) unsigned NOT NULL DEFAULT '0',
  `mode` enum('black','white') NOT NULL DEFAULT 'black',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  PRIMARY KEY (`ip`),
  UNIQUE KEY `ip_upto` (`ip_upto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_mail_contacts`;
CREATE TABLE `cms_mail_contacts` (
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


DROP TABLE IF EXISTS `cms_mail_messages`;
CREATE TABLE `cms_mail_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `contact_id` int(10) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `delete` int(10) unsigned NOT NULL DEFAULT '0',
  `delete_in` int(10) unsigned NOT NULL DEFAULT '0',
  `delete_out` int(10) unsigned NOT NULL DEFAULT '0',
  `elected_in` int(10) unsigned NOT NULL DEFAULT '0',
  `elected_out` int(10) unsigned NOT NULL DEFAULT '0',
  `read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sys` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(100) NOT NULL DEFAULT '',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `filecount` int(10) unsigned NOT NULL DEFAULT '0',
  `theme` varchar(30) NOT NULL DEFAULT '',
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_modules`;
CREATE TABLE `cms_modules` (
  `module` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `cms_modules` (`module`) VALUES
        ('admin'),
        ('album'),
        ('avatars'),
        ('cabinet'),
        ('contacts'),
        ('download'),
        ('exit'),
        ('forum'),
        ('friends'),
        ('guestbook'),
        ('help'),
        ('language'),
        ('library'),
        ('login'),
        ('mail'),
        ('news'),
        ('notifications'),
        ('online'),
        ('profile'),
        ('redirect'),
        ('registration'),
        ('rss'),
        ('smileys'),
        ('users');


DROP TABLE IF EXISTS `cms_sessions`;
CREATE TABLE `cms_sessions` (
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


DROP TABLE IF EXISTS `cms_settings`;
CREATE TABLE `cms_settings` (
  `key` tinytext NOT NULL,
  `val` text NOT NULL,
  PRIMARY KEY (`key`(30))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `cms_settings` (`key`, `val`) VALUES ('lng', 'ru'),
        ('email', 'user@test.com'),
        ('timeshift', '0'),
        ('copyright', 'Powered by JohnCMS'),
        ('flsz', '2000'),
        ('gzip', '1'),
        ('clean_time', ''),
        ('mod_forum', '2'),
        ('mod_guest', '2'),
        ('mod_lib', '2'),
        ('mod_lib_comm', '1'),
        ('mod_down', '2'),
        ('mod_down_comm', '1'),
        ('meta_key', ''),
        ('meta_desc', 'Powered by JohnCMS http://johncms.com'),
        ('skindef', 'default'),
        ('news', 'a:8:{s:4:"view";i:1;s:4:"size";i:200;s:8:"quantity";i:5;s:4:"days";i:3;s:6:"breaks";i:1;s:7:"smileys";i:1;s:4:"tags";i:1;s:3:"kom";i:1;}');


DROP TABLE IF EXISTS `cms_user_guestbook`;
CREATE TABLE `cms_user_guestbook` (
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


DROP TABLE IF EXISTS `cms_user_ip`;
CREATE TABLE `cms_user_ip` (
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


DROP TABLE IF EXISTS `cms_user_relationship`;
CREATE TABLE `cms_user_relationship` (
  `from` int(10) unsigned NOT NULL DEFAULT '0',
  `to` int(10) unsigned NOT NULL DEFAULT '0',
  `value` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`from`,`to`),
  KEY `tovalue` (`to`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_user_settings`;
CREATE TABLE `cms_user_settings` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `key` varchar(32) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`user_id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_download_bookmark`;
CREATE TABLE `cms_download_bookmark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `file_id` (`file_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `cms_download_comments`;
CREATE TABLE `cms_download_comments` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `cms_download_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `refid` int(11) unsigned NOT NULL DEFAULT '0',
  `dir` text NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `name` text NOT NULL,
  `total` int(11) unsigned NOT NULL DEFAULT '0',
  `rus_name` text NOT NULL,
  `text` text NOT NULL,
  `field` int(11) NOT NULL DEFAULT '0',
  `desc` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `refid` (`refid`),
  KEY `total` (`total`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;


CREATE TABLE IF NOT EXISTS `cms_download_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `refid` int(11) unsigned NOT NULL DEFAULT '0',
  `dir` text NOT NULL,
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `name` text NOT NULL,
  `type` int(2) NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `total` int(11) unsigned NOT NULL DEFAULT '0',
  `rus_name` text NOT NULL,
  `text` text NOT NULL,
  `field` int(11) NOT NULL DEFAULT '0',
  `rate` varchar(30) NOT NULL DEFAULT '0|0',
  `about` text NOT NULL,
  `desc` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `refid` (`refid`),
  KEY `total` (`total`),
  KEY `type` (`type`),
  KEY `user_id` (`user_id`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;


DROP TABLE IF EXISTS `cms_download_more`;
CREATE TABLE `cms_download_more` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `name` text NOT NULL,
  `rus_name` text NOT NULL,
  `size` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `refid` (`refid`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `forum`;
CREATE TABLE `forum` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `from` varchar(25) NOT NULL DEFAULT '',
  `realid` int(3) NOT NULL DEFAULT '0',
  `ip` bigint(11) NOT NULL DEFAULT '0',
  `ip_via_proxy` bigint(11) NOT NULL DEFAULT '0',
  `soft` text NOT NULL,
  `text` text NOT NULL,
  `close` tinyint(1) NOT NULL DEFAULT '0',
  `close_who` varchar(25) NOT NULL DEFAULT '',
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `guest`;
CREATE TABLE `guest` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adm` tinyint(1) NOT NULL DEFAULT '0',
  `time` int(15) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `nickname` varchar(25) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `ip` bigint(11) NOT NULL DEFAULT '0',
  `user_agent` tinytext NOT NULL,
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


DROP TABLE IF EXISTS `lib`;
CREATE TABLE `lib` (
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


DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL DEFAULT '0',
  `avt` varchar(25) NOT NULL DEFAULT '',
  `name` text NOT NULL,
  `text` text NOT NULL,
  `kom` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
  `notifications` text NOT NULL,
  `relationship` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `last_visit` (`last_visit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Создаем суперпользователя
-- LOGIN:    admin
-- PASSWORD: admin
INSERT INTO `users` SET `nickname` = 'admin',
        `password` = '$2a$09$3dc6eee4535ff2912c44fO4djfEMWdsfFM9dw4NKsWCaeLIRyzB6u',
        `email` = 'admin@test.com',
        `rights` = 9,
        `level` = 1,
        `sex` = 'm',
        `about` = '',
        `notifications` = '';

--
-- Модуль статистики
--
DROP TABLE IF EXISTS `counter`;
CREATE TABLE IF NOT EXISTS `counter` (
  `date` int(11) NOT NULL,
  `browser` text NOT NULL,
  `robot` text NOT NULL,
  `robot_type` text NOT NULL,
  `ip` varchar(15) NOT NULL,
  `ip_via_proxy` varchar(15) NOT NULL,
  `ref` text NOT NULL,
  `host` int(11) NOT NULL,
  `hits` int(11) NOT NULL AUTO_INCREMENT,
  `site` text NOT NULL,
  `pop` text NOT NULL,
  `head` text NOT NULL,
  `operator` text NOT NULL,
  `country` text NOT NULL,
  `user` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`hits`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `countersall`;
CREATE TABLE IF NOT EXISTS `countersall` (
  `date` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  `host` int(11) NOT NULL,
  `yandex` int(11) NOT NULL,
  `rambler` int(11) NOT NULL,
  `google` int(11) NOT NULL,
  `mail` int(11) NOT NULL,
  `gogo` int(11) NOT NULL,
  `yahoo` int(11) NOT NULL,
  `bing` int(11) NOT NULL,
  `nigma` int(11) NOT NULL,
  `qip` int(11) NOT NULL,
  `aport` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `counter_ip_base`;
CREATE TABLE IF NOT EXISTS `counter_ip_base` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start` bigint(11) NOT NULL,
  `stop` bigint(11) NOT NULL,
  `operator` text NOT NULL,
  `country` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=267 ;

INSERT INTO `counter_ip_base` (`id`, `start`, `stop`, `operator`, `country`) VALUES
(2, 1494507776, 1494508031, 'Utel', 'Россия'),
(3, 1601019392, 1601019647, 'UMC', 'Украина'),
(4, 1578729472, 1578745855, 'Компьютеры', 'Россия'),
(5, 1360162816, 1360164350, 'BWC', 'Россия'),
(6, -1016011816, -1016011781, 'ЕТК', 'Россия'),
(7, 1588723712, 1588854783, 'Utel', 'Украина'),
(8, 1427813376, 1427814399, 'Мегафон', 'Россия'),
(9, -1041268736, -1041268225, 'life:)', 'Украина'),
(10, 1427809280, 1427810303, 'Мегафон', 'Россия'),
(11, 1427812352, 1427813375, 'Мегафон', 'Россия'),
(12, 1402278912, 1402279935, 'Мегафон', 'Россия'),
(13, 1536099328, 1536099839, 'СМАРТС', 'Россия'),
(14, 1295298720, 1295298751, 'Компьютеры', 'Украина'),
(15, 1333559296, 1333575679, 'life:)', 'Украина'),
(16, 1402275840, 1402276863, 'Мегафон', 'Россия'),
(17, -730603520, -730603009, 'life:)', 'Украина'),
(18, -1037271040, -1037270785, 'Компьютеры', 'Украина'),
(19, -649949184, -649948161, 'MTS', 'Россия'),
(20, -649398016, -649397761, 'MTS', 'Россия'),
(21, 1588920320, 1588932607, 'Компьютеры', 'Россия'),
(22, 1295269888, 1295286271, 'MTS', 'Украина'),
(23, 1602486272, 1602551807, 'Компьютеры', 'Украина'),
(24, 1536278528, 1536286719, 'life:)', 'Украина'),
(25, 1333575680, 1333592063, 'life:)', 'Украина'),
(26, 1486487552, 1486553087, 'life:)', 'Украина'),
(27, -1139802112, -1139539969, 'Utel', 'Россия'),
(28, 1310210048, 1310211071, 'СМАРТС', 'Россия'),
(29, -730071040, -730066945, 'Компьютеры', 'Россия'),
(30, -734353408, -734351361, 'life:)', 'Украина'),
(31, 1402279936, 1402280959, 'Мегафон', 'Россия'),
(32, 1519796224, 1519800319, 'Utel', 'Россия'),
(33, -646557696, -646556673, 'Билайн', 'Россия'),
(34, 1596293120, 1596325887, 'MTS', 'Россия'),
(35, 1347538944, 1347543039, 'Компьютеры', 'Россия'),
(36, 1518993408, 1519058943, 'TELE2', 'Россия'),
(37, 1519321088, 1519386623, 'Компьютеры', 'Россия'),
(38, -730284440, -730284433, 'Компьютеры', 'Россия'),
(39, -715698176, -715694081, 'MTS', 'Россия'),
(40, 1402273792, 1402275839, 'Мегафон', 'Россия'),
(41, 1410269184, 1410334719, 'BITE', 'Литва'),
(42, 1441411072, 1441415167, 'Компьютеры', 'Россия'),
(43, 1572081664, 1572083711, 'Компьютеры', 'Россия'),
(44, -653729792, -653726721, 'MTS', 'Россия'),
(45, 1536286720, 1536294911, 'life:)', 'Украина'),
(46, 1336176640, 1336180735, 'Компьютеры', 'Россия'),
(47, -649397504, -649397249, 'MTS', 'Россия'),
(48, 1505595392, 1505599487, 'Компьютеры', 'Россия'),
(49, 1402284032, 1402285055, 'Мегафон', 'Россия'),
(50, 1579745280, 1579778047, 'Компьютеры', 'Россия'),
(51, 1294467072, 1294499839, 'Компьютеры', 'Россия'),
(52, 1550958592, 1550974975, 'Компьютеры', 'Украина'),
(53, 1467375616, 1467383807, 'Компьютеры', 'Германия'),
(54, 1402277888, 1402278911, 'Мегафон', 'Россия'),
(55, 1402281984, 1402283007, 'Мегафон', 'Россия'),
(56, 1402286080, 1402287103, 'Мегафон', 'Россия'),
(57, 1402287104, 1402288127, 'Мегафон', 'Россия'),
(58, -1043733504, -1043732481, 'Мегафон', 'Россия'),
(59, -712935520, -712935489, 'Компьютеры', 'Россия'),
(60, 1346621440, 1346622463, 'Компьютеры', 'Россия'),
(61, 1433657344, 1433657599, 'Билайн', 'Россия'),
(62, -646560768, -646559745, 'Билайн', 'Россия'),
(63, -646558720, -646557697, 'Билайн', 'Россия'),
(64, -646556672, -646555649, 'Билайн', 'Россия'),
(65, -646555648, -646554625, 'Билайн', 'Россия'),
(66, -646554624, -646553601, 'Билайн', 'Россия'),
(67, 1047070464, 1047072255, 'Utel', 'Россия'),
(68, 1401450496, 1401450751, 'Utel', 'Россия'),
(69, 1401451520, 1401451647, 'Utel', 'Россия'),
(70, 1425981440, 1425981695, 'Utel', 'Россия'),
(71, 1441366016, 1441371903, 'Utel', 'Россия'),
(72, -1022602752, -1022602497, 'Utel', 'Россия'),
(73, -730292224, -730291713, 'Utel', 'Россия'),
(74, -730290464, -730290433, 'Utel', 'Россия'),
(75, -723779584, -723775489, 'Utel', 'Россия'),
(76, -653151232, -653150977, 'Utel', 'Россия'),
(77, -653150720, -653150465, 'Utel', 'Россия'),
(78, 1519779840, 1519783935, 'Utel', 'Россия'),
(79, 1347592192, 1347600383, 'BWC', 'Россия'),
(80, -706447360, -706446337, 'BWC', 'Россия'),
(81, -1020368128, -1020367873, 'BWC', 'Россия'),
(82, 1042394624, 1042394879, 'MTS', 'Россия'),
(83, 1346950400, 1346950655, 'MTS', 'Россия'),
(84, 1347674112, 1347674623, 'MTS', 'Россия'),
(85, 1360933376, 1360933887, 'MTS', 'Россия'),
(86, 1372794624, 1372794879, 'MTS', 'Россия'),
(87, 1410457600, 1410459647, 'MTS', 'Россия'),
(88, -1036610560, -1036609537, 'MTS', 'Россия'),
(89, -1017778688, -1017778433, 'MTS', 'Россия'),
(90, -1018539008, -1018538753, 'MTS', 'Россия'),
(91, -1013514240, -1013513985, 'MTS', 'Россия'),
(92, -1007707904, -1007707649, 'MTS', 'Россия'),
(93, -735278080, -735277825, 'MTS', 'Россия'),
(94, -732132608, -732132417, 'MTS', 'Россия'),
(95, -732132416, -732132353, 'MTS', 'Россия'),
(96, -716135424, -716135169, 'MTS', 'Россия'),
(97, -715707904, -715701761, 'MTS', 'Россия'),
(98, -715700224, -715699361, 'MTS', 'Россия'),
(99, -649400320, -649398273, 'MTS', 'Россия'),
(100, -646488576, -646488065, 'MTS', 'Россия'),
(101, 1535627776, 1535628031, 'MTS', 'Россия'),
(102, -715699200, -715698689, 'MTS', 'Россия'),
(103, 1404862464, 1404870655, 'TELE2', 'Россия'),
(104, 1404846080, 1404854271, 'TELE2', 'Россия'),
(105, 1404854272, 1404862463, 'TELE2', 'Россия'),
(106, 1404837888, 1404846079, 'TELE2', 'Россия'),
(107, 1404829696, 1404837887, 'TELE2', 'Россия'),
(108, 1518927872, 1518944255, 'TELE2', 'Россия'),
(109, -2097938432, -2097872897, 'TELE2', 'Швеция'),
(110, -644598784, -644598529, 'Мотив', 'Россия'),
(111, -644599808, -644599553, 'Мотив', 'Россия'),
(112, -1016011008, -1016010753, 'ЕТК', 'Россия'),
(113, -1016007168, -1016006913, 'ЕТК', 'Россия'),
(114, 1389383040, 1389383167, 'НСС', 'Россия'),
(115, 1432330240, 1432334335, 'НСС', 'Россия'),
(116, -1012795392, -1012793345, 'НСС', 'Россия'),
(117, -709795840, -709793025, 'НСС', 'Россия'),
(118, -1027958784, -1027958529, 'НСС', 'Россия'),
(119, -1016974848, -1016974337, 'НСС', 'Россия'),
(120, 1441609984, 1441610239, 'НСС', 'Россия'),
(121, 1346736128, 1346737151, 'СМАРТС', 'Россия'),
(122, -1034680960, -1034680833, 'СМАРТС', 'Россия'),
(123, -649236224, -649236217, 'СМАРТС', 'Россия'),
(124, -707747840, -707747585, 'СМАРТС', 'Россия'),
(125, -646672384, -646671361, 'STEK GSM', 'Россия'),
(126, 1481787392, 1481787647, 'Татинком-Т', 'Россия'),
(127, -642969600, -642965505, 'Татинком-Т', 'Россия'),
(128, 1506762752, 1506763007, 'Татинком-Т', 'Россия'),
(129, 1052193280, 1052193535, 'MTT', 'Россия'),
(130, 1347125248, 1347125759, 'MTT', 'Россия'),
(131, 1347125248, 1347129343, 'MTT', 'Россия'),
(132, 1358118912, 1358119423, 'НТК', 'Россия'),
(133, 1358119424, 1358120703, 'НТК', 'Россия'),
(134, 1406740480, 1406746623, 'Sky Link', 'Россия'),
(135, -730374144, -730373377, 'Sky Link', 'Россия'),
(136, -730371328, -730370561, 'Sky Link', 'Россия'),
(137, -730367488, -730365953, 'Sky Link', 'Россия'),
(138, -729718784, -729717249, 'Sky Link', 'Россия'),
(139, -729717248, -729716737, 'Sky Link', 'Россия'),
(140, -729716736, -729712641, 'Sky Link', 'Россия'),
(141, -716144640, -716111873, 'Sky Link', 'Россия'),
(142, -646754304, -646754049, 'Sky Link', 'Россия'),
(143, 1509752832, 1509756927, 'Sky Link', 'Россия'),
(144, 1386348544, 1386349567, 'Компьютеры', 'Россия'),
(145, -1054262272, -1054261249, 'Киевстар', 'Украина'),
(146, -734354944, -734354433, 'Киевстар', 'Украина'),
(147, -734354432, -734353409, 'Киевстар', 'Украина'),
(148, 1360467968, 1360470015, 'Киевстар', 'Украина'),
(149, 1360465920, 1360467967, 'Киевстар', 'Украина'),
(150, -734351360, -734347265, 'life:)', 'Украина'),
(151, -734355456, -734355201, 'life:)', 'Украина'),
(152, 1295253504, 1295269887, 'UMC', 'Украина'),
(153, 1358905344, 1358905471, 'UMC', 'Украина'),
(154, 1358907392, 1358907519, 'UMC', 'Украина'),
(155, 1358907648, 1358907775, 'UMC', 'Украина'),
(156, 1358907904, 1358908031, 'UMC', 'Украина'),
(157, 1358908160, 1358908239, 'UMC', 'Украина'),
(158, 1358908416, 1358908431, 'UMC', 'Украина'),
(159, 1358908928, 1358909439, 'UMC', 'Украина'),
(160, 1490436096, 1490444287, 'UMC', 'Украина'),
(161, 1490444288, 1490452479, 'UMC', 'Украина'),
(162, -1010987520, -1010987009, 'Opera-Mini', 'Норвегия'),
(163, 1403965440, 1403967487, 'Компьютеры', 'Россия'),
(164, -649397760, -649397505, 'MTS', 'Россия'),
(165, -1036908288, -1036908033, 'Компьютеры', 'Украина'),
(166, 1593212416, 1593212927, 'Opera-Mini', 'Норвегия'),
(167, 1317601280, 1317609471, 'Компьютеры', 'Украина'),
(168, 1570762752, 1570763775, 'Компьютеры', 'Россия'),
(169, 1360164352, 1360166910, 'BWC', 'Россия'),
(170, 1536521728, 1536521983, 'Компьютеры', 'Россия'),
(171, 1347677184, 1347678207, 'MTS', 'Россия'),
(172, -733229056, -733224961, 'Good Line', 'Россия'),
(173, -733233152, -733229057, 'Компьютеры', 'Россия'),
(174, -712548352, -712545901, 'Компьютеры', 'Россия'),
(175, -1019551744, -1019543553, 'Компьютеры', 'Украина'),
(176, 1602486272, 1602748415, 'Компьютеры', 'Украина'),
(177, 1572192256, 1572200447, 'Компьютеры', 'Россия'),
(178, 1572087808, 1572089855, 'Компьютеры', 'Россия'),
(179, 1467285504, 1467286527, 'Компьютеры', 'Россия'),
(180, 1568178176, 1568194559, 'Компьютеры', 'Россия'),
(181, 1466826752, 1466859519, 'TELE2', 'Латвия'),
(182, 1365222400, 1365223423, 'GE-MAGTICOM', 'Грузия'),
(183, 1540055040, 1540056063, 'Opera-Mini', 'Норвегия'),
(184, 1587085312, 1587150847, 'Киевстар', 'Украина'),
(185, 1595408384, 1595932671, 'Компьютеры', 'Россия'),
(186, 1592803328, 1592811519, 'Компьютеры', 'Россия'),
(187, 1475812368, 1475812371, 'Компьютеры', 'Казахстан'),
(188, 1592811520, 1592815615, 'НСС', 'Россия'),
(189, 1519058944, 1519124479, 'TELE2', 'Россия'),
(190, 1346623232, 1346625535, 'НТК', 'Россия'),
(191, -1016005120, -1016004865, 'Компьютеры', 'Россия'),
(192, 1437532160, 1437597695, 'Компьютеры', 'Россия'),
(193, 1550867968, 1550868223, 'Компьютеры', 'Украина'),
(194, -1020264704, -1020264449, 'KCELL', 'Казахстан'),
(195, 1297055744, 1297063935, 'Компьютеры', 'Россия'),
(196, 1360941312, 1360941567, 'Компьютеры', 'Беларусь'),
(197, 1550942208, 1550958591, 'Компьютеры', 'Украина'),
(198, 1042368000, 1042368127, 'Компьютеры', 'Россия'),
(199, 1307182080, 1307185151, 'Компьютеры', 'Россия'),
(200, 1334149120, 1334153215, 'Компьютеры', 'Россия'),
(201, 1357411584, 1357411839, 'Компьютеры', 'Норвегия'),
(202, 1298948736, 1298948799, 'Компьютеры', 'Россия'),
(203, 1578948608, 1578950655, 'Компьютеры', 'Россия'),
(204, 1495015424, 1495017471, 'Компьютеры', 'Молдавия'),
(205, 1053675520, 1053679615, 'Компьютеры', 'Латвия'),
(206, 1406855168, 1406856191, 'Компьютеры', 'Украина'),
(207, -644471296, -644471041, 'Компьютеры', 'Россия'),
(208, 1505605632, 1505607679, 'Компьютеры', 'Россия'),
(209, 1539824896, 1539825151, 'Компьютеры', 'Узбекистан'),
(210, 1306433280, 1306433535, 'Компьютеры', 'Украина'),
(211, 2130706433, 2130706433, 'Localhost', 'Локальный сервер'),
(212, 3648409600, 3648410623, 'Билайн', 'Россия'),
(213, 3239825408, 3239825919, 'Не определён', 'Украина'),
(214, 3253698560, 3253699071, 'Билайн', 'Украина'),
(215, 3273026560, 3273027583, 'МТС', 'Россия'),
(216, 1582137344, 1582139391, 'ЦТС Интернет', 'Россия'),
(217, 3645018112, 3645019135, 'МТС', 'Россия'),
(218, 1519124480, 1519190015, 'TELE2', 'Россия'),
(219, 1404870656, 1404872703, 'TELE2', 'Латвия'),
(220, 3261777408, 3261777919, 'KCELL', 'Казахстан'),
(221, 1603907584, 1603911679, 'МТС', 'Россия'),
(222, 1402275840, 1402276863, 'Мегафон', 'Россия'),
(223, 1599307776, 1599315967, 'СПАРК', 'Россия'),
(224, 1601011712, 1601036287, 'МТС', 'Украина'),
(225, 3648411648, 3648412671, 'Билайн', 'Россия'),
(226, 1402285056, 1402286079, 'Мегафон', 'Россия'),
(227, 1402280960, 1402281983, 'Мегафон', 'Россия'),
(228, -1033189888, -1033189377, 'KCELL', 'Казахстан'),
(229, 1839349760, 1839366143, 'Киевстар', 'Украина'),
(230, 1407035392, 1407036415, 'Sharq Telekom', 'Узбекистан'),
(231, 1357902336, 1357902847, 'Opera-Mini', 'Не определена'),
(232, -1299906560, -1299890177, 'МТС', 'Украина'),
(233, -1299857408, -1299849217, 'МТС', 'Украина'),
(234, -1299881984, -1299873793, 'МТС', 'Украина'),
(235, -1299873792, -1299857409, 'МТС', 'Украина'),
(236, -1055797248, -1055796993, 'Norma-Plus', 'Украина'),
(237, 1427814400, 1427815423, 'Мегафон', 'Россия'),
(238, 1603903488, 1603907583, 'МТС', 'Россия'),
(239, 1306001408, 1306132479, 'TELE2', 'Россия'),
(240, 1402283008, 1402284031, 'Мегафон', 'Россия'),
(241, 1123631104, 1123639295, 'Google Bot', 'BotLand'),
(242, 1311641561, 1311641561, 'SymbOS Monitoring', 'BotLand'),
(243, -1297580032, -1297563649, 'MTS', 'Белорусия'),
(244, 1518665728, 1518698495, 'TELE2', 'Россия'),
(245, 1600975872, 1600976127, 'Yandex Bot', 'BotLand'),
(246, 1427826688, 1427828735, 'Мегафон', 'Россия'),
(247, 38273024, 38535167, 'KCELL', 'Казахстан'),
(248, 1600952064, 1600952319, 'YandexBOT', 'BotLand'),
(249, 1600977152, 1600977407, 'YandexBOT', 'BotLand'),
(250, 1297619968, 1297620223, 'YandexBOT', 'BotLand'),
(251, 1373519872, 1373520383, 'RamblerBOT', 'BotLand'),
(252, -1299644416, -1299611649, 'Киевстар', 'Украина'),
(253, 1427823616, 1427824639, 'Мегафон', 'Кыргызстан'),
(254, 1441570816, 1441571327, 'Компьютеры', 'Россия'),
(255, 1296704512, 1296705535, 'Velcom', 'Белорусия'),
(256, -1303584768, -1303582721, 'Спарк', 'Россия'),
(257, 3582611456, 3582615551, 'Спарк', 'Россия'),
(258, -1303969792, -1303904257, 'North-West Telecom', 'Россия'),
(259, 1136852992, 1136918527, 'YahooBOT', 'BotLand'),
(260, 1427806208, 1427807231, 'Мегафон', 'Россия'),
(261, -1302593536, -1302528001, 'Ukrtelecom', 'Украина'),
(262, -1305526272, -1305518081, 'Уралсвязьинформ', 'Россия'),
(263, -819068928, -819003393, 'BingBOT', 'BotLand'),
(264, 1427826688, 1427828735, 'Мегафон', 'Россия');

DROP TABLE IF EXISTS `stat_robots`;
CREATE TABLE `stat_robots` (
  `engine` text NOT NULL,
  `date` int(11) NOT NULL,
  `url` text NOT NULL,
  `query` text NOT NULL,
  `ua` text NOT NULL,
  `ip` bigint(11) NOT NULL,
  `count` int(11) NOT NULL,
  `today` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `cms_modules` (`module`) VALUES ('stats');
INSERT INTO `cms_settings` (`key`, `val`) VALUES ('stat', '3');