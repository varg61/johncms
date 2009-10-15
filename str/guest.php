<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                                                                    //
// Официальный сайт сайт проекта:      http://johncms.com                     //
// Дополнительный сайт поддержки:      http://gazenwagen.com                  //
////////////////////////////////////////////////////////////////////////////////
// JohnCMS core team:                                                         //
// Евгений Рябинин aka john77          john77@johncms.com                     //
// Олег Касьянов aka AlkatraZ          alkatraz@johncms.com                   //
//                                                                            //
// Информацию о версиях смотрите в прилагаемом файле version.txt              //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);

$headmod = 'guest';
$textl = 'Гостевая';
require_once ("../incfiles/core.php");
require_once ("../incfiles/head.php");

// Проверяем права доступа в Админ-Клуб
if (isset($_SESSION['ga']) && $dostmod != 1)
    unset($_SESSION['ga']);

// Задаем заголовки страницы
$textl = isset($_SESSION['ga']) ? 'Админ-Клуб' : 'Гостевая';

// Если гостевая закрыта, выводим сообщение и закрываем доступ (кроме Админов)
if (!$set['mod_guest'] && !$dostadm)
{
    echo '<div class="rmenu"><p>Гостевая закрыта</p></div>';
    require_once ("../incfiles/end.php");
    exit;
}

