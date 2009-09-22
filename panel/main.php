<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                             Content Management System              //
// Официальный сайт сайт проекта:      http://johncms.com                     //
// Дополнительный сайт поддержки:      http://gazenwagen.com                  //
////////////////////////////////////////////////////////////////////////////////
// JohnCMS core team:                                                         //
// Евгений Рябинин aka john77          john77@gazenwagen.com                  //
// Олег Касьянов aka AlkatraZ          alkatraz@gazenwagen.com                //
//                                                                            //
// Информацию о версиях смотрите в прилагаемом файле version.txt              //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);
define('_IN_JOHNADM', 1);

$textl = 'Админка';
require_once ('../incfiles/core.php');
require_once ('../incfiles/head.php');

if ($dostmod == 1)
{
    $do = isset($_GET['do']) ? $_GET['do'] : '';
    switch ($do)
    {
        case 'antispy':
            ////////////////////////////////////////////////////////////
            // Антишпион, сканирование на подозрительные файлы        //
            ////////////////////////////////////////////////////////////
            if ($dostadm == 1)
            {
                require_once ('scan_class.php');
                require_once ('scan_list.php');
                $scaner = new scaner();
                $scaner->scan_folders = $scan_folders;
                $scaner->good_files = $good_files;
                $act = isset($_GET['act']) ? $_GET['act'] : null;
                switch ($act)
                {
                    case 'scan':
                        // Сканируем на соответствие дистрибутиву
                        $scaner->scan();
                        echo '<div class="phdr"><b>Результат сканирования</b></div>';
                        if (count($scaner->bad_files))
                        {
                            echo '<div class="rmenu">Несоответствие дистрибутиву<br /><small>Внимание! Все файлы, перечисленные в списке необходимо удалить, так, как они представляют угрозу для безопасности Вашего сайта.</small></div>';
                            echo '<div class="menu">';
                            foreach ($scaner->bad_files as $idx => $data)
                            {
                                echo $data['file_path'] . '<br />';
                            }
                            echo '</div><div class="rmenu">Всего файлов: ' . count($scaner->bad_files) .
                                '<br /><small>Если обнаруженные файлы относятся к дополнительным модулям, которые были устанавлены и Вы уверены в их надежности, можете игнорировать предупреждение.</small></div>';
                        } else
                        {
                            echo '<div class="gmenu">Отлично!<br />Список файлов соот ветствует дистрибутиву.</div>';
                        }
                        echo '<div class="bmenu"><a href="main.php?do=antispy&amp;act=scan">Пересканировать</a></div><p><a href="main.php?do=antispy">Меню сканера</a><br /><a href="main.php">В админку</a></p>';
                        break;
                    case 'snapscan':
                        // Сканируем на соответствие образу
                        $scaner->snapscan();
                        echo '<div class="phdr"><b>Результат сканирования</b></div>';
                        if (count($scaner->track_files) == 0)
                        {
                            echo '<p>Образ файлов еще не был создан.</p><p><a href="main.php?do=antispy&amp;act=snap">Создание образа</a></p>';
                        } else
                        {
                            if (count($scaner->bad_files))
                            {
                                echo '<div class="rmenu">Несоответствие образу<br /><small>Внимание!!! Вам необходимо обратить внимание на все файлы из данного списка. Они были добавлены, или модифицированы с момента создания образа.</small></div>';
                                echo '<div class="menu">';
                                foreach ($scaner->bad_files as $idx => $data)
                                {
                                    echo $data['file_path'] . '<br />';
                                }
                                echo '</div><div class="rmenu">Всего файлов: ' . count($scaner->bad_files) . '</div>';
                            } else
                            {
                                echo '<div class="gmenu">Отлично!<br />Все файлы соответствуют ранее сделанному образу.</div>';
                            }
                            echo '<div class="bmenu"><a href="main.php?do=antispy&amp;act=snapscan">Пересканировать</a></div><p><a href="main.php?do=antispy">Меню сканера</a><br /><a href="main.php">В админку</a></p>';
                        }
                        break;
                    case 'snap':
                        // Добавляем в базу образы файлов
                        if (isset($_POST['submit']))
                        {
                            $scaner->snap();
                            echo '<p>Образ файлов успешно создан</p><p><a href="main.php?do=antispy">Продолжить</a></p>';
                        } else
                        {
                            echo '<div class="phdr"><b>Создание образа</b></div>';
                            echo '<div class="rmenu"><b>ВНИМАНИЕ!!!</b><br />Перед продолжением, убедитесь, что все файлы, которые были выявлены в режиме сканирования "<a href="main.php?do=antispy&amp;act=scan">Дистрибутив</a>" и "<a href="main.php?do=antispy&amp;act=check">По образу</a>" надежны и не содержат несанкционированных модификаций.</div>';
                            echo '<div class="menu"><p>Данная процедура создает список всех скриптовых файлов Вашего сайта, вычисляет их контрольные суммы и заносит в базу, для последующего сравнения.</p><p><form action="main.php?do=antispy&amp;act=snap" method="post"><input type="submit" name="submit" value="Создать образ" /></form></p></div>';
                            echo '<div class="bmenu"><a href="main.php?do=antispy">Назад</a> (отмена)</div>';
                        }
                        break;
                    default:
                        echo '<div class="phdr"><b>Сканер файлов</b></div>';
                        echo '<div class="menu">Данный модуль позволяет проводить сканирование директорий сайта и выявлять подозрительные файлы.</div>';
                        echo '<div class="bmenu">Режим сканирования</div>';
                        echo '<div class="menu"><a href="main.php?do=antispy&amp;act=scan">Дистрибутив</a><br /><small>Выявление "лишних" файлов, тех, что не входят в оригинальный дистрибутив.</small></div>';
                        echo '<div class="menu"><a href="main.php?do=antispy&amp;act=snapscan">По образу</a><br /><small>Сравнение списка и контрольных сумм файлов с заранее сделанным образом.<br />Позволяет выявить неизвестные файлы, и несанкционированные изменения.</small></div>';
                        echo '<div class="bmenu"><a href="main.php?do=antispy&amp;act=snap">Создание образа</a><br /><small>Данная процедура делает "снимок" всех скриптовых файлов сайта, вычисляет их контрольные суммы и запоминает в базе.</small></div>';
                        echo '<p><a href="main.php">В админку</a></p>';
                }
            }
            break;

        case 'users':
            if (empty($_POST['user']))
            {
                echo '<br /><b>Вы не заполнили поле!</b><br/><br/><a href="main.php?do=search">Назад</a><br/><br/>';
                require_once ("../incfiles/end.php");
                exit;
            }
            // Поиск по ID
            if ($_POST['term'] == '1')
            {
                $search = intval($_POST['user']);
                $req = mysql_query("select * from `users` where `id`='" . $search . "';");
            }
            // Поиск по Нику
            else
            {
                $search = rus_lat(mb_strtolower($_POST['user']));
                $req = mysql_query("select * from `users` where `name_lat`='" . mysql_real_escape_string($search) . "';");
            }
            if (mysql_num_rows($req) == 0)
            {
                echo "Нет такого юзера!<br/><a href='main.php'>Назад</a><br/>";
                require_once ("../incfiles/end.php");
                exit;
            }
            $res = mysql_fetch_array($req);
            header("location: ../str/anketa.php?user=$res[id]");
            break;

        case 'search':
            echo '<div class="phdr">Поиск юзера</div>';
            echo '<br />Кого ищем?:<br/>';
            echo '<form action="main.php?do=users" method="post">';
            echo '<input type="text" name="user"/><br/>';
            echo '<input name="term" type="radio" value="0" checked="checked" />Поиск по Нику<br />';
            echo '<input name="term" type="radio" value="1" />Поиск по ID<br /><br />';
            echo '<input type="submit" value="Поиск"/>';
            echo '</form>';
            echo '<p><a href="main.php">В админку</a></p>';
            break;

        case 'smileys':
            echo '<div class="phdr"><b>Смайлы</b></div>';
            if ($total = smileys(0, 2))
            {
                echo '<div class="gmenu"><p>Кэш смайлов успешно обновлен</p></div>';
            } else
            {
                echo '<div class="rmenu"><p>Ошибка лоступа к Кэшу смайлов</p></div>';
                $total = 0;
            }
            echo '<div class="phdr">Всего смайлов: ' . $total . '</div>';
            echo '<p><a href="main.php">В админку</a></p>';
            break;

        default:
            ////////////////////////////////////////////////////////////
            // Главное меню админки                                   //
            ////////////////////////////////////////////////////////////
            echo '<div class="phdr"><b>Админ Панель</b></div>';
            $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users`"), 0);
            $regtotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `preg`='0'"), 0);
            $bantotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time`>'" . $realtime . "'"), 0);
            echo '<div class="menu">';
            // Блок пользователей
            //TODO: Разобраться с правами доступа на подтверждение реги.
            echo '<p><img src="../images/users.png" width="16" height="16" class="left" />&nbsp;<b>Пользователи</b><ul>
			<li><a href="../str/users.php">Весь список</a>&nbsp;(' . $total . ')</li>';
            if ($regtotal > 0)
                echo '<li><span class="red"><a href="preg.php">На регистрации</a>&nbsp;(' . $regtotal . ')</span></li>';
            else
                echo '<li><a href="preg.php">На регистрации</a>&nbsp;(' . $regtotal . ')</li>';
            echo ($dostsadm ? '<li><a href="">Чистка базы</a></li>' : '') . '
			<li><a href="zaban.php">Бан-панель</a>&nbsp;(' . $bantotal . ')</li>
			<li><a href="main.php?do=search">Поиск</a></li>
			</ul></p>';
            // Блок модулей
            if ($dostadm)
            {
                echo '<p><img src="../images/modules.png" width="16" height="16" class="left" />&nbsp;<b>Модули</b><ul>
				<li><a href="modules.php">Права доступа</a></li>
				' . ($dostsadm ? '<li><a href="counters.php">Счетчики</a></li>' : '') . '
				<li><a href="news.php">Новости</a></li>
				<li><a href="forum.php">Форум</a></li>
				<li><a href="chat.php">Чат</a></li>
				</ul></p>';
            }
            echo '</div>';
            // Блок системных настроек</b>
            if ($dostadm)
            {
                echo '<div class="bmenu"><p><img src="../images/settings.png" width="16" height="16" class="left" />&nbsp;<b>Система</b><ul>
				<li><a href="ipban.php">Бан по IP</a></li>
				<li><a href="main.php?do=smileys">Обновить смайлы</a></li>
				<li><a href="main.php?do=antispy">Сканер антишпион</a></li>
				<li><a href="set.php">Настройки</a></li>
				</ul></p></div>';
            }
    }
} else
{
    header("Location: ../index.php?err");
}

require_once ("../incfiles/end.php");

?>