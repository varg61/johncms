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
Редактировать mp3 тегов
-----------------------------------------------------------------
*/
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . VARS::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
if (mysql_num_rows($req_down) == 0 || !is_file($res_down['dir'] . '/' . $res_down['name']) || functions::format($res_down['name']) != 'mp3' || Vars::$USER_RIGHTS < 6) {
    echo Functions::displayError('<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
    exit;
}
echo '<div class="phdr"><b>' . lng('edit_mp3tags') . ':</b> ' . Validate::filterString($res_down['rus_name']) . '</div>';
require (SYSPATH . 'lib/getid3/getid3.php');
$getID3 = new getID3;
$getID3->encoding = 'cp1251';
$getid = $getID3->analyze($res_down['dir'] . '/' . $res_down['name']);
 if(!empty($getid['tags']['id3v2'])) $tagsArray = $getid['tags']['id3v2'];
elseif(!empty($getid['tags']['id3v1'])) $tagsArray = $getid['tags']['id3v1'];

if (isset($_POST['submit'])) {
	$tagsArray['artist'][0] = isset($_POST['artist']) ? Download::mp3tagsOut($_POST['artist'], 1) : '';
	$tagsArray['title'][0] = isset($_POST['title']) ? Download::mp3tagsOut($_POST['title'],1) : '';
	$tagsArray['album'][0] = isset($_POST['album']) ? Download::mp3tagsOut($_POST['album'], 1) : '';
	$tagsArray['genre'][0] = isset($_POST['genre']) ? Download::mp3tagsOut($_POST['genre'], 1) : '';
	$tagsArray['year'][0] = isset($_POST['year']) ?  (int) $_POST['year'] : 0;
	require(SYSPATH . 'lib/getid3/write.php');
	$tagsWriter = new getid3_writetags;
	$tagsWriter->filename = $res_down['dir'] . '/' . $res_down['name'];
	$tagsWriter->tagformats = array('id3v1', 'id3v2.3');
	$tagsWriter->tag_encoding = 'cp1251';
	$tagsWriter->tag_data = $tagsArray;
	$tagsWriter->WriteTags();
    echo '<div class="gmenu">' . lng('mp3tags_saved') . '</div>';


 }
echo '<div class="list1"><form action="' . Vars::$URI . '?act=mp3tags&amp;id=' . VARS::$ID . '" method="post">' .
'<b>' . lng('mp3_artist') . '</b>:<br /> <input name="artist" type="text" value="' . Download::mp3tagsOut($tagsArray['artist'][0]) . '" /><br />' .
'<b>' . lng('mp3_title') . '</b>:<br /> <input name="title" type="text" value="' . Download::mp3tagsOut($tagsArray['title'][0]) . '" /><br />' .
'<b>' . lng('mp3_album') . '</b>:<br /> <input name="album" type="text" value="' . Download::mp3tagsOut($tagsArray['album'][0]) . '" /><br />' .
'<b>' . lng('mp3_genre') . '</b>: <br /><input name="genre" type="text" value="' . Download::mp3tagsOut($tagsArray['genre'][0]) . '" /><br />' .
'<b>' . lng('mp3_year') . '</b>:<br /> <input name="year" type="text" value="' . (int) $tagsArray['year'][0] . '" /><br />' .
'<input type="submit" name="submit" value="' . lng('sent') . '"/></form></div>' .
'<div class="phdr"><a href="' . Vars::$URI . '?act=view&amp;id=' . Vars::$ID . '">' . lng('back') . '</a></div>';