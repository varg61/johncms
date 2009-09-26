<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                             Content Management System              //
// Официальный сайт сайт проекта:      http://johncms.com                     //
// Доп. сайт поддержки:                http://gazenwagen.com                  //
////////////////////////////////////////////////////////////////////////////////
// JohnCMS core team:                                                         //
// Евгений Рябинин aka john77          john77@gazenwagen.com                  //
// Олег Касьянов aka AlkatraZ          alkatraz@gazenwagen.com                //
//                                                                            //
// Плагиат и удаление копирайтов заруганы на ближайших родственников!!!       //
////////////////////////////////////////////////////////////////////////////////
// Визуальный мод инсталлятора от Piks                                        //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);

header("Cache-Control: no-cache, must-revalidate");
header("Content-type: application/xhtml+xml; charset=UTF-8");
echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en'>
<head><meta http-equiv='content-type' content='application/xhtml+xml; charset=utf-8'/>";
echo "<title>JohnCMS 3.0 - обновление</title>
<style type='text/css'>
body {font-family: Arial, Helvetica, sans-serif; font-size: small; color: #000000; background-color: #FFFFFF}
h2{ margin: 0; padding: 0; padding-bottom: 4px; }
ul{ margin:0; padding-left:20px; }
li { padding-bottom: 6px; }
.red { color: #FF0000; font-weight: bold; }
.green{ color: #009933; font-weight: bold; }
.gray{ color: #FF0000; font: small; }
</style>
</head><body>";
echo '<h2 class="green">JohnCMS v.3.0.0</h2>Обновление с версии 2.4.0<hr />';

// Подключаемся к базе данных
require_once ("incfiles/db.php");
require_once ("incfiles/func.php");
$connect = mysql_connect($db_host, $db_user, $db_pass) or die('cannot connect to server</body></html>');
mysql_select_db($db_name) or die('cannot connect to db');
mysql_query("SET NAMES 'utf8'", $connect);

$do = isset($_GET['do']) ? $_GET['do'] : '';
switch ($do)
{
    case 'step1':
        echo '<h2>Проверка прав доступа</h2><ul>';
        // Проверка прав доступа к файлам и папкам
        function permissions($filez) {
            $filez = @decoct(@fileperms($filez)) % 1000;
            return $filez;
        }
        $cherr = '';
        $err = false;
        // Проверка прав доступа к папкам
        $arr = array("gallery/foto/", "gallery/temp/", "library/files/", "library/temp/", "pratt/", "forum/files/", "forum/temtemp/", "download/arctemp/", "download/files/", "download/graftemp/", "download/screen/", "download/mp3temp/",
            "download/upl/");
        foreach ($arr as $v)
        {
            if (permissions($v) < 777)
            {
                $cherr = $cherr . '<div class="smenu"><span class="red">Ошибка!</span> - ' . $v . '<br /><span class="gray">Необходимо установить права доступа 777.</span></div>';
                $err = 1;
            } else
            {
                $cherr = $cherr . '<div class="smenu"><span class="green">Oк</span> - ' . $v . '</div>';
            }
        }
        // Проверка прав доступа к файлам
        $arr = array('flood.dat', 'smileys.dat', 'library/java/textfile.txt', 'library/java/META-INF/MANIFEST.MF', 'panel/filebase.dat');
        foreach ($arr as $v)
        {
            if (permissions($v) < 666)
            {
                $cherr = $cherr . '<div class="smenu"><span class="red">Ошибка!</span> - ' . $v . '<br/><span class="gray">Необходимо установить права доступа 666.</span></div>';
                $err = 1;
            } else
            {
                $cherr = $cherr . '<div class="smenu"><span class="green">Ок</span> - ' . $v . '</div>';
            }
        }
        echo '<div>';
        echo $cherr;
        echo '</div></ul><hr />';
        if ($err)
        {
            echo '<span class="red">Внимание!</span> Имеются критические ошибки!<br />Вы не сможете продолжить инсталляцию, пока не устраните их.';
            echo '<p clss="step"><a class="button" href="index.php?act=check">Проверить заново</a></p>';
        } else
        {
            echo '<span class="green">Отлично!</span><br />Все настройки правильные.<p><a class="button" href="update.php?do=step2">Продолжить</a></p>';
        }
        break;

    case 'step2':
        echo '<h2>Подготовка таблиц</h2>';
        // Таблица `users`
        mysql_query("ALTER TABLE `users` DROP `offpg`");
        mysql_query("ALTER TABLE `users` DROP `offgr`");
        echo '<span class="green">OK</span> таблица `users` обновлена.<br />';
        // Таблица `cms_settings`
        $array = array('gb', 'rmod', 'mod_reg_msg', 'mod_forum_msg', 'mod_chat_msg', 'mod_guest_msg', 'mod_lib_msg', 'mod_gal_msg', 'mod_down_msg');
        foreach ($array as $val)
        {
            mysql_query("DELETE FROM `cms_settings` WHERE `key` = '$val' LIMIT 1");
        }
        $array = array('mod_lib_comm', 'mod_gal_comm', 'mod_down_comm');
        foreach ($array as $val)
        {
            mysql_query("INSERT INTO `cms_settings` SET `key` = '$val', `val` = '1'");
        }
        $array = array('mod_reg', 'mod_forum', 'mod_chat', 'mod_guest', 'mod_lib', 'mod_gal', 'mod_down');
        foreach ($array as $val)
        {
            mysql_query("UPDATE `cms_settings` SET `val` = '2' WHERE `key` = '$val'");
        }
        echo '<span class="green">OK</span> таблица `cms_settings` обновлена.<br />';
        // Таблицы форума
        mysql_query("ALTER TABLE `forum` CHANGE `close` `close` TINYINT( 1 ) NOT NULL DEFAULT '0'");
        mysql_query("ALTER TABLE `forum` CHANGE `vip` `vip` TINYINT( 1 ) NOT NULL DEFAULT '0'");
        mysql_query("ALTER TABLE `forum` CHANGE `moder` `moder` TINYINT( 1 ) NOT NULL DEFAULT '0'");
        mysql_query("ALTER TABLE `forum` DROP INDEX `moder`");
        echo '<span class="green">OK</span> таблица `forum` обновлена.<br />';
        mysql_query("CREATE TABLE IF NOT EXISTS `cms_forum_files` (
        `id` int(11) NOT NULL auto_increment,
        `cat` int(11) NOT NULL,
        `subcat` int(11) NOT NULL,
        `topic` int(11) NOT NULL,
        `post` int(11) NOT NULL,
        `time` int(11) NOT NULL,
        `filename` text NOT NULL,
        `filetype` tinyint(4) NOT NULL,
        `dlcount` int(11) NOT NULL,
        `del` tinyint(1) NOT NULL default '0',
        PRIMARY KEY  (`id`),
        KEY `cat` (`cat`),
        KEY `subcat` (`subcat`),
        KEY `topic` (`topic`),
        KEY `post` (`post`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8");
        echo '<span class="green">OK</span> таблица `cms_forum_files` создана.<br />';
        echo '<hr /><a href="update.php?do=step3">Продолжить</a>';
        break;

    case 'step3':
        echo '<h2>Очистка форума</h2>';
        // Очистка форума
        $i = 0;
        $f = 0;
        $req = mysql_query("SELECT * FROM `forum` WHERE `type` = 'm'");
        while ($res = mysql_fetch_array($req))
        {
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `id` = '" . $res['refid'] . "'"), 0);
            if ($count == 0)
            {
                // Если есть файл, удаляем
                if (!empty($res['attach']) && file_exists('forum/files/' . $res['attach']))
                {
                    unlink('forum/files/' . $res['attach']);
                    ++$f;
                }
                // Удаляем запись из базы
                mysql_query("DELETE FROM `forum` WHERE `id` = '" . $res['id'] . "'");
                ++$i;
            }
        }
        mysql_query("DELETE FROM `forum` WHERE `type` = 'l'");
        echo '<span class="green">OK</span> Форум очищен, удалено <span class="red">' . $i . '</span> мертвых записей из базы и <span class="red">' . $f . '</span> файлов.<br />';
        echo '<hr /><a href="update.php?do=step4">Продолжить</a>';
        break;

    case 'step4':
        echo '<h2>Перенос файлов форума</h2>';
        // Перечисляем типы файлов, разрешенных к выгрузке на форуме
        $ext_win = array('exe', 'msi');
        $ext_java = array('jar', 'jad');
        $ext_sis = array('sis', 'sisx');
        $ext_doc = array('txt', 'pdf', 'doc', 'rtf', 'djvu');
        $ext_pic = array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'tiff', 'wmf');
        $ext_zip = array('zip', 'rar', '7z', 'tar');
        $ext_video = array('3gp', 'avi', 'flv', 'mpeg', 'mp4');
        $ext_audio = array('mp3', 'amr');
        // Переносим данные в новую таблицу
        $req = mysql_query("SELECT * FROM `forum` WHERE `attach` != ''");
        while ($res = mysql_fetch_array($req))
        {
            if (file_exists('forum/files/' . $res['attach']))
            {
                $ext = explode('.', $res['attach']);
                $ext = strtolower($ext[1]);
                if (in_array($ext, $ext_win))
                    $type = 1;
                elseif (in_array($ext, $ext_java))
                    $type = 2;
                elseif (in_array($ext, $ext_sis))
                    $type = 3;
                elseif (in_array($ext, $ext_doc))
                    $type = 4;
                elseif (in_array($ext, $ext_pic))
                    $type = 5;
                elseif (in_array($ext, $ext_zip))
                    $type = 6;
                elseif (in_array($ext, $ext_video))
                    $type = 7;
                elseif (in_array($ext, $ext_audio))
                    $type = 8;
                else
                    $type = 9;
                // Получаем ID подкатегории
                $req1 = mysql_query("SELECT `refid` FROM `forum` WHERE `id` = '" . $res['refid'] . "'");
                $res1 = mysql_fetch_array($req1);
                // Получаем ID категории
                $req2 = mysql_query("SELECT `refid` FROM `forum` WHERE `id` = '" . $res1['refid'] . "'");
                $res2 = mysql_fetch_array($req2);
                // Переносим информацию о файлах в новую таблицу
                mysql_query("INSERT INTO `cms_forum_files` SET
				`cat` = '" . $res2['refid'] . "',
				`subcat` = '" . $res1['refid'] . "',
				`topic` = '" . $res['refid'] . "',
				`post` = '" . $res['id'] . "',
				`time` = '" . $res['time'] . "',
				`filename` = '" . mysql_real_escape_string($res['attach']) . "',
				`filetype` = '$type',
				`dlcount` = '" . $res['dlcount'] . "',
				`del` = '" . $res['close'] . "'");
            }
        }
        echo '<span class="green">OK</span> файлы Форума перенесены.<br />';
        mysql_query("ALTER TABLE `forum` DROP `attach`");
        mysql_query("ALTER TABLE `forum` DROP `dlcount`");
        echo '<span class="green">OK</span> старые данные удалены.<br />';
        mysql_query("OPTIMIZE TABLE `forum`");
        echo '<span class="green">OK</span> таблица оптимизирована.<br />';
        echo '<hr /><a href="update.php?do=final">Продолжить</a>';
        break;

    case 'final':
        echo '<b><span class="green">Поздравляем!</span></b><br />Процедура обновления успешно завершена.<br />Не забудьте удалить файл /update.php';
        echo '<hr /><a href="../../index.php">На сайт</a>';
        break;

    default:
        echo '<h2><span class="red">ВНИМАНИЕ!</span></h2><ul>';
        echo '<li>Учтите, что обновление возможно только для оригинальной (без модов) системы <b>JohnCMS 2.4.0</b><br />Если Вы используете какие-либо моды, то возможность обновления обязательно согласуйте с их авторами.<br />Установка данного обновления на модифицированную систему может привести к полной неработоспособности сайта.</li>';
        echo '<li>Некоторые этапы обновления могут занимать довольно продолжительное время (несколько минут), которое зависит от размера базы данных сайта и скорости сервера хостинга.</li>';
        echo '<li>Перед началом процедуры обновления, ОБЯЗАТЕЛЬНО сделайте резервную копию базы данных.<br />Если по какой то причине обновление не пройдет до конца, Вам придется восстанавливать базу из резервной копии.</li>';
        echo '<li>В течение всего периода работы данного инсталлятора, НЕЛЬЗЯ нажимать кнопки браузена "Назад" и "Обновить", иначе может быть нарушена целостность данных.</li>';
        echo '<li>Если Вы нажмете ссылку "Продолжить", то отмена изменений будет невозможна без восстановления из резервной копии.</li>';
        echo '</ul><hr />Вы уверены? У Вас есть резервная копия базы данных?<br /><a href="update.php?do=step1">Начать обновление</a>';
}

echo '</body>
</html>';

?>