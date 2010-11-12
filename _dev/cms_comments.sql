-- phpMyAdmin SQL Dump
-- version 3.3.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Ноя 12 2010 г., 14:53
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
-- Структура таблицы `cms_comments`
--

DROP TABLE IF EXISTS `cms_comments`;
CREATE TABLE `cms_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(10) NOT NULL,
  `sub_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `text` text NOT NULL,
  `reply` text NOT NULL,
  `time` int(11) NOT NULL,
  `ip` bigint(12) NOT NULL,
  `browser` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`),
  KEY `sub_id` (`sub_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `cms_comments`
--

INSERT INTO `cms_comments` (`id`, `module`, `sub_id`, `user_id`, `name`, `text`, `reply`, `time`, `ip`, `browser`) VALUES
(1, 'album', 3, 3, '', 'Проверка слуха', '', 1289545820, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(2, 'album', 3, 3, '', 'Исчо адна праверка', '', 1289545841, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(3, 'album', 3, 3, '', 'sdgsdfg', '', 1289547681, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(4, 'album', 3, 3, '', 'Ghjdthrf', '', 1289547691, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(5, 'album', 4, 295, '', 'Проверка', '', 1289554167, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(6, 'album', 3, 3, 'AlkatraZ', 'Куку', '', 1289554361, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(7, 'album', 3, 295, 'kisakuku', 'Тест', '', 1289554423, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(8, 'album', 3, 295, 'kisakuku', 'Куку', '', 1289555004, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(9, 'album', 3, 295, 'kisakuku', 'Привет', '', 1289555110, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(10, 'album', 3, 295, 'kisakuku', 'Куку', '', 1289555176, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(11, 'album', 3, 295, 'kisakuku', 'Привет', '', 1289555295, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(12, 'album', 3, 295, 'kisakuku', 'Куку', '', 1289555323, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(13, 'album', 3, 295, 'kisakuku', 'Привет', '', 1289555551, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(14, 'album', 3, 295, 'kisakuku', 'Куку', '', 1289556184, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(15, 'album', 3, 295, 'kisakuku', 'Тест', '', 1289556349, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(16, 'album', 3, 295, 'kisakuku', 'тест', '', 1289556487, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(17, 'album', 3, 295, 'kisakuku', 'Тест', '', 1289556498, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(18, 'album', 3, 295, 'kisakuku', 'тест', '', 1289556584, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(19, 'album', 3, 295, 'kisakuku', 'Тест', '', 1289556639, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00'),
(20, 'album', 4, 295, 'kisakuku', 'Куку', '', 1289556895, 2130706433, 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.7.39 Version/11.00');