switch ($act)
{
    case "delpost":
        ////////////////////////////////////////////////////////////
        // Удаление отдельного поста                              //
        ////////////////////////////////////////////////////////////
        if ($dostsmod && $id)
        {
            if (isset($_GET['yes']))
            {
                mysql_query("DELETE FROM `guest` WHERE `id`='" . $id . "' LIMIT 1");
                header("Location: guest.php");
            } else
            {
                echo '<p>Вы действительно хотите удалить пост?<br/>';
                echo '<a href="guest.php?act=delpost&amp;id=' . $id . '&amp;yes">Удалить</a> | <a href="guest.php">Отмена</a></p>';
            }
        }
        break;

    case "trans":
        ////////////////////////////////////////////////////////////
        // Справка по транслиту                                   //
        ////////////////////////////////////////////////////////////
        include ("../pages/trans.$ras_pages");
        echo '<br/><br/><a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">Назад</a><br/>';
        break;

    case "say":
        ////////////////////////////////////////////////////////////
        // Добавление нового поста                                //
        ////////////////////////////////////////////////////////////
        if (empty($user_id) && empty($_POST['name']))
        {
            echo "<p>Вы не ввели имя!<br/><a href='guest.php'>Назад</a></p>";
            require_once ("../incfiles/end.php");
            exit;
        }
        if (empty($_POST['msg']))
        {
            echo "<p>Вы не ввели сообщение!<br/><a href='guest.php'>Назад</a></p>";
            require_once ("../incfiles/end.php");
            exit;
        }
        if (empty($_SESSION['guest']) || $ban['1'] || $ban['13'])
        {
            echo "<p><b>Спам!</b></p>";
            require_once ("../incfiles/end.php");
            exit;
        }
        if (!$user_id && $_SESSION['code'] != $_POST['code'])
        {
            echo "<p>Код введен неверно!<br/><a href='guest.php'>Назад</a></p>";
            require_once ("../incfiles/end.php");
            exit;
        }
        $agn = strtok($agn, ' ');
        // Задаем куда вставляем, в Админ клуб (1), или в Гастивуху (0)
        $admset = isset($_SESSION['ga']) ? 1:
        0;
        // Антиспам, проверка на частоту добавления сообщений
        if ($user_id)
        {
            $old = ($rights > 0 || $dostsadm = 1) ? 10 : 30;
            $spam = $lastpost > ($realtime - $old) ? 1 : false;
        } else
        {
            $req = mysql_query("SELECT COUNT(*) FROM `guest` WHERE `soft`='" . mysql_real_escape_string($agn) . "' AND `time` >='" . ($realtime - 30) . "' AND `ip` ='" . $ipl . "' AND `adm`='" . $admset . "'");
            $spam = mysql_result($req, 0) > 0 ? 1 : false;
        }
        if ($spam)
        {
            echo "<p><b>Антифлуд!</b><br />Вы не можете так часто добавлять сообщения<br/>Порог $old секунд<br/><br/><a href='guest.php'>Назад</a></p>";
            require_once ("../incfiles/end.php");
            exit;
        }
        unset($_SESSION['guest']);
        $_SESSION['code'] = rand(1000, 9999);
        $name = mb_substr(trim($_POST['name']), 0, 20);
        if ($user_id)
        {
            $from = $login;
        } else
        {
            $from = $name;
            $user_id = 0;
        }
        $msg = trim($_POST['msg']);
        $msg = mb_substr($msg, 0, 500);
        if ($_POST['msgtrans'] == 1)
        {
            $msg = trans($msg);
        }
        // Проверка на одинаковые сообщения
        $req = mysql_query("SELECT * FROM `guest` WHERE `user_id` = '$user_id' ORDER BY `time` DESC");
        $res = mysql_fetch_array($req);
        if ($res['text'] == $msg)
        {
            header("location: guest.php");
            exit;
        }
        // Вставляем сообщение в базу
        mysql_query("INSERT INTO `guest` SET
		`adm`='" . $admset . "',
		`time`='" . $realtime . "',
		`user_id`='" . $user_id . "',
		`name`='" . mysql_real_escape_string($from) . "',
		`text`='" . mysql_real_escape_string($msg) . "',
		`ip`='" . $ipl . "',
		`soft`='" . mysql_real_escape_string($agn) . "'");
        // Фиксируем время последнего поста (антиспам)
        if ($user_id)
            mysql_query("UPDATE `users` SET `lastpost` = '" . $realtime . "' WHERE `id` = '" . $user_id . "'");
        header("location: guest.php");
        break;

    case "otvet":
        ////////////////////////////////////////////////////////////
        // Добавление "ответа Админа"                             //
        ////////////////////////////////////////////////////////////
        if ($dostsmod && $id)
        {
            if (isset($_POST['submit']))
            {
                $otv = mb_substr($_POST['otv'], 0, 500);
                mysql_query("UPDATE `guest` SET
				`admin` = '" . $login . "',
				`otvet` = '" . mysql_real_escape_string($otv) . "',
				`otime` = '" . $realtime . "'
				WHERE `id` = '" . $id . "'");
                header("location: guest.php");
            } else
            {
                $ps = mysql_query("select * from `guest` where id='" . $id . "'");
                $ps1 = mysql_fetch_array($ps);
                if (!empty($ps1['otvet']))
                {
                    echo "<br /><b>Внимание!<br />На этот пост уже ответили.</b><br/><br/>";
                }
                $text = htmlentities($ps1['text'], ENT_QUOTES, 'UTF-8');
                $otv = htmlentities($ps1['otvet'], ENT_QUOTES, 'UTF-8');
                echo "Пост в гостевой:<br /><b>$ps1[name]:</b> $text&quot;<br/><br/><form action='guest.php?act=otvet&amp;id=" . $id . "' method='post'>Ответ:<br/><textarea rows='3' name='otv'>$otv</textarea><br/><input type='submit' name='submit' value='Ok!'/><br/></form><a href='guest.php?'>В гостевую</a><br/>";
            }
        }
        break;

    case "edit":
        ////////////////////////////////////////////////////////////
        // Редактирование поста                                   //
        ////////////////////////////////////////////////////////////
        if ($dostsmod && $id)
        {
            if (isset($_POST['submit']))
            {
                $req = mysql_query("SELECT `edit_count` FROM `guest` WHERE `id`='" . $id . "' LIMIT 1");
                $res = mysql_fetch_array($req);
                $edit_count = $res['edit_count'] + 1;
                $msg = mb_substr($_POST['msg'], 0, 500);
                mysql_query("UPDATE `guest` SET
				`text`='" . mysql_real_escape_string($msg) . "',
				`edit_who`='" . $login . "',
				`edit_time`='" . $realtime . "',
				`edit_count`='" . $edit_count . "'
				WHERE `id`='" . $id . "'");
                header("location: guest.php");
            } else
            {
                $req = mysql_query("SELECT * FROM `guest` WHERE `id` = '" . $id . "' LIMIT 1");
                $res = mysql_fetch_array($req);
                $text = htmlentities($res['text'], ENT_QUOTES, 'UTF-8');
                echo '<div class="phdr"><b>Гостевая</b>: редактируем пост</div>';
                echo '<div class="rmenu"><form action="guest.php?act=edit&amp;id=' . $id . '" method="post">
				<textarea rows="3" name="msg">' . $text . '</textarea><br/>
				<input type="submit" name="submit" value="Отправить"/></form></div>';
                echo '<div class="phdr"><a href="index.php?act=trans">Транслит</a> | <a href="../str/smile.php">Смайлы</a></div>';
                echo '<p><a href="guest.php">Назад</a></p>';
            }
        }
        break;

    case 'clean':
        ////////////////////////////////////////////////////////////
        // Очистка Гостевой                                       //
        ////////////////////////////////////////////////////////////
        if ($dostadm)
        {
            if (isset($_POST['submit']))
            {
                $adm = isset($_SESSION['ga']) ? 1 : 0;
                $cl = isset($_POST['cl']) ? intval($_POST['cl']) : '';
                switch ($cl)
                {
                    case '1':
                        // Чистим сообщения, старше 1 дня
                        mysql_query("DELETE FROM `guest` WHERE `adm`='$adm' AND `time` < '" . ($realtime - 86400) . "'");
                        echo '<p>Удалены все сообщения, старше 1 дня.</p><p><a href="guest.php">Вернуться</a></p>';
                        break;

                    case '2':
                        // Проводим полную очистку
                        mysql_query("DELETE FROM `guest` WHERE `adm`='$adm'");
                        echo '<p>Удалены все сообщения.</p><p><a href="guest.php">Вернуться</a></p>';
                        break;

                    default:
                        // Чистим сообщения, старше 1 недели
                        mysql_query("DELETE FROM `guest` WHERE `adm`='$adm' AND `time`<='" . ($realtime - 604800) . "';");
                        echo '<p>Все сообщения, старше 1 недели удалены из Гостевой.</p><p><a href="guest.php">В Гостевую</a></p>';
                }
                mysql_query("OPTIMIZE TABLE `guest`");
            } else
            {
                echo '<p><b>Очистка сообщений</b></p>';
                echo '<u>Что чистим?</u>';
                echo '<form id="clean" method="post" action="guest.php?act=clean">';
                echo '<input type="radio" name="cl" value="0" checked="checked" />Старше 1 недели<br />';
                echo '<input type="radio" name="cl" value="1" />Старше 1 дня<br />';
                echo '<input type="radio" name="cl" value="2" />Очищаем все<br />';
                echo '<input type="submit" name="submit" value="Очистить" />';
                echo '</form>';
                echo '<p><a href="guest.php">Отмена</a></p>';
            }
        }
        break;

    case 'ga':
        ////////////////////////////////////////////////////////////
        // Переключение режима работы Гостевая / Админ-клуб       //
        ////////////////////////////////////////////////////////////
        if ($dostmod == 1)
        {
            if ($_GET['do'] == 'set')
            {
                $_SESSION['ga'] = 1;
                $textl = 'Админ-Клуб';
            } else
            {
                unset($_SESSION['ga']);
                $textl = 'Гостевая';
            }
        }

    default:
        ////////////////////////////////////////////////////////////
        // Отображаем Гостевую, или Админ клуб                    //
        ////////////////////////////////////////////////////////////
        if (!$set['mod_guest'])
            echo '<p><span class="red"><b>Гостевая закрыта!</b></span></p>';
        echo '<div class="phdr"><b>Гостевая</b></div>';
        // Форма ввода нового сообщения
        if (($user_id || $set['mod_guest'] == 2) && !$ban['1'] && !$ban['13'])
        {
            $_SESSION['guest'] = rand(1000, 9999);
            echo '<div class="gmenu"><form action="guest.php?act=say" method="post">';
            if (!$user_id)
            {
                echo "Имя(max. 25):<br/><input type='text' name='name' maxlength='25'/><br/>";
            }
            echo 'Сообщение(max. 500):<br/><textarea cols="20" rows="2" name="msg"></textarea><br/>';
            if ($offtr != 1)
            {
                echo "<input type='checkbox' name='msgtrans' value='1' /> Транслит сообщения<br/>";
            }
            if (!$user_id)
            {
                // CAPTCHA для гостей
                $_SESSION['code'] = rand(1000, 9999);
                echo '<img src="../code.php" alt="Код"/><br />';
                echo '<input type="text" size="4" maxlength="4"  name="code"/>&nbsp;введите код<br />';
            }
            echo "<input type='submit' title='Нажмите для отправки' name='submit' value='Отправить'/></form></div>";
        } else
        {
            echo '<div class="rmenu">Писать могут только <a href="../in.php">авторизованные</a> посетители</div>';
        }
        if (isset($_SESSION['ga']) && ($login == $nickadmina || $login == $nickadmina2 || $rights >= "1"))
        {
            $req = mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='1'");
        } else
        {
            $req = mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='0'");
        }
        $colmes = mysql_result($req, 0); // Число сообщений в гастивухе
        if ($colmes > 0)
        {
            if (isset($_SESSION['ga']) && ($login == $nickadmina || $login == $nickadmina2 || $rights >= "1"))
            {
                // Запрос для Админ клуба
                echo '<div class="rmenu"><b>АДМИН-КЛУБ</b></div>';
                $req = mysql_query("SELECT `guest`.*, `users`.`rights`, `users`.`lastdate`, `users`.`sex`, `users`.`status`, `users`.`datereg`, `users`.`id` AS `uid`
				FROM `guest` LEFT JOIN `users` ON `guest`.`user_id` = `users`.`id` WHERE `guest`.`adm`='1' ORDER BY `time` DESC LIMIT " . $start . "," . $kmess);
            } else
            {
                // Запрос для обычной Гастивухи
                $req = mysql_query("SELECT `guest`.*, `users`.`rights`, `users`.`lastdate`, `users`.`sex`, `users`.`status`, `users`.`datereg`, `users`.`id` AS `uid`
				FROM `guest` LEFT JOIN `users` ON `guest`.`user_id` = `users`.`id` WHERE `guest`.`adm`='0' ORDER BY `time` DESC LIMIT " . $start . "," . $kmess);
            }
            while ($res = mysql_fetch_array($req))
            {
                echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                if ($res['user_id'] != "0")
                {
                    if ($res['sex'])
                        echo '<img src="../theme/' . $skin . '/images/' . ($res['sex'] == 'm' ? 'm' : 'f') . ($res['datereg'] > $realtime - 86400 ? '_new.gif" width="20"' : '.gif" width="16"') . ' height="16"/>&nbsp;';
                    else
                        echo '<img src="../images/del.png" width="12" height="12" />&nbsp;';
                    // Ник юзера и ссылка на Анкету
                    if (!empty($user_id) && ($user_id != $res['user_id']))
                    {
                        echo '<a href="anketa.php?user=' . $res['user_id'] . '"><b>' . $res['name'] . '</b></a> ';
                    } else
                    {
                        echo '<b>' . $res['name'] . '</b>';
                    }
                    // Должность
                    switch ($res['rights'])
                    {
                        case 7:
                            echo ' Adm ';
                            break;
                        case 6:
                            echo ' Smd ';
                            break;
                        case 2:
                            echo ' Mod ';
                            break;
                        case 1:
                            echo ' Kil ';
                            break;
                    }
                    // Онлайн / Офлайн
                    $ontime = $res['lastdate'] + 300;
                    if ($realtime > $ontime)
                    {
                        echo '<font color="#FF0000"> [Off]</font>';
                    } else
                    {
                        echo '<font color="#00AA00"> [ON]</font>';
                    }
                } else
                {
                    // Ник Гостя
                    echo '<b>Гость ' . htmlentities($res['name'], ENT_QUOTES, 'UTF-8') . '</b>';
                }
                $vrp = $res['time'] + $sdvig * 3600;
                $vr = date("d.m.y / H:i", $vrp);
                echo ' <font color="#999999">(' . $vr . ')</font><br/>';
                if (!empty($res['status']))
                    echo '<div class="status"><img src="../images/star.gif" alt=""/>&nbsp;' . $res['status'] . '</div>';
                $text = htmlentities($res['text'], ENT_QUOTES, 'UTF-8');
                if ($res['user_id'] != "0")
                {
                    // Для зарегистрированных показываем ссылки и смайлы
                    $text = tags($text);
                    $text = nl2br($text);
                    if ($offsm != 1)
                    {
                        $text = smileys($text, ($res['name'] == $nickadmina || $res['name'] == $nickadmina2 || $res['rights'] >= 1) ? 1 : 0);
                    }
                } else
                {
                    // Для гостей фильтруем ссылки
                    $text = antilink($text);
                }
                // Отображаем текст поста
                echo $text;
                // Если пост редактировался, показываем кто и когда
                if ($res['edit_count'] >= 1)
                {
                    $diz = $res['edit_time'] + $sdvig * 3600;
                    $dizm = date("d.m.y /H:i", $diz);
                    echo "<br /><small><font color='#999999'>Посл. изм. <b>$res[edit_who]</b>  ($dizm)<br />Всего изм.:<b> $res[edit_count]</b></font></small>";
                }
                // Ответ Модера
                if (!empty($res['otvet']))
                {
                    $otvet = htmlentities($res['otvet'], ENT_QUOTES, 'UTF-8');
                    $otvet = nl2br($otvet);
                    $otvet = tags($otvet);
                    $vrp1 = $res['otime'] + $sdvig * 3600;
                    $vr1 = date("d.m.Y / H:i", $vrp1);
                    if ($offsm != 1)
                    {
                        $otvet = smileys($otvet, 1);
                    }
                    echo '<div class="reply"><b>' . $res['admin'] . '</b>: (' . $vr1 . ')<br/>' . $otvet . '</div>';
                }
                // Ссылки на Модерские функции
                if ($dostsmod == 1)
                {
                    echo '<div class="func"><a href="guest.php?act=otvet&amp;id=' . $res['id'] . '">Отв.</a> | <a href="guest.php?act=edit&amp;id=' . $res['id'] . '">Изм.</a> | <a href="guest.php?act=delpost&amp;id=' . $res['id'] . '">Удалить</a><br/>';
                    echo long2ip($res['ip']) . ' - ' . $res['soft'] . '</div>';
                }
                echo "</div>";
                ++$i;
            }
            echo '<div class="phdr">Всего сообщений: ' . $colmes . '</div>';
            if ($colmes > $kmess)
            {
                echo '<p>' . pagenav('guest.php?', $start, $colmes, $kmess) . '</p>';
                echo '<p><form action="guest.php" method="get"><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
            }
            echo '<p><div class="func">';
            // Для Админов даем ссылку на чистку Гостевой
            if ($dostadm)
                echo '<a href="guest.php?act=clean">Чистка истории</a><br />';
            echo '</div></p>';
        } else
        {
            echo '<p>В Гостевой сообщений нет.</p>';
        }
        // Ссылка на Админ-клуб
        if ($dostmod)
            echo (isset($_SESSION['ga']) ? '<p><a href="guest.php?act=ga"><b>Гостевая &gt;&gt;</b></a></p>' : '<p><a href="guest.php?act=ga&amp;do=set"><b>Админ-Клуб &gt;&gt;</b></a></p>');
        break;
}

require_once ("../incfiles/end.php");

?>