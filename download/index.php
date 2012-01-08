<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

$headmod = 'load';
require_once('../includes/core.php');
$lng_dl = Vars::loadLanguage('downloads');
require_once('../includes/lib/mp3.php');
require_once('../includes/lib/pclzip.lib.php');
$textl = Vars::$LNG['downloads'];
$filesroot = '../download';
$screenroot = "$filesroot/screen";
$loadroot = "$filesroot/files";

// Ограничиваем доступ к Загрузкам
$error = '';
if (!Vars::$SYSTEM_SET['mod_down'] && Vars::$USER_RIGHTS < 7)
    $error = $lng_dl['downloads_closed'];
elseif (Vars::$SYSTEM_SET['mod_down'] == 1 && !Vars::$USER_ID)
    $error = Vars::$LNG['access_guest_forbidden'];
if ($error) {
    require_once('../includes/head.php');
    echo '<div class="rmenu"><p>' . $error . '</p></div>';
    require_once("../includes/end.php");
    exit;
}

function provcat($catalog)
{
    $cat1 = mysql_query("select * from `download` where type = 'cat' and id = '" . $catalog . "';");
    $cat2 = mysql_num_rows($cat1);
    $adrdir = mysql_fetch_array($cat1);
    if (($cat2 == 0) || (!is_dir("$adrdir[adres]/$adrdir[name]"))) {
        echo 'ERROR<br/><a href="?">Back</a><br/>';
        require_once('../includes/end.php');
        exit;
    }
}

