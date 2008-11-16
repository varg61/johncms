<?php

define('_IN_JOHNCMS', 1);

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
header("Content-type: application/xhtml+xml; charset=UTF-8");
echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en'>
<head>
<meta http-equiv='content-type' content='application/xhtml+xml; charset=utf-8'/>";
echo "<link rel='shortcut icon' href='ico.gif' />
<title>Обновление системы</title>
<style type='text/css'>
body { font-weight: normal; font-family: Century Gothic; font-size: 12px; color: #FFFFFF; background-color: #000033}
a:link { text-decoration: underline; color : #D3ECFF}
a:active { text-decoration: underline; color : #2F3528 }
a:visited { text-decoration: underline; color : #31F7D4}
a:hover { text-decoration: none; font-size: 12px; color : #E4F992 }
.red { color: #FF0000; font-weight: bold; }
.green{ color: #009933; font-weight: bold; }
.gray{ color: #FF0000; font: small; }
</style>
</head><body>";
echo '<big><b>JohnCMS v.2.0.0</b></big> Обновление<hr />';

// Подключаемся к базе данных
require_once ("../incfiles/db.php");
require_once ("../incfiles/func.php");
$connect = mysql_connect($db_host, $db_user, $db_pass) or die('cannot connect to server</body></html>');
mysql_select_db($db_name) or die('cannot connect to db');
mysql_query("SET NAMES 'utf8'", $connect);

$do = isset($_GET['do']) ? $_GET['do'] : '';
switch ($do)
{
    case 'forum':
        echo '<b><u>Обновляем форум</u></b><br />';
		
		// Удаляем метки (l)
        $req = mysql_query("DELETE FROM `forum` WHERE `type`='l';");
        echo '<span class="green">OK</span> метки удалены.<br />';

        // Обновляем типы
        $req = mysql_query("SELECT * FROM `forum` WHERE `type`='f';");
        while ($res = mysql_fetch_array($req))
        {
            mysql_query("UPDATE `forum` SET `type`='1' WHERE `id`='" . $res['id'] . "';");
        }
        $req = mysql_query("SELECT * FROM `forum` WHERE `type`='r';");
        while ($res = mysql_fetch_array($req))
        {
            mysql_query("UPDATE `forum` SET `type`='2' WHERE `id`='" . $res['id'] . "';");
        }
        $req = mysql_query("SELECT * FROM `forum` WHERE `type`='t';");
        while ($res = mysql_fetch_array($req))
        {
            mysql_query("UPDATE `forum` SET `type`='3' WHERE `id`='" . $res['id'] . "';");
        }
        $req = mysql_query("SELECT * FROM `forum` WHERE `type`='m';");
        while ($res = mysql_fetch_array($req))
        {
            mysql_query("UPDATE `forum` SET `type`='4' WHERE `id`='" . $res['id'] . "';");
        }
        $req = mysql_query("SELECT * FROM `forum` WHERE `type`='n';");
        while ($res = mysql_fetch_array($req))
        {
            mysql_query("UPDATE `forum` SET `type`='5' WHERE `id`='" . $res['id'] . "';");
        }
        echo '<span class="green">OK</span> типы обновлены.<br />';

        // Модифицируем таблицу "forum"
        mysql_query("ALTER TABLE `forum` DROP INDEX `type`;");
        mysql_query("ALTER TABLE `forum` CHANGE `type` `type` TINYINT( 3 ) NOT NULL;");
        mysql_query("ALTER TABLE `forum` ADD INDEX ( `type` );");
        mysql_query("OPTIMIZE TABLE `forum`;");
        echo '<span class="green">OK</span> таблица "forum" модифицирована.<br />';
        
        // Создаем таблицу меток прочтения
        mysql_query("DROP TABLE IF EXISTS `cms_forum_rdm`;");
		mysql_query("CREATE TABLE `cms_forum_rdm` (
		`topic_id` int(11) NOT NULL,
		`user_id` int(11) NOT NULL,
		`time` int(11) NOT NULL,
		PRIMARY KEY  (`topic_id`,`user_id`),
		KEY `time` (`time`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		echo '<span class="green">OK</span> таблица меток создана.<br />';

        echo '<hr /><a href="up_160_200.php?do=final">Продолжить</a>';
        break;

    case 'final':
        echo '<b><span class="green">Поздравляем!</span></b><br />Процедура обновления успешно завершена.<br />Не забудьте удалить папку /install';
        echo '<hr /><a href="../../index.php">На сайт</a>';
        break;

    default:
        echo '<p><big><span class="red">ВНИМАНИЕ!</span></big><ul>';
        echo '<li>Учтите, что обновление возможно только для системы JohnCMS 1.6.0</li>';
        echo '<li>Если Вы используете какие-либо моды, то возможность обновления обязательно согласуйте с их авторами.</li>';
        echo '<li>Перед началом процедуры обновления, ОБЯЗАТЕЛЬНО сделайте резервную копию базы данных. Если по какой то причине обновление не пройдет до конца, Вам придется восстанавливать базу из резервной копии.</li>';
        echo '<li>Если Вы нажмете ссылку "Продолжить", то отмена изменений будет невозможна без восстановления из резервной копии.</li>';
        echo '<li></li>';
        echo '</ul></p>';

        echo '<hr />Вы уверены?<br /><a href="up_160_200.php?do=forum">Продолжить</a>';
}

echo '</body>
</html>';

?>