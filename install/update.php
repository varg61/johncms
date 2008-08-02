<?php

define('_IN_JOHNCMS', 1);
require_once ("../incfiles/core.php");

////////////////////////////////////////////////////////////
// Перенос настроек в новую таблицу                       //
////////////////////////////////////////////////////////////
$req = mysql_query("SELECT * FROM `settings`;") or die('Ошибка импорта настроек</body></html>');
$tmp = mysql_fetch_array($req);
mysql_query("INSERT INTO `cms_settings` VALUES('nickadmina', '" . $tmp['nickadmina'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('emailadmina', '" . $tmp['emailadmina'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('nickadmina2', '" . $tmp['nickadmina2'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('sdvigclock', '" . $tmp['sdvigclock'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('copyright', '" . $tmp['copyright'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('homeurl', '" . $tmp['homeurl'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('rashstr', '" . $tmp['rashstr'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('admp', '" . $tmp['admp'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('flsz', '" . $tmp['flsz'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('gzip', '" . $tmp['gzip'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('rmod', '" . $tmp['rmod'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('fmod', '" . $tmp['fmod'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('gb', '" . $tmp['gb'] . "');");
mysql_query("INSERT INTO `cms_settings` VALUES('clean_time', '" . $tmp['clean_time'] . "');");
// Удаление старой таблицы настроек
mysql_query("DROP TABLE IF EXISTS `settings`;");

?>