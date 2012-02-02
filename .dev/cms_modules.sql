--
-- Структура таблицы `cms_modules`
--
DROP TABLE IF EXISTS `cms_modules`;
CREATE TABLE `cms_modules` (
  `module` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `cms_modules`
--
INSERT INTO `cms_modules` (`module`) VALUES
('avatars'),
('exit'),
('help'),
('mainmenu'),
('office'),
('online'),
('registration'),
('smileys');