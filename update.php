<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
echo '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
echo '<title>JohnCMS 4.0.0 - обновление</title>
<style type="text/css">
body {font-family: Arial, Helvetica, sans-serif; font-size: small; color: #000000; background-color: #FFFFFF}
h2{ margin: 0; padding: 0; padding-bottom: 4px; }
ul{ margin:0; padding-left:20px; }
li { padding-bottom: 6px; }
.red { color: #FF0000; font-weight: bold; }
.green{ color: #009933; font-weight: bold; }
.gray{ color: #FF0000; font: small; }
</style>
</head><body>';
echo '<h2 class="green">JohnCMS 4.0.0</h2>Обновление с версии 3.2.2<hr />';

// Подключаемся к базе данных
require_once('incfiles/db.php');
require_once('incfiles/func.php');
$connect = mysql_connect($db_host, $db_user, $db_pass) or die('cannot connect to server</body></html>');
mysql_select_db($db_name) or die('cannot connect to db');
mysql_query("SET NAMES 'utf8'", $connect);

$do = isset($_GET['do']) ? $_GET['do'] : '';
switch ($do) {
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
        $arr = array (
            'files/forum/attach/',
            'files/forum/thumbinals/',
            'files/forum/topics/',
            'files/users/avatar/',
            'files/users/photo/',
            'files/users/pm/',
            'files/cache/',
            'gallery/foto/',
            'gallery/temp/',
            'library/files/',
            'library/temp/',
            'download/arctemp/',
            'download/files/',
            'download/graftemp/',
            'download/screen/',
            'download/mp3temp/',
            'download/upl/'
        );
        foreach ($arr as $v) {
            if (permissions($v) < 777) {
                $cherr = $cherr . '<div class="smenu"><span class="red">Ошибка!</span> - ' . $v . '<br /><span class="gray">Необходимо установить права доступа 777.</span></div>';
                $err = 1;
            } else {
                $cherr = $cherr . '<div class="smenu"><span class="green">Oк</span> - ' . $v . '</div>';
            }
        }
        // Проверка прав доступа к файлам
        $arr = array (
            'library/java/textfile.txt',
            'library/java/META-INF/MANIFEST.MF'
        );
        foreach ($arr as $v) {
            if (permissions($v) < 666) {
                $cherr = $cherr . '<div class="smenu"><span class="red">Ошибка!</span> - ' . $v . '<br/><span class="gray">Необходимо установить права доступа 666.</span></div>';
                $err = 1;
            } else {
                $cherr = $cherr . '<div class="smenu"><span class="green">Ок</span> - ' . $v . '</div>';
            }
        }
        echo '<div>';
        echo $cherr;
        echo '</div></ul><hr />';
        if ($err) {
            echo '<span class="red">Внимание!</span> Имеются критические ошибки!<br />Вы не сможете продолжить инсталляцию, пока не устраните их.';
            echo '<p clss="step"><a class="button" href="update.php?do=step1">Проверить заново</a></p>';
        } else {
            echo '<span class="green">Отлично!</span><br />Все настройки правильные.<p><a class="button" href="update.php?do=step2">Продолжить</a></p>';
        }
        break;

    case 'step2':
        echo '<h2>Подготовка таблиц</h2>';
        // Таблицы голосований форума
        mysql_query("RENAME TABLE `forum_vote` TO `cms_forum_vote`");
        echo '<span class="green">OK</span> таблица `cms_forum_vote` обновлена.<br />';
        mysql_query("RENAME TABLE `forum_vote_us` TO `cms_forum_vote_users`");
        echo '<span class="green">OK</span> таблица `cms_forum_vote_users` обновлена.<br />';
        mysql_query("ALTER TABLE `users` DROP `set_user`");
        mysql_query("ALTER TABLE `users` ADD `set_user` TEXT NOT NULL AFTER `place`");
        mysql_query("ALTER TABLE `users` DROP `set_forum`");
        mysql_query("ALTER TABLE `users` ADD `set_forum` TEXT NOT NULL AFTER `set_user`");
        mysql_query("ALTER TABLE `users` CHANGE `ip` `ip` BIGINT( 11 ) NOT NULL DEFAULT '0'");
        echo '<span class="green">OK</span> таблица `users` обновлена.<br />';
        // Таблица истории IP адресов
        mysql_query("DROP TABLE IF EXISTS `cms_users_iphistory`");
        mysql_query("CREATE TABLE `cms_users_iphistory` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(10) unsigned NOT NULL,
        `user_ip` bigint(11) NOT NULL,
        `time` int(10) unsigned NOT NULL,
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `user_ip` (`user_ip`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8");
        echo '<span class="green">OK</span> таблица истории IP создана.<br />';
        // Перенос IP адресов в таблицу истории
        mysql_query("LOCK TABLES `users` READ, `cms_users_iphistory` WRITE");
        $req = mysql_query("SELECT `id`, `ip`, `lastdate` FROM `users`");
        while ($res = mysql_fetch_assoc($req)) {
            mysql_query("INSERT INTO `cms_users_iphistory` SET
            `user_id` = '" . $res['id'] . "',
            `user_ip` = '" . $res['ip'] . "',
            `time` = '" . $res['lastdate'] . "'");
        }
        mysql_query("UNLOCK TABLES");
        echo '<span class="green">OK</span> IP адреса сконвертированы.<br />';
        // Изменяем таблицу `guest`
        mysql_query("ALTER TABLE `guest` CHANGE `ip` `ip` BIGINT( 11 ) NOT NULL DEFAULT '0'");
        echo '<span class="green">OK</span> таблица `guest` обновлена.<br />';
        // Изменяем таблицу `cms_guests`
        mysql_query("ALTER TABLE `cms_guests` CHANGE `ip` `ip` BIGINT( 11 ) NOT NULL DEFAULT '0'");
        echo '<span class="green">OK</span> таблица `cms_guests` обновлена.<br />';
        // Изменяем таблицу `cms_ban_ip`
        mysql_query("ALTER TABLE `cms_ban_ip` CHANGE `ip1` `ip1` BIGINT( 11 ) NOT NULL DEFAULT '0'");
        mysql_query("ALTER TABLE `cms_ban_ip` CHANGE `ip2` `ip2` BIGINT( 11 ) NOT NULL DEFAULT '0'");
        echo '<span class="green">OK</span> таблица `cms_ban_ip` обновлена.<br />';
        // Создаем базу операторов
        mysql_query("DROP TABLE IF EXISTS `cms_operators`");
        mysql_query("CREATE TABLE `cms_operators` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) NOT NULL,
        `ip_min` bigint(11) NOT NULL DEFAULT '0',
        `ip_max` bigint(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8");
        echo '<span class="green">OK</span> таблица `cms_operators` создана.<br />';
        echo '<hr /><a href="update.php?do=final">Продолжить</a>';
        break;

    case 'final':
        echo '<h2 class="green">Поздравляем!</h2>Процедура обновления успешно завершена.<br /><br /><h2 class="red">Не забудьте удалить!!!</h2>';
        echo '<div>/update.php</div>';
        echo '<hr /><a href="../../index.php">На сайт</a>';
        break;

    default:
        echo '<h2><span class="red">ВНИМАНИЕ!</span></h2><ul>';
        echo
            '<li>Учтите, что обновление возможно только для оригинальной (без модов) системы <b>JohnCMS 3.1.1</b><br />Если Вы используете какие-либо моды, то возможность обновления обязательно согласуйте с их авторами.<br />Установка данного обновления на модифицированную систему может привести к полной неработоспособности сайта.</li>';
        echo '<li>Некоторые этапы обновления могут занимать довольно продолжительное время (несколько минут), которое зависит от размера базы данных сайта и скорости сервера хостинга.</li>';
        echo '<li>Перед началом процедуры обновления, <b>ОБЯЗАТЕЛЬНО</b> сделайте резервную копию базы данных.<br />Если по какой то причине обновление не пройдет до конца, Вам придется восстанавливать базу из резервной копии.</li>';
        echo '<li>В течение всего периода работы инсталлятора, НЕЛЬЗЯ нажимать кнопки браузера "Назад" и "Обновить", иначе может быть нарушена целостность данных.</li>';
        echo '<li>Если Вы нажмете ссылку "Продолжить", то отмена изменений будет невозможна без восстановления из резервной копии.</li>';
        echo '</ul><hr />Вы уверены? У Вас есть резервная копия базы данных?<br /><a href="update.php?do=step1">Начать обновление</a>';
}

echo '</body></html>';

?>
