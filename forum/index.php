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

////////////////////////////////////////////////////////////
// Список расширений, разрешенных к выгрузке файлов       //
////////////////////////////////////////////////////////////
// Файлы Windows
$ext_win = array('exe', 'msi');
// Файлы Java
$ext_java = array('jar', 'jad');
// Файлы SIS
$ext_sis = array('sis', 'sisx');
// Файлы документов и тексты
$ext_doc = array('txt', 'pdf', 'doc', 'rtf', 'djvu', 'xls');
// Файлы картинок
$ext_pic = array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'wmf');
// Файлы архивов
$ext_zip = array('zip', 'rar', '7z', 'tar', 'gz');
// Файлы видео
$ext_video = array('3gp', 'avi', 'flv', 'mpeg', 'mp4');
// Звуковые файлы
$ext_audio = array('mp3', 'amr');
// Другие типы файлов (что не перечислены выше)
$ext_other = array();

$headmod = 'forum';
require_once ('../incfiles/core.php');

// Ограничиваем доступ к Форуму
$error = '';
if (!$set['mod_forum'] && !$dostadm)
    $error = 'Форум закрыт';
elseif ($set['mod_forum'] == 1 && !$user_id)
    $error = 'Доступ на форум открыт только <a href="../in.php">авторизованным</a> посетителям';
if ($error)
{
    require_once ("../incfiles/head.php");
    echo '<div class="rmenu"><p>' . $error . '</p></div>';
    require_once ("../incfiles/end.php");
    exit;
}

// Заголовки форума
if (empty($id))
{
    $textl = 'Форум';
} else
{
    $req = mysql_query("SELECT `text` FROM `forum` WHERE `id`= '" . $id . "' LIMIT 1;");
    $res = mysql_fetch_array($req);
    $hdr = strtr($res['text'], array('&quot;' => '', '&amp;' => '', '&lt;' => '', '&gt;' => '', '&#039;' => ''));
    $hdr = mb_substr($hdr, 0, 30);
    $hdr = htmlentities($hdr, ENT_QUOTES, 'UTF-8');
    $textl = mb_strlen($res['text']) > 30 ? $hdr . '...' : $hdr;
}

