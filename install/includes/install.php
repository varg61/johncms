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

defined('INSTALL') or die('Error: restricted access');
switch ($_GET['mod']) {
    case 'setup':
        ////////////////////////////////////////////////////////////
        // Создание таблиц в базе данных MySQL                    //
        ////////////////////////////////////////////////////////////
        echo '<h2>Установка системы</h2><ul>';
        require_once("../incfiles/db.php");
        require_once("../incfiles/func.php");
        $connect = mysql_connect($db_host, $db_user, $db_pass) or die('cannot connect to server</div></body></html>');
        mysql_select_db($db_name) or die('cannot connect to db</div></body></html>');
        mysql_query("SET NAMES 'utf8'", $connect);
        $error = '';
        @set_magic_quotes_runtime(0);
        // Читаем SQL файл и заносим его в базу данных
        $query = fread(fopen('data/install.sql', 'r'), filesize('data/install.sql'));
        $pieces = split_sql($query);
        for ($i = 0; $i < count($pieces); $i++) {
            $pieces[$i] = trim($pieces[$i]);
            if (!empty($pieces[$i]) && $pieces[$i] != "#") {
                if (!mysql_query($pieces[$i])) {
                    $error = $error . mysql_error() . '<br />';
                }
            }
        }
        if (empty($error)) {
            echo '<span class="green">Oк</span> - Таблицы созданы<br />';

            // Принимаем данные из формы
            $log = trim($_POST['wnickadmina']);
            $latlog = functions::rus_lat(mb_strtolower($log));
            $par = trim($_POST['wpassadmina']);
            $par1 = md5(md5($par));
            $meil = trim($_POST['wemailadmina']);
            $hom = trim($_POST[whome]);
            $brow = $_SERVER["HTTP_USER_AGENT"];
            $ip = $_SERVER["REMOTE_ADDR"];

            // Настройка администратора
            mysql_query("insert into `users` set
            `name`='" . mysql_real_escape_string($log) . "',
            `name_lat`='" . mysql_real_escape_string($latlog) . "',
            `password`='" . mysql_real_escape_string($par1) . "',
            `sex`='m',
            `datereg`='" . time() . "',
            `lastdate`='" . time() . "',
            `mail`='" . mysql_real_escape_string($meil) . "',
            `www`='" . mysql_real_escape_string($hom) . "',
            `rights`='9',
            `ip`='" . $ip . "',
            `browser`='" . mysql_real_escape_string($brow) . "',
            `preg`='1';") or die('Ошибка настройки администратора</div></body></html>');
            $user_id = mysql_insert_id();
            echo '<span class="green">Oк</span> - администратор настроен<br />';

            // Импорт настроек
            mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string($meil) . "' WHERE `key`='emailadmina';");
            mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string(trim($_POST['wcopyright'])) . "' WHERE `key`='copyright';");
            mysql_query("UPDATE `cms_settings` SET `val`='" . mysql_real_escape_string($hom) . "' WHERE `key`='homeurl';");
            echo '<span class="green">Oк</span> - настройки импортированы<br />';

            // Импорт вопросов Викторины
            $file = file("data/vopros.txt");
            $count = count($file);
            for ($i = 0; $i < $count; $i++) {
                $tx = explode("||", $file[$i]);
                mysql_query("INSERT INTO `vik` SET
                `vopros`='" . mysql_real_escape_string(trim($tx[0])) . "',
                `otvet`='" . mysql_real_escape_string(trim($tx[1])) . "'
                ");
            }
            echo '<span class="green">Oк</span> - викторина импортирована (' . $i . ' вопросов)</ul>';
            echo '<br /><h2 class="green">Установка завершена</h2>';
            // Установка ДЕМО данных
            echo '<ul>При желании, Вы можете установить <a href="index.php?act=demo&amp;id=' . $user_id . '&amp;ps=' . $_POST['wpassadmina'] .
                '">Демо данные</a><br />Это может быть полезно для начинающих сайтостроителей.<br />В базу будут внесены некоторые исходные настроики и материалы.</ul>';
            echo '<br /><h2 class="red">Не забудьте:</h2><ul><li>Сменить права к папке incfiles на 755</li><li>Сменить права на файл incfiles/db.php 644</li><li>Удалить папку install с сайта</li></ul>';
            echo '<hr /><a href="../login.php?id=' . $user_id . '&amp;p=' . $_POST['wpassadmina'] . '">Вход на сайт</a>';
        } else {
            // Если были ошибки, выводим их
            echo $error;
            echo '<br /><span class="red">Error!</span><br />При создании таблиц возникла ошибка.<br />Продолжение невозможно.';
        }
        break;

    case 'demo':
        ////////////////////////////////////////////////////////////
        // Установка ДЕМО данных                                  //
        ////////////////////////////////////////////////////////////
        require_once("../incfiles/db.php");
        require_once("../incfiles/func.php");
        $connect = mysql_connect($db_host, $db_user, $db_pass) or die('cannot connect to server</div></body></html>');
        mysql_select_db($db_name) or die('cannot connect to db');
        mysql_query("SET NAMES 'utf8'", $connect);
        $error = '';
        @set_magic_quotes_runtime(0);
        // Читаем SQL файл и заносим его в базу данных
        $query = fread(fopen('data/demo.sql', 'r'), filesize('data/demo.sql'));
        $pieces = split_sql($query);
        for ($i = 0; $i < count($pieces); $i++) {
            $pieces[$i] = trim($pieces[$i]);
            if (!empty($pieces[$i]) && $pieces[$i] != "#") {
                if (!mysql_query($pieces[$i])) {
                    $error = $error . mysql_error() . '<br />';
                }
            }
        }
        if (empty($error)) {
            echo '<span class="green">OK</span> - ДЕМО данные установлены<br />';
        } else {
            // Если были ошибки, выводим их
            echo $error;
            echo '<br /><span class="red">Error!</span><br />В процессе установки ДЕМО данных возникли ошибки.<br />';
        }
        echo "Поздравляем! Установка " . $version . "" . $codename .
            " закончена.<br />Не забудьте:<br />1) Сменить права к папке incfiles на 755<br />2) Сменить права на файл incfiles/db.php 644<br />3) Удалить папку install с сайта.<br />";
        echo "<p style='step'><a class='button' href='../login.php?id=" . $_GET['id'] . "&amp;p=" . $_GET['ps'] . "'>Вход на сайт</a></p>";
        break;

    case "admin":
        ////////////////////////////////////////////////////////////
        // Настройки сайта и Администратора                       //
        ////////////////////////////////////////////////////////////
        $dhost = trim($_POST['host']);
        $duser = trim($_POST['user']);
        $dpass = trim($_POST['pass']);
        $dname = trim($_POST['name']);
        $text = "<?php\r\n\r\n" . "defined('_IN_JOHNCMS') or die ('Error: restricted access');\r\n\r\n" . "$" . "db_host=\"$dhost\";\r\n" . "$" . "db_user=\"$duser\";\r\n" . "$" . "db_pass=\"$dpass\";\r\n" . "$" . "db_name=\"$dname\";\r\n"
            . "\r\n?>";
        $fp = @fopen("../incfiles/db.php", "w");
        fputs($fp, $text);
        fclose($fp);
        echo '<p>Создаем Администратора системы</p>';
        echo '<form method="post" action="index.php?act=set">';
        echo '<p><b>Ваш ник</b><br/><input name="wnickadmina" maxlength="50" value="Admin" /></p>';
        echo '<p><b>Ваш пароль</b><br/><input name="wpassadmina" maxlength="50" value="password" /></p>';
        echo '<p><b>Ваш e-mail</b><br/><input name="wemailadmina" maxlength="50" /></p>';
        echo '<p><b>Копирайт</b><br/><input name="wcopyright" maxlength="100" /></p>';
        echo '<p><b>Главная сайта</b> без слэша в конце<br/><input name="whome" maxlength="100" value="http://' . $_SERVER["SERVER_NAME"] . '" /></p>';
        echo '<hr /><input value="Продолжить" type="submit" class="button" /></form>';
        break;

    case 'db':
        ////////////////////////////////////////////////////////////
        // Настройка соединения с MySQL                           //
        ////////////////////////////////////////////////////////////
        echo '<form action="index.php?act=admin" method="post">';
        echo '<p>Ниже вы должны ввести настройки соединения с базой данных MySQL.<br />Если вы не уверенны в них, свяжитесь с вашим хостинг-провайдером.</p>';
        echo '<p><b>Адрес сервера</b><br /><input type="text" name="host" value="localhost"/></p>';
        echo '<p><b>Название базы</b><br /><input type="text" name="name" value="johncms"/></p>';
        echo '<p><b>Имя пользователя</b><br /><input type="text" name="user" value="root"/></p>';
        echo '<p><b>MySQL пароль</b><br /><input type="text" name="pass"/></p>';
        echo '<hr /><input type="submit" class="button" value="Продолжить"/></form>';
        break;

    case 'set':
        /*
        -----------------------------------------------------------------
        Создание базы данных и Администратора системы
        -----------------------------------------------------------------
        */
        $check = false;
        $db_error = array ();
        $db_host = isset($_POST['dbhost']) ? trim($_POST['dbhost']) : 'localhost';
        $db_name = isset($_POST['dbname']) ? trim($_POST['dbname']) : 'johncms';
        $db_user = isset($_POST['dbuser']) ? trim($_POST['dbuser']) : 'root';
        $db_pass = isset($_POST['dbpass']) ? trim($_POST['dbpass']) : '';
        $admin_user = isset($_POST['admin']) ? trim($_POST['admin']) : 'admin';
        $admin_pass = isset($_POST['password']) ? trim($_POST['password']) : '';
        $site_url = isset($_POST['siteurl']) ? trim($_POST['siteurl']) : false;
        $site_mail = isset($_POST['sitemail']) ? trim($_POST['sitemail']) : false;
        if (isset($_POST['check']) || isset($_POST['install'])) {
            // Проверяем заполнение реквизитов базы данных
            if (empty($db_host))
                $db_error['host'] = $lng['error_db_host_empty'];
            if (empty($db_name))
                $db_error['name'] = $lng['error_db_name_empty'];
            if (empty($db_user))
                $db_error['user'] = $lng['error_db_user_empty'];
            if (empty($db_error)) {
                // Проверяем подключение к серверу базы данных
                $con_err = false;
                @mysql_connect($db_host, $db_user, $db_pass) or $con_err = mysql_error();
                if ($con_err && stristr($con_err, 'no such host'))
                    $db_error['host'] = $lng['error_db_host'];
                elseif ($con_err && stristr($con_err, 'access denied for user'))
                    $db_error['access'] = $lng['error_db_user'];
                elseif ($con_err)
                    $db_error['unknown'] = $lng['error_db_unknown'];
            }
            // Проверяем наличие базы данных
            if (empty($db_error) && @mysql_select_db($db_name) == false)
                $db_error['name'] = $lng['error_db_name'];
            if (empty($db_error))
                $check = true;
        }
        if (empty($db_error) && isset($_POST['install'])) {
            // Проверяем административные данные
            // Создаем системный файл db.php
            $dbfile = "<?php\r\n\r\n" .
                "defined('_IN_JOHNCMS') or die ('Error: restricted access');\r\n\r\n" .
                '$db_host = ' . "'$db_host';\r\n" .
                '$db_name = ' . "'$db_name';\r\n" .
                '$db_user = ' . "'$db_user';\r\n" .
                '$db_pass = ' . "'$db_pass';\r\n\r\n" .
                '$system_build = ' . "'$system_build';\r\n\r\n" .
                '?>';
            if (!file_put_contents('../incfiles/db.php', $dbfile)) {
                echo 'ERROR: Can not write system file';
            }
        }
        echo '<h2 class="blue">' . $lng['database'] . '</h2>';
        if (!empty($db_error)) {
            // Показываем ошибки
            echo '<p><div class="red"><b>' . $lng['error'] . '</b>';
            foreach ($db_error as $val)echo '<div>' . $val . '</div>';
            echo '</div></p>';
        }
        echo '<form action="index.php?act=install&amp;mod=set&amp;lng_id=' . $lng_id . '" method="post">' .
            '<small class="blue"><b>MySQL Host:</b></small><br />' .
            '<input type="text" name="dbhost" value="' . $db_host . '"' . ($check ? ' readonly="readonly"' : '') . (isset($db_error['host']) ? ' style="background-color: #FFCCCC"' : '') . '><br />' .
            '<small class="blue"><b>MySQL Database:</b></small><br />' .
            '<input type="text" name="dbname" value="' . $db_name . '"' . ($check ? ' readonly="readonly"' : '') . (isset($db_error['name']) ? ' style="background-color: #FFCCCC"' : '') . '><br />' .
            '<small class="blue"><b>MySQL User:</b></small><br />' .
            '<input type="text" name="dbuser" value="' . $db_user . '"' . ($check ? ' readonly="readonly"' : '') . (isset($db_error['access']) || isset($db_error['user']) ? ' style="background-color: #FFCCCC"' : '') . '><br />' .
            '<small class="blue"><b>MySQL Password:</b></small><br />' .
            '<input type="text" name="dbpass" value="' . $db_pass . '"' . ($check ? ' readonly="readonly"' : '') . (isset($db_error['access']) ? ' style="background-color: #FFCCCC"' : '') . '>';
        if ($check) {
            echo '<p><input type="submit" name="install" value="' . $lng['setup'] . '"></p>';
        } else {
            echo '<p><input type="submit" name="check" value="' . $lng['check'] . '"></p>';
        }
        echo '</form>';
        echo '<p><a href="index.php?act=install&amp;mod=set&amp;lng_id=' . $lng_id . '">' . $lng['reset_form'] . '</a></p>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Проверка прав доступа
        -----------------------------------------------------------------
        */
        $folders = array (
            '/download/arctemp/',
            '/download/files/',
            '/download/graftemp/',
            '/download/screen/',
            '/files/cache/',
            '/files/forum/attach/',
            '/files/library/',
            '/files/users/album/',
            '/files/users/album/',
            '/files/users/avatar/',
            '/files/users/photo/',
            '/files/users/pm/',
            '/gallery/foto/',
            '/gallery/temp/',
            '/incfiles/'
        );
        $files = array (
            '/library/java/textfile.txt',
            '/library/java/META-INF/MANIFEST.MF'
        );
        require('check.php');
        echo '<hr />';
        if (!empty($error_php) || !empty($error_rights_folders) || !empty($error_rights_files)) {
            echo '<p>' . $lng['critical_errors'] . '</p>' .
                '<p><a href="index.php?lng_id=' . $lng_id . '">&lt;&lt; ' . $lng['back'] . '</a> | ' .
                '<a href="index.php?act=install&amp;lng_id=' . $lng_id . '">' . $lng['check_again'] . '</a></p>';
        } elseif (!empty($warning)) {
            echo '<p>' . $lng['are_warnings'] . '</p>' .
                '<p><a href="index.php?lng_id=' . $lng_id . '">&lt;&lt; ' . $lng['back'] . '</a> | ' .
                '<a href="index.php?act=install&amp;lng_id=' . $lng_id . '">' . $lng['check_again'] . '</a></p>' .
                '<p>' . $lng['ignore_warnings'] . '</p>' .
                '<p><a href="">' . $lng['start_installation'] . '</a> ' . $lng['not_recommended'] . '</p>';
        } else {
            echo '<p>' . $lng['configuration_successful'] . '</p>' .
                '<a href="index.php?lng_id=' . $lng_id . '">&lt;&lt; ' . $lng['back'] . '</a> | ' .
                '<a href="index.php?act=install&amp;mod=set&amp;lng_id=' . $lng_id . '">' . $lng['start_installation'] . ' &gt;&gt;</a>';
            echo '</p>';
        }
        break;
}
echo '</body></html>';
?>