$array = array(
    'scan_dir',
    'rat',
    'delmes',
    'search',
    'addkomm',
    'komm',
    'new',
    'zip',
    'arc',
    'down',
    'dfile',
    'opis',
    'screen',
    'ren',
    'import',
    'refresh',
    'upl',
    'view',
    'makdir',
    'select',
    'preview',
    'delcat',
    'mp3'
);
if (in_array(Vars::$ACT, $array)) {
    require_once(Vars::$ACT . '.php');
} else {
    require_once('../includes/head.php');
    if (!Vars::$SYSTEM_SET['mod_down'])
        echo '<p><font color="#FF0000"><b>' . $lng_dl['downloads_closed'] . '</b></font></p>';
    // Ссылка на новые файлы
    echo '<p><a href="?act=new">' . Vars::$LNG['new_files'] . '</a> (' . mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `time` > '" . (time() - 259200) . "' AND `type` = 'file'"), 0) . ')</p>';
    $cat = isset($_GET['cat']) ? intval($_GET['cat']) : '';
    if (empty($_GET['cat'])) {
        // Заголовок начальной страницы загрузок
        echo '<div class="phdr">' . Vars::$LNG['downloads'] . '</div>';
    } else {
        // Заголовок страниц категорий
        $req = mysql_query("SELECT * FROM `download` WHERE `type` = 'cat' AND `id` = '" . $cat . "' LIMIT 1");
        $res = mysql_fetch_array($req);
        if (mysql_num_rows($req) == 0 || !is_dir($res['adres'] . '/' . $res['name'])) {
            // Если неправильно выбран каталог, выводим ошибку
            echo Functions::displayError($lng_dl['folder_does_not_exist'], '<a href="index.php">' . Vars::$LNG['back'] . '</a>');
            require_once('../includes/end.php');
            exit;
        }
        ////////////////////////////////////////////////////////////
        // Получаем структуру каталогов                           //
        ////////////////////////////////////////////////////////////
        $tree = array();
        $dirid = $cat;
        while ($dirid != '0' && $dirid != "") {
            $req = mysql_query("SELECT * FROM `download` WHERE `type` = 'cat' and `id` = '" . $dirid . "' LIMIT 1");
            $res = mysql_fetch_array($req);
            $tree[] = '<a href="index.php?cat=' . $dirid . '">' . $res['text'] . '</a>';
            $dirid = $res['refid'];
        }
        krsort($tree);
        $cdir = array_pop($tree);
        echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['downloads'] . '</b></a> | ';
        foreach ($tree as $value) {
            echo $value . ' | ';
        }
        echo strip_tags($cdir) . '</div>';
    }
    // Подсчитываем число папок
    $totalcat = mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `refid` = '$cat' AND `type` = 'cat'"), 0);
    // Подсчитываем число файлов
    $totalfile = mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `refid` = '$cat' AND `type` = 'file'"), 0);
    $total = (int)$totalcat + $totalfile;
    if ($total > 0) {
        $zap = mysql_query("SELECT * FROM `download` WHERE `refid` = '$cat' ORDER BY `type` ASC, `text` ASC, `name` ASC LIMIT " . Vars::db_pagination());
        while ($zap2 = mysql_fetch_array($zap)) {
            ////////////////////////////////////////////////////////////
            // Выводим список папок                                   //
            ////////////////////////////////////////////////////////////
            if ($totalcat > 0 && $zap2['type'] == 'cat') {
                echo '<div class="list1">';
                echo '<a href="?cat=' . $zap2['id'] . '">' . $zap2['text'] . '</a>';
                $g1 = 0;
                // Считаем число файлов в подкаталогах
                $req = mysql_query("SELECT COUNT(*) FROM `download` WHERE `type` = 'file' AND `adres` LIKE '" . ($zap2['adres'] . '/' . $zap2['name']) . "%'");
                $g = mysql_result($req, 0);
                // Считаем новые файлы в подкаталогах
                $req = mysql_query("SELECT COUNT(*) FROM `download` WHERE `type` = 'file' AND `adres` LIKE '" . ($zap2['adres'] . '/' . $zap2['name']) . "%' AND `time` > '" . (time() - 259200) . "'");
                $g1 = mysql_result($req, 0);
                echo "($g";
                if ($g1 != 0) {
                    echo "/+$g1)</div>";
                } else {
                    echo ")</div>";
                }
            }
            ////////////////////////////////////////////////////////////
            // Выводим cписок файлов                                  //
            ////////////////////////////////////////////////////////////
            if ($totalfile > 0 && $zap2['type'] == 'file') {
                echo '<div class="list2">';
                $ft = Functions::format($zap2['name']);
                switch ($ft) {
                    case "mp3":
                        $imt = "mp3.png";
                        break;

                    case "zip":
                        $imt = "rar.png";
                        break;

                    case "jar":
                        $imt = "jar.png";
                        break;

                    case "gif":
                        $imt = "gif.png";
                        break;

                    case "jpg":
                        $imt = "jpg.png";
                        break;

                    case "png":
                        $imt = "png.png";
                        break;
                    default :
                        $imt = "file.gif";
                        break;
                }
                echo '<img src="' . $filesroot . '/img/' . $imt . '" alt=""/><a href="?act=view&amp;file=' . $zap2['id'] . '">' . htmlentities($zap2['name'], ENT_QUOTES, 'UTF-8') . '</a>';
                if ($zap2['text'] != "") {
                    // Выводим анонс текстового описания (если есть)
                    $tx = $zap2['text'];
                    if (mb_strlen($tx) > 100) {
                        $tx = mb_substr(strip_tags($tx), 0, 90);
                        $tx .= '...';
                    }
                    echo '<div class="sub">' . Validate::filterString($tx) . '</div>';
                }
                echo '</div>';
            }
        }
    } else {
        echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
    }
    echo '<div class="phdr">';
    if ($totalcat > 0)
        echo $lng_dl['folders'] . ': ' . $totalcat;
    echo '&#160;&#160;';
    if ($totalfile > 0)
        echo $lng_dl['files'] . ': ' . $totalfile;
    echo '</div>';
    // Постраничная навигация
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<p>' . Functions::displayPagination('index.php?cat=' . $cat . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>';
    }
    if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
        ////////////////////////////////////////////////////////////
        // Выводим ссылки на модерские функции                    //
        ////////////////////////////////////////////////////////////
        echo '<p><div class="func">';
        echo '<a href="?act=makdir&amp;cat=' . $cat . '">' . $lng_dl['make_folder'] . '</a><br/>';
        if (!empty($_GET['cat'])) {
            $delcat = mysql_query("select * from `download` where type = 'cat' and refid = '" . $cat . "';");
            $delcat1 = mysql_num_rows($delcat);
            if ($delcat1 == 0) {
                echo '<a href="index.php?act=delcat&amp;cat=' . $cat . '">' . $lng_dl['delete_folder'] . '</a><br />';
            }
            echo '<a href="index.php?act=ren&amp;cat=' . $cat . '">' . $lng_dl['rename_folder'] . '</a><br />';
            echo '<a href="index.php?act=select&amp;cat=' . $cat . '">' . $lng_dl['upload_file'] . '</a><br />';
            echo '<a href="index.php?act=import&amp;cat=' . $cat . '">' . $lng_dl['import_file'] . '</a><br />';
        }
        echo '<a href="index.php?act=refresh">' . $lng_dl['refresh_downloads'] . '</a>';
        echo '</div></p>';
    }
    if (!empty($cat))
        echo '<p><a href="index.php">' . Vars::$LNG['downloads'] . '</a></p>';
    echo '<p><a href="index.php?act=preview">' . $lng_dl['images_size'] . '</a></p>';
    if (empty($cat)) {
        echo '<form action="index.php?act=search" method="post">';
        echo $lng_dl['search_file'] . ': <br/><input type="text" name="srh" size="20" maxlength="20" value=""/><br/>';
        echo '<input type="submit" value="' . Vars::$LNG['search'] . '"/></form>';
    }
}

require_once('../includes/end.php');