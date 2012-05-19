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
Открытие ZIP прхива
-----------------------------------------------------------------
*/
$dir_clean = opendir(ROOTPATH . 'files/download/temp/open_zip');
while ($file = readdir($dir_clean)) {
    if ($file != 'index.php' && $file != '.htaccess' && $file != '.' && $file != '..' && $file != '.svn') {
        $time_file = filemtime(ROOTPATH . 'files/download/temp/open_zip/' . $file);
        if ($time_file < (time() - 300)) @unlink(ROOTPATH . 'files/download/temp/open_zip/' . $file);
    }
}
closedir($dir_clean);
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . Vars::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
if (mysql_num_rows($req_down) == 0 || !is_file($res_down['dir'] . '/' . $res_down['name']) || ($res_down['type'] == 3 && Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
    echo Functions::displayError(lng('not_found_file'), '<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
    exit;
}
if (isset($_GET['more'])) {
    $more = abs(intval($_GET['more']));
    $req_more = mysql_query("SELECT * FROM `cms_download_more` WHERE `id` = '$more' AND `refid`= '" . Vars::$ID . "' LIMIT 1");
    $res_more = mysql_fetch_assoc($req_more);
    if (!mysql_num_rows($req_more) || !is_file($res_down['dir'] . '/' . $res_more['name'])) {
        echo Functions::displayError(lng('not_found_file'), '<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
        exit;
    }
    $file_open = $res_down['dir'] . '/' . $res_more['name'];
    $isset_more = '&amp;more=' . $more;
    $title_pages = $res_more['rus_name'];
} else {
    $file_open = $res_down['dir'] . '/' . $res_down['name'];
    $title_pages = $res_down['rus_name'];
    $isset_more = '';
}
$title_pages = Validate::filterString(mb_substr($title_pages, 0, 20));
$textl = lng('open_archive') . ' &raquo; ' . (mb_strlen($res_down['rus_name']) > 20 ? $title_pages . '...' : $title_pages);
require (SYSPATH . 'lib/pclzip.lib.php');
$array = array('cgi', 'pl', 'asp', 'aspx', 'shtml', 'shtm', 'fcgi', 'fpl', 'jsp', 'py', 'htaccess', 'ini', 'php', 'php3', 'php4', 'php5', 'php6', 'phtml', 'phps');
if (!isset($_GET['file'])) {
    /*
	-----------------------------------------------------------------
	Открываем архив
	-----------------------------------------------------------------
	*/
    $zip = new PclZip($file_open);
    if (($list = $zip->listContent()) == 0) {
        echo functions::displayError(lng('open_archive_error'), '<p><a href="' . Vars::$URI. '?act=view&amp;id=' . Vars::$ID . '">' . lng('back') . '</a></p>');
		exit;
    }
    $list_size = false;
    $list_content = false;
    $save_list = false;
    for ($i = 0; $i < sizeof($list); $i++) {
        for (reset($list[$i]); $key = key($list[$i]); next($list[$i])) {
            $file_size = str_replace("--size:", "", strstr($list_content, "--size"));
            $list_size .= str_replace($file_size, $file_size . '|', $file_size);
            $list_content = "[$i]--$key:" . $list[$i][$key];
            $zip_file = str_replace("--filename:", "", strstr($list_content, "--filename"));
            $save_list .= str_replace($zip_file, $zip_file . '|', $zip_file);
        }
    }
    $file_size_two = explode('|', $list_size);
    /*
	-----------------------------------------------------------------
	Выводим список файлов
	-----------------------------------------------------------------
	*/
    echo '<div class="phdr"><b>' . lng('open_archive') . ':</b> ' . Validate::filterString($res_down['name']) . '</div>' .
    '<div class="topmenu">' . lng('open_archive_faq') . '</div>';
	$preview = explode('|', $save_list);
    $total = count($preview) - 1;
    /*
	-----------------------------------------------------------------
	Навигация
	-----------------------------------------------------------------
	*/
	if ($total > Vars::$USER_SET['page_size'])
		echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?id=' . Vars::$ID . '&amp;act=open_zip' . $isset_more . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
	if ($total > 0) {
        $end = Vars::$START + Vars::$USER_SET['page_size'];
        if ($end > $total) $end = $total;
		for ($i = Vars::$START; $i < $end; $i++) {
            $path = $preview[$i];
            $file_name = preg_replace("#.*[\\/]#si", '', $path);
            $dir = preg_replace("#[\\/]?[^\\/]*$#si", '', $path);
            $format = explode('.', $file_name);
            $format_file = strtolower($format[count($format) - 1]);
            echo (($i % 2) ? '<div class="list2">' : '<div class="list1">') .
			'<b>' . ($i + 1) . ')</b> ' . $dir . '/' . Validate::filterString(mb_convert_encoding($file_name, "UTF-8", "Windows-1251"));
            if ($file_size_two[$i] > 0) echo ' (' . Download::displayFileSize($file_size_two[$i]) . ')';
			if ($format_file)
				echo ' - <a href="' . Vars::$URI . '?act=open_zip&amp;id=' . Vars::$ID . '&amp;file=' . rawurlencode($path) . '&amp;page=' . Vars::$PAGE . $isset_more . '">' . (in_array($format_file, $array) ? lng('open_archive_code') : lng('download')) . '</a>';
            echo '</div>';
        }
    } else {
    	echo '<div class="rmenu"><p>' . lng('list_empty') . '</p></div>';
    }
    echo '<div class="gmenu">' . lng('open_archive_size') . ': ' . Download::displayFileSize(array_sum($file_size_two)) . '</div>' .
    '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
    /*
	-----------------------------------------------------------------
	Навигация
	-----------------------------------------------------------------
	*/
	if ($total > Vars::$USER_SET['page_size']) {
		echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?id=' . Vars::$ID . '&amp;act=open_zip' . $isset_more . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
 		'<p><form action="' . Vars::$URI . '" method="get">' .
  		'<input type="hidden" value="open_zip" name="act" />' .
  		'<input type="hidden" value="' . Vars::$ID . '" name="id" />' .
  		(isset($more) ? '<input type="hidden" value="' . $more . '" name="more" />' : '') .
    	'<input type="text" name="page" size="2"/><input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
	}
	echo '<p>';
} else {
    /*
	-----------------------------------------------------------------
	Просмотр и скачка файла
	-----------------------------------------------------------------
	*/
    $FileName = rawurldecode(trim($_GET['file']));
    $format = explode('.', $FileName);
    $format_file = strtolower($format[count($format) - 1]);
    if (strpos($FileName, '..') !== false or strpos($FileName, './') !== false) {
        echo functions::displayError(lng('not_found_file'), '<p><a href="' . Vars::$URI. '?act=open_zip&amp;id=' . Vars::$ID . $isset_more . '">' . lng('back') . '</a></p>');
        exit;
    }
    $FileName = htmlspecialchars(trim($FileName), ENT_QUOTES, 'UTF-8');
    $FileName = strtr($FileName, array('&' => '', '$' => '', '>' => '', '<' => '', '~' => '', '`' => '', '#' => '', '*' => ''));
    $zip = new PclZip($file_open);
    $content = $zip->extract(PCLZIP_OPT_BY_NAME, $FileName, PCLZIP_OPT_EXTRACT_AS_STRING);
    $content = $content[0]['content'];
	$FileName = preg_replace("#.*[\\/]#si", "", $FileName);
    if (in_array($format_file, $array)) {
    	/*
		-----------------------------------------------------------------
		Просмотр кода файла
		-----------------------------------------------------------------
		*/
        $UTF = false;
        $content_two = explode("\r\n", $content);
        echo '<div class="phdr"><b>' . htmlspecialchars(mb_convert_encoding($FileName, "UTF-8", "Windows-1251"), ENT_QUOTES, 'UTF-8') . '</b></div><div class="list1"><div class="phpcode">';
        $rus_simvol = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я');
        for ($i = 0; $i < 66; $i++) {
            if (strstr($content, $rus_simvol[$i]) !== false) $UTF = 1;
        }
        $php_code = trim($content);
        $php_code = substr($php_code, 0, 2) != "<?" ? "<?php\n" . $php_code . "\n?>" : $php_code;
        echo $UTF ? highlight_string($php_code, true) : highlight_string(iconv('windows-1251', 'utf-8', $php_code), true);
        echo '</div></div><div class="phdr">' . lng('total') . ': ' . count($content_two) . '</div>';
	} else {
    	/*
		-----------------------------------------------------------------
		Скачка файла
		-----------------------------------------------------------------
		*/
        $NewNameFile = strtr(Download::translateFileName(mb_convert_encoding($FileName, "UTF-8", "Windows-1251")), array(' ' => '_', '@' => '', '%' => ''));
        if (file_exists(ROOTPATH . 'files/download/temp/open_zip/' . $NewNameFile)) {
            header('Location: ' . Vars::$HOME_URL. '/files/download/temp/open_zip/' . $NewNameFile);
            exit;
        }
		$dir = @fopen(ROOTPATH . 'files/download/temp/open_zip/' . $NewNameFile, "wb");
        if ($dir) {
            if (flock($dir, LOCK_EX)) {
                fwrite($dir, $content);
                flock($dir, LOCK_UN);
            }
            fclose($dir);
            header('Location: ' . Vars::$HOME_URL. '/files/download/temp/open_zip/' . $NewNameFile);
            exit;
        }
        else  echo functions::displayError(lng('error_file_save'));
    }
	echo '<p><a href="' . Vars::$URI. '?act=open_zip&amp;id=' . Vars::$ID . '&amp;page=' . Vars::$PAGE . $isset_more . '">' . lng('back') . '</a><br />';
}
echo '<p><a href="' . Vars::$URI. '?act=view&amp;id=' . Vars::$ID . '">' . lng('download_title') . '</a></p>';