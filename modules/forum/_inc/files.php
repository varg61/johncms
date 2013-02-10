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

$url = Router::getUri(2);

$types = array(
    1 => __('files_type_win'),
    2 => __('files_type_java'),
    3 => __('files_type_sis'),
    4 => __('files_type_txt'),
    5 => __('files_type_pic'),
    6 => __('files_type_arc'),
    7 => __('files_type_video'),
    8 => __('files_type_audio'),
    9 => __('files_type_other')
);
$new = time() - 86400; // Сколько времени файлы считать новыми?

/*
-----------------------------------------------------------------
Получаем ID раздела и подготавливаем запрос
-----------------------------------------------------------------
*/
$c = isset($_GET['c']) ? abs(intval($_GET['c'])) : FALSE; // ID раздела
$s = isset($_GET['s']) ? abs(intval($_GET['s'])) : FALSE; // ID подраздела
$t = isset($_GET['t']) ? abs(intval($_GET['t'])) : FALSE; // ID топика
$do = isset($_GET['do']) && intval($_GET['do']) > 0 && intval($_GET['do']) < 10 ? intval($_GET['do']) : 0;
if ($c) {
    $id = $c;
    $lnk = '&amp;c=' . $c;
    $sql = " AND `cat` = '" . $c . "'";
    $caption = '<b>' . __('files_category') . '</b>: ';
    $input = '<input type="hidden" name="c" value="' . $c . '"/>';
} elseif ($s) {
    $id = $s;
    $lnk = '&amp;s=' . $s;
    $sql = " AND `subcat` = '" . $s . "'";
    $caption = '<b>' . __('files_section') . '</b>: ';
    $input = '<input type="hidden" name="s" value="' . $s . '"/>';
} elseif ($t) {
    $id = $t;
    $lnk = '&amp;t=' . $t;
    $sql = " AND `topic` = '" . $t . "'";
    $caption = '<b>' . __('files_topic') . '</b>: ';
    $input = '<input type="hidden" name="t" value="' . $t . '"/>';
} else {
    $id = FALSE;
    $sql = '';
    $lnk = '';
    $caption = '<b>' . __('files_forum') . '</b>';
    $input = '';
}
if ($c || $s || $t) {
    // Получаем имя нужной категории форума
    $req = DB::PDO()->query("SELECT `text` FROM `forum` WHERE `id` = '$id'");
    if ($req->rowCount()) {
        $res = $req->fetch();
        $caption .= $res['text'];
    } else {
        echo Functions::displayError(__('error_wrong_data'), '<a href="' . $url . '">' . __('to_forum') . '</a>');
        exit;
    }
}
if ($do || isset($_GET['new'])) {
    /*
    -----------------------------------------------------------------
    Выводим список файлов нужного раздела
    -----------------------------------------------------------------
    */
    $total = DB::PDO()->query("SELECT COUNT(*) FROM `cms_forum_files` WHERE " . (isset($_GET['new'])
                                              ? " `time` > '$new'" : " `filetype` = '$do'") . $sql)->fetchColumn();
    if ($total > 0) {
        // Заголовок раздела
        echo '<div class="phdr">' . $caption . (isset($_GET['new']) ? '<br />' . __('new_files') : '') . '</div>' . ($do ? '<div class="bmenu">' . $types[$do] . '</div>' : '');
        $req = DB::PDO()->query("SELECT `cms_forum_files`.*, `forum`.`user_id`, `forum`.`text`, `topicname`.`text` AS `topicname`
            FROM `cms_forum_files`
            LEFT JOIN `forum` ON `cms_forum_files`.`post` = `forum`.`id`
            LEFT JOIN `forum` AS `topicname` ON `cms_forum_files`.`topic` = `topicname`.`id`
            WHERE " . (isset($_GET['new']) ? " `cms_forum_files`.`time` > '$new'" : " `filetype` = '$do'") . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `del` != '1'") . $sql .
            "ORDER BY `time` DESC " . Vars::db_pagination()
        );
        $i = 0;
        while ($res = $req->fetch()) {
            $req_u = DB::PDO()->query("SELECT `id`, `nickname`, `sex`, `rights`, `last_visit`, `status`, `join_date`, `ip`, `user_agent` FROM `users` WHERE `id` = '" . $res['user_id'] . "'");
            $res_u = $req_u->fetch();
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            // Выводим текст поста
            $text = mb_substr($res['text'], 0, 500);
            $text = Functions::checkout($text, 1, 1);
            $text = preg_replace('#\[c\](.*?)\[/c\]#si', '', $text);
            $page = ceil(DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['topic'] . "' AND `id` " . ($set_forum['upfp']
                                                          ? ">=" : "<=") . " '" . $res['post'] . "'")->fetchColumn() / Vars::$USER_SET['page_size']);
            $text = '<b><a href="' . $url . '?id=' . $res['topic'] . '&amp;page=' . $page . '">' . $res['topicname'] . '</a></b><br />' . $text;
            if (mb_strlen($res['text']) > 500)
                $text .= '<br /><a href="' . $url . '?act=post&amp;id=' . $res['post'] . '">' . __('read_all') . ' &gt;&gt;</a>';
            // Формируем ссылку на файл
            $fls = @filesize(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $res['filename']);
            $fls = round($fls / 1024, 0);
            $att_ext = strtolower(Functions::format(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $res['filename']));
            $pic_ext = array(
                'gif',
                'jpg',
                'jpeg',
                'png'
            );
            if (in_array($att_ext, $pic_ext)) {
                // Если картинка, то выводим предпросмотр
                $file = '<div><a href="' . $url . '?act=file&amp;id=' . $res['id'] . '">';
                $file .= '<img src="' . Vars::$HOME_URL . 'assets/misc/forum_thumbinal.php?file=' . (urlencode($res['filename'])) . '" alt="' . __('click_to_view') . '" /></a></div>';
            } else {
                // Если обычный файл, выводим значок и ссылку
                $file = Functions::getIcon(($res['del'] ? 'delete.png' : 'filetype-' . $res['filetype'] . '.png'), '', '', 'align="middle"') . '&#160;';
            }
            $file .= '<a href="' . $url . '?act=file&amp;id=' . $res['id'] . '">' . htmlspecialchars($res['filename']) . '</a><br />';
            $file .= '<small><span class="gray">' . __('size') . ': ' . $fls . ' kb.<br />' . __('downloaded') . ': ' . $res['dlcount'] . ' ' . __('time') . '</span></small>';
            $arg = array(
                'iphide' => 1,
                'sub' => $file,
                'body' => $text
            );
            echo Functions::displayUser($res_u, $arg);
            echo '</div>';
            ++$i;
        }
        echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            // Постраничная навигация
            echo '<p>' . Functions::displayPagination($url . '?act=files&amp;' . (isset($_GET['new']) ? 'new' : 'do=' . $do) . $lnk . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>' .
                 '<p><form action="' . $url . '" method="get">' .
                 '<input type="hidden" name="act" value="files"/>' .
                 '<input type="hidden" name="do" value="' . $do . '"/>' . $input . '<input type="text" name="page" size="2"/>' .
                 '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
        }
    } else {
        echo '<div class="list1">' . __('list_empty') . '</div>';
    }
} else {
    /*
    -----------------------------------------------------------------
    Выводим список разделов, в которых есть файлы
    -----------------------------------------------------------------
    */
    $countnew = DB::PDO()->query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `time` > '$new'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `del` != '1'") . $sql)->fetchColumn();
    echo '<p>' . ($countnew > 0 ? '<a href="' . $url . '?act=files&amp;new' . $lnk . '">' . __('new_files') . ' (' . $countnew . ')</a>' : __('new_files_empty')) . '</p>';
    echo '<div class="phdr">' . $caption . '</div>';
    $link = array();
    $total = 0;
    for ($i = 1; $i < 10; $i++) {
        $count = DB::PDO()->query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `filetype` = '$i'" . (Vars::$USER_RIGHTS >= 7
                                                  ? '' : " AND `del` != '1'") . $sql)->fetchColumn();
        if ($count > 0) {
            $link[] = Functions::getIcon('filetype-' . $i . '.png') . '&#160;<a href="' . $url . '?act=files&amp;do=' . $i . $lnk . '">' . $types[$i] . '</a>&#160;(' . $count . ')';
            $total = $total + $count;
        }
    }
    foreach ($link as $var) {
        echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') . $var . '</div>';
        ++$i;
    }
    echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
}
echo '<p>' . (($do || isset($_GET['new']))
        ? '<a href="' . $url . '?act=files' . $lnk . '">' . __('section_list') . '</a><br />'
        : '') . '<a href="' . $url . ($id ? '?id=' . $id : '') . '">' . __('forum') . '</a></p>';