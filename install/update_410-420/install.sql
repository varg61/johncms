--
-- Удаляем ненужные таблицы
--
DROP TABLE IF EXISTS `cms_lng_list`;
DROP TABLE IF EXISTS `cms_lng_phrases`;

--
-- Модифицируем таблицы
--
ALTER TABLE `users` DROP `set_language`;
ALTER TABLE `users` DROP `postchat`;
ALTER TABLE `users` DROP `otvetov`;
ALTER TABLE `users` DROP `mailact`;
ALTER TABLE `users` DROP `vrrat`;
ALTER TABLE `users` DROP `cctx`;
ALTER TABLE `users` DROP `alls`;
ALTER TABLE `users` DROP `balans`;
ALTER TABLE `users` DROP `set_chat`;
ALTER TABLE `users` DROP `kod`;

DELETE FROM `cms_settings` WHERE `key` = 'lng_id' LIMIT 1;
UPDATE `cms_settings` SET `key` = 'lng' WHERE `key` = 'lng_iso' LIMIT 1;