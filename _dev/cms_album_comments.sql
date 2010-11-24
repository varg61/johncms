-- phpMyAdmin SQL Dump
-- version 3.3.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Ноя 24 2010 г., 16:32
-- Версия сервера: 5.1.40
-- Версия PHP: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `johncms_4`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cms_album_comments`
--

DROP TABLE IF EXISTS `cms_album_comments`;
CREATE TABLE `cms_album_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(10) NOT NULL,
  `sub_id` int(10) unsigned NOT NULL,
  `time` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `reply` text NOT NULL,
  `attributes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`),
  KEY `sub_id` (`sub_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
