-- phpMyAdmin SQL Dump
-- version 3.4.3.2
-- http://www.phpmyadmin.net
--
-- Хост: openserver:3306
-- Время создания: Авг 13 2011 г., 13:58
-- Версия сервера: 5.1.57
-- Версия PHP: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `dev_johncms-mail`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cms_pm`
--

DROP TABLE IF EXISTS `cms_pm`;
CREATE TABLE `cms_pm` (
  `pm_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pm_time` int(10) unsigned NOT NULL DEFAULT '0',
  `pm_subject` varchar(255) NOT NULL DEFAULT '',
  `pm_body` text NOT NULL,
  `pm_sender_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pm_sender_name` varchar(100) NOT NULL DEFAULT '',
  `pm_sender_ip` bigint(11) NOT NULL DEFAULT '0',
  `pm_sender_ip_via_proxy` bigint(11) NOT NULL DEFAULT '0',
  `pm_sender_ua` varchar(255) NOT NULL,
  `pm_sender_trash` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pm_sender_trash_time` int(10) unsigned NOT NULL DEFAULT '0',
  `pm_draft` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pm_id`),
  KEY `pm_sender_id` (`pm_sender_id`),
  KEY `pm_sender_trash` (`pm_sender_trash`),
  KEY `pm_sender_trash_time` (`pm_sender_trash_time`),
  KEY `sender_ip` (`pm_sender_ip`),
  KEY `sender_ip_viaproxy` (`pm_sender_ip_via_proxy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_pm_recipients`
--

DROP TABLE IF EXISTS `cms_pm_recipients`;
CREATE TABLE `cms_pm_recipients` (
  `pm_id` int(10) unsigned NOT NULL DEFAULT '0',
  `recipient_id` int(10) unsigned NOT NULL DEFAULT '0',
  `recipient_read` tinyint(1) NOT NULL DEFAULT '0',
  `recipient_trash` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `recipient_trash_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`pm_id`,`recipient_id`),
  KEY `recipient_trash` (`recipient_trash`),
  KEY `recipient_trash_time` (`recipient_trash_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
