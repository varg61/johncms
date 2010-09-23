/*
Navicat MySQL Data Transfer

Source Server         : NEW johncms.com
Source Server Version : 50077
Source Host           : localhost:3306
Source Database       : admin_johncms

Target Server Type    : MYSQL
Target Server Version : 50077
File Encoding         : 65001

Date: 2010-09-23 12:42:14
*/

-- ----------------------------
-- Table structure for `cms_ads`
-- ----------------------------
DROP TABLE IF EXISTS `cms_ads`;
CREATE TABLE `cms_ads` (
  `id` int(11) NOT NULL auto_increment,
  `type` int(2) NOT NULL,
  `view` int(2) NOT NULL,
  `layout` int(2) NOT NULL,
  `count` int(11) NOT NULL,
  `count_link` int(11) NOT NULL,
  `name` text NOT NULL,
  `link` text NOT NULL,
  `to` int(10) NOT NULL default '0',
  `color` varchar(10) NOT NULL,
  `time` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `mesto` int(2) NOT NULL,
  `bold` tinyint(1) NOT NULL default '0',
  `italic` tinyint(1) NOT NULL default '0',
  `underline` tinyint(1) NOT NULL default '0',
  `show` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;