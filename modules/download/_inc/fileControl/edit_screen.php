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
Управление скриншотами
-----------------------------------------------------------------
*/
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . VARS::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
if (mysql_num_rows($req_down) == 0 || !is_file($res_down['dir'] . '/' . $res_down['name']) || (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
    echo Functions::displayError('<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
    exit;
}
$screen = array ();
$do = isset($_GET['do']) ? trim($_GET['do']) : '';
if ($do && is_file($screens_path . '/' . Vars::$ID . '/' . $do)) {
	/*
	-----------------------------------------------------------------
	Удаление скриншота
	-----------------------------------------------------------------
	*/
    unlink($screens_path . '/' . Vars::$ID . '/' . $do);
    header('Location: ' . Vars::$URI . '?act=edit_screen&id=' . Vars::$ID);
    exit;
} else if (isset($_POST['submit'])) {
	/*
	-----------------------------------------------------------------
	Загрузка скриншота
	-----------------------------------------------------------------
	*/
    $handle = new upload($_FILES['screen']);
    if ($handle->uploaded) {
        $handle->file_new_name_body = Vars::$ID;
        $handle->allowed = array (
            'image/jpeg',
            'image/gif',
            'image/png'
        );
        $handle->file_max_size = 1024 * Vars::$SYSTEM_SET['flsz'];
        if ($set_down['screen_resize']) {
            $handle->image_resize = true;
            $handle->image_x = 240;
            $handle->image_ratio_y = true;
        }
        $handle->process($screens_path . '/' . Vars::$ID . '/');
        if ($handle->processed) {
            echo '<div class="gmenu"><b>' . lng('upload_screen_ok') . '</b>';
        } else
            echo '<div class="rmenu"><b>' . lng('upload_screen_no') . ': ' . $handle->error . '</b>';
    } else
        echo '<div class="rmenu"><b>' . lng('upload_screen_no') . '</b>';
	echo '<br /><a href="' . Vars::$URI . '?act=edit_screen&amp;id=' . Vars::$ID . '">' . lng('upload_file_more') . '</a>' .
    '<br /><a href="' . Vars::$URI . '?act=view&amp;id=' . Vars::$ID . '">' . lng('back') . '</a></div>';
} else {
	/*
	-----------------------------------------------------------------
	Форма выгрузки
	-----------------------------------------------------------------
	*/
	echo '<div class="phdr"><b>' . lng('screen_file') . '</b>: ' . Validate::checkout($res_down['rus_name']) . '</div>' .
 	'<div class="list1"><form action="' . Vars::$URI . '?act=edit_screen&amp;id=' . Vars::$ID . '"  method="post" enctype="multipart/form-data"><input type="file" name="screen"/><br />' .
 	'<input type="submit" name="submit" value="' . lng('upload') . '"/></form></div>' .
 	'<div class="phdr"><small>' . lng('file_size_faq') . ' ' . Vars::$SYSTEM_SET['flsz'] . 'kb' .
 	($set_down['screen_resize'] ? '<br />' . lng('add_screen_faq')  : '') . '</small></div>';
	/*
	-----------------------------------------------------------------
	Выводим скриншоты
	-----------------------------------------------------------------
	*/
	$screen = array ();
    if (is_dir($screens_path . '/' . Vars::$ID)) {
        $dir = opendir($screens_path . '/' . Vars::$ID);
        while ($file = readdir($dir)) {
            if (($file != '.') && ($file != "..") && ($file != "name.dat") && ($file != ".svn") && ($file != "index.php")) {
                $screen[] = $screens_path . '/' . Vars::$ID . '/' . $file;
            }
        }
        closedir($dir);
    } else {
        if (mkdir($screens_path . '/' . Vars::$ID, 0777) == true)
            @chmod($screens_path . '/' . Vars::$ID, 0777);
    }
    if ($screen) {
        $total = count($screen);
        for ($i = 0; $i < $total; $i++) {
            $screen_name = htmlentities($screen[$i], ENT_QUOTES, 'utf-8');
            $file = preg_replace('#^' . $screens_path . '/' . Vars::$ID . '/(.*?)$#isU', '$1', $screen_name, 1);
            echo (($i % 2) ? '<div class="list2">' : '<div class="list1">') .
            '<table  width="100%"><tr><td width="40" valign="top">' .
            '<a href="' . $screen_name . '"><img src="' . Vars::$HOME_URL . '/assets/misc/thumbinal.php?type=1&amp;img=' . rawurlencode($screen_name) . '" alt="screen_' . $i . '" /></a></td><td>' . $file .
            '<div class="sub"><a href="' . Vars::$URI . '?act=edit_screen&amp;id=' . Vars::$ID . '&amp;do=' . $file . '">' . lng('delete') . '</a></div></td></tr></table></div>';
        }
    }
    echo '<div class="phdr"><a href="' . Vars::$URI . '?act=view&amp;id=' . Vars::$ID . '">' . lng('back') . '</a></div>';
}