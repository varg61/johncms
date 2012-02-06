<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

$types = array(
    1 => $lng_forum['files_type_win'],
    2 => $lng_forum['files_type_java'],
    3 => $lng_forum['files_type_sis'],
    4 => $lng_forum['files_type_txt'],
    5 => $lng_forum['files_type_pic'],
    6 => $lng_forum['files_type_arc'],
    7 => $lng_forum['files_type_video'],
    8 => $lng_forum['files_type_audio'],
    9 => $lng_forum['files_type_other']
);
$new = time() - 86400; // Сколько времени файлы считать новыми?

/*
-----------------------------------------------------------------
Получаем ID раздела и подготавливаем запрос
-----------------------------------------------------------------
*/
$c = isset($_GET['c']) ? abs(intval($_GET['c'])) : false; // ID раздела
$s = isset($_GET['s']) ? abs(intval($_GET['s'])) : false; // ID подраздела
$t = isset($_GET['t']) ? abs(intval($_GET['t'])) : false; // ID топика
$do = isset($_GET['do']) && intval($_GET['do']) > 0 && intval($_GET['do']) < 10 ? intval($_GET['do']) : 0;
if ($c) {
    $id = $c;
    $lnk = '&amp;c=' . $c;
    $sql = " AND `cat` = '" . $c . "'";
    $caption = '<b>' . $lng_forum['files_category'] . '</b>: ';
    $input = '<input type="hidden" name="c" value="' . $c . '"/>';
} elseif ($s) {
    $id = $s;
    $lnk = '&amp;s=' . $s;
    $sql = " AND `subcat` = '" . $s . "'";
    $caption = '<b>' . $lng_forum['files_section'] . '</b>: ';
    $input = '<input type="hidden" name="s" value="' . $s . '"/>';
} elseif ($t) {
    $id = $t;
    $lnk = '&amp;t=' . $t;
    $sql = " AND `topic` = '" . $t . "'";
    $caption = '<b>' . $lng_forum['files_topic'] . '</b>: ';
    $input = '<input type="hidden" name="t" value="' . $t . '"/>';
} else {
    $id = false;
    $sql = '';
    $lnk = '';
    $caption = '<b>' . $lng_forum['files_forum'] . '</b>';
    $input = '';
}
if ($c || $s || $t) {
    // Получаем имя нужной категории форума
    $req = mysql_query("SELECT `text` FROM `forum` WHERE `id` = '$id'");
    if (mysql_num_rows($req) > 0) {
        $res = mysql_fetch_array($req);
        $caption .= $res['text'];
    } else {
        echo Functions::displayError(Vars::$LNG['error_wrong_data'], '<a href="index.php">' . Vars::$LNG['to_forum'] . '</a>');
        exit;
    }
}
if ($do || isset($_GET['new'])) {
    /*
    -----------------------------------------------------------------
    Выводим список файлов нужного раздела
    -----------------------------------------------------------------
    */
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE " . (isset($_GET['new'])
                                              ? " `time` > '$new'" : " `filetype` = '$do'") . $sql), 0);
    if ($total > 0) {
        // Заголовок раздела
        echo '<div class="phdr">' . $caption . (isset($_GET['new']) ? '<br />' . Vars::$LNG['new_files'] : '') . '</div>' . ($do ? '<div class="bmenu">' . $types[$do] . '</div>' : '');
        $req = mysql_query("SELECT `cms_forum_files`.*, `forum`.`user_id`, `forum`.`text`, `topicname`.`text` AS `topicname`
            FROM `cms_forum_files`
            LEFT JOIN `forum` ON `cms_forum_files`.`post` = `forum`.`id`
            LEFT JOIN `forum` AS `topicname` ON `cms_forum_files`.`topic` = `topicname`.`id`
            WHERE " . (isset($_GET['new']) ? " `cms_forum_files`.`time` > '$new'" : " `filetype` = '$do'") . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `del` != '1'") . $sql .
            "ORDER BY `time` DESC LIMIT " . Vars::db_pagination()
        );
        $i = 0;
        while ($res = mysql_fetch_assoc($req)) {
            $req_u = mysql_query("SELECT `id`, `name`, `sex`, `rights`, `lastdate`, `status`, `datereg`, `ip`, `browser` FROM `users` WHERE `id` = '" . $res['user_id'] . "'");
            $res_u = mysql_fetch_assoc($req_u);
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            // Выводим текст поста
            $text = mb_substr($res['text'], 0, 500);
            $text = Validate::filterString($text, 1, 1);
            $text = preg_replace('#\[c\](.*?)\[/c\]#si', '', $text);
            $page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['topic'] . "' AND `id` " . ($set_forum['upfp']
                                                          ? ">=" : "<=") . " '" . $res['post'] . "'"), 0) / Vars::$USER_SET['page_size']);
            $text = '<b><a href="index.php?id=' . $res['topic'] . '&amp;page=' . $page . '">' . $res['topicname'] . '</a></b><br />' . $text;
            if (mb_strlen($res['text']) > 500)
                $text .= '<br /><a href="index.php?act=post&amp;id=' . $res['post'] . '">' . $lng_forum['read_all'] . ' &gt;&gt;</a>';
            // Формируем ссылку на файл
            $fls = @filesize('../files/forum/attach/' . $res['filename']);
            $fls = round($fls / 1024, 0);
            $att_ext = strtolower(Functions::format('./files/forum/attach/' . $res['filename']));
            $pic_ext = array(
                'gif',
                'jpg',
                'jpeg',
                'png'
            );
            if (in_array($att_ext, $pic_ext)) {
                // Если картинка, то выводим предпросмотр
                $file = '<div><a href="index.php?act=file&amp;id=' . $res['id'] . '">';
                $file .= '<img src="thumbinal.php?file=' . (urlencode($res['filename'])) . '" alt="' . $lng_forum['click_to_view'] . '" /></a></div>';
            } else {
                // Если обычный файл, выводим значок и ссылку
                $file = Functions::getImage(($res['del'] ? 'delete.png' : 'filetype_' . $res['filetype'] . '.png'), '', 'align="middle"') . '&#160;';
            }
            $file .= '<a href="index.php?act=file&amp;id=' . $res['id'] . '">' . htmlspecialchars($res['filename']) . '</a><br />';
            $file .= '<small><span class="gray">' . $lng_forum['size'] . ': ' . $fls . ' kb.<br />' . $lng_forum['downloaded'] . ': ' . $res['dlcount'] . ' ' . $lng_forum['time'] . '</span></small>';
            $arg = array(
                'iphide' => 1,
                'sub' => $file,
                'body' => $text
            );
            echo Functions::displayUser($res_u, $arg);
            echo '</div>';
            ++$i;
        }
        echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            // Постраничная навигация
            echo '<p>' . Functions::displayPagination('index.php?act=files&amp;' . (isset($_GET['new']) ? 'new' : 'do=' . $do) . $lnk . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>' .
                 '<p><form action="index.php" method="get">' .
                 '<input type="hidden" name="act" value="files"/>' .
                 '<input type="hidden" name="do" value="' . $do . '"/>' . $input . '<input type="text" name="page" size="2"/>' .
                 '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/></form></p>';
        }
    } else {
        echo '<div class="list1">' . Vars::$LNG['list_empty'] . '</div>';
    }
} else {
    /*
    -----------------------------------------------------------------
    Выводим список разделов, в которых есть файлы
    -----------------------------------------------------------------
    */
    $countnew = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `time` > '$new'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `del` != '1'") . $sql), 0);
    echo '<p>' . ($countnew > 0 ? '<a href="index.php?act=files&amp;new' . $lnk . '">' . Vars::$LNG['new_files'] . ' (' . $countnew . ')</a>' : $lng_forum['new_files_empty']) . '</p>';
    echo '<div class="phdr">' . $caption . '</div>';
    $link = array();
    $total = 0;
    for ($i = 1; $i < 10; $i++) {
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `filetype` = '$i'" . (Vars::$USER_RIGHTS >= 7
                                                  ? '' : " AND `del` != '1'") . $sql), 0);
        if ($count > 0) {
            $link[] = Functions::getImage('filetype_' . $i . '.png') . '&#160;<a href="index.php?act=files&amp;do=' . $i . $lnk . '">' . $types[$i] . '</a>&#160;(' . $count . ')';
            $total = $total + $count;
        }
    }
    foreach ($link as $var) {
        echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') . $var . '</div>';
        ++$i;
    }
    echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
}
echo '<p>' . (($do || isset($_GET['new']))
        ? '<a href="index.php?act=files' . $lnk . '">' . $lng_forum['section_list'] . '</a><br />'
        : '') . '<a href="index.php' . ($id ? '?id=' . $id : '') . '">' . Vars::$LNG['forum'] . '</a></p>';