if ($user_id)
{
    // Фиксируем местоположение юзера
    $tti = round(($datauser['ftime'] - $realtime) / 60);
    if ($act == 'files')
    {
        $where = 'forumfiles';
    } elseif ($id)
    {
        $where = "forum,$id";
    } else
    {
        $where = "forum";
    }
    mysql_query("INSERT INTO `count`  SET
	`ip`='$ipp',
	`browser`='$agn',
	`time`='$realtime',
	`where`='$where',
	`name`='$login';");
}

$do = array('new', 'who', 'addfile', 'file', 'moders', 'per', 'ren', 'deltema', 'vip', 'close', 'delpost', 'editpost', 'nt', 'tema', 'loadtem', 'say', 'post', 'read', 'faq', 'trans', 'massdel', 'files', 'filter');
if (in_array($act, $do))
{
    require_once ($act . '.php');
} else
{
    require_once ("../incfiles/head.php");
    // Если форум закрыт, то для Админов выводим напоминание
    if (!$set['mod_forum'])
        echo '<p><font color="#FF0000"><b>Форум закрыт!</b></font></p>';
    if (!$user_id)
    {
        if (isset($_GET['newup']))
        {
            $_SESSION['uppost'] = 1;
        }
        if (isset($_GET['newdown']))
        {
            $_SESSION['uppost'] = 0;
        }
    }

    if ($id)
    {
        $type = mysql_query("SELECT * FROM `forum` WHERE `id`= '" . $id . "' LIMIT 1");
        $type1 = mysql_fetch_array($type);
        $tip = $type1['type'];
        switch ($tip)
        {
            case "f":
                ////////////////////////////////////////////////////////////
                // Список Подразделов форума                              //
                ////////////////////////////////////////////////////////////

                // Ссылка на Новые темы
                echo '<p><a href="index.php?act=new">' . ($user_id ? 'Непрочитанное&nbsp;(' . forum_new() . ')' : 'Последние 10 тем') . '</a></p>';

                // Панель навигации
                echo '<div class="phdr">';
                echo '<a href="index.php">Форум</a> &gt;&gt; <b>' . $type1['text'] . '</b>';
                echo '</div>';
                $req = mysql_query("SELECT `id`, `text` FROM `forum` WHERE `type`='r' AND `refid`='$id' ORDER BY `realid`");
                $total = mysql_num_rows($req);
                while ($mass1 = mysql_fetch_array($req))
                {
                    echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                    $coltem = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `refid` = '" . $mass1['id'] . "'");
                    $coltem1 = mysql_result($coltem, 0);
                    echo '<a href="?id=' . $mass1['id'] . '">' . $mass1['text'] . '</a>';
                    if ($coltem1 > 0)
                    {
                        echo " [$coltem1]";
                    }
                    echo '</div>';
                    ++$i;
                }
                echo '<div class="phdr">Всего: ' . $total . '</div>';
                break;

            case "r":
                ////////////////////////////////////////////////////////////
                // Список тем                                             //
                ////////////////////////////////////////////////////////////
                $qz = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='t' AND `refid`='$id'" . ($dostadm ? '' : " AND `close`!='1'"));
                $coltem = mysql_result($qz, 0);
                // Ссылка на Новые темы
                echo '<p><a href="index.php?act=new">' . ($user_id ? 'Непрочитанное&nbsp;(' . forum_new() . ')' : 'Последние 10 тем') . '</a></p>';
                // Панель навигации
                $forum = mysql_query("SELECT * FROM `forum` WHERE `type`='f' AND `id`='" . $type1['refid'] . "'");
                $forum1 = mysql_fetch_array($forum);
                echo '<div class="phdr">';
                echo '<a href="index.php">Форум</a> &gt;&gt; <a href="index.php?id=' . $type1['refid'] . '">' . $forum1['text'] . '</a> &gt;&gt; <b>' . $type1['text'] . '</b>';
                echo '</div>';
                if ($user_id && !$ban['1'] && !$ban['11'])
                {
                    echo '<div class="gmenu"><a href="index.php?act=nt&amp;id=' . $id . '">Новая тема</a></div>';
                }
                $q1 = mysql_query("SELECT * FROM `forum` WHERE `type`='t'" . ($dostadm == 1 ? '' : " AND `close`!='1'") . " AND `refid`='$id' ORDER BY `vip` DESC, `time` DESC LIMIT " . $start . "," . $kmess);
                while ($mass = mysql_fetch_array($q1))
                {
                    echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                    $nikuser = mysql_query("SELECT `from` FROM `forum` WHERE `type` = 'm' AND `close` != '1' AND `refid` = '" . $mass['id'] . "' ORDER BY `time` DESC LIMIT 1");
                    $nam = mysql_fetch_array($nikuser);
                    $colmes = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='m' AND `refid`='" . $mass['id'] . "'" . ($dostadm == 1 ? '' : " AND `close` != '1'"));
                    $colmes1 = mysql_result($colmes, 0);
                    $cpg = ceil($colmes1 / $kmess);
                    $colmes1 = $colmes1 - 1;
                    if ($colmes1 < 0)
                    {
                        $colmes1 = 0;
                    }
                    // Выводим список тем
                    if ($mass['vip'] == 1)
                    {
                        echo '<img src="../theme/' . $skin . '/images/pt.gif" alt=""/>';
                    } elseif ($mass['edit'] == 1)
                    {
                        echo '<img src="../theme/' . $skin . '/images/tz.gif" alt=""/>';
                    } elseif ($mass['close'] == 1)
                    {
                        echo '<img src="../theme/' . $skin . '/images/dl.gif" alt=""/>';
                    } else
                    {
                        $np = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_rdm` WHERE `time` > '" . $mass['time'] . "' AND `topic_id` = '" . $mass['id'] . "' AND `user_id`='$user_id'"), 0);
                        echo '<img src="../theme/' . $skin . '/images/' . ($np ? 'op' : 'np') . '.gif" alt=""/>';
                    }
                    echo "&nbsp;<a href='index.php?id=$mass[id]'>$mass[text]</a> [$colmes1]";
                    if ($cpg > 1)
                    {
                        echo "<a href='index.php?id=$mass[id]&amp;page=$cpg'>&nbsp;&gt;&gt;</a>";
                    }
                    echo '<div class="sub">';
                    echo $mass['from'];
                    if (!empty($nam['from']))
                    {
                        echo '&nbsp;/&nbsp;' . $nam['from'];
                    }
                    $vrp = $mass['time'] + $sdvig * 3600;
                    echo ' <font color="#777777">' . date("d.m.y / H:i", $vrp) . "</font></div></div>";
                    ++$i;
                }
                echo '<div class="phdr">Всего: ' . $coltem . '</div>';
                if ($coltem > $kmess)
                {
                    echo '<p>' . pagenav('index.php?id=' . $id . '&amp;', $start, $coltem, $kmess) . '</p>';
                    echo '<p><form action="index.php" method="get"><input type="hidden" name="id" value="' . $id . '"/><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
                }
                break;

            case "t":
                ////////////////////////////////////////////////////////////
                // Читаем топик                                           //
                ////////////////////////////////////////////////////////////
                $filter = isset($_SESSION['fsort_id']) && $_SESSION['fsort_id'] == $id ? 1:
                0;
                $sql = '';
                if ($filter && !empty($_SESSION['fsort_users']))
                {
                    // Подготавливаем запрос на фильтрацию юзеров
                    $sw = 0;
                    $sql = ' AND (';
                    $fsort_users = unserialize($_SESSION['fsort_users']);
                    foreach ($fsort_users as $val)
                    {
                        if ($sw)
                            $sql .= ' OR ';
                        $sortid = intval($val);
                        $sql .= "`forum`.`user_id` = '$sortid'";
                        $sw = 1;
                    }
                    $sql .= ')';
                }
                if ($user_id && !$filter)
                {
                    //блок, фиксирующий факт прочтения топика
                    $req = mysql_query("SELECT COUNT(*) FROM `cms_forum_rdm` WHERE `topic_id` = '$id' AND `user_id` = '$user_id'");
                    if (mysql_result($req, 0) == 1)
                    {
                        // Обновляем время метки о прочтении
                        mysql_query("UPDATE `cms_forum_rdm` SET `time` = '$realtime' WHERE `topic_id`='$id' AND `user_id` = '$user_id'");
                    } else
                    {
                        // Ставим метку о прочтении
                        mysql_query("INSERT INTO `cms_forum_rdm` SET  `topic_id` = '$id', `user_id` = '$user_id', `time` = '$realtime'");
                    }
                }
                // Ссылка на Новые темы
                echo '<p><a href="index.php?act=new">' . ($user_id ? 'Непрочитанное&nbsp;(' . forum_new() . ')' : 'Последние 10 тем') . '</a></p>';
                if ($dostsadm != 1 && $type1['close'] == 1)
                {
                    echo '<div class="rmenu"><p>Тема удалена!<br/><a href="?id=' . $type1['refid'] . '">В раздел</a></p></div>';
                    require_once ("../incfiles/end.php");
                    exit;
                }
                // Счетчик постов темы
                $colmes = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='m'$sql AND `refid`='$id'" . ($dostadm == 1 ? '' : " AND `close` != '1'")), 0);
                // Задаем правила сортировки (новые внизу / вверху)
                if ($user_id)
                {
                    $order = $datauser['upfp'] == 1 ? 'DESC' : 'ASC';
                } else
                {
                    $order = ((empty($_SESSION['uppost'])) || ($_SESSION['uppost'] == 0)) ? 'ASC' : 'DESC';
                }
                $q3 = mysql_query("SELECT `id`, `refid`, `text` FROM `forum` WHERE `id` = '" . $type1['refid'] . "' LIMIT 1");
                $razd = mysql_fetch_array($q3);
                $q4 = mysql_query("SELECT `id`, `text` FROM `forum` WHERE `id` = '" . $razd['refid'] . "' LIMIT 1");
                $frm = mysql_fetch_array($q4);
                // Панель навигации
                echo '<div class="gmenu">';
                echo '<a href="index.php">Форум</a> &gt;&gt; <a href="index.php?id=' . $frm['id'] . '">' . $frm['text'] . '</a> &gt;&gt; <a href="index.php?id=' . $razd['id'] . '">' . $razd['text'] . '</a>';
                echo '</div>';
                echo '<p><a name="up" id="up"></a><a href="#down">Вниз</a></p>';
                // Выводим название топика
                echo '<div class="phdr"><b>' . $type1['text'] . '</b></div>';
                if ($filter)
                    echo '<div class="rmenu">В теме включена фильтрация по авторам постов</div>';
                if ($type1['edit'] == 1)
                {
                    echo '<b><span class="red">Тема закрыта</span></b><br/>';
                } elseif ($type1['close'] == 1)
                {
                    echo '<b><span class="red">Тема удалена</span></b><br/>';
                }
                if ($user_id && $type1['edit'] != 1 && $upfp == 1)
                {
                    if ($datauser['farea'] == 1 && $datauser['postforum'] >= 1)
                    {
                        echo "<div class='e'>Написать<br/><form action='index.php?act=say&amp;id=" . $id . "' method='post' enctype='multipart/form-data'><textarea cols='20' rows='2' title='Введите текст сообщения' name='msg'></textarea><br/>";
                        echo "<input type='checkbox' name='addfiles' value='1' /> Добавить файл<br/>";
                        if ($offtr != 1)
                        {
                            echo "<input type='checkbox' name='msgtrans' value='1' /> Транслит сообщения<br/>";
                        }
                        echo "<input type='submit' title='Нажмите для отправки' name='submit' value='Отправить'/><br/></form></div>";
                    } else
                    {
                        echo '<a href="?act=say&amp;id=' . $id . '">Написать</a><br />';
                    }
                }
                if ($dostfmod == 1)
                    echo '<form action="index.php?act=massdel" method="post">';

                ////////////////////////////////////////////////////////////
                // Запросы
                ////////////////////////////////////////////////////////////
                $req = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`lastdate`, `users`.`status`, `users`.`datereg`
				FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
				WHERE `forum`.`type` = 'm' AND `forum`.`refid` = '$id'" . ($dostadm == 1 ? "" : " AND `forum`.`close` != '1'") . "$sql ORDER BY `forum`.`time` $order LIMIT $start, $kmess");
                while ($res = mysql_fetch_array($req))
                {
                    echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                    echo $res['datereg'] > $realtime - 86400 ? '<img src="../theme/' . $skin . '/images/add.gif" alt=""/>&nbsp;' : '';
                    // Значок пола
                    if ($res['sex'])
                        echo '<img src="../theme/' . $skin . '/images/' . ($res['sex'] == 'm' ? 'm' : 'f') . '.gif" alt=""  width="16" height="16"/>&nbsp;';
                    else
                        echo '<img src="../images/del.png" width="12" height="12" />&nbsp;';
                    // Ник юзера и ссылка на его анкету
                    if ($user_id && $user_id != $res['user_id'])
                    {
                        echo '<a href="../str/anketa.php?user=' . $res['user_id'] . '&amp;fid=' . $res['id'] . '"><b>' . $res['from'] . '</b></a> ';
                        echo '<a href="index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . $start . '"> [о]</a> <a href="index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . $start . '&amp;cyt"> [ц]</a>';
                    } else
                    {
                        echo '<b>' . $res['from'] . '</b>';
                    }
                    // Метка должности
                    switch ($mass1['rights'])
                    {
                        case 7:
                            echo " Adm ";
                            break;
                        case 6:
                            echo " Smd ";
                            break;
                        case 3:
                            echo " Mod ";
                            break;
                        case 1:
                            echo " Kil ";
                            break;
                    }
                    // Метка Онлайн / Офлайн
                    echo ($realtime > $res['lastdate'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>');
                    // Время поста
                    echo ' <span class="gray">(' . date("d.m.Y / H:i", $res['time'] + $sdvig * 3600) . ')</span><br/>';
                    // Статус юзера
                    if (!empty($res['status']))
                        echo '<div class="status"><img src="../theme/' . $skin . '/images/star.gif" alt=""/>&nbsp;' . $res['status'] . '</div>';
                    if ($res['close'])
                    {
                        echo '<span class="red">Пост удалён!</span><br/>';
                    }
                    ////////////////////////////////////////////////////////////
                    // Вывод текста поста                                     //
                    ////////////////////////////////////////////////////////////
                    $text = $res['text'];
                    if (mb_strlen($text) > 500)
                    {
                        // Если текст длинный, обрезаем и даем ссылку на полный вариант
                        $text = mb_substr($text, 0, 400);
                        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
                        $text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
                        $text = nl2br($text);
                        $text = tags($text);
                        echo $text . '...<br /><a href="index.php?act=post&amp;s=' . $page . '&amp;id=' . $res['id'] . '">Читать все &gt;&gt;</a>';
                    } else
                    {
                        // Или, обрабатываем тэги и выводим весь текст
                        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
                        //TODO: Переделать с ников Суперадминов, на их ID
                        if ($offsm != 1)
                            $text = smileys($text, ($res['from'] == $nickadmina || $res['from'] == $nickadmina2 || $res['rights'] >= 1) ? 1 : 0);
                        $text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
                        $text = nl2br($text);
                        $text = tags($text);
                        echo $text;
                    }
                    if ($res['kedit'] > 0) //TODO: Доработать

                    {
                        $diz = $res['tedit'] + $sdvig * 3600;
                        $dizm = date("d.m /H:i", $diz);
                        echo '<br /><span class="gray"><small>Изм. <b>' . $res['edit'] . '</b> (' . $dizm . ') <b>[' . $res['kedit'] . ']</b></small></span>';
                    }
                    // Если есть прикрепленный файл, выводим его описание
                    $freq = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = '" . $res['id'] . "'");
                    if (mysql_num_rows($freq) > 0)
                    {
                        $fres = mysql_fetch_array($freq);
                        $fls = filesize('./files/' . $fres['filename']);
                        $fls = round($fls / 1024, 2);
                        echo "<br /><font color='#999999'>Прикреплённый файл:<br /><a href='index.php?act=file&amp;id=" . $fres['id'] . "'>$fres[filename]</a> ($fls кб.)<br/>";
                        echo 'Скачано: ' . $fres['dlcount'] . ' раз.</font>';
                    }
                    $lp = mysql_query("select `from`, `id` from `forum` where type='m' and refid='" . $id . "'  order by time desc LIMIT 1;");
                    $arr1 = mysql_fetch_array($lp);
                    $tpp = $realtime - 300;
                    if ($dostfmod || (($arr1['from'] == $login) && ($arr1['id'] == $res['id']) && ($res['time'] > $tpp)))
                    {
                        //TODO: Разобраться с алгоритмом, по возможности исключить лишний запрос к базе
                        echo '<br /><a href="index.php?act=editpost&amp;id=' . $res['id'] . '&amp;start=' . $start . '">Изменить</a>';
                    }
                    if ($dostfmod)
                    {
                        echo '<br />';
                        if ($res['close'])
                        {
                            //TODO: Написать восстановление удаленного поста
                            //TODO: Написать чистку темы от удаленных постов
                            echo '<a href="">Восстановить</a><br />';
                        } else
                        {
                            echo '<input type="checkbox" name="delch[]" value="' . $res['id'] . '"/>&nbsp;';
                            echo '<a href="?act=delpost&amp;id=' . $res['id'] . '&amp;start=' . $start . '">Удалить</a><br/>';
                        }
                        echo '<span class="gray">' . $res['ip'] . ' - ' . $res['soft'] . '</span><br/>';
                    }
                    echo '</div>';
                    ++$i;
                }
                echo '<div class="phdr">Всего сообщений: ' . $colmes . '</div>';
                if ($dostfmod == 1)
                {
                    echo '<input type="submit" value="Удалить отмеченные"/>';
                    echo '</form>';
                }
                echo '<div id="down"><a href="#up">Вверх</a></div>'; //TODO: Разобраться с якорями
                if ($dostadm || ($type1['edit'] != 1 && $user_id && $upfp != 1 && !$ban['1'] && !$ban['11']))
                {
                    if ($datauser['farea'] == 1 && $datauser['postforum'] >= 1)
                    {
                        echo '<div>Написать<br/><form action="index.php?act=say&amp;id=' . $id . '" method="post"><textarea cols="20" rows="2" name="msg"></textarea><br/>';
                        echo '<input type="checkbox" name="addfiles" value="1" /> Добавить файл<br/>';
                        if ($offtr != 1)
                        {
                            echo '<input type="checkbox" name="msgtrans" value="1" /> Транслит сообщения<br/>';
                        }
                        echo '<input type="submit" title="Нажмите для отправки" name="submit" value="Отправить"/></form></div>';
                    } else
                    {
                        echo '<a href="?act=say&amp;id=' . $id . '&amp;start=' . $start . '">Написать</a><br />';
                    }
                }
                if ($colmes > $kmess)
                {
                    echo '<p>' . pagenav('index.php?id=' . $id . '&amp;', $start, $colmes, $kmess) . '</p>';
                    echo '<p><form action="index.php" method="get"><input type="hidden" name="id" value="' . $id . '"/><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
                }
                if ($dostfmod == 1)
                {
                    echo '<p><div class="func">';
                    echo "<a href='index.php?act=ren&amp;id=" . $id . "'>Переименовать тему</a><br/>";
                    if ($type1['edit'] == 1)
                        echo "<a href='index.php?act=close&amp;id=" . $id . "'>Открыть тему</a><br/>";
                    else
                        echo "<a href='index.php?act=close&amp;id=" . $id . "&amp;closed'>Закрыть тему</a><br/>";
                    echo "<a href='index.php?act=deltema&amp;id=" . $id . "'>Удалить тему</a><br/>";
                    if ($type1['vip'] == 1)
                        echo "<a href='index.php?act=vip&amp;id=" . $id . "'>Открепить тему</a>";
                    else
                        echo "<a href='index.php?act=vip&amp;id=" . $id . "&amp;vip'>Закрепить тему</a>";
                    echo "<br/><a href='index.php?act=per&amp;id=" . $id . "'>Переместить тему</a></div></p>";
                }
                if ($user_id)
                    echo "<a href='index.php?act=who&amp;id=" . $id . "'>Кто здесь?(" . wfrm($id) . ")</a><br/>";
                if ($filter)
                    echo '<div><a href="index.php?act=filter&amp;id=' . $id . '&amp;do=unset">Отменить фильтрацию</a></div>';
                else
                    echo '<div><a href="index.php?act=filter&amp;id=' . $id . '&amp;start=' . $start . '">Фильтровать сообщения</a></div>';
                echo '<a href="index.php?act=tema&amp;id=' . $id . '">Скачать тему</a>';
                break;

            default:
                echo '<p><b>Ошибка!</b><br />Тема удалена или не существует!</p>';
                break;
        }
    } else
    {
        ////////////////////////////////////////////////////////////
        // Список Разделов форума                                 //
        ////////////////////////////////////////////////////////////

        // Ссылка на Новые темы
        echo '<p><a href="index.php?act=new">' . ($user_id ? 'Непрочитанное&nbsp;(' . forum_new() . ')' : 'Последние 10 тем') . '</a></p>';
        // Панель навигации
        echo '<div class="phdr">';
        echo '<b>Форум</b></div>';
        $req = mysql_query("SELECT `id`, `text` FROM `forum` WHERE `type`='f' ORDER BY `realid`");
        while ($res = mysql_fetch_array($req))
        {
            echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='r' and `refid`='" . $res['id'] . "'"), 0);
            echo '<a href="index.php?id=' . $res['id'] . '">' . $res['text'] . '</a> [' . $count . ']</div>';
            ++$i;
        }
        echo '<div class="phdr">' . ($user_id ? '<a href="index.php?act=who">Кто в форуме</a>' : 'Кто в форуме') . '&nbsp;(' . wfrm() . ')</div>';
    }

    ////////////////////////////////////////////////////////////
    // Счетчик файлов и ссылка на них                         //
    ////////////////////////////////////////////////////////////
    $sql = $dostsadm ? "" : " AND `del` != '1'";
    if ($id && $tip == 'f')
    {
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `cat` = '$id'" . $sql), 0);
        if ($count > 0)
            echo '<p><a href="index.php?act=files&amp;c=' . $id . '">Файлы раздела</a>&nbsp;(' . $count . ')</p>';
        else
            echo '<p>Прикрепленных файлов нет</p>';
    } elseif ($id && $tip == 'r')
    {
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `subcat` = '$id'" . $sql), 0);
        if ($count > 0)
            echo '<p><a href="index.php?act=files&amp;s=' . $id . '">Файлы подраздела</a>&nbsp;(' . $count . ')</p>';
        else
            echo '<p>Прикрепленных файлов нет</p>';
    } elseif ($id && $tip == 't')
    {
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `topic` = '$id'" . $sql), 0);
        if ($count > 0)
            echo '<p><a href="index.php?act=files&amp;t=' . $id . '">Файлы топика</a>&nbsp;(' . $count . ')</p>';
        else
            echo '<p>Прикрепленных файлов нет</p>';
    } else
    {
        $sql = $dostsadm ? '' : " WHERE `del` != '1'";
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files`" . $sql), 0);
        if ($count > 0)
            echo '<p><a href="index.php?act=files">Файлы форума</a>&nbsp;(' . $count . ')</p>';
        else
            echo '<p>Прикрепленных файлов нет</p>';
    }
    // Навигация внизу страницы
    echo '<p>' . ($id ? '<a href="index.php">В Форум</a><br />' : '') . '<a href="search.php">Поиск по форуму</a>';
    if (!$id)
    {
        echo '<br /><a href="index.php?act=read">Правила форума</a><br/>';
        echo '<a href="index.php?act=moders&amp;id=' . $id . '">Модераторы</a><br />';
        echo '<a href="index.php?act=faq">FAQ</a>';
    }
    echo '</p>';
    if (!$user_id)
    {
        if ((empty($_SESSION['uppost'])) || ($_SESSION['uppost'] == 0))
        {
            echo "<a href='index.php?id=" . $id . "&amp;page=" . $page . "&amp;newup'>Новые вверху</a><br/>";
        } else
        {
            echo "<a href='index.php?id=" . $id . "&amp;page=" . $page . "&amp;newdown'>Новые внизу</a><br/>";
        }
    }
}
require_once ("../incfiles/end.php");

?>