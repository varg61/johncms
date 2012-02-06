--
-- Структура таблицы `cms_modules`
--
DROP TABLE IF EXISTS `cms_modules`;
CREATE TABLE `cms_modules` (
  `module` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `cms_modules`
--
INSERT INTO `cms_modules` (`module`) VALUES
('admin'),
('avatars'),
('exit'),
('help'),
('language'),
('login'),
('mainmenu'),
('online'),
('registration'),
('smileys'),
('users');