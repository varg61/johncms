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
$url = Router::getUri(2);

/*
-----------------------------------------------------------------
Выводим файл
-----------------------------------------------------------------
*/
$req_down = DB::PDO()->query("SELECT * FROM `cms_download_files` WHERE `id` = '" . Vars::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = $req_down->fetch();
if (!$req_down->rowCount() || !is_file($res_down['dir'] . '/' . $res_down['name'])) {
    echo Functions::displayError(__('not_found_file'), '<a href="' . $url . '">' . __('download_title') . '</a>');
    exit;
}
$title_pages = Validate::checkout(mb_substr($res_down['rus_name'], 0, 30));
$textl = mb_strlen($res_down['rus_name']) > 30 ? $title_pages . '...' : $title_pages;
if ($res_down['type'] == 3) {
    echo '<div class="rmenu">' . __('file_mod') . '</div>';
    if (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4) {
        exit;
    }
}


echo '<div class="phdr"><b>' . Validate::checkout($res_down['rus_name']) . '</b></div>';
$format_file = Functions::format($res_down['name']);

/*
-----------------------------------------------------------------
Управление закладками
-----------------------------------------------------------------
*/
if (Vars::$USER_ID) {
    $bookmark = DB::PDO()->query("SELECT COUNT(*) FROM `cms_download_bookmark` WHERE `file_id` = " . Vars::$ID . "  AND `user_id` = " . Vars::$USER_ID)->fetchColumn();
    if (isset($_GET['addBookmark']) && !$bookmark) {
        DB::PDO()->exec("INSERT INTO `cms_download_bookmark` SET `file_id`='" . Vars::$ID . "', `user_id`=" . Vars::$USER_ID);
        $bookmark = 1;
    } elseif (isset($_GET['delBookmark']) && $bookmark) {
        DB::PDO()->exec("DELETE FROM `cms_download_bookmark` WHERE `file_id`='" . Vars::$ID . "' AND `user_id`=" . Vars::$USER_ID);
        $bookmark = 0;
    }
    echo '<div class="topmenu">';
    if (!$bookmark) {
        echo '<a href="' . $url . '?act=view&amp;id=' . Vars::$ID . '&amp;addBookmark">' . __('add_favorite') . '</a>';
    } else {
        echo '<a href="' . $url . '?act=view&amp;id=' . Vars::$ID . '&amp;delBookmark">' . __('delete_favorite') . '</a>';
    }
    echo '</div>';
}
/*
-----------------------------------------------------------------
Получаем список скриншотов
-----------------------------------------------------------------
*/

$text_info = '';
$screen = array();
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
    echo'<div class="menu"><b>' . __('view') . '</b><br />
	<div id="mediaplayer">JW Player goes here</div>
    <script type="text/javascript" src="' . Vars::$HOME_URL . 'files/download/system/players/mediaplayer-5.7-viral/jwplayer.js"></script>
    <script type="text/javascript">
        jwplayer("mediaplayer").setup({
            flashplayer: "' . Vars::$HOME_URL . 'files/download/system/players/mediaplayer-5.7-viral/player.swf",
            file: "' . Vars::$HOME_URL . $res_down['dir'] . '/' . $res_down['name'] . '",
            image: "' . Vars::$HOME_URL . 'assets/misc/thumbinal.php?type=3&amp;img=' . rawurlencode($screen[0]) . '"
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
    //echo '<div class="gmenu"><img src="' . Vars::$HOME_URL . 'assets/misc/thumbinal.php?type=2&amp;img=' . rawurlencode($res_down['dir'] . '/' . $res_down['name']) . '" alt="preview" /></div>';
	$screen[] = $res_down['dir'] . '/' . $res_down['name'];
	$text_info = '<b>' . __('resolution') . ': </b>' . $info_file[0] . 'x' . $info_file[1] . ' px<br />';
}
else if (($format_file == '3gp' || $format_file == 'avi' || $format_file == 'mp4') && !$screen && $set_down['video_screen'])
    $screen[] = Download::screenAuto($res_down['dir'] . '/' . $res_down['name'], $res_down['id'], $format_file);
elseif (($format_file == 'thm' || $format_file == 'nth') && !$screen && $set_down['theme_screen'])
    $screen[] = Download::screenAuto($res_down['dir'] . '/' . $res_down['name'], $res_down['id'], $format_file);
elseif ($format_file == 'mp3') {
    if (!Vars::$IS_MOBILE) {
        $text_info = '<object type="application/x-shockwave-flash" data="' . Vars::$HOME_URL . 'files/download/system/players/player.swf" width="240" height="20" id="dewplayer" name="dewplayer">' .
            '<param name="wmode" value="transparent" /><param name="movie" value="' . Vars::$HOME_URL . 'files/download/system/download/players/player.swf" />' .
            '<param name="flashVars" value="mp3=' . Vars::$HOME_URL . str_replace('../', '', $res_down['dir']) . '/' . $res_down['name'] . '" /> </object><br />';
    }
    require (SYSPATH . 'lib/getid3/getid3.php');
    $getID3 = new getID3;
    $getID3->encoding = 'cp1251';
    $getid = $getID3->analyze($res_down['dir'] . '/' . $res_down['name']);
    $mp3info = TRUE;
    if (!empty($getid['tags']['id3v2'])) $tagsArray = $getid['tags']['id3v2'];
    elseif (!empty($getid['tags']['id3v1'])) $tagsArray = $getid['tags']['id3v1'];
    else $mp3info = FALSE;
    $text_info .= '<b>' . __('mp3_channels') . '</b>: ' . $getid['audio']['channels'] . ' (' . $getid['audio']['channelmode'] . ')<br/>' .
        '<b>' . __('mp3_sample_rate') . '</b>: ' . ceil($getid['audio']['sample_rate'] / 1000) . ' KHz<br/>' .
        '<b>' . __('mp3_bitrate') . '</b>: ' . ceil($getid['audio']['bitrate'] / 1000) . ' Kbit/s<br/>' .
        '<b>' . __('mp3_playtime_seconds') . '</b>: ' . date('i:s', $getid['playtime_seconds']) . '<br />';
    if ($mp3info) {
        if (isset($tagsArray['artist'][0])) $text_info .= '<b>' . __('mp3_artist') . '</b>: ' . Download::mp3tagsOut($tagsArray['artist'][0]) . '<br />';
        if (isset($tagsArray['title'][0])) $text_info .= '<b>' . __('mp3_title') . '</b>: ' . Download::mp3tagsOut($tagsArray['title'][0]) . '<br />';
        if (isset($tagsArray['album'][0])) $text_info .= '<b>' . __('mp3_album') . '</b>: ' . Download::mp3tagsOut($tagsArray['album'][0]) . '<br />';
        if (isset($tagsArray['genre'][0])) $text_info .= '<b>' . __('mp3_genre') . '</b>: ' . Download::mp3tagsOut($tagsArray['genre'][0]) . '<br />';
        if (intval($tagsArray['year'][0])) $text_info .= '<b>' . __('mp3_year') . '</b>: ' . (int)$tagsArray['year'][0] . '<br />';
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
        echo '<div class="topmenu"> ' . Functions::displayPagination($url . '?act=view&amp;id=' . Vars::$ID . '&amp;', Vars::$PAGE - 1, $total, 1) . '</div>' .
            '<div class="gmenu"><b>' . __('screen_file') . ' (' . Vars::$PAGE . '/' . $total . '):</b><br />' .
            '<img src="' . Vars::$HOME_URL . 'assets/misc/thumbinal.php?type=2&amp;img=' . rawurlencode($screen[Vars::$PAGE - 1]) . '" alt="screen" /></div>';
    } else {
        echo '<div class="gmenu"><b>' . __('screen_file') . ':</b><br />' .
            '<img src="' . Vars::$HOME_URL . 'assets/misc/thumbinal.php?type=2&amp;img=' . rawurlencode($screen[0]) . '" alt="screen" /></div>';
    }
}
/*
-----------------------------------------------------------------
Выводим данные
-----------------------------------------------------------------
*/
Vars::$USER = $res_down['user_id'];
Vars::$USER_SET['avatar'] = 0;
//TODO: Переделать на класс Users
$user = Vars::getUser();
echo '<div class="list1"><b>' . __('name_for_server') . ':</b> ' . $res_down['name'] . '<br />' .
    '<b>' . __('user_upload') . ':</b> ' . Functions::displayUser($user, array('iphide' => 1)) . '<br />' . $text_info .
    '<b>' . __('number_of_races') . ':</b> ' . $res_down['field'] . '<br />';
if ($res_down['about'])
    echo '<b>' . __('dir_desc') . ':</b> ' . Validate::checkout($res_down['about'], 1, 1);
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
    DB::PDO()->exec("UPDATE `cms_download_files` SET `rate`='" . $file_rate[0] . '|' . $file_rate[1] . "' WHERE `id`=" . Vars::$ID);
    echo '<b><span class="green">' . __('your_vote') . '</span></b><br />';
    $_SESSION['rate_file_' . Vars::$ID] = TRUE;
}
$sum = ($file_rate[1] + $file_rate[0]) ? round(100 / ($file_rate[1] + $file_rate[0]) * $file_rate[0]) : 50;
echo '<b>' . __('rating') . ' </b>';
if (!isset($_SESSION['rate_file_' . Vars::$ID]) && Vars::$USER_ID)
    echo '(<a href="' . $url . '?act=view&amp;id=' . Vars::$ID . '&amp;plus">+</a>/<a href="' . $url . '?act=view&amp;id=' . Vars::$ID . '&amp;minus">-</a>)';
else echo '(+/-)';
echo ': <b><span class="green">' . $file_rate[0] . '</span>/<span class="red">' . $file_rate[1] . '</span></b><br />' .
    '<img src="' . Vars::$HOME_URL . 'assets/misc/rating.php?img=' . $sum . '" alt="' . __('rating') . '" />';
/*
-----------------------------------------------------------------
Скачка изображения в особом размере
-----------------------------------------------------------------
*/
if ($format_file == 'jpg' || $format_file == 'jpeg' || $format_file == 'gif' || $format_file == 'png') {
    $array = array('101x80', '128x128', '128x160', '176x176', '176x208', '176x220', '208x208', '208x320', '240x266', '240x320', '240x432', '352x416', '480x800');
    echo'<div class="sub"></div>' .
        '<form action="' . $url . '" method="get">' .
        '<input name="id" type="hidden" value="' . Vars::$ID . '" />' .
        '<input name="act" type="hidden" value="custom_size" />' .
        __('custom_size') . ': ' . '<select name="img_size">';
    $img = 0;
    foreach ($array as $v) {
        echo '<option value="' . $img . '">' . $v . '</option>';
        ++$img;
    }
    echo'</select><br />' .
        __('quality') . ': <select name="val">' .
        '<option value="100">100</option>' .
        '<option value="90">90</option>' .
        '<option value="80">80</option>' .
        '<option value="70">70</option>' .
        '<option value="60">60</option>' .
        '<option value="50">50</option>' .
        '</select><br />' .
        '<input name="proportion" type="checkbox" value="1" />&nbsp;' . __('proportion') . '<br />' .
        '<input type="submit" value="' . __('download') . '" /></form>';
}
if (Vars::$SYSTEM_SET['mod_down_comm'] || Vars::$USER_RIGHTS >= 7)
    echo '<div class="sub"></div><a href="' . $url . '?act=comments&amp;id=' . $res_down['id'] . '">' . __('comments') . '</a> (' . $res_down['total'] . ')';
echo '</div>';
/*
-----------------------------------------------------------------
Запрашиваем дополнительные файлы
-----------------------------------------------------------------
*/
$req_file_more = DB::PDO()->query("SELECT * FROM `cms_download_more` WHERE `refid` = " . Vars::$ID . " ORDER BY `time` ASC");
$total_files_more = $req_file_more->rowCount();
/*
-----------------------------------------------------------------
Скачка файла
-----------------------------------------------------------------
*/
echo '<div class="phdr"><b>' . ($total_files_more ? __('download_files') : __('download_file')) . '</b></div>' .
    '<div class="list1">' . Download::downloadLlink(array('format' => $format_file,
                                                          'res'    => $res_down)) . '</div>';
/*
-----------------------------------------------------------------
Дополнительные файлы
-----------------------------------------------------------------
*/
$i = 0;
if ($total_files_more) {
    while ($res_file_more = $req_file_more->fetch()) {
        $res_file_more['dir'] = $res_down['dir'];
        $res_file_more['text'] = $res_file_more['rus_name'];
        echo (($i++ % 2) ? '<div class="list1">' : '<div class="list2">') .
            Download::downloadLlink(array('format' => Functions::format($res_file_more['name']),
                                          'res'    => $res_file_more,
                                          'more'   => $res_file_more['id'])) . '</div>';
    }
}

/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
echo '<div class="phdr">' . Download::navigation(array('dir' => $res_down['dir'], 'refid' => 1, 'count' => 0)) . '</div>';

/*
-----------------------------------------------------------------
Управление файлами
-----------------------------------------------------------------
*/
if (Vars::$USER_RIGHTS > 6 || Vars::$USER_RIGHTS == 4) {
    echo '<p><div class="func">' .
        '<a href="' . $url . '?act=edit_file&amp;id=' . Vars::$ID . '">' . __('edit_file') . '</a><br />' .
        '<a href="' . $url . '?act=edit_about&amp;id=' . Vars::$ID . '">' . __('edit_about') . '</a><br />' .
        '<a href="' . $url . '?act=edit_screen&amp;id=' . Vars::$ID . '">' . __('edit_screen') . '</a><br />' .
        '<a href="' . $url . '?act=files_more&amp;id=' . Vars::$ID . '">' . __('files_more') . '</a><br />' .
        '<a href="' . $url . '?act=delete_file&amp;id=' . Vars::$ID . '">' . __('delete_file') . '</a>';
    if (Vars::$USER_RIGHTS > 6) {
        echo '<br /><a href="' . $url . '?act=transfer_file&amp;id=' . Vars::$ID . '">' . __('transfer_file') . '</a>';
        if ($format_file == 'mp3')
            echo '<br /><a href="' . $url . '?act=mp3tags&amp;id=' . Vars::$ID . '">' . __('edit_mp3tags') . '</a>';
    }
    echo '</div></p>';
}