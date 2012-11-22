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
Дополнительные файлы
-----------------------------------------------------------------
*/
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . Vars::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
if (mysql_num_rows($req_down) == 0 || !is_file($res_down['dir'] . '/' . $res_down['name']) || (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
    echo Functions::displayError('<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
    exit;
}
$del = isset($_GET['del']) ? abs(intval($_GET['del'])) : false;
$edit = isset($_GET['edit']) ? abs(intval($_GET['edit'])) : false;
if ($edit) {
	/*
	-----------------------------------------------------------------
	Изменяем файл
	-----------------------------------------------------------------
	*/
    $name_link = isset($_POST['name_link']) ? mysql_real_escape_string(Validate::checkout(mb_substr($_POST['name_link'], 0, 200))) : null;
    $req_file_more = mysql_query("SELECT `rus_name` FROM `cms_download_more` WHERE `id` = '$edit' LIMIT 1");
    if ($name_link && mysql_num_rows($req_file_more) && isset($_POST['submit'])) {
        mysql_query("UPDATE `cms_download_more` SET `rus_name`='$name_link' WHERE `id` = '$edit' LIMIT 1");
        header('Location: ' . Vars::$URI . '?act=files_more&id=' . Vars::$ID);
    } else {
        $res_file_more = mysql_fetch_assoc($req_file_more);
        echo '<div class="phdr"><b>' . Validate::checkout($res_down['rus_name']) . '</b></div>' .
        '<div class="gmenu"><b>' . lng('edit_file') . '</b></div>' .
        '<div class="list1"><form action="' . Vars::$URI . '?act=files_more&amp;id=' . Vars::$ID . '&amp;edit=' . $edit . '"  method="post">' .
         lng('link_file') . ' (мах. 200)<span class="red">*</span>:<br />' .
        '<input type="text" name="name_link" value="' . $res_file_more['rus_name'] . '"/><br />' .
        '<input type="submit" name="submit" value="' . lng('sent') . '"/></form>' .
        '</div><div class="phdr"><a href="' . Vars::$URI . '?act=files_more&amp;id=' . Vars::$ID . '">' . lng('back') . '</a></div>';
    }
} else if ($del) {
	/*
	-----------------------------------------------------------------
	Удаление файла
	-----------------------------------------------------------------
	*/
    $req_file_more = mysql_query("SELECT `name` FROM `cms_download_more` WHERE `id` = '$del' LIMIT 1");
    if (mysql_num_rows($req_file_more) && isset($_GET['yes'])) {
        $res_file_more = mysql_fetch_assoc($req_file_more);
        if (is_file($res_down['dir'] . '/' . $res_file_more['name']))
            unlink($res_down['dir'] . '/' . $res_file_more['name']);
        mysql_query("DELETE FROM `cms_download_more` WHERE `id` = '$del' LIMIT 1");
        header('Location: ' . Vars::$URI . '?act=files_more&id=' . Vars::$ID);
    } else {
        echo '<div class="rmenu">Вы действительно хотите удалить файл?<br /> <a href="' . Vars::$URI . '?act=files_more&amp;id=' . Vars::$ID . '&amp;del=' . $del . '&amp;yes">Удалить</a> | <a href="' . Vars::$URI . '?act=files_more&amp;id=' . Vars::$ID . '">Отмена</a></div>';
    }
} else if (isset($_POST['submit'])) {
	/*
	-----------------------------------------------------------------
	Выгружаем файл
	-----------------------------------------------------------------
	*/
    $error = array ();
    $link_file = isset($_POST['link_file']) ? str_replace('./', '_', trim($_POST['link_file'])) : null;
    $do_file = false;
    if ($link_file) {
        if (mb_substr($link_file, 0, 7) !== 'http://')
            $error[] = lng('error_link_import');
        else {
            $link_file = str_replace('http://', '', $link_file);
            if ($link_file) {
                $do_file = true;
                $fname = basename($link_file);
                $fsize = 0;
            } else {
                $error[] = lng('error_link_import');
            }
        }
        if ($error) {
            $error[] = '<a href="' . Vars::$URI . '?act=files_more&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a>';
      		echo functions::displayError($error);
            exit;
        }
	} elseif ($_FILES['fail']['size'] > 0) {
        $do_file = true;
        $fname = strtolower($_FILES['fail']['name']);
        $fsize = $_FILES['fail']['size'];
    }
    if ($do_file) {
        $new_file = isset($_POST['new_file']) ? trim($_POST['new_file']) : null;
        $name_link = isset($_POST['name_link']) ? mysql_real_escape_string(Validate::checkout(mb_substr($_POST['name_link'], 0, 200))) : null;
		$ext = explode(".", $fname);
        if (!empty($new_file)) {
            $fname = strtolower($new_file . '.' . $ext[1]);
            $ext = explode(".", $fname);
        }
        if (empty($name_link))
            $error[] = lng('error_empty_fields');
        if ($fsize > 1024 * Vars::$SYSTEM_SET['flsz'] && !$link_file)
        	$error[] = lng('error_file_size') . ' ' . Vars::$SYSTEM_SET['flsz'] . 'kb.';
        if (!in_array($ext[(count($ext)-1)], $defaultExt))
       		$error[] = lng('error_file_ext') .  ': ' . implode(', ', $defaultExt);
        if (strlen($fname) > 100)
            $error[] = lng('error_file_name_size ');
        if (preg_match("/[^\da-zA-Z_\-.]+/", $fname))
            $error[] = lng('error_file_symbols');
		if ($error) {
   			$error[] = '<a href="' . Vars::$URI . '?act=files_more&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a>';
      		echo functions::displayError($error);
		} else {
        	$newFile = 'file' . Vars::$ID . '_' . $fname;
            if(file_exists($res_down['dir'] . '/' . $newFile)) $fname = 'file' . Vars::$ID . '_' . time() . $fname;
            else $fname = $newFile;
            if ($link_file) {
                $up_file = copy('http://' . $link_file, "$res_down[dir]/$fname");
                $fsize = filesize("$res_down[dir]/$fname");
            } else {
                $up_file = move_uploaded_file($_FILES["fail"]["tmp_name"], "$res_down[dir]/$fname");
            }
            if ($up_file == true) {
                @chmod("$fname", 0777);
                @chmod("$res_down[dir]/$fname", 0777);
                echo '<div class="gmenu">' . lng('upload_file_ok') . '<br />' .
                '<a href="' . Vars::$URI. '?act=files_more&amp;id=' . Vars::$ID. '">' . lng('upload_file_more') . '</a> | <a href="' . Vars::$URI. '?id=' . Vars::$ID. '&amp;act=view">' . lng('back') . '</a></div>';
				$fname = mysql_real_escape_string($fname);
                mysql_query("INSERT INTO `cms_download_more` SET `refid`='" . Vars::$ID . "', `time`='" . time() . "',`name`='$fname', `rus_name` = '$name_link',`size`='" . intval($fsize) . "'");
            } else
                echo '<div class="rmenu">' . lng('upload_file_no') . '<br /><a href="' . Vars::$URI. '?act=files_more&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a></div>';
		}
    } else
        echo '<div class="rmenu">' . lng('upload_file_no') . '<br /><a href="' . Vars::$URI. '?act=files_more&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a></div>';
} else {
	/*
	-----------------------------------------------------------------
	Выводим форму
	-----------------------------------------------------------------
	*/
	echo '<div class="phdr"><b>' . lng('files_more') . ':</b> ' . Validate::checkout($res_down['rus_name']) . '</div>' .
    '<div class="menu"><form action="' . Vars::$URI . '?act=files_more&amp;id=' . Vars::$ID . '"  method="post" enctype="multipart/form-data">' .
    lng('select_file') . '<span class="red">*</span>::<br /><input type="file" name="fail"/><br />' .
    lng('or_link_to_it') . ':<br /><input type="post" name="link_file" value=""/><br />' .
    lng('save_name_file') . ':<br /><input type="text" name="new_file"/><br />' .
    lng('link_file') . ' (мах. 200)<span class="red">*</span>:<br />' .
    '<input type="text" name="name_link" value="' . lng('download_file_more') . '"/><br />' .
    '<input type="submit" name="submit" value="' . lng('upload') . '"/>' .
    '</form></div>' .
    '<div class="phdr"><small>' . lng('file_size_faq') . ' ' . Vars::$SYSTEM_SET['flsz'] . 'kb<br />' .
    lng('extensions') . ': ' . implode(', ', $defaultExt) . ($set_down['screen_resize'] ? '<br />' . lng('add_screen_faq')  : '') . '</small></div>';
	/*
	-----------------------------------------------------------------
	Дополнительные файлы
	-----------------------------------------------------------------
	*/
    $req_file_more = mysql_query("SELECT * FROM `cms_download_more` WHERE `refid` = " . Vars::$ID);
    $total_file = mysql_num_rows($req_file_more);
    $i = 0;
	if ($total_file) {
        while ($res_file_more = mysql_fetch_assoc($req_file_more)) {
            $format = explode('.', $res_file_more['name']);
            $format_file = strtolower($format[count($format) - 1]);
			echo (($i++ % 2) ? '<div class="list2">' : '<div class="list1">');
			echo '<b>' . $res_file_more['rus_name'] . '</b>' .
			'<div class="sub">' . $res_file_more['name'] . ' (' .  Download::displayFileSize($res_file_more['size']) . '), ' . functions::displayDate($res_file_more['time']) . '<br />' .
			'<a href="' . Vars::$URI . '?act=files_more&amp;id=' . Vars::$ID . '&amp;edit=' . $res_file_more['id'] . '">' . lng('edit') . '</a> | ' .
            '<span class="red"><a href="' . Vars::$URI . '?act=files_more&amp;id=' . Vars::$ID . '&amp;del=' . $res_file_more['id'] . '">' . lng('delete') . '</a></span></div></div>';
        }
        echo '<div class="phdr">' . lng('total') . ': ' . $total_file . '</div>';
    }
    echo '<p><a href="' . Vars::$URI. '?act=view&amp;id=' . Vars::$ID . '">' . lng('back') . '</a></p>';
}