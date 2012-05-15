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

if ($_GET['file'] == "") {
    echo Functions::displayError(lng('file_not_selected'), '<a href="' . Vars::$URI . '">' . lng('back') . '</a>');
    exit;
}
if ($_GET['f'] == "") {
    echo "Не выбран файл из архива<br/><a href='?act=zip&amp;file=" . $file . "'>В архив</a><br/>";
    exit;
}
$file = intval(trim($_GET['file']));
$file1 = mysql_query("select * from `download` where type = 'file' and id = '" . $file . "';");
$file2 = mysql_num_rows($file1);
$adrfile = mysql_fetch_array($file1);
if (($file1 == 0) || (!is_file("$adrfile[adres]/$adrfile[name]"))) {
    echo "Ошибка при выборе файла<br/><a href='?'>К категориям</a><br/>";
    exit;
}
$zip = new PclZip("$adrfile[adres]/$adrfile[name]");

if (($list = $zip->listContent()) == 0) {
    die("Ошибка: " . $zip->errorInfo(true));
}
for ($i = 0; $i < sizeof($list); $i++) {
    for (reset($list[$i]); $key = key($list[$i]); next($list[$i])) {
        $zfilesize = strstr($listcontent, "--size");
        $zfilesize = ereg_replace("--size:", "", $zfilesize);
        $zfilesize = @ ereg_replace("$zfilesize", "$zfilesize|", $zfilesize);
        $sizelist .= "$zfilesize";
        $listcontent = "[$i]--$key:" . $list[$i][$key] . "";
        $zfile = strstr($listcontent, "--filename");
        $zfile = ereg_replace("--filename:", "", $zfile);
        $zfile = @ ereg_replace("$zfile", "$zfile|", $zfile);
        $savelist .= "$zfile";
    }
}
$sizefiles2 = explode("|", $sizelist);
$sizelist2 = array_sum($sizefiles2);
$obkb = round($sizelist2 / 1024, 2);
$preview = "$savelist";
$preview = explode("|", $preview);
$sizefiles = explode("|", $sizelist);
$selectfile = explode("|", $savelist);
$f = $_GET['f'];
$path = $selectfile[$f];
$fname = ereg_replace(".*[\\/]", "", $path);
$zdir = ereg_replace("[\\/]?[^\\/]*$", "", $path);
$tfl = strtolower(Functions::format($fname));
$df = array("asp", "aspx", "shtml", "htd", "php", "php3", "php4", "php5", "phtml", "htt", "cfm", "tpl", "dtd", "hta", "pl", "js", "jsp");
if (!in_array($tfl, $df)) {
    $content = $zip->extract(PCLZIP_OPT_BY_NAME, $path, PCLZIP_OPT_EXTRACT_AS_STRING);
    $content1 = $zip->extract(PCLZIP_OPT_BY_NAME, $open, PCLZIP_OPT_EXTRACT_IN_OUTPUT);
    $content = $content[0]['content'];
    $FileName = $filesroot . 'arctemp' . DIRECTORY_SEPARATOR . $fname;
    $fid = @ fopen($FileName, "wb");
    if ($fid) {
        if (flock($fid, LOCK_EX)) {
            fwrite($fid, $content);
            flock($fid, LOCK_UN);
        }
        fclose($fid);
    }
    if (is_file($FileName)) {
        //TODO: Доработать!
        header("location: $filesroot/arctemp/$fname");
    }
}