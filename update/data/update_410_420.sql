--
-- Обновление с версии 4.x.x
--
DROP TABLE IF EXISTS `cms_lng_list`;
DROP TABLE IF EXISTS `cms_lng_phrases`;

ALTER TABLE `users` DROP `set_language`;
ALTER TABLE `users` DROP `postchat`;
ALTER TABLE `users` DROP `otvetov`;
ALTER TABLE `users` DROP `mailact`;
ALTER TABLE `users` DROP `vrrat`;
ALTER TABLE `users` DROP `cctx`;
ALTER TABLE `users` DROP `alls`;
ALTER TABLE `users` DROP `balans`;
ALTER TABLE `users` DROP `set_chat`;