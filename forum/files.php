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

defined('_IN_JOHNCMS') or die('Error: restricted access');
require_once ('../incfiles/head.php');

$types = array(1 => 'Приложения WIN', 2 => 'Приложения Java', 3 => 'Приложения SIS', 4 => 'Текстовые файлы', 5 => 'Картинки', 6 => 'Архивы', 7 => 'Видео', 8 => 'MP3', 9 => 'Другое');
$new = $realtime - 86400; // Сколько времени файлы считать новыми?

// Получаем ID раздела и подготавливаем запрос
$c = abs(intval($_GET['c'])); // ID раздела
$s = abs(intval($_GET['s'])); // ID подраздела
$t = abs(intval($_GET['t'])); // ID топика
if ($c)
{
    $id = $c;
    $lnk = '&amp;c=' . $c;
    $sql = " AND `cat` = '" . $c . "'";
    $caption = '<b>Файлы раздела</b>: ';
    $input = '<input type="hidden" name="c" value="' . $c . '"/>';
} elseif ($s)
{
    $id = $s;
    $lnk = '&amp;s=' . $s;
    $sql = " AND `subcat` = '" . $s . "'";
    $caption = '<b>Файлы подраздела</b>: ';
    $input = '<input type="hidden" name="s" value="' . $s . '"/>';
} elseif ($t)
{
    $id = $t;
    $lnk = '&amp;t=' . $t;
    $sql = " AND `topic` = '" . $t . "'";
    $caption = '<b>Файлы темы</b>: ';
    $input = '<input type="hidden" name="t" value="' . $t . '"/>';
} else
{
    $id = false;
    $sql = '';
    $lnk = '';
    $caption = '<b>Файлы всего форума</b>';
    $input = '';
}
if ($c || $s || $t)
{
    // Получаем имя нужной категории форума
    $req = mysql_query("SELECT `text` FROM `forum` WHERE `id` = '$id' LIMIT 1");
    if (mysql_num_rows($req) > 0)
    {
        $res = mysql_fetch_array($req);
        $caption .= $res['text'];
    } else
    {
        echo '<div class="rmenu"><p><b>ОШИБКА!</b><br />Категории не существует<br /><a href="index.php">Вернуться в форум</a></p></div>';
        require_once ('../incfiles/end.php');
        exit;
    }
}

$do = isset($_GET['do']) ? abs(intval($_GET['do'])) : 0;
if ($do > 0 && $do < 10 || isset($_GET['new']))
{
    ////////////////////////////////////////////////////////////
    // Выводим список файлов нужного раздела                  //
    ////////////////////////////////////////////////////////////
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE " . (isset($_GET['new']) ? " `time` > '$new'" : " `filetype` = '$do'") . $sql), 0);
    if ($total > 0)
    {
        echo '<div class="phdr"><b>'.(isset($_GET['new']) ? 'Новые файлы' : 'Список файлов').'</b></div>';
        $req = mysql_query("SELECT `cms_forum_files`.*, `forum`.`from`, `forum`.`text`, `topicname`.`text` AS `topicname`
		FROM `cms_forum_files`
		LEFT JOIN `forum` ON `cms_forum_files`.`post` = `forum`.`id`
		LEFT JOIN `forum` AS `topicname` ON `cms_forum_files`.`topic` = `topicname`.`id`
		WHERE " . (isset($_GET['new']) ? " `cms_forum_files`.`time` > '$new'" : " `filetype` = '$do'") . $sql . " ORDER BY `time` DESC LIMIT " . $start . "," . $kmess);
        while ($res = mysql_fetch_array($req))
        {
            echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
            echo '<img src="images/' . $res['filetype'] . '.png" width="16" height="16" class="left" />&nbsp;<a href="index.php?act=file&amp;id=' . $res['id'] . '">' . htmlspecialchars($res['filename']) . '</a>&nbsp;[' . $res['dlcount'] . ']';
            // Название темы
            echo '<div class="sub">';
            // Выводим данные юзера, кто и когда написал пост
            $uz = mysql_query("SELECT `id`, 'from', `sex`, `rights`, `lastdate`, `dayb`, `status`, `datereg` FROM `users` WHERE `name`='" . $res['from'] . "' LIMIT 1");
            $mass1 = mysql_fetch_array($uz);
            // Значок пола
            echo '<img src="../theme/' . $skin . '/images/' . ($mass1['sex'] == 'm' ? 'm' : 'f') . '.gif" alt=""  width="10" height="10"/>';
            if ($user_id && $user_id != $mass1['id'])
            {
                echo '<a href="../str/anketa.php?user=' . $mass1['id'] . '&amp;fid=' . $res['id'] . '"><b>' . $res['from'] . '</b></a> ';
            } else
            {
                echo '<b>' . $res['from'] . '</b>';
            }
            $vrp = $res['time'] + $sdvig * 3600;
            $vr = date("d.m.Y / H:i", $vrp);
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
            $ontime = $mass1['lastdate'];
            $ontime2 = $ontime + 300;
            if ($realtime > $ontime2)
            {
                echo '<font color="#FF0000"> [Off]</font>';
            } else
            {
                echo '<font color="#00AA00"> [ON]</font>';
            }
            echo ' <font color="#999999">(' . $vr . ')</font><br/>';
            // Выводим текст поста
            $text = mb_substr($res['text'], 0, 200);
            $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
            $text = preg_replace('#\[c\](.*?)\[/c\]#si', '', $text);
            echo '<b><a href="index.php?id=' . $res['topic'] . '">' . $res['topicname'] . '</a></b><br />' . $text . '</div></div>';
            ++$i;
        }
        echo '<div class="phdr">Всего: ' . $total . '</div>';
        if ($total > $kmess)
        {
            // Постраничная навигация
            echo '<p>' . pagenav('index.php?act=files&amp;do=' . $do . $lnk . '&amp;', $start, $total, $kmess) . '</p>';
            echo '<p><form action="index.php" method="get"><input type="hidden" name="act" value="files"/><input type="hidden" name="do" value="' . $do . '"/>' . $input .
                '<input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
        }
    } else
    {
        echo '<div class="list1">Прикрепленных файлов нет</div>';
    }
} else
{
    ////////////////////////////////////////////////////////////
    // Выводим список разделов, в которых есть файлы          //
    ////////////////////////////////////////////////////////////
    $countnew = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `time` > '$new'" . $sql), 0);
	echo '<p>'.($countnew > 0 ? '<a href="index.php?act=files&amp;new' . $lnk . '">Новые файлы (' . $countnew . ')</a>' : 'Новых файлов нет').'</p>';
    echo '<div class="phdr">' . $caption . '</div>';
    $link = array();
    $total = 0;
    for ($i = 1; $i < 10; $i++)
    {
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `filetype` = '$i'" . ($dostadm ? '' : " AND `del` != '1'") . $sql), 0);
        if ($count > 0)
        {
            $link[] = '<img src="images/' . $i . '.png" width="16" height="16" class="left" />&nbsp;<a href="index.php?act=files&amp;do=' . $i . $lnk . '">' . $types[$i] . '</a>&nbsp;(' . $count . ')';
            $total = $total + $count;
        }
    }
    foreach ($link as $var)
    {
        echo (is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">') . $var . '</div>';
        ++$i;
    }
    echo '<div class="phdr">Всего файлов: ' . $total . '</div>';
}
echo '<p>' . (($do || isset($_GET['new'])) ? '<a href="index.php?act=files' . $lnk . '">К списку разделов</a><br />' : '') . '<a href="index.php' . ($id ? '?id=' . $id : '') . '">Форум</a></p>';

require_once ('../incfiles/end.php');

?>