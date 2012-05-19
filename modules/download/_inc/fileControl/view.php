<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
/*
-----------------------------------------------------------------
Выводим файл
-----------------------------------------------------------------
*/
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . Vars::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
if (mysql_num_rows($req_down) == 0 || !is_file($res_down['dir'] . '/' . $res_down['name'])) {
    echo Functions::displayError(lng('not_found_file'), '<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
    exit;
}
$title_pages = Validate::filterString(mb_substr($res_down['rus_name'], 0, 30));
$textl = mb_strlen($res_down['rus_name']) > 30 ? $title_pages . '...' : $title_pages;
if ($res_down['type'] == 3) {
    echo '<div class="rmenu">' . lng('file_mod') . '</div>';
    if (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4) {
        exit;
    }
}
$format_file = Functions::format($res_down['name']);
echo '<div class="phdr"><b>' . Validate::filterString($res_down['rus_name']) . '</b></div>';
/*
-----------------------------------------------------------------
Управление закладками
-----------------------------------------------------------------
*/
if(Vars::$USER_ID) {
	$bookmark = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_bookmark` WHERE `file_id` = " . Vars::$ID . "  AND `user_id` = " . Vars::$USER_ID), 0);
    if(isset($_GET['addBookmark']) && !$bookmark) {
    	mysql_query("INSERT INTO `cms_download_bookmark` SET `file_id`='" . Vars::$ID . "', `user_id`=" . Vars::$USER_ID);
        $bookmark = 1;
    } elseif(isset($_GET['delBookmark']) && $bookmark) {
        mysql_query("DELETE FROM `cms_download_bookmark` WHERE `file_id`='" . Vars::$ID . "' AND `user_id`=" . Vars::$USER_ID);
    	$bookmark = 0;
    }
	echo '<div class="topmenu">';
	if(!$bookmark) echo '<a href="' . Vars::$URI . '?act=view&amp;id=' . Vars::$ID . '&amp;addBookmark">Добавить в закладки</a>';
    else echo '<a href="' . Vars::$URI . '?act=view&amp;id=' . Vars::$ID . '&amp;delBookmark">Удалить из закладок</a>';
	echo '</div>';
}
/*
-----------------------------------------------------------------
Получаем список скриншотов
-----------------------------------------------------------------
*/

$text_info = '';
$screen = array ();
if (is_dir($screens_path . '/' . Vars::$ID)) {
    $dir = opendir($screens_path . '/' . Vars::$ID);
    while ($file = readdir($dir)) {
        if (($file != '.') && ($file != "..") && ($file != "name.dat") && ($file != ".svn") && ($file != "index.php")) {
            $screen[] = $screens_path . '/' . Vars::$ID . '/' . $file;
        }
    }
    closedir($dir);
}
/*
-----------------------------------------------------------------
Плэер видео файлов
-----------------------------------------------------------------
*/
if (($format_file == 'mp4' || $format_file == 'flv') && !Vars::$IS_MOBILE) {
	echo'<div class="menu"><b>Просмотр</b><br />
	<div id="mediaplayer">JW Player goes here</div>
    <script type="text/javascript" src="' . Vars::$HOME_URL . '/files/download/system/players/mediaplayer-5.7-viral/jwplayer.js"></script>
    <script type="text/javascript">
        jwplayer("mediaplayer").setup({
            flashplayer: "' . Vars::$HOME_URL . '/files/download/system/players/mediaplayer-5.7-viral/player.swf",
            file: "' . Vars::$HOME_URL . '/' . $res_down['dir'] . '/' . $res_down['name'].'",
            image: "' . Vars::$HOME_URL . '/files/download/system/thumbinal.php?type=3&amp;img=' . rawurlencode($screen[0]) . '"
        });
    </script></div>';
}
/*
-----------------------------------------------------------------
Получаем данные
-----------------------------------------------------------------
*/
if ($format_file == 'jpg' || $format_file == 'jpeg' || $format_file == 'gif' || $format_file == 'png') {
    $info_file = getimagesize($res_down['dir'] . '/' . $res_down['name']);
    echo '<div class="gmenu"><img src="' . Vars::$HOME_URL . '/files/download/system/thumbinal.php?type=2&amp;img=' . rawurlencode($res_down['dir'] . '/' . $res_down['name']) . '" alt="preview" /></div>';
    $text_info = '<b>' . lng('resolution') . ': </b>' . $info_file[0] . 'x' . $info_file[1] . ' px<br />';
}
else if (($format_file == '3gp' || $format_file == 'avi' || $format_file == 'mp4') && !$screen && $set_down['video_screen'])
    $screen[] = Download::screenAuto($res_down['dir'] . '/' . $res_down['name'], $res_down['id'], $format_file);
elseif (($format_file == 'thm' || $format_file == 'nth') && !$screen && $set_down['theme_screen'])
    $screen[] = Download::screenAuto($res_down['dir'] . '/' . $res_down['name'], $res_down['id'], $format_file);
elseif ($format_file == 'mp3') {
	if (!Vars::$IS_MOBILE) {
    	$text_info ='<object type="application/x-shockwave-flash" data="' . Vars::$HOME_URL . '/files/download/system/players/player.swf" width="240" height="20" id="dewplayer" name="dewplayer">' .
		'<param name="wmode" value="transparent" /><param name="movie" value="' . Vars::$HOME_URL . '/files/download/system/download/players/player.swf" />' .
		'<param name="flashVars" value="mp3=' . Vars::$HOME_URL . '/' . str_replace('../', '', $res_down['dir']) . '/' . $res_down['name'] . '" /> </object><br />';
	}
    require (SYSPATH . 'lib/getid3/getid3.php');
	$getID3 = new getID3;
	$getID3->encoding = 'cp1251';
	$getid = $getID3->analyze($res_down['dir'] . '/' . $res_down['name']);
    $mp3info = true;
	if(!empty($getid['tags']['id3v2'])) $tagsArray = $getid['tags']['id3v2'];
	elseif(!empty($getid['tags']['id3v1'])) $tagsArray = $getid['tags']['id3v1'];
	else $mp3info = false;
	$text_info .= '<b>' . lng('mp3_channels') . '</b>: ' . $getid['audio']['channels'] . ' (' . $getid['audio']['channelmode'] . ')<br/>' .
	'<b>' . lng('mp3_sample_rate') . '</b>: ' . ceil($getid['audio']['sample_rate']/1000) . ' KHz<br/>' .
	'<b>' . lng('mp3_bitrate') . '</b>: ' . ceil($getid['audio']['bitrate']/1000) . ' Kbit/s<br/>' .
	'<b>' . lng('mp3_playtime_seconds') . '</b>: ' . date('i:s', $getid['playtime_seconds']) . '<br />';
	if($mp3info){
		if(isset($tagsArray['artist'][0])) $text_info .= '<b>' . lng('mp3_artist') . '</b>: ' . Download::mp3tagsOut($tagsArray['artist'][0]) . '<br />';
		if(isset($tagsArray['title'][0])) $text_info .= '<b>' . lng('mp3_title') . '</b>: ' . Download::mp3tagsOut($tagsArray['title'][0]) . '<br />';
		if(isset($tagsArray['album'][0])) $text_info .= '<b>' . lng('mp3_album') . '</b>: ' . Download::mp3tagsOut($tagsArray['album'][0]) . '<br />';
		if(isset($tagsArray['genre'][0])) $text_info .= '<b>' . lng('mp3_genre') . '</b>: ' . Download::mp3tagsOut($tagsArray['genre'][0]) . '<br />';
 		if(intval($tagsArray['year'][0])) $text_info .= '<b>' . lng('mp3_year') . '</b>: ' . (int) $tagsArray['year'][0] . '<br />';
	}
}
/*
-----------------------------------------------------------------
Выводим скриншоты
-----------------------------------------------------------------
*/
if ($screen) {
    $total = count($screen);
    if ($total > 1) {
        if (Vars::$PAGE >= $total) Vars::$PAGE = $total;
        echo '<div class="topmenu"> ' . Functions::displayPagination(Vars::$URI. '?act=view&amp;id=' . Vars::$ID . '&amp;', Vars::$PAGE-1, $total, 1) . '</div>' .
		'<div class="gmenu"><b>' . lng('screen_file') . ' (' . Vars::$PAGE . '/' . $total . '):</b><br />' .
        '<img src="' . Vars::$HOME_URL . '/files/download/system/thumbinal.php?type=3&amp;img=' . rawurlencode($screen[Vars::$PAGE-1]) . '" alt="screen" /></div>';
    } else {
        echo '<div class="gmenu"><b>' . lng('screen_file') . ':</b><br />' .
        '<img src="' . Vars::$HOME_URL . '/files/download/system/thumbinal.php?type=3&amp;img=' . rawurlencode($screen[0]) . '" alt="screen" /></div>';
    }
}
/*
-----------------------------------------------------------------
Выводим данные
-----------------------------------------------------------------
*/
Vars::$USER =  $res_down['user_id'];
Vars::$USER_SET['avatar'] = 0;
$user = Vars::getUser();
echo '<div class="list1"><b>' . lng('name_for_server') . ':</b> ' . $res_down['name'] . '<br />' .
'<b>' . lng('number_of_races') . ':</b> ' . $res_down['field'] . '<br />' .
'<b>' . lng('user_upload') . ':</b> ' . Functions::displayUser($user, array('iphide' => 1))  . '<br />' . $text_info;
if ($res_down['about'])
    echo '<b>' . lng('dir_desc') . ':</b> ' . Validate::filterString($res_down['about'], 1, 1);
echo '<div class="sub"></div>';
/*
-----------------------------------------------------------------
Рейтинг файла
-----------------------------------------------------------------
*/
$file_rate = explode('|', $res_down['rate']);
if ((isset($_GET['plus']) || isset($_GET['minus'])) && !isset($_SESSION['rate_file_' . Vars::$ID]) && Vars::$USER_ID) {
    if (isset($_GET['plus'])) $file_rate[0] = $file_rate[0] + 1;
    else $file_rate[1] = $file_rate[1] + 1;
    mysql_query("UPDATE `cms_download_files` SET `rate`='" . $file_rate[0] . '|' . $file_rate[1] . "' WHERE `id`=" . Vars::$ID);
    echo '<b><span class="green">' . lng('your_vote') . '</span></b><br />';
    $_SESSION['rate_file_' . Vars::$ID] = true;
}
$sum = ($file_rate[1] + $file_rate[0]) ? round(100 / ($file_rate[1] + $file_rate[0]) * $file_rate[0]) : 50;
echo '<b>' . lng('rating') . ' </b>';
if(!isset($_SESSION['rate_file_' . Vars::$ID]) && Vars::$USER_ID)
	echo '(<a href="' . Vars::$URI . '?act=view&amp;id=' . Vars::$ID . '&amp;plus">+</a>/<a href="' . Vars::$URI . '?act=view&amp;id=' . Vars::$ID . '&amp;minus">-</a>)';
else echo '(+/-)';
echo ': <b><span class="green">' . $file_rate[0] . '</span>/<span class="red">' . $file_rate[1] . '</span></b><br />' .
'<img src="' . Vars::$HOME_URL . '/files/download/system/rating.php?img=' . $sum . '" alt="' . lng('rating') . '" />';
/*
-----------------------------------------------------------------
Скачка изображения в особом размере
-----------------------------------------------------------------
*/
if ($format_file == 'jpg' || $format_file == 'jpeg' || $format_file == 'gif' || $format_file == 'png') {
	$array = array('101x80', '128x128', '128x160', '176x176', '176x208', '176x220', '208x208', '208x320', '240x266', '240x320', '240x432', '352x416', '480x800');
    echo '<div class="sub"></div>' .
    '<form action="' . Vars::$URI . '" method="get">' .
    '<input name="id" type="hidden" value="' . Vars::$ID . '" />' .
    '<input name="act" type="hidden" value="custom_size" />' .
    lng('custom_size') . ': ' . '<select name="img_size">';
    $img = 0;
    foreach ($array as $v) {
        echo '<option value="' . $img . '">' . $v . '</option>';
        ++$img;
    }
    echo '</select><br />' .
    lng('quality') . ': <select name="val">' .
    '<option value="100">100</option>' .
    '<option value="90">90</option>' .
    '<option value="80">80</option>' .
    '<option value="70">70</option>' .
    '<option value="60">60</option>' .
    '<option value="50">50</option>' .
    '</select><br />' .
    '<input name="proportion" type="checkbox" value="1" />&nbsp;' . lng('proportion') . '<br />' .
    '<input type="submit" value="' . lng('download') . '" /></form>';
}
if(Vars::$SYSTEM_SET['mod_down_comm'] || Vars::$USER_RIGHTS >= 7)
	echo '<div class="sub"></div><a href="' . Vars::$URI . '?act=comments&amp;id=' . $res_down['id'] . '">Комментарии</a> (' . $res_down['total'] . ')';
echo '</div>';
/*
-----------------------------------------------------------------
Запрашиваем дополнительные файлы
-----------------------------------------------------------------
*/
$req_file_more = mysql_query("SELECT * FROM `cms_download_more` WHERE `refid` = " . Vars::$ID . " ORDER BY `time` ASC");
$total_files_more = mysql_num_rows($req_file_more);
/*
-----------------------------------------------------------------
Скачка файла
-----------------------------------------------------------------
*/
echo '<div class="phdr"><b>' . ($total_files_more ? lng('download_files') : lng('download_file'))  . '</b></div>' .
'<div class="list1">' . Download::downloadLlink(array('format' => $format_file, 'res' => $res_down)) . '</div>';
/*
-----------------------------------------------------------------
Дополнительные файлы
-----------------------------------------------------------------
*/
$i = 0;
if (mysql_num_rows($req_file_more)) {
    while ($res_file_more = mysql_fetch_assoc($req_file_more)) {
        $res_file_more['dir'] = $res_down['dir'];
        $res_file_more['text'] = $res_file_more['rus_name'];
        echo (($i++ % 2) ? '<div class="list1">' : '<div class="list2">').
        Download::downloadLlink(array('format' => Functions::format($res_file_more['name']), 'res' => $res_file_more, 'more' => $res_file_more['id'])) . '</div>';
	}
}
/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
$tree = array ();
$dirid = $res_down['refid'];
while ($dirid != '0' && $dirid != "") {
    $req = mysql_query("SELECT * FROM `cms_download_files` WHERE `type` = 1 AND `id` = '$dirid' LIMIT 1");
    $res = mysql_fetch_assoc($req);
    $tree[] = '<a href="' . Vars::$URI . '?id=' . $dirid . '">' . Validate::filterString($res['rus_name']) . '</a>';
    $dirid = $res['refid'];
}
krsort($tree);
$cdir = array_pop($tree);
echo '<div class="phdr"><a href="' . Vars::$URI . '">' . lng('download_title') . '</a> &raquo; ';
foreach ($tree as $value) {
    echo $value . ' &raquo; ';
}
echo '<a href="' . Vars::$URI . '?id=' . $res_down['refid'] . '">' . strip_tags($cdir) . '</a></div>';
/*
-----------------------------------------------------------------
Управление файлами
-----------------------------------------------------------------
*/
if (Vars::$USER_RIGHTS > 6 || Vars::$USER_RIGHTS == 4) {
    echo '<p><div class="func">' .
    '<a href="' . Vars::$URI . '?act=edit_file&amp;id=' . Vars::$ID . '">' . lng('edit_file') . '</a><br />' .
    '<a href="' . Vars::$URI . '?act=edit_about&amp;id=' . Vars::$ID . '">' . lng('edit_about') . '</a><br />' .
    '<a href="' . Vars::$URI . '?act=edit_screen&amp;id=' . Vars::$ID . '">' . lng('edit_screen') . '</a><br />' .
    '<a href="' . Vars::$URI . '?act=files_more&amp;id=' . Vars::$ID . '">' . lng('files_more') . '</a><br />' .
    '<a href="' . Vars::$URI . '?act=delete_file&amp;id=' . Vars::$ID . '">' . lng('delete_file') . '</a>';
    if(Vars::$USER_RIGHTS > 6) {
    	echo '<br /><a href="' . Vars::$URI . '?act=transfer_file&amp;id=' . Vars::$ID . '">' . lng('transfer_file') . '</a>';
    	if ($format_file == 'mp3')
    		echo '<br /><a href="' . Vars::$URI . '?act=mp3tags&amp;id=' . Vars::$ID . '">' . lng('edit_mp3tags') . '</a>';
    }
    echo '</div></p>';
}