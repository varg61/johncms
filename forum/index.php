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
    $hdr = checkout($hdr);
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
	`name`='$login'");
}

$do = array('new', 'who', 'addfile', 'file', 'users', 'moders', 'addvote', 'editvote', 'delvote', 'vote', 'per', 'ren', 'deltema', 'vip', 'close', 'delpost', 'editpost', 'nt', 'tema', 'loadtem', 'say', 'post', 'read', 'faq', 'trans',
    'massdel', 'files', 'filter', 'restore');
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
            $_SESSION['uppost'] = 1;
        if (isset($_GET['newdown']))
            $_SESSION['uppost'] = 0;
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
                $req = mysql_query("SELECT `id`, `text`, `soft` FROM `forum` WHERE `type`='r' AND `refid`='$id' ORDER BY `realid`");
                $total = mysql_num_rows($req);
                while ($mass1 = mysql_fetch_array($req))
                {
                    echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                    $coltem = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `refid` = '" . $mass1['id'] . "'"), 0);
                    echo '<a href="?id=' . $mass1['id'] . '">' . $mass1['text'] . '</a>';
                    if ($coltem)
                        echo " [$coltem]";
                    if (!empty($mass1['soft']))
                        echo '<br /><span class="gray"><small>' . $mass1['soft'] . '</small></span><br />';
                    echo '</div>';
                    ++$i;
                }
                echo '<div class="phdr">Всего: ' . $total . '</div>';
                unset($_SESSION['fsort_id']);
                unset($_SESSION['fsort_users']);
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
                $q1 = mysql_query("SELECT * FROM `forum` WHERE `type`='t'" . ($dostadm == 1 ? '' : " AND `close`!='1'") . " AND `refid`='$id' ORDER BY `vip` DESC, `time` DESC LIMIT $start, $kmess");
                while ($mass = mysql_fetch_array($q1))
                {
                    echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                    $nikuser = mysql_query("SELECT `from` FROM `forum` WHERE `type` = 'm' AND `close` != '1' AND `refid` = '" . $mass['id'] . "' ORDER BY `time` DESC LIMIT 1");
                    $nam = mysql_fetch_array($nikuser);
                    $colmes = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='m' AND `refid`='" . $mass['id'] . "'" . ($dostadm == 1 ? '' : " AND `close` != '1'"));
                    $colmes1 = mysql_result($colmes, 0);
                    $cpg = ceil($colmes1 / $kmess);
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
                    if ($mass['realid'] == 1)
                        echo '&nbsp;<img src="../images/rate.gif" alt=""/>';
                    echo '&nbsp;<a href="index.php?id=' . $mass['id'] . '">' . $mass['text'] . '</a> [' . $colmes1 . ']';
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
                unset($_SESSION['fsort_id']);
                unset($_SESSION['fsort_users']);
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
                    // Фиксация факта прочтения топика
                    $req = mysql_query("SELECT * FROM `cms_forum_rdm` WHERE `topic_id` = '$id' AND `user_id` = '$user_id' LIMIT 1");
                    if (mysql_num_rows($req) > 0)
                    {
                        $res = mysql_fetch_array($req);
                        if ($type1['time'] > $res['time'])
                            mysql_query("UPDATE `cms_forum_rdm` SET `time` = '$realtime' WHERE `topic_id`='$id' AND `user_id` = '$user_id'");
                    } else
                    {
                        // Ставим метку о прочтении
                        mysql_query("INSERT INTO `cms_forum_rdm` SET  `topic_id` = '$id', `user_id` = '$user_id', `time` = '$realtime'");
                    }
                }
                // Ссылка на Новые темы
                echo '<a name="up" id="up"></a><p><a href="index.php?act=new">' . ($user_id ? 'Непрочитанное&nbsp;(' . forum_new() . ')' : 'Последние 10 тем') . '</a></p>';
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
                    $order = $datauser['upfp'] == 1 ? 'DESC' : 'ASC';
                else
                    $order = ((empty($_SESSION['uppost'])) || ($_SESSION['uppost'] == 0)) ? 'ASC' : 'DESC';
                // Панель навигации
                $razd = mysql_fetch_array(mysql_query("SELECT `id`, `refid`, `text` FROM `forum` WHERE `id` = '" . $type1['refid'] . "' LIMIT 1"));
                $frm = mysql_fetch_array(mysql_query("SELECT `id`, `text` FROM `forum` WHERE `id` = '" . $razd['refid'] . "' LIMIT 1"));
                echo '<div class="phdr"><a href="index.php">Форум</a> &gt;&gt; <a href="index.php?id=' . $frm['id'] . '">' . $frm['text'] . '</a> &gt;&gt; <a href="index.php?id=' . $razd['id'] . '">' . $razd['text'] . '</a></div>';
                // Выводим название топика
                echo '<div class="phdr"><a href="#down"><img src="../theme/' . $skin . '/images/down.png" alt="Вниз" width="20" height="10" border="0"/></a>&nbsp;&nbsp;<b>' . $type1['text'] . '</b></div>';
                if ($type1['close'])
                    echo '<div class="rmenu"><b>Тема удалена</b></div>';
                if ($type1['edit'])
                    echo '<div class="rmenu">Тема закрыта</div>';
                ////////////////////////////////////////////////////////////
                // Блок голосований (by FlySelf)                          //
                ////////////////////////////////////////////////////////////
                if ($type1['realid'])
                {
                    if (isset($_GET['clip']))
                        $clip_forum = '&amp;clip';
                    $vote_user = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_vote_us` WHERE `user`='$user_id' AND `topic`='$id'"), 0);
                    $topic_vote = mysql_fetch_array(mysql_query("SELECT `name`, `time`, `count` FROM `forum_vote` WHERE `type`='1' AND `topic`='$id' LIMIT 1"));
                    echo '<div  class="list2"><b>' . checkout($topic_vote['name']) . '</b><br />';
                    $vote_result = mysql_query("SELECT `id`, `name`, `count` FROM `forum_vote` WHERE `type`='2' AND `topic`='" . $id . "'");
                    if (!isset($_GET['vote_result']) && $user_id && $vote_user == 0)
                    {
                        // Выводим форму с опросами
                        echo '<form action="index.php?act=vote&amp;id=' . $id . '" method="post">';
                        while ($vote = mysql_fetch_array($vote_result))
                        {
                            echo '<input type="radio" value="' . $vote['id'] . '" name="vote"/> ' . checkout($vote['name']) . '<br />';
                        }
                        echo '<p><input type="submit" name="submit" value="Голосовать"/><br /><a href="index.php?id=' . $id . '&amp;start=' . $start . '&amp;vote_result' . $clip_forum . '">Результаты</a></p></form></div>';
                    } else
                    {
                        // Выводим результаты голосования
                        echo '<small>';
                        while ($vote = mysql_fetch_array($vote_result))
                        {
                            $count_vote = round(100 / $topic_vote['count'] * $vote['count']);
                            echo checkout($vote['name']) . ' [' . $vote['count'] . ']<br />';
                            echo '<img src="vote_img.php?img=' . $count_vote . '" alt="Рейтинг: ' . $count_vote . '%" /><br />';
                        }
                        echo '</small></div><div class="bmenu">Всего голосов: ';
                        if ($datauser['rights'] > 6)
                            echo '<a href="index.php?act=users&amp;id=' . $id . '">' . $topic_vote['count'] . '</a>';
                        else
                            echo $topic_vote['count'];
                        echo '</div>';
                        if ($user_id && $vote_user == 0)
                            echo '<div class="bmenu"><a href="index.php?id=' . $id . '&amp;start=' . $start . $clip_forum . '">Голосовать</a></div>';
                    }
                }
                ////////////////////////////////////////////////////////////
                // Фиксация первого поста в теме                          //
                ////////////////////////////////////////////////////////////
                if (($datauser['postclip'] == 2 && ($upfp ? $start < (ceil($colmes - $kmess)) : $start > 0)) || isset($_GET['clip']))
                {
                    $postreq = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`lastdate`, `users`.`status`, `users`.`datereg`
                    FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
                    WHERE `forum`.`type` = 'm' AND `forum`.`refid` = '$id'" . ($dostadm == 1 ? "" : " AND `forum`.`close` != '1'") . " ORDER BY `forum`.`id` LIMIT 1");
                    $postres = mysql_fetch_array($postreq);
                    echo '<div class="clip">';
                    if ($postres['sex'])
                        echo '<img src="../theme/' . $skin . '/images/' . ($postres['sex'] == 'm' ? 'm' : 'f') . ($postres['datereg'] > $realtime - 86400 ? '_new.gif" width="14"' : '.gif" width="10"') . ' height="10"/>&nbsp;';
                    else
                        echo '<img src="../images/del.png" width="10" height="10" />&nbsp;';
                    if ($user_id && $user_id != $postres['user_id'])
                    {
                        echo '<a href="../str/anketa.php?user=' . $postres['user_id'] . '&amp;fid=' . $postres['id'] . '"><b>' . $postres['from'] . '</b></a> ';
                        echo '<a href="index.php?act=say&amp;id=' . $postres['id'] . '&amp;start=' . $start . '"> [о]</a> <a href="index.php?act=say&amp;id=' . $postres['id'] . '&amp;start=' . $start . '&amp;cyt"> [ц]</a> ';
                    } else
                    {
                        echo '<b>' . $postres['from'] . '</b> ';
                    }
                    $user_rights = array(1 => 'Kil', 3 => 'Mod', 6 => 'Smd', 7 => 'Adm', 8 => 'SV');
                    echo $user_rights[$postres['rights']];
                    echo ($realtime > $postres['lastdate'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>');
                    echo ' <span class="gray">(' . date("d.m.Y / H:i", $postres['time'] + $sdvig * 3600) . ')</span><br/>';
                    if ($postres['close'])
                    {
                        echo '<span class="red">Пост удалён!</span><br/>';
                    }
                    echo checkout(mb_substr($postres['text'], 0, 500), 0, 2);
                    if (mb_strlen($postres['text']) > 500)
                        echo '...<a href="index.php?act=post&amp;id=' . $postres['id'] . '">читать все</a>';
                    echo '</div>';
                }
                if ($filter)
                    echo '<div class="rmenu">В теме включена фильтрация по авторам постов</div>';
                // Запрос в базу
                $req = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`lastdate`, `users`.`status`, `users`.`datereg`
				FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
				WHERE `forum`.`type` = 'm' AND `forum`.`refid` = '$id'" . ($dostadm == 1 ? "" : " AND `forum`.`close` != '1'") . "$sql ORDER BY `forum`.`time` $order LIMIT $start, $kmess");
                // Верхнее поле "Написать"
                //TODO:Дописать возможность Админу писать в закрытой теме и переделать НАПИСАТЬ на кнопку
                if ($user_id && $type1['edit'] != 1 && $upfp)
                {
                    if ($datauser['farea'] == 1 && $datauser['postforum'] >= 1)
                    {
                        echo '<div class="gmenu">Написать<br/><form action="index.php?act=say&amp;id=' . $id . '" method="post"><textarea cols="20" rows="2" name="msg"></textarea><br/>';
                        echo '<input type="checkbox" name="addfiles" value="1" /> Добавить файл<br/>';
                        if ($offtr != 1)
                            echo '<input type="checkbox" name="msgtrans" value="1" /> Транслит сообщения<br/>';
                        echo '<input type="submit" title="Нажмите для отправки" name="submit" value="Отправить"/></form></div>';
                    } else
                    {
                        echo '<div class="gmenu"><a href="?act=say&amp;id=' . $id . '">Написать</a></div>';
                    }
                }
                if ($dostfmod)
                    echo '<form action="index.php?act=massdel" method="post">';
                while ($res = mysql_fetch_array($req))
                {
                    echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                    if ($res['sex'])
                        echo '<img src="../theme/' . $skin . '/images/' . ($res['sex'] == 'm' ? 'm' : 'f') . ($res['datereg'] > $realtime - 86400 ? '_new.gif" width="20"' : '.gif" width="16"') . ' height="16"/>&nbsp;';
                    else
                        echo '<img src="../images/del.png" width="12" height="12" />&nbsp;';
                    // Ник юзера и ссылка на его анкету
                    if ($user_id && $user_id != $res['user_id'])
                    {
                        echo '<a href="../str/anketa.php?user=' . $res['user_id'] . '&amp;fid=' . $res['id'] . '"><b>' . $res['from'] . '</b></a> ';
                        echo '<a href="index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . $start . '"> [о]</a> <a href="index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . $start . '&amp;cyt"> [ц]</a> ';
                    } else
                    {
                        echo '<b>' . $res['from'] . '</b> ';
                    }
                    // Метка должности
                    $user_rights = array(1 => 'Kil', 3 => 'Mod', 6 => 'Smd', 7 => 'Adm', 8 => 'SV');
                    echo $user_rights[$res['rights']];
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
                        $text = mb_substr($text, 0, 500);
                        $text = checkout($text, 1, 0);
                        $text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
                        echo $text . '...<br /><a href="index.php?act=post&amp;id=' . $res['id'] . '">Читать все &gt;&gt;</a>';
                    } else
                    {
                        // Или, обрабатываем тэги и выводим весь текст
                        $text = checkout($text, 1, 1);
                        if ($offsm != 1)
                            $text = smileys($text, $res['rights'] ? 1 : 0);
                        echo $text;
                    }
                    if ($res['kedit'] > 0)
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
                        $fls = round(filesize('./files/' . $fres['filename']) / 1024, 2);
                        echo '<br /><span class="gray">Прикреплённый файл:<br /><a href="index.php?act=file&amp;id=' . $fres['id'] . '">' . $fres['filename'] . '</a> (' . $fls . ' кб.)<br/>';
                        echo 'Скачано: ' . $fres['dlcount'] . ' раз.</span>';
                    }
                    if ($dostfmod || (($arr1['from'] == $login) && ($arr1['id'] == $res['id']) && ($res['time'] > $tpp)))
                    {
                        echo '<br /><a href="index.php?act=editpost&amp;id=' . $res['id'] . '&amp;start=' . $start . '">Изменить</a>';
                    }
                    if ($dostfmod)
                    {
                        echo '<br />';
                        if ($res['close'])
                        {
                            //TODO: Написать чистку темы от удаленных постов
                            echo '<a href="index.php?act=restore&amp;id=' . $res['id'] . '">Восстановить</a><br />';
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
                if ($dostfmod)
                {
                    echo '<div class="rmenu"><input type="submit" value=" Удалить "/></div>';
                    echo '</form>';
                }
                if (!$dostfmod && $tip == 't')
                {
                    // Изменение последнего поста для обычного юзера
                    $lp = mysql_query("SELECT * FROM `forum` WHERE `type` = 'm' AND `refid` = '$id' ORDER BY `time` DESC LIMIT 1");
                    $arr1 = mysql_fetch_array($lp);
                    if ($arr1['user_id'] == $user_id && $arr1['time'] > ($realtime - 300))
                        echo (is_integer($i / 2) ? '<div class="list2">' : '<div class="list1">') . '<a href="index.php?act=editpost&amp;id=' . $arr1['id'] . '&amp;start=' . $start . '">Изменить</a></div>';
                }
                // Нижнее поле "Написать"
                if ($user_id && $type1['edit'] != 1 && !$upfp)
                {
                    if ($datauser['farea'] == 1 && $datauser['postforum'] >= 1)
                    {
                        echo '<div class="gmenu">Написать<br/><form action="index.php?act=say&amp;id=' . $id . '" method="post"><textarea cols="20" rows="2" name="msg"></textarea><br/>';
                        echo '<input type="checkbox" name="addfiles" value="1" /> Добавить файл<br/>';
                        if ($offtr != 1)
                            echo '<input type="checkbox" name="msgtrans" value="1" /> Транслит сообщения<br/>';
                        echo '<input type="submit" name="submit" value="Отправить"/></form></div>';
                    } else
                    {
                        echo '<div class="gmenu"><form action="index.php?act=say&amp;id=' . $id . '&amp;start=' . $start . '" method="post"><input type="submit" name="go" value="Написать"/></form></div>';
                    }
                }
                echo '<div class="phdr"><a name="down" id="down"></a><a href="#up"><img src="../theme/' . $skin . '/images/up.png" alt="Наверх" width="20" height="10" border="0"/></a>&nbsp;&nbsp;Всего сообщений: ' . $colmes . '</div>';
                if ($colmes > $kmess)
                {
                    echo '<p>' . pagenav('index.php?id=' . $id . '&amp;', $start, $colmes, $kmess) . '</p>';
                    echo '<p><form action="index.php" method="get"><input type="hidden" name="id" value="' . $id . '"/><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
                } else
                {
                    echo '<br />';
                }
                if ($dostfmod == 1)
                {
                    echo '<p><div class="func">';
                    echo $topic_vote > 0 ? '<a href="index.php?act=editvote&amp;id=' . $id . '">Изменить опрос</a><br/><a href="index.php?act=delvote&amp;id=' . $id . '">Удалить опрос</a><br/>' : '<a href="index.php?act=addvote&amp;id=' . $id .
                        '">Добавить опрос</a><br/>';
                    echo "<a href='index.php?act=ren&amp;id=" . $id . "'>Переименовать тему</a><br/>";
                    // Закрыть - открыть тему
                    if ($type1['edit'] == 1)
                        echo "<a href='index.php?act=close&amp;id=" . $id . "'>Открыть тему</a><br/>";
                    else
                        echo "<a href='index.php?act=close&amp;id=" . $id . "&amp;closed'>Закрыть тему</a><br/>";
                    // Удалить - восстановить тему
                    if ($type1['close'] == 1)
                        echo "<a href='index.php?act=restore&amp;id=" . $id . "'>Восстановить тему</a><br/>";
                    else
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
        $req = mysql_query("SELECT `id`, `text`, `soft` FROM `forum` WHERE `type`='f' ORDER BY `realid`");
        while ($res = mysql_fetch_array($req))
        {
            echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='r' and `refid`='" . $res['id'] . "'"), 0);
            echo '<a href="index.php?id=' . $res['id'] . '">' . $res['text'] . '</a> [' . $count . ']';
            if (!empty($res['soft']))
                echo '<br /><span class="gray"><small>' . $res['soft'] . '</small></span><br />';
            echo '</div>';
            ++$i;
        }
        echo '<div class="phdr">' . ($user_id ? '<a href="index.php?act=who">Кто в форуме</a>' : 'Кто в форуме') . '&nbsp;(' . wfrm() . ')</div>';
        unset($_SESSION['fsort_id']);
        unset($_SESSION['fsort_users']);